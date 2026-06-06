<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'disk',
    'directory',
    'file_name',
])]
class TempFile extends Model
{
}
