<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeMaster extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function discounts(): HasMany
    {
        return $this->hasMany(Discount::class);
    }
}
