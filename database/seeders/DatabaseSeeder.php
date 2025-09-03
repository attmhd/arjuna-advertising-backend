<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Unit;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            UserSeeder::class,
            UnitSeeder::class,
        ]);
    }
}
