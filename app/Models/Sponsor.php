<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    protected $fillable = [
        'name',
        'type',
        'logo_path',
        'url',
        'order',
        'is_active',
    ];
}
