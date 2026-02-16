<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'title',
        'original_amount',
        'discount_applied',
        'final_amount',
        'status',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Optional: If we want to link payments later
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
