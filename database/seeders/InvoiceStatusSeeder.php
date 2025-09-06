<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InvoiceStatus;

class InvoiceStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $status = ["Tertunda", "Lunas", "Jatuh Tempo"];

        InvoiceStatus::withoutTimestamps(function () use ($status) {
            foreach ($status as $s) {
                InvoiceStatus::firstOrCreate(["status_name" => $s]);
            }
        });
    }
}
