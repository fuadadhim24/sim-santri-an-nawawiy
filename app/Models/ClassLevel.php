<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassLevel extends Model
{
    protected $fillable = ['name', 'level_order', 'unit_code'];

    public function studyGroups()
    {
        return $this->hasMany(StudyGroup::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
