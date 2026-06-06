<?php

namespace App\Models;

use App\Enums\UserOtpFor;
use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'otp', 'user_id', 'otp_for', 'verified_at',
])]
#[Hidden(['updated_at', 'deleted_at'])]
class UserOtp extends Model
{
    use BaseModel;

    protected function casts(): array
    {
        return [
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
            'deleted_at' => 'timestamp',
            'otp_for' => UserOtpFor::class,
        ];
    }

    protected $exactFilters = [
        'id',
        'otp',
        'otp_for',
        'user_id',
    ];

    protected $relationship = [
        'user' => [
            'model' => User::class,
        ],
        'media' => [
            'model' => Media::class,
        ],
    ];

    /**
     * Model's relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
