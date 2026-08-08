<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoreCommittee extends Model
{
    protected $fillable = [
        'name',
        'role',
        'facebook',
        'instagram',
        'twitter',
        'linkedin',
        'whatsapp',
        'image_path',
        'order',
        'is_active',
    ];
}
