<?php

namespace Database\Seeders;

use Hash;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::create(["name" => "user"]);

        $user = User::factory()->create([
            "name" => "John Doe",
            "email" => "john.doe@example.com",
            "password" => bcrypt("test_123"),
        ]);

        $user->assignRole($role);
    }
}
