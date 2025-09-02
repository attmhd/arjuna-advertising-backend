<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Buat role
        $adminRole = Role::firstOrCreate(["name" => "admin"]);
        $karyawanRole = Role::firstOrCreate(["name" => "karyawan"]);

        // (opsional) Buat permission
        $permissions = ["manage users", "manage transaksi", "view laporan"];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(["name" => $perm]);
        }

        // Assign permission ke role
        $adminRole->givePermissionTo(Permission::all());
        $karyawanRole->givePermissionTo(["view laporan"]); // contoh: karyawan hanya bisa lihat laporan

        // (opsional) Assign role ke user
        $admin = User::first();
        if ($admin) {
            $admin->assignRole("admin");
        }
    }
}
