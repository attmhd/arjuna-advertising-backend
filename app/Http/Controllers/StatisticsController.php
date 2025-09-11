<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    /**
     * Get invoice summary statistics for dashboard
     */
    public function invoiceSummary()
    {
        try {
            // Total income from paid invoices
            $totalIncome = Invoice::whereIn("status", [
                "paid",
                "Lunas",
                "lunas",
            ])->sum("grand_total");

            // Total receivable from pending invoices (grand_total - down_payment)
            $totalReceivable =
                Invoice::whereIn("status", [
                    "pending",
                    "partially_paid",
                    "overdue",
                    "Tertunda",
                    "Tenggat Waktu",
                    "tertunda",
                    "tenggat waktu",
                ])
                    ->selectRaw("SUM(grand_total - down_payment) as receivable")
                    ->value("receivable") ?? 0;

            // Top 3 products by quantity sold
            $topProducts = DB::table("invoice_items")
                ->join(
                    "inventory",
                    "invoice_items.inventory_id",
                    "=",
                    "inventory.id",
                )
                ->join(
                    "invoices",
                    "invoice_items.invoice_id",
                    "=",
                    "invoices.id",
                )
                ->whereNotIn("invoices.status", ["cancelled", "draft"])
                ->select(
                    "inventory.product_name as name",
                    DB::raw("SUM(invoice_items.quantity) as total_sold"),
                )
                ->groupBy("inventory.id", "inventory.product_name")
                ->orderBy("total_sold", "desc")
                ->limit(3)
                ->get()
                ->map(function ($item) {
                    return [
                        "name" => $item->name,
                        "total_sold" => (int) $item->total_sold,
                    ];
                });

            // Monthly sales for last 6 months
            $monthlySales = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $monthName = $date->format("M");

                $startOfMonth = $date->copy()->startOfMonth()->format("Y-m-d");
                $endOfMonth = $date->copy()->endOfMonth()->format("Y-m-d");

                $total = Invoice::whereDate("issue_date", ">=", $startOfMonth)
                    ->whereDate("issue_date", "<=", $endOfMonth)
                    ->whereNotIn("status", [
                        "cancelled",
                        "draft",
                        "dibatalkan",
                        "konsep",
                    ])
                    ->sum("grand_total");

                $monthlySales[] = [
                    "month" => $monthName,
                    "total" => (int) $total,
                ];
            }

            // Customer sources
            $customerSources = Invoice::whereNotIn("status", [
                "cancelled",
                "draft",
            ])
                ->select("source", DB::raw("COUNT(*) as count"))
                ->groupBy("source")
                ->pluck("count", "source")
                ->map(function ($count) {
                    return (int) $count;
                })
                ->toArray();

            return response()->json([
                "total_income" => (int) $totalIncome,
                "total_receivable" => (int) $totalReceivable,
                "top_products" => $topProducts,
                "monthly_sales" => $monthlySales,
                "customer_sources" => $customerSources,
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    "status" => "error",
                    "message" =>
                        "Failed to fetch invoice summary: " . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Get detailed invoice report statistics
     */
    public function invoiceReport(Request $request)
    {
        try {
            $period = $request->get("period", "thisMonth");

            // Determine date range based on period
            $dateRange = $this->getDateRange($period);
            $startDate = $dateRange["start"];
            $endDate = $dateRange["end"];

            // Base query for the period
            $baseQuery = Invoice::whereBetween("issue_date", [
                $startDate,
                $endDate,
            ]);

            // Total income from paid invoices in period
            $totalIncome = (clone $baseQuery)
                ->whereIn("status", ["paid", "Lunas", "lunas"])
                ->sum("grand_total");

            // Total receivable from pending invoices in period
            $totalReceivable =
                (clone $baseQuery)
                    ->whereIn("status", [
                        "pending",
                        "partially_paid",
                        "overdue",
                        "Tertunda",
                        "Tenggat Waktu",
                        "tertunda",
                        "tenggat waktu",
                    ])
                    ->selectRaw("SUM(grand_total - down_payment) as receivable")
                    ->value("receivable") ?? 0;

            // Top product in the period
            $topProductQuery = DB::table("invoice_items")
                ->join(
                    "inventory",
                    "invoice_items.inventory_id",
                    "=",
                    "inventory.id",
                )
                ->join(
                    "invoices",
                    "invoice_items.invoice_id",
                    "=",
                    "invoices.id",
                )
                ->whereBetween("invoices.issue_date", [$startDate, $endDate])
                ->whereNotIn("invoices.status", ["cancelled", "draft"])
                ->select(
                    "inventory.product_name as name",
                    DB::raw("SUM(invoice_items.quantity) as total_sold"),
                )
                ->groupBy("inventory.id", "inventory.product_name")
                ->orderBy("total_sold", "desc")
                ->first();

            $topProduct = $topProductQuery ? $topProductQuery->name : null;

            // Weekly income (only for monthly periods)
            $weeklyIncome = [];
            if (in_array($period, ["thisMonth", "lastMonth"])) {
                $weeklyIncome = $this->getWeeklyIncome($startDate, $endDate);
            }

            // Product sales in the period
            $productSales = DB::table("invoice_items")
                ->join(
                    "inventory",
                    "invoice_items.inventory_id",
                    "=",
                    "inventory.id",
                )
                ->join(
                    "invoices",
                    "invoice_items.invoice_id",
                    "=",
                    "invoices.id",
                )
                ->whereBetween("invoices.issue_date", [$startDate, $endDate])
                ->whereNotIn("invoices.status", ["cancelled", "draft"])
                ->select(
                    "inventory.product_name as name",
                    DB::raw("SUM(invoice_items.quantity) as quantity"),
                )
                ->groupBy("inventory.id", "inventory.product_name")
                ->orderBy("quantity", "desc")
                ->get()
                ->map(function ($item) {
                    return [
                        "name" => $item->name,
                        "quantity" => (int) $item->quantity,
                    ];
                });

            // Invoices list in the period
            $invoices = (clone $baseQuery)
                ->select([
                    "invoice_number",
                    "customer_name",
                    "issue_date",
                    "grand_total as total_amount",
                    "status",
                    "down_payment as dp",
                ])
                ->orderBy("issue_date", "desc")
                ->get()
                ->map(function ($invoice) {
                    return [
                        "invoice_number" => $invoice->invoice_number,
                        "customer_name" => $invoice->customer_name,
                        "issue_date" => Carbon::parse(
                            $invoice->issue_date,
                        )->format("Y-m-d"),
                        "total_amount" => (int) $invoice->total_amount,
                        "status" => ucfirst($invoice->status),
                        "dp" => (int) $invoice->dp,
                    ];
                });

            return response()->json([
                "total_income" => (int) $totalIncome,
                "total_receivable" => (int) $totalReceivable,
                "top_product" => $topProduct,
                "weekly_income" => $weeklyIncome,
                "product_sales" => $productSales,
                "invoices" => $invoices,
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    "status" => "error",
                    "message" =>
                        "Failed to fetch invoice report: " . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Get date range based on period parameter
     */
    private function getDateRange($period)
    {
        switch ($period) {
            case "lastMonth":
                return [
                    "start" => Carbon::now()->subMonth()->startOfMonth(),
                    "end" => Carbon::now()->subMonth()->endOfMonth(),
                ];
            case "thisYear":
                return [
                    "start" => Carbon::now()->startOfYear(),
                    "end" => Carbon::now()->endOfYear(),
                ];
            case "thisMonth":
            default:
                return [
                    "start" => Carbon::now()->startOfMonth(),
                    "end" => Carbon::now()->endOfMonth(),
                ];
        }
    }

    /**
     * Get weekly income for a given date range
     */
    private function getWeeklyIncome($startDate, $endDate)
    {
        $weeklyIncome = [0, 0, 0, 0]; // Initialize 4 weeks

        $invoices = Invoice::whereBetween("issue_date", [$startDate, $endDate])
            ->whereNotIn("status", [
                "cancelled",
                "draft",
                "dibatalkan",
                "konsep",
            ])
            ->select("issue_date", "grand_total")
            ->get();

        foreach ($invoices as $invoice) {
            $issueDate = Carbon::parse($invoice->issue_date);
            $weekOfMonth = $issueDate->weekOfMonth;

            // Ensure week index is within bounds (1-4)
            if ($weekOfMonth >= 1 && $weekOfMonth <= 4) {
                $weeklyIncome[$weekOfMonth - 1] += $invoice->grand_total;
            }
        }

        return array_map("intval", $weeklyIncome);
    }

    /**
     * Debug method to check actual invoice statuses in database
     */
    public function debugStatuses()
    {
        $statuses = Invoice::select("status", DB::raw("COUNT(*) as count"))
            ->groupBy("status")
            ->get();

        $allInvoices = Invoice::select(
            "id",
            "invoice_number",
            "status",
            "issue_date",
            "grand_total",
        )
            ->orderBy("created_at", "desc")
            ->limit(10)
            ->get();

        return response()->json([
            "status_counts" => $statuses,
            "recent_invoices" => $allInvoices,
            "total_invoices" => Invoice::count(),
        ]);
    }
}
