<?php

namespace App\Livewire;

use App\Models\Billing;
use Livewire\Component;
use Livewire\WithPagination;

class FinancialReport extends Component
{
    use WithPagination;

    public $startDate;
    public $endDate;
    public $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
    ];

    public function mount()
    {
        $this->startDate = date('Y-m-01');
        $this->endDate = date('Y-m-t');
    }

    public function updated($property)
    {
        if (in_array($property, ['search', 'startDate', 'endDate'])) {
            $this->resetPage();
        }
    }

    public function exportExcel()
    {
        $startDate = $this->startDate;
        $endDate = $this->endDate;
        $search = $this->search;

        $query = \App\Models\Payment::where('status', 'paid');

        if ($startDate) {
            $query->whereDate('paid_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('paid_at', '<=', $endDate);
        }
        if ($search) {
            $query->whereHas('billing.student', function($q) use ($search) {
                $q->where('full_name', 'like', '%' . $search . '%')
                  ->orWhere('nis', 'like', '%' . $search . '%');
            });
        }

        $payments = $query->with(['billing.student'])->orderBy('paid_at', 'asc')->get();

        $cashPayments = $payments->where('method', 'cash');
        $cashlessPayments = $payments->where('method', 'duitku');

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="laporan-keuangan-' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($cashPayments, $cashlessPayments) {
            $file = fopen('php://output', 'w');
            
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['LAPORAN KEUANGAN SIM SANTRI AN-NAWAWIY']);
            fputcsv($file, ['Tanggal Unduh: ' . now()->format('d/m/Y H:i')]);
            fputcsv($file, []);

            fputcsv($file, ['BAGIAN 1: PEMBAYARAN TUNAI (CASH)']);
            fputcsv($file, ['Tanggal Bayar', 'NIS', 'Nama Santri', 'Deskripsi', 'Jumlah']);
            
            $totalCash = 0;
            foreach ($cashPayments as $payment) {
                fputcsv($file, [
                    $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i') : '-',
                    $payment->billing->student->nis,
                    $payment->billing->student->full_name,
                    $payment->billing->title,
                    (int) $payment->amount
                ]);
                $totalCash += (int) $payment->amount;
            }
            fputcsv($file, ['Total Tunai', '', '', '', $totalCash]);
            fputcsv($file, []);

            fputcsv($file, ['BAGIAN 2: PEMBAYARAN CASHLESS (DUITKU)']);
            fputcsv($file, ['Tanggal Bayar', 'NIS', 'Nama Santri', 'Deskripsi', 'Referensi Duitku', 'Jumlah']);
            
            $totalCashless = 0;
            foreach ($cashlessPayments as $payment) {
                fputcsv($file, [
                    $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i') : '-',
                    $payment->billing->student->nis,
                    $payment->billing->student->full_name,
                    $payment->billing->title,
                    $payment->duitku_reference ?? '-',
                    (int) $payment->amount
                ]);
                $totalCashless += (int) $payment->amount;
            }
            fputcsv($file, ['Total Cashless', '', '', '', '', $totalCashless]);
            fputcsv($file, []);

            fputcsv($file, ['GRAND TOTAL PENDAPATAN', '', '', '', '', $totalCash + $totalCashless]);

            fclose($file);
        };

        return response()->streamDownload($callback, 'laporan-keuangan-' . now()->format('Y-m-d') . '.csv', $headers);
    }

    public function render()
    {
        $query = \App\Models\Payment::where('status', 'paid');

        if ($this->startDate) {
            $query->whereDate('paid_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('paid_at', '<=', $this->endDate);
        }

        if ($this->search) {
            $query->whereHas('billing.student', function($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                  ->orWhere('nis', 'like', '%' . $this->search . '%');
            });
        }

        $payments = $query->with(['billing.student'])->orderBy('paid_at', 'desc')->paginate(10);

        $statsQuery = clone $query;
        $cashIncome = (int) (clone $statsQuery)->where('method', 'cash')->sum('amount');
        $cashlessIncome = (int) (clone $statsQuery)->where('method', 'duitku')->sum('amount');
        $totalIncome = (int) $statsQuery->sum('amount');
        $totalTransactions = $statsQuery->count();

        return view('livewire.financial-report', [
            'payments' => $payments,
            'cashIncome' => $cashIncome,
            'cashlessIncome' => $cashlessIncome,
            'totalIncome' => $totalIncome,
            'totalTransactions' => $totalTransactions
        ])->layout('layouts.admin');
    }
}
