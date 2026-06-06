<?php

namespace App\Policies;

use App\Models\Approval;
use App\Models\User;

class ApprovalPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'manager', 'procurement']);
    }

    public function view(User $user, Approval $approval): bool
    {
        return $user->hasAnyRole(['admin', 'manager', 'procurement']);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('approve-procurement-requests');
    }
    
    public function update(User $user, Approval $approval): bool
    {
        return $user->hasPermissionTo('approve-procurement-requests');
    }
}