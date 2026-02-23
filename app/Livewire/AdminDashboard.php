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
        $totalStudents = Student::count();
        $totalGuardians = Guardian::count();
        $unpaidInvoices = Billing::where('status', 'UNPAID')->count();
        $totalIncome = Billing::where('status', 'PAID')->sum('final_amount');

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

        $paidCount = Billing::where('status', 'PAID')->count();
        $unpaidCount = Billing::where('status', 'UNPAID')->count();

        $recentPayments = Billing::with('student')
            ->where('status', 'PAID')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        return view('livewire.admin-dashboard', [
            'totalStudents' => $totalStudents,
            'totalGuardians' => $totalGuardians,
            'unpaidInvoices' => $unpaidInvoices,
            'totalIncome' => $totalIncome,
            'incomeData' => $incomeData,
            'months' => $months,
            'paidCount' => $paidCount,
            'unpaidCount' => $unpaidCount,
            'recentPayments' => $recentPayments,
        ])->layout('layouts.admin');
    }
}
