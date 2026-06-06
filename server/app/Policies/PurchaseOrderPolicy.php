<?php

namespace App\Policies;

use App\Models\PurchaseOrder;
use App\Models\User;

class PurchaseOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'procurement', 'manager', 'vendor']);
    }

    public function view(User $user, PurchaseOrder $purchaseOrder): bool
    {
        if ($user->hasRole('vendor')) {
            return $user->hasPermissionTo('view-purchase-orders') && $purchaseOrder->vendor_id === $user->vendor?->id;
        }
        return $user->hasAnyRole(['admin', 'procurement', 'manager']);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('generate-purchase-orders');
    }
}