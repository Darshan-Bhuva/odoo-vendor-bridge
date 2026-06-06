<?php

namespace App\Policies;

use App\Models\Vendor;
use App\Models\User;

class VendorPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'procurement', 'manager']);
    }

    public function view(User $user, Vendor $vendor): bool
    {
        if ($user->hasRole('vendor')) {
            return $vendor->user_id === $user->id;
        }
        return $user->hasAnyRole(['admin', 'procurement', 'manager']);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage-vendors') || $user->hasAnyRole(['admin', 'procurement']);
    }

    public function update(User $user, Vendor $vendor): bool
    {
        if ($user->hasRole('vendor') && $vendor->user_id === $user->id) {
            return true; // Vendors can update their own profile
        }
        return $user->hasPermissionTo('manage-vendors') || $user->hasAnyRole(['admin', 'procurement']);
    }
}