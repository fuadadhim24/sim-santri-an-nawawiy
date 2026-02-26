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
    #[Rule('required|integer|min:2025|max:2035')]
    public $year;

    public $genOnce = false;
    public $genMonthly = true;
    public $genYearly = false;
    public $onlyDue = true;

    public function mount()
    {
        $this->month = date('n');
        $this->year = date('Y');
    }

    public function generate(BillingService $billingService)
    {
        $this->validate();

        $students = Student::where('is_active', true)
            ->where('residence_status', '!=', 'NGAJI_ONLY')
            ->get();

        $totalGenerated = 0;

        foreach ($students as $student) {
            if ($this->genOnce) {
                $totalGenerated += $billingService->generateOnceBills($student);
            }
            if ($this->genMonthly) {
                // If onlyDue is true, we logic this in BillingService or here?
                // Let's pass a flag to BillingService soon, but for now,
                // let's do a simple check here if onlyDue is true.
                if ($this->onlyDue) {
                    $dayToday = date('j');
                    // We need to check if ANY applicable fee for this student has billing_day == today
                    $hasDueFee = \App\Models\FeeMaster::whereHas('category', function ($q) {
                            $q->where('billing_interval', 'MONTHLY');
                        })
                        ->where('billing_day', $dayToday)
                        ->where(function ($q) use ($student) {
                            $q->where('unit_target', $student->unit_code)->orWhereNull('unit_target');
                        })
                        ->exists();

                    if ($hasDueFee) {
                        $totalGenerated += $billingService->generateMonthlySPP($student, $this->month, $this->year);
                    }
                } else {
                    $totalGenerated += $billingService->generateMonthlySPP($student, $this->month, $this->year);
                }
            }
            if ($this->genYearly) {
                $totalGenerated += $billingService->generateYearlyBills($student, $this->year);
            }
        }

        session()->flash('message', "Berhasil menerbitkan $totalGenerated tagihan baru.");
        return redirect()->route('admin.billings');
    }

    public function render()
    {
        return view('livewire.billing-form')->layout('layouts.admin');
    }
}
