<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Inventory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // Create test inventories first if they don't exist
            $inventories = [
                [
                    'product_name' => 'Banner Vinyl Premium',
                    'type' => 'Material',
                    'quality' => 'Premium',
                    'unit' => 'm²',
                    'stock' => 100,
                ],
                [
                    'product_name' => 'Sticker Vinyl',
                    'type' => 'Material',
                    'quality' => 'Standard',
                    'unit' => 'm²',
                    'stock' => 150,
                ],
                [
                    'product_name' => 'Spanduk Kain',
                    'type' => 'Material',
                    'quality' => 'Premium',
                    'unit' => 'm²',
                    'stock' => 75,
                ],
            ];

            foreach ($inventories as $inventoryData) {
                $inventory = Inventory::firstOrCreate(
                    ['product_name' => $inventoryData['product_name']],
                    $inventoryData
                );
            }

            // Create sample invoices with different down payment scenarios
            $invoices = [
                [
                    'customer_name' => 'PT ABC Company',
                    'source' => 'website',
                    'issue_date' => now(),
                    'due_date' => now()->addDays(30),
                    'discount' => 50000,
                    'down_payment' => 500000,
                    'tax_enabled' => true,
                    'status' => 'partially_paid',
                    'items' => [
                        ['inventory_id' => 1, 'quantity' => 10, 'price' => 75000],
                        ['inventory_id' => 2, 'quantity' => 5, 'price' => 120000],
                    ]
                ],
                [
                    'customer_name' => 'CV XYZ Printing',
                    'source' => 'phone',
                    'issue_date' => now(),
                    'due_date' => now()->addDays(14),
                    'discount' => 0,
                    'down_payment' => 1000000,
                    'tax_enabled' => true,
                    'status' => 'paid',
                    'items' => [
                        ['inventory_id' => 1, 'quantity' => 8, 'price' => 75000],
                        ['inventory_id' => 3, 'quantity' => 12, 'price' => 65000],
                    ]
                ],
                [
                    'customer_name' => 'Toko Mandiri Jaya',
                    'source' => 'walk_in',
                    'issue_date' => now(),
                    'due_date' => now()->addDays(21),
                    'discount' => 25000,
                    'down_payment' => 0,
                    'tax_enabled' => false,
                    'status' => 'pending',
                    'items' => [
                        ['inventory_id' => 2, 'quantity' => 15, 'price' => 110000],
                    ]
                ],
                [
                    'customer_name' => 'PT Kreatif Media',
                    'source' => 'email',
                    'issue_date' => now(),
                    'due_date' => now()->addDays(45),
                    'discount' => 100000,
                    'down_payment' => 250000,
                    'tax_enabled' => true,
                    'status' => 'partially_paid',
                    'items' => [
                        ['inventory_id' => 1, 'quantity' => 6, 'price' => 80000],
                        ['inventory_id' => 2, 'quantity' => 4, 'price' => 125000],
                        ['inventory_id' => 3, 'quantity' => 8, 'price' => 70000],
                    ]
                ],
                [
                    'customer_name' => 'UD Berkah Advertising',
                    'source' => 'website',
                    'issue_date' => now(),
                    'due_date' => now()->addDays(7),
                    'discount' => 0,
                    'down_payment' => 0,
                    'tax_enabled' => false,
                    'status' => 'overdue',
                    'items' => [
                        ['inventory_id' => 3, 'quantity' => 20, 'price' => 60000],
                    ]
                ],
            ];

            foreach ($invoices as $invoiceData) {
                $items = $invoiceData['items'];
                unset($invoiceData['items']);

                // Create invoice
                $invoice = Invoice::create($invoiceData);

                $subTotalItems = 0;

                // Create invoice items and update inventory stock
                foreach ($items as $itemData) {
                    $inventory = Inventory::find($itemData['inventory_id']);

                    if ($inventory) {
                        $subTotal = $itemData['price'] * $itemData['quantity'];

                        InvoiceItem::create([
                            'invoice_id' => $invoice->id,
                            'inventory_id' => $inventory->id,
                            'quantity' => $itemData['quantity'],
                            'price' => $itemData['price'],
                            'sub_total' => $subTotal,
                        ]);

                        // Update inventory stock
                        $inventory->stock -= $itemData['quantity'];
                        $inventory->save();

                        $subTotalItems += $subTotal;
                    }
                }

                // Calculate and update grand total
                $totalAfterDiscount = $subTotalItems - $invoice->discount;
                $taxAmount = $invoice->tax_enabled ? $totalAfterDiscount * 0.11 : 0;
                $invoice->grand_total = $totalAfterDiscount + $taxAmount;
                $invoice->save();

                echo "Created invoice: {$invoice->invoice_number} for {$invoice->customer_name} (Down Payment: " . number_format($invoice->down_payment) . ", Grand Total: " . number_format($invoice->grand_total) . ")\n";
            }

            DB::commit();
            echo "Invoice seeding completed successfully!\n";

        } catch (\Exception $e) {
            DB::rollBack();
            echo "Error seeding invoices: " . $e->getMessage() . "\n";
        }
    }
}
