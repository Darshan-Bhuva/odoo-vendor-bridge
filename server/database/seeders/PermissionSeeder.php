<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        } elseif ($driver === 'pgsql') {
            DB::statement('SET session_replication_role = replica;');
        }

        Permission::truncate();
        DB::table('role_has_permissions')->truncate();

        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } elseif ($driver === 'pgsql') {
            DB::statement('SET session_replication_role = DEFAULT;');
        }

        $permissionsByRole = [
            config('site.roles.procurement') => [
                'create-rfqs',
                'compare-quotations',
                'generate-purchase-orders',
                'generate-invoices'
            ],
            config('site.roles.vendor') => [
                'submit-quotations',
                'track-rfq-status',
                'view-purchase-orders'
            ],
            config('site.roles.manager') => [
                'approve-procurement-requests',
                'reject-procurement-requests',
                'monitor-procurement-workflows'
            ],
            config('site.roles.admin') => [
                'manage-users',
                'manage-vendors',
                'view-procurement-analytics'
            ]
        ];

        // Flatten permissions and create them uniquely
        $allPermissions = collect($permissionsByRole)->flatten()->unique();
        
        $permissionData = $allPermissions->map(function ($name) {
            return [
                'name' => $name,
                'guard_name' => 'api',
            ];
        })->toArray();

        Permission::insert($permissionData);

        // Assign permissions to roles
        foreach ($permissionsByRole as $roleName => $permissions) {
            $role = Role::where('name', $roleName)->where('guard_name', 'api')->first();
            if ($role) {
                $role->givePermissionTo($permissions);
            }
        }

        // Admin gets all permissions
        $adminRole = Role::where('name', config('site.roles.admin'))->where('guard_name', 'api')->first();
        if ($adminRole) {
            $adminRole->syncPermissions(Permission::all());
        }
    }
}
