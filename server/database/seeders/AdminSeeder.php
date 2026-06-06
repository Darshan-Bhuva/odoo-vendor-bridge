<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\UserStatus;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = env('APP_ADMIN_EMAIL', 'admin@example.com');
        $password = env('APP_ADMIN_PASSWORD', 'password');

        $user = User::where('email', $email)->first();
        if (!$user) {
            $user = User::create([
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'username' => 'admin',
                'email' => $email,
                'password' => Hash::make($password),
                'status' => UserStatus::ACTIVE,
            ]);
        }

        // Assign admin role if not already assigned
        $adminRole = config('site.roles.admin');
        if ($adminRole && !$user->hasRole($adminRole)) {
            $user->assignRole($adminRole);
        }
    }
}
