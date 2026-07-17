<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image_path',
        'link',
        'button_name',
        'is_active',
        'order',
    ];
}
