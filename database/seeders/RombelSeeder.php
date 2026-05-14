<?php

namespace Database\Seeders;

use App\Models\ClassLevel;
use App\Models\StudyGroup;
use Illuminate\Database\Seeder;

class RombelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            ['name' => 'Kelas 7 SMP', 'level_order' => 1],
            ['name' => 'Kelas 8 SMP', 'level_order' => 2],
            ['name' => 'Kelas 9 SMP', 'level_order' => 3],
            ['name' => 'Kelas 10 SMA', 'level_order' => 4],
            ['name' => 'Kelas 11 SMA', 'level_order' => 5],
            ['name' => 'Kelas 12 SMA', 'level_order' => 6],
            ['name' => 'Kelas Tahfidz', 'level_order' => 7],
        ];

        foreach ($levels as $l) {
            $level = ClassLevel::create($l);
            
            StudyGroup::create([
                'class_level_id' => $level->id,
                'name' => $level->name . ' - A',
                'max_capacity' => 30,
            ]);
            
            StudyGroup::create([
                'class_level_id' => $level->id,
                'name' => $level->name . ' - B',
                'max_capacity' => 30,
            ]);
        }
    }
}
