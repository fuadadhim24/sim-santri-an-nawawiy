<?php

namespace App\Services;

use App\Models\Student;

class NisGeneratorService
{
    public function generate(string $unitCode, int $year): string
    {
        $prefix = sprintf('%d.%s', $year, $unitCode);

        $lastStudent = Student::where('nis', 'like', $prefix . '.%')
            ->orderBy('nis', 'desc')
            ->first();

        if (!$lastStudent) {
            $sequence = 1;
        } else {
            $parts = explode('.', $lastStudent->nis);
            $lastSequence = (int) end($parts);
            $sequence = $lastSequence + 1;
        }

        return sprintf('%s.%04d', $prefix, $sequence);
    }
}
