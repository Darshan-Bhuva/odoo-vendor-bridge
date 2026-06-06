<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'user_id', 'onesignal_player_id', 'device_id', 'device_type',
])]
class UserDevice extends Model
{
    use BaseModel;

    protected function casts(): array
    {
        return [
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
