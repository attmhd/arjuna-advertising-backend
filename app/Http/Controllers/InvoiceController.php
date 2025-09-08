<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $invoices = Invoice::with("items")->get();
            return response()->json(
                [
                    "status" => "success",
                    "data" => $invoices,
                    "message" => "Invoices retrieved successfully",
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "customer_name" => "required|string",
            "source_id" => "required|exists:sumber_pelanggans,id",
            "due_date" => "required|date",
            "status_id" => "required|exists:invoice_statuses,id",
            "items" => "required|array",
            "items.*.inventory_id" => "required|exists:inventory,id",
            "items.*.quantity" => "required|integer|min:1",
        ]);

        try {
            DB::beginTransaction();

            $invoice = Invoice::create([
                "customer_name" => $request->customer_name,
                "source_id" => $request->source_id,
                "issue_date" => now(),
                "due_date" => $request->due_date,
                "discount" => $request->discount ?? 0,
                "tax_enabled" => $request->tax_enabled ?? false,
                "status_id" => $request->status_id,
            ]);

            $grandTotal = 0;

            foreach ($request->items as $itemData) {
                $inventory = Inventory::find($itemData["inventory_id"]);

                if ($inventory->stock < $itemData["quantity"]) {
                    throw new \Exception(
                        "Insufficient stock for product: " .
                            $inventory->product_name,
                    );
                }

                $subTotal = $inventory->price * $itemData["quantity"];

                InvoiceItem::create([
                    "invoice_id" => $invoice->id,
                    "inventory_id" => $inventory->id,
                    "quantity" => $itemData["quantity"],
                    "price" => $inventory->price,
                    "sub_total" => $subTotal,
                ]);

                $inventory->stock -= $itemData["quantity"];
                $inventory->save();

                $grandTotal += $subTotal;
            }

            $invoice->grand_total = $grandTotal;
            $invoice->save();

            DB::commit();

            return response()->json(
                [
                    "status" => "success",
                    "data" => $invoice->load("items"),
                    "message" => "Invoice created successfully",
                ],
                201,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    "status" => "error",
                    "message" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        try {
            return response()->json(
                [
                    "status" => "success",
                    "data" => $invoice->load("items"),
                    "message" => "Invoice retrieved successfully",
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            "customer_name" => "sometimes|required|string",
            "source_id" => "sometimes|required|exists:sumber_pelanggans,id",
            "due_date" => "sometimes|required|date",
            "status_id" => "sometimes|required|exists:invoice_statuses,id",
            "items" => "sometimes|array",
            "items.*.inventory_id" => "required|exists:inventory,id",
            "items.*.quantity" => "required|integer|min:1",
        ]);

        try {
            DB::beginTransaction();

            // Update invoice details
            $invoice->update(
                $request->only([
                    "customer_name",
                    "source_id",
                    "due_date",
                    "status_id",
                    "discount",
                    "tax_enabled",
                ]),
            );

            if ($request->has("items")) {
                $newItems = $request->items;
                $oldItems = $invoice->items->keyBy("inventory_id");
                $newItemsCollection = collect($newItems)->keyBy("inventory_id");

                // Items to delete
                foreach ($oldItems as $oldItemId => $oldItem) {
                    if (!$newItemsCollection->has($oldItemId)) {
                        $inventory = Inventory::find($oldItem->inventory_id);
                        $inventory->stock += $oldItem->quantity;
                        $inventory->save();
                        $oldItem->delete();
                    }
                }

                // Items to add or update
                foreach ($newItems as $itemData) {
                    $inventory = Inventory::find($itemData["inventory_id"]);
                    $oldItem = $oldItems->get($inventory->id);

                    $newQuantity = $itemData["quantity"];

                    if ($oldItem) {
                        // Update existing item
                        $quantityDiff = $newQuantity - $oldItem->quantity;
                        if ($inventory->stock < $quantityDiff) {
                            throw new \Exception(
                                "Insufficient stock for product: " .
                                    $inventory->product_name,
                            );
                        }
                        $inventory->stock -= $quantityDiff;
                        $oldItem->quantity = $newQuantity;
                        $oldItem->sub_total = $inventory->price * $newQuantity;
                        $oldItem->save();
                    } else {
                        // Add new item
                        if ($inventory->stock < $newQuantity) {
                            throw new \Exception(
                                "Insufficient stock for product: " .
                                    $inventory->product_name,
                            );
                        }
                        InvoiceItem::create([
                            "invoice_id" => $invoice->id,
                            "inventory_id" => $inventory->id,
                            "quantity" => $newQuantity,
                            "price" => $inventory->price,
                            "sub_total" => $inventory->price * $newQuantity,
                        ]);
                        $inventory->stock -= $newQuantity;
                    }
                    $inventory->save();
                }
            }

            // Recalculate grand total
            $invoice->refresh(); // Refresh to get the latest items
            $invoice->grand_total = $invoice->items()->sum("sub_total");
            $invoice->save();

            DB::commit();

            return response()->json(
                [
                    "status" => "success",
                    "data" => $invoice->load("items"),
                    "message" => "Invoice updated successfully",
                ],
                200,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    "status" => "error",
                    "message" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        try {
            DB::beginTransaction();

            foreach ($invoice->items as $item) {
                $inventory = Inventory::find($item->inventory_id);
                $inventory->stock += $item->quantity;
                $inventory->save();
            }

            $invoice->delete();

            DB::commit();

            return response()->json(
                [
                    "status" => "success",
                    "message" => "Invoice deleted successfully",
                ],
                200,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    "status" => "error",
                    "message" => $e->getMessage(),
                ],
                500,
            );
        }
    }
}
