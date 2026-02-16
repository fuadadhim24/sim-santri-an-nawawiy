<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Discount extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function feeMaster(): BelongsTo
    {
        return $this->belongsTo(FeeMaster::class);
    }
}
