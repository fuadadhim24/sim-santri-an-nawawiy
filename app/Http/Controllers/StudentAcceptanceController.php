<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentAcceptanceController extends Controller
{
    public function accept($studentId)
    {
        $student = Student::findOrFail($studentId);

        try {
            // Set is_active to true
            $student->update(['is_active' => true]);
            $student->markAsAccepted();

            return redirect()->route('admin.student-acceptance')
                ->with('success', 'Santri ' . $student->full_name . ' berhasil diterima.');
        } catch (\Exception $e) {
            return redirect()->route('admin.student-acceptance')
                ->with('error', 'Gagal menerima santri: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $studentId)
    {
        $student = Student::findOrFail($studentId);

        try {
            $student->update([
                'is_active' => false,
                'status' => \App\Enums\StudentStatus::REJECTED->value,
                'rejection_note' => $request->input('reason'),
            ]);

            return redirect()->route('admin.student-acceptance')
                ->with('success', 'Santri ' . $student->full_name . ' berhasil ditolak.');
        } catch (\Exception $e) {
            return redirect()->route('admin.student-acceptance')
                ->with('error', 'Gagal menolak santri: ' . $e->getMessage());
        }
    }
}
