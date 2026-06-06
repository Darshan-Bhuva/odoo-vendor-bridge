<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Enums\UserStatus;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@vendorbridge.com'],
            [
                'first_name' => 'System',
                'last_name' => 'Admin',
                'username' => 'admin',
                'password' => Hash::make('password123'),
                'status' => 'active',
                'mobile_no' => '9999999999'
            ]
        );

        // Assign the admin role
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }
    }
}
