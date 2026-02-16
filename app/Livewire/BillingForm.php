<?php

namespace App\Livewire;

use App\Models\Student;
use App\Services\BillingService;
use Livewire\Attributes\Rule;
use Livewire\Component;

class BillingForm extends Component
{
    #[Rule('required|integer|min:1|max:12')]
    public $month;

    #[Rule('required|integer|min:2025|max:2030')]
    public $year;

    public function mount()
    {
        $this->month = date('n');
        $this->year = date('Y');
    }

    public function generate(BillingService $billingService)
    {
        $this->validate();

        // fetch all active students who are BILLABLE
        // Exclude NGAJI_ONLY based on requirement?
        // Plan said: "Explicitly exclude NGAJI_ONLY students from billing generation."

        $students = Student::where('is_active', true)
            ->where('residence_status', '!=', 'NGAJI_ONLY')
            ->get();

        $count = 0;
        $skipped = 0;

        foreach ($students as $student) {
            $bill = $billingService->generateMonthlySPP($student, $this->month, $this->year);
            if ($bill) {
                $count++;
            } else {
                $skipped++;
            }
        }

        session()->flash('message', "Generated $count bills. Skipped $skipped (already exists or no fee found).");
        return redirect()->route('admin.billings');
    }

    public function render()
    {
        return view('livewire.billing-form')->layout('layouts.admin');
    }
}
