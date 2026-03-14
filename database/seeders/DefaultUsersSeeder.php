<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Manager / Admin
        User::updateOrCreate(
            ['email' => 'manager@invento.com'],
            [
                'name'      => 'Inventory Manager',
                'password'  => Hash::make('manager123'),
                'role'      => 'admin',
                'is_active' => true,
            ]
        );

        // Staff
        User::updateOrCreate(
            ['email' => 'staff@invento.com'],
            [
                'name'      => 'Warehouse Staff',
                'password'  => Hash::make('staff123'),
                'role'      => 'staff',
                'is_active' => true,
            ]
        );
    }
}
