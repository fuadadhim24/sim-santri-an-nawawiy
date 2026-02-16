<?php

namespace App\Livewire;

use App\Models\Billing;
use App\Models\Guardian;
use App\Models\Student;
use Livewire\Component;
use Carbon\Carbon;

class AdminDashboard extends Component
{
    public function render()
    {
        // Summary Cards
        $totalStudents = Student::count();
        $totalGuardians = Guardian::count();
        $unpaidInvoices = Billing::where('status', 'UNPAID')->count();
        $totalIncome = Billing::where('status', 'PAID')->sum('final_amount');

        // Chart Data: Monthly Income (Last 12 Months)
        $incomeData = [];
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M Y');
            $monthlyIncome = Billing::where('status', 'PAID')
                ->whereYear('updated_at', $date->year)
                ->whereMonth('updated_at', $date->month)
                ->sum('final_amount');

            $incomeData[] = $monthlyIncome;
            $months[] = $monthName;
        }

        // Chart Data: Payment Status
        $paidCount = Billing::where('status', 'PAID')->count();
        $unpaidCount = Billing::where('status', 'UNPAID')->count();

        return view('livewire.admin-dashboard', [
            'totalStudents' => $totalStudents,
            'totalGuardians' => $totalGuardians,
            'unpaidInvoices' => $unpaidInvoices,
            'totalIncome' => $totalIncome,
            'incomeData' => $incomeData, // Array of income values
            'months' => $months,       // Array of month labels
            'paidCount' => $paidCount,
            'unpaidCount' => $unpaidCount,
        ])->layout('layouts.admin');
    }
}
