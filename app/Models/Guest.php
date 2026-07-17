<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $fillable = [
        'name',
        'designation',
        'type',
        'image_path',
        'order',
        'is_active',
    ];
}
