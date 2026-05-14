<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyGroup extends Model
{
    protected $fillable = ['class_level_id', 'name', 'max_capacity'];

    public function classLevel()
    {
        return $this->belongsTo(ClassLevel::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
