<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoreCommittee extends Model
{
    protected $fillable = [
        'name',
        'role',
        'image_path',
        'order',
        'is_active',
    ];
}
