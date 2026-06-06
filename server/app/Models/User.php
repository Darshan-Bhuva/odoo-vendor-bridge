<?php

namespace App\Models;

use App\Enums\UserStatus;
use App\Traits\BaseModel;
use Plank\Mediable\Mediable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\Contracts\OAuthenticatable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Appends;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property UserStatus $status
 */
#[Fillable([
    'first_name', 'last_name', 'username', 'email', 'status', 'password',
    'mobile_no', 'email_verified_at', 'last_login_at', 'created_at', 'avatar'
])]
#[Hidden([
    'password', 'remember_token',
])]
#[Appends([
    'name', 'display_status', 'display_mobile_no',
])]
class User extends Authenticatable implements OAuthenticatable
{
    use BaseModel, HasApiTokens, HasRoles, Mediable, Notifiable, SoftDeletes;

    protected $guard_name = 'api';
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'timestamp',
            'password' => 'hashed',
            'last_login_at' => 'timestamp',
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
            'deleted_at' => 'timestamp',
            'status' => UserStatus::class,
        ];
    }

    /** Accessors and Mutators */
    protected $relationship = [
        'user_device' => [
            'model' => UserDevice::class,
        ],
    ];

    public function userDevice()
    {
        return $this->hasOne(UserDevice::class);
    }

    public function rfqs()
    {
        return $this->hasMany(Rfq::class, 'created_by');
    }

    public function vendor()
    {
        return $this->hasOne(Vendor::class);
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->first_name . ' ' . $this->last_name,
        );
    }

    protected function displayStatus(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status->label(),
        );
    }

    protected function displayMobileNo(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->mobile_no,
        );
    }
}
