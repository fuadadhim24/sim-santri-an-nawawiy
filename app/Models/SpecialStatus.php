<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialStatus extends Model
{
    protected $table = 'special_statuses';

    protected $fillable = [
        'code',
        'name',
        'description',
        'is_system',
        'is_visible',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'is_visible' => 'boolean',
    ];
}
