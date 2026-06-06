<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'procurement', 'manager', 'vendor']);
    }

    public function view(User $user, Invoice $invoice): bool
    {
        if ($user->hasRole('vendor')) {
            return $invoice->vendor_id === $user->vendor?->id;
        }
        return $user->hasAnyRole(['admin', 'procurement', 'manager']);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('generate-invoices');
    }
}