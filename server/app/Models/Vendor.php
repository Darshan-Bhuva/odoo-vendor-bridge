<?php

namespace App\Models;

use App\Traits\BaseModel;
use App\Enums\Procurement\VendorStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id', 'company_name', 'vendor_code', 'category', 'gst_number',
    'contact_person', 'email', 'address', 'status',
])]
class Vendor extends Model
{
    use HasFactory, BaseModel;

    protected function casts(): array
    {
        return [
            'status' => VendorStatusEnum::class,
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rfqs(): BelongsToMany
    {
        return $this->belongsToMany(Rfq::class, 'rfq_vendor')
            ->withPivot(['invitation_status', 'invited_at', 'responded_at'])
            ->withTimestamps();
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}