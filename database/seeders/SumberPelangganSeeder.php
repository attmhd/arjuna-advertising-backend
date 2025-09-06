<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SumberPelanggan;

class SumberPelangganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sumber_pelanggan = [
            "Project",
            "Customer Order",
            "Sosial Media",
            "Other",
        ];

        SumberPelanggan::withoutTimestamps(function () use ($sumber_pelanggan) {
            foreach ($sumber_pelanggan as $sp) {
                SumberPelanggan::firstOrCreate(["nama_sumber" => $sp]);
            }
        });
    }
}
