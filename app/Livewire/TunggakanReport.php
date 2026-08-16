<?php

namespace App\Livewire;

use App\Models\Billing;
use App\Models\ClassLevel;
use App\Models\Student;
use Livewire\Component;
use Livewire\WithPagination;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TunggakanReport extends Component
{
    use WithPagination;

    public $search       = '';
    public $unitFilter   = '';
    public $kelasFilter  = '';

    protected $queryString = [
        'search'      => ['except' => ''],
        'unitFilter'  => ['except' => ''],
        'kelasFilter' => ['except' => ''],
    ];

    public function updated($property)
    {
        if (in_array($property, ['search', 'unitFilter', 'kelasFilter'])) {
            $this->resetPage();
        }
    }

    private function buildQuery()
    {
        $query = Student::query()
            ->where('status', 'diterima')
            ->whereHas('billings', fn($q) => $q->where('status', 'UNPAID'))
            ->with([
                'billings' => fn($q) => $q->where('status', 'UNPAID')->orderBy('created_at'),
            ]);

        if ($this->unitFilter) {
            $query->where('unit_code', $this->unitFilter);
        }

        if ($this->kelasFilter) {
            $query->where('class_level_id', $this->kelasFilter);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                  ->orWhere('nis', 'like', '%' . $this->search . '%')
                  ->orWhere('class_name', 'like', '%' . $this->search . '%');
            });
        }

        return $query;
    }

    public function exportExcel()
    {
        $students = $this->buildQuery()
            ->orderBy('class_name')
            ->orderBy('full_name')
            ->get();

        $unitLabels = ['01' => 'SMP', '02' => 'SMA', '03' => 'PPTQ'];

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setShowGridLines(true);

        // ── Styles ──────────────────────────────────────────────
        $titleStyle = [
            'font'      => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ];
        $headerStyle = [
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E7D32']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1B5E20']]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];
        $subHeaderStyle = [
            'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '388E3C']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '2E7D32']]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];
        $dataStyle = [
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
            'alignment' => ['vertical' => Alignment::VERTICAL_TOP, 'wrapText' => true],
        ];
        $totalStyle = [
            'font'      => ['bold' => true, 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F9FAFB']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '9CA3AF']]],
        ];
        $grandTotalStyle = [
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C62828']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '7F0000']]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
        ];

        // ── Title ────────────────────────────────────────────────
        $row = 1;
        $sheet->setCellValue('A' . $row, 'LAPORAN REKAPAN TUNGGAKAN SANTRI — YPSPPTQ AN-NAWAWIY');
        $sheet->getStyle('A' . $row)->applyFromArray($titleStyle);
        $row++;

        $filterDesc = 'Semua Unit & Kelas';
        if ($this->unitFilter) {
            $filterDesc = 'Unit: ' . ($unitLabels[$this->unitFilter] ?? $this->unitFilter);
        }
        if ($this->kelasFilter) {
            $kelas = ClassLevel::find($this->kelasFilter);
            $filterDesc .= ($this->unitFilter ? ' | ' : '') . 'Kelas: ' . ($kelas->name ?? '-');
        }
        $sheet->setCellValue('A' . $row, 'Filter: ' . $filterDesc . ' | Dicetak: ' . now()->locale('id')->isoFormat('D MMMM Y, HH:mm') . ' WIB');
        $sheet->getStyle('A' . $row)->getFont()->setItalic(true)->setSize(9);
        $row += 2;

        // ── Header Kolom ─────────────────────────────────────────
        $headers = ['No', 'NIS', 'Nama Santri', 'Unit', 'Kelas', 'Deskripsi Tagihan', 'Tanggal Tagihan', 'Total Tunggakan'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValue(chr(65 + $i) . $row, $h);
        }
        $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray($headerStyle);
        $sheet->getRowDimension($row)->setRowHeight(20);
        $row++;

        // ── Data ─────────────────────────────────────────────────
        $no          = 1;
        $grandTotal  = 0;
        $startDataRow = $row;

        // Group by kelas
        $grouped = $students->groupBy('class_name');

        foreach ($grouped as $kelas => $kelasStudents) {
            // Sub-header kelas
            $kelasLabel = $kelas ?: 'Kelas Belum Ditentukan';
            $sheet->setCellValue('A' . $row, $kelasLabel);
            $sheet->mergeCells('A' . $row . ':H' . $row);
            $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray($subHeaderStyle);
            $row++;

            $kelasTotal = 0;
            $kelasStartRow = $row;

            foreach ($kelasStudents as $student) {
                $totalTunggakan = $student->billings->sum('final_amount');
                $grandTotal    += $totalTunggakan;
                $kelasTotal    += $totalTunggakan;

                $deskripsi = $student->billings->map(fn($b) => $b->title . ' (Rp ' . number_format($b->final_amount, 0, ',', '.') . ')')->implode("\n");
                $tglTagihan = $student->billings->map(fn($b) => $b->created_at->format('d/m/Y'))->implode("\n");

                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValueExplicit('B' . $row, $student->nis, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValue('C' . $row, $student->full_name);
                $sheet->setCellValue('D' . $row, $unitLabels[$student->unit_code] ?? $student->unit_code);
                $sheet->setCellValue('E' . $row, $kelas ?: '-');
                $sheet->setCellValue('F' . $row, $deskripsi);
                $sheet->setCellValue('G' . $row, $tglTagihan);
                $sheet->setCellValue('H' . $row, (int) $totalTunggakan);
                $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('Rp#,##0');
                $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray($dataStyle);
                $row++;
            }

            // Sub-total per kelas
            $sheet->setCellValue('A' . $row, 'Sub-total ' . $kelasLabel);
            $sheet->mergeCells('A' . $row . ':G' . $row);
            $sheet->getStyle('A' . $row . ':G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('H' . $row, (int) $kelasTotal);
            $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('Rp#,##0');
            $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray($totalStyle);
            $row++;
            $row++; // spasi
        }

        // ── Grand Total ───────────────────────────────────────────
        $sheet->setCellValue('A' . $row, 'GRAND TOTAL TUNGGAKAN');
        $sheet->mergeCells('A' . $row . ':G' . $row);
        $sheet->getStyle('A' . $row . ':G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('H' . $row, (int) $grandTotal);
        $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('Rp#,##0');
        $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray($grandTotalStyle);

        // ── Column widths ─────────────────────────────────────────
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(16);
        $sheet->getColumnDimension('C')->setWidth(28);
        $sheet->getColumnDimension('D')->setWidth(8);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(40);
        $sheet->getColumnDimension('G')->setWidth(16);
        $sheet->getColumnDimension('H')->setWidth(20);

        $writer   = new Xlsx($spreadsheet);
        $fileName = 'laporan-tunggakan-' . now()->format('Y-m-d') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    public function render()
    {
        $unitLabels = ['01' => 'SMP', '02' => 'SMA', '03' => 'PPTQ'];

        $students = $this->buildQuery()
            ->orderBy('class_name')
            ->orderBy('full_name')
            ->paginate(15);

        // Stats
        $statsQuery = $this->buildQuery();
        $totalSiswa  = $statsQuery->count();
        $totalTagihan = Billing::whereIn(
            'student_id',
            $statsQuery->pluck('id')
        )->where('status', 'UNPAID')->sum('final_amount');

        $classLevels = ClassLevel::when($this->unitFilter, fn($q) => $q->where('unit_code', $this->unitFilter))
            ->orderBy('level_order')
            ->get();

        return view('livewire.tunggakan-report', [
            'students'    => $students,
            'unitLabels'  => $unitLabels,
            'totalSiswa'  => $totalSiswa,
            'totalTagihan' => $totalTagihan,
            'classLevels' => $classLevels,
        ])->layout('layouts.admin');
    }
}
