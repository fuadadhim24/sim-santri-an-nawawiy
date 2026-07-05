<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
                'rejection_note' => $request->input('reason'),
            ]);
            $student->markAsRejected();

            $expandedIds = $request->session()->get('expanded_schedule_ids', []);
            $expandedIds = array_filter(array_unique(array_merge($expandedIds, [$student->spmb_schedule_id])));
            $request->session()->put('expanded_schedule_ids', $expandedIds);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Santri ' . $student->full_name . ' berhasil ditolak.',
                ]);
            }

            return redirect()->route('admin.student-acceptance')
                ->with('success', 'Santri ' . $student->full_name . ' berhasil ditolak.');
        } catch (\Exception $e) {
            Log::error('Failed to reject student', [
                'student_id' => $studentId,
                'error' => $e->getMessage(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menolak santri: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->route('admin.student-acceptance')
                ->with('error', 'Gagal menolak santri: ' . $e->getMessage());
        }
    }
}
