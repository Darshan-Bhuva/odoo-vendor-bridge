<?php

namespace App\Policies;

use App\Models\Rfq;
use App\Models\User;

class RfqPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'procurement', 'manager']);
    }

    public function view(User $user, Rfq $rfq): bool
    {
        if ($user->hasAnyRole(['admin', 'procurement', 'manager'])) {
            return true;
        }
        if ($user->hasRole('vendor')) {
            return $rfq->vendors()->where('vendor_id', $user->vendor?->id)->exists();
        }
        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create-rfqs');
    }

    public function update(User $user, Rfq $rfq): bool
    {
        return $user->hasPermissionTo('create-rfqs');
    }

    public function delete(User $user, Rfq $rfq): bool
    {
        return $user->hasPermissionTo('create-rfqs');
    }
}