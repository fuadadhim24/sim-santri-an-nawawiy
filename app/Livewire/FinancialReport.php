<?php

namespace App\Livewire;

use App\Models\Billing;
use Livewire\Component;
use Livewire\WithPagination;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setShowGridLines(true);

        $headerStyle = [
            'font' => ['bold' => true, 'size' => 10],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F3F4F6']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB']
                ]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_LEFT
            ]
        ];

        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E5E7EB']
                ]
            ]
        ];

        $totalStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F9FAFB']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB']
                ]
            ]
        ];

        $currentRow = 1;

        $sheet->setCellValue('A' . $currentRow, 'LAPORAN KEUANGAN SIM SANTRI AN-NAWAWIY');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(14);
        $currentRow++;

        $sheet->setCellValue('A' . $currentRow, 'Tanggal Unduh: ' . now()->format('d/m/Y H:i'));
        $sheet->getStyle('A' . $currentRow)->getFont()->setItalic(true)->setSize(10);
        $currentRow += 2;

        $sheet->setCellValue('A' . $currentRow, 'BAGIAN 1: PEMBAYARAN TUNAI (CASH)');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(11);
        $currentRow++;

        $cashHeaders = ['Tanggal Bayar', 'NIS', 'Nama Santri', 'Deskripsi', 'Jumlah'];
        foreach ($cashHeaders as $colIdx => $headerTitle) {
            $colLetter = chr(65 + $colIdx);
            $sheet->setCellValue($colLetter . $currentRow, $headerTitle);
        }
        $sheet->getStyle('A' . $currentRow . ':E' . $currentRow)->applyFromArray($headerStyle);
        $currentRow++;

        $cashStartRow = $currentRow;
        foreach ($cashPayments as $payment) {
            $sheet->setCellValue('A' . $currentRow, $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i') : '-');
            
            $sheet->setCellValueExplicit(
                'B' . $currentRow, 
                $payment->billing->student->nis, 
                \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
            );

            $sheet->setCellValue('C' . $currentRow, $payment->billing->student->full_name);
            $sheet->setCellValue('D' . $currentRow, $payment->billing->title);
            
            $sheet->setCellValue('E' . $currentRow, (int) $payment->amount);
            $sheet->getStyle('E' . $currentRow)->getNumberFormat()->setFormatCode('Rp#,##0');

            $sheet->getStyle('A' . $currentRow . ':E' . $currentRow)->applyFromArray($dataStyle);
            $currentRow++;
        }
        $cashEndRow = $currentRow - 1;

        $sheet->setCellValue('A' . $currentRow, 'Total Tunai');
        $sheet->mergeCells("A{$currentRow}:D{$currentRow}");
        $sheet->getStyle("A{$currentRow}:D{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        if ($cashEndRow >= $cashStartRow) {
            $sheet->setCellValue('E' . $currentRow, "=SUM(E{$cashStartRow}:E{$cashEndRow})");
        } else {
            $sheet->setCellValue('E' . $currentRow, 0);
        }
        $sheet->getStyle('E' . $currentRow)->getNumberFormat()->setFormatCode('Rp#,##0');
        $sheet->getStyle('A' . $currentRow . ':E' . $currentRow)->applyFromArray($totalStyle);
        
        $totalCashRowIdx = $currentRow;
        $currentRow += 3;

        $sheet->setCellValue('A' . $currentRow, 'BAGIAN 2: PEMBAYARAN CASHLESS (DUITKU)');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(11);
        $currentRow++;

        $cashlessHeaders = ['Tanggal Bayar', 'NIS', 'Nama Santri', 'Deskripsi', 'Referensi Duitku', 'Jumlah'];
        foreach ($cashlessHeaders as $colIdx => $headerTitle) {
            $colLetter = chr(65 + $colIdx);
            $sheet->setCellValue($colLetter . $currentRow, $headerTitle);
        }
        $sheet->getStyle('A' . $currentRow . ':F' . $currentRow)->applyFromArray($headerStyle);
        $currentRow++;

        $cashlessStartRow = $currentRow;
        foreach ($cashlessPayments as $payment) {
            $sheet->setCellValue('A' . $currentRow, $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i') : '-');
            
            $sheet->setCellValueExplicit(
                'B' . $currentRow, 
                $payment->billing->student->nis, 
                \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
            );

            $sheet->setCellValue('C' . $currentRow, $payment->billing->student->full_name);
            $sheet->setCellValue('D' . $currentRow, $payment->billing->title);
            $sheet->setCellValue('E' . $currentRow, $payment->duitku_reference ?? '-');
            
            $sheet->setCellValue('F' . $currentRow, (int) $payment->amount);
            $sheet->getStyle('F' . $currentRow)->getNumberFormat()->setFormatCode('Rp#,##0');

            $sheet->getStyle('A' . $currentRow . ':F' . $currentRow)->applyFromArray($dataStyle);
            $currentRow++;
        }
        $cashlessEndRow = $currentRow - 1;

        $sheet->setCellValue('A' . $currentRow, 'Total Cashless');
        $sheet->mergeCells("A{$currentRow}:E{$currentRow}");
        $sheet->getStyle("A{$currentRow}:E{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        if ($cashlessEndRow >= $cashlessStartRow) {
            $sheet->setCellValue('F' . $currentRow, "=SUM(F{$cashlessStartRow}:F{$cashlessEndRow})");
        } else {
            $sheet->setCellValue('F' . $currentRow, 0);
        }
        $sheet->getStyle('F' . $currentRow)->getNumberFormat()->setFormatCode('Rp#,##0');
        $sheet->getStyle('A' . $currentRow . ':F' . $currentRow)->applyFromArray($totalStyle);
        
        $totalCashlessRowIdx = $currentRow;
        $currentRow += 3;

        $sheet->setCellValue('A' . $currentRow, 'GRAND TOTAL PENDAPATAN');
        $sheet->mergeCells("A{$currentRow}:E{$currentRow}");
        $sheet->getStyle("A{$currentRow}:E{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        $sheet->setCellValue('F' . $currentRow, "=E{$totalCashRowIdx}+F{$totalCashlessRowIdx}");
        $sheet->getStyle('F' . $currentRow)->getNumberFormat()->setFormatCode('Rp#,##0');
        
        $grandTotalStyle = [
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '10B981']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '047857']
                ]
            ]
        ];
        $sheet->getStyle('A' . $currentRow . ':F' . $currentRow)->applyFromArray($grandTotalStyle);

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'laporan-keuangan-' . now()->format('Y-m-d') . '.xlsx';
        
        $callback = function () use ($writer) {
            $writer->save('php://output');
        };

        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0',
        ];

        return response()->streamDownload($callback, $fileName, $headers);
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
