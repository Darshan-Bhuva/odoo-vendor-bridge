<?php

namespace App\Policies;

use App\Models\Quotation;
use App\Models\User;

class QuotationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'procurement', 'manager', 'vendor']);
    }

    public function view(User $user, Quotation $quotation): bool
    {
        if ($user->hasRole('vendor')) {
            return $quotation->vendor_id === $user->vendor?->id;
        }
        return $user->hasAnyRole(['admin', 'procurement', 'manager']);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('submit-quotations');
    }

    public function update(User $user, Quotation $quotation): bool
    {
        if ($user->hasRole('vendor')) {
            return $user->hasPermissionTo('submit-quotations') && $quotation->vendor_id === $user->vendor?->id;
        }
        return false;
    }

    public function compare(User $user): bool
    {
        return $user->hasPermissionTo('compare-quotations');
    }
}