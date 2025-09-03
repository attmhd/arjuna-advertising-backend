<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = ["Meter", "Pcs", "Box"];

        Unit::withoutTimestamps(function () use ($units) {
            foreach ($units as $name) {
                Unit::firstOrCreate(["unit_name" => $name]);
            }
        });
    }
}
