<?php

namespace App\Services;

use App\Models\Student;

class NisGeneratorService
{
    public function generate(string $unitCode, int $year): string
    {
        $currentMonth = (int) date('n');
        $currentYear = (int) date('Y');

        if ($year === $currentYear) {
            if ($currentMonth >= 7) {
                $startYear = $year;
            } else {
                $startYear = $year - 1;
            }
        } else {
            $startYear = $year;
        }

        $endYear = $startYear + 1;
        $academicPrefix = sprintf('%02d%02d', $startYear % 100, $endYear % 100);
        $prefix = sprintf('%s.%s', $academicPrefix, $unitCode);

        $lastStudent = Student::whereNotNull('nis')
            ->where('nis', 'like', $prefix . '.%')
            ->orderBy('nis', 'desc')
            ->first();

        if (!$lastStudent) {
            $sequence = 1;
        } else {
            $parts = explode('.', $lastStudent->nis);
            $lastSequence = (int) end($parts);
            $sequence = $lastSequence + 1;
        }

        $generated = sprintf('%s.%04d', $prefix, $sequence);
        
        while (Student::where('nis', $generated)->exists()) {
            $sequence++;
            $generated = sprintf('%s.%04d', $prefix, $sequence);
        }

        return $generated;
    }

    public function generateRegistrationNumber(int $year): string
    {
        $prefix = sprintf('%d', $year);

        $lastStudent = Student::whereNotNull('registration_number')
            ->where('registration_number', 'like', $prefix . '.%')
            ->orderBy('registration_number', 'desc')
            ->first();

        if (!$lastStudent) {
            $sequence = 1;
        } else {
            $parts = explode('.', $lastStudent->registration_number);
            $lastSequence = (int) end($parts);
            $sequence = $lastSequence + 1;
        }

        $generated = sprintf('%s.%04d', $prefix, $sequence);

        while (Student::where('registration_number', $generated)->exists()) {
            $sequence++;
            $generated = sprintf('%s.%04d', $prefix, $sequence);
        }

        return $generated;
    }
}
