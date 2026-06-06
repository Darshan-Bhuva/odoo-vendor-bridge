<?php

namespace App\Models;

use App\Traits\BaseModel;
use App\Enums\Procurement\QuotationStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'rfq_id', 'vendor_id', 'quotation_number', 'delivery_days', 
    'notes', 'subtotal', 'tax_amount', 'total_amount', 'status', 'submitted_at',
])]
class Quotation extends Model
{
    use HasFactory, BaseModel;

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'status' => QuotationStatusEnum::class,
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
        ];
    }

    public function rfq(): BelongsTo
    {
        return $this->belongsTo(Rfq::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function approval(): HasOne
    {
        return $this->hasOne(Approval::class);
    }

    public function purchaseOrder(): HasOne
    {
        return $this->hasOne(PurchaseOrder::class);
    }
}