<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'user_id', 'module', 'reference_type', 'reference_id', 'action', 'description', 'created_at',
])]
class ActivityLog extends Model
{
    use BaseModel;

    // The activity logs table doesn't have updated_at
    const UPDATED_AT = null;

    protected static function booted()
    {
        static::updating(function () {
            throw new \Exception('Activity logs are immutable and cannot be updated.');
        });

        static::deleting(function () {
            throw new \Exception('Activity logs are immutable and cannot be deleted.');
        });
    }

    protected function casts(): array
    {
        return [
            'reference_id' => 'integer',
            'created_at' => 'timestamp',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the owning reference model (e.g. Rfq, Quotation, PO, Invoice).
     */
    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}