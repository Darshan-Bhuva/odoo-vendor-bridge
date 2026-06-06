<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id', 'module', 'reference_type', 'reference_id', 'action', 'description',
])]
class ActivityLog extends Model
{
    use HasFactory, BaseModel;

    public $timestamps = false; // Because we only have created_at in the schema

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
            'created_at' => 'timestamp',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}