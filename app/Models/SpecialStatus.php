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
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];
}
