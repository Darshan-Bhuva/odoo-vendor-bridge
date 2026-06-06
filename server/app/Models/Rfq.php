<?php

namespace App\Models;

use App\Traits\BaseModel;
use Plank\Mediable\Mediable;
use App\Enums\Procurement\RfqStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'created_by', 'rfq_number', 'title', 'description', 
    'deadline', 'status', 'selected_quotation_id',
])]
class Rfq extends Model
{
    use HasFactory, BaseModel, Mediable;

    protected function casts(): array
    {
        return [
            'deadline' => 'datetime',
            'status' => RfqStatusEnum::class,
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RfqItem::class);
    }

    public function vendors(): BelongsToMany
    {
        return $this->belongsToMany(Vendor::class, 'rfq_vendor')
            ->withPivot(['invitation_status', 'invited_at', 'responded_at'])
            ->withTimestamps();
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function approval(): HasOne
    {
        return $this->hasOne(Approval::class);
    }

    public function purchaseOrder(): HasOne
    {
        return $this->hasOne(PurchaseOrder::class);
    }

    public function selectedQuotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class, 'selected_quotation_id');
    }
}