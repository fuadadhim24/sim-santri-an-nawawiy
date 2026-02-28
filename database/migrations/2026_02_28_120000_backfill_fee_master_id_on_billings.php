<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    protected int $matched = 0;
    protected int $unmatched = 0;
    protected array $unmatchedBillings = [];

    public function up(): void
    {
        $this->matched = 0;
        $this->unmatched = 0;
        $this->unmatchedBillings = [];

        DB::table('billings')
            ->whereNull('fee_master_id')
            ->chunkById(100, function ($billings) {
                foreach ($billings as $billing) {
                    $feeMasterId = $this->findFeeMasterIdForBilling($billing);
                    if ($feeMasterId) {
                        DB::table('billings')
                            ->where('id', $billing->id)
                            ->update(['fee_master_id' => $feeMasterId]);
                        $this->matched++;
                    } else {
                        $this->unmatched++;
                        $this->unmatchedBillings[] = [
                            'id' => $billing->id,
                            'title' => $billing->title,
                            'student_id' => $billing->student_id,
                        ];
                    }
                }
            });

        Log::info('Backfill fee_master_id on billings: complete', [
            'matched' => $this->matched,
            'unmatched' => $this->unmatched,
        ]);

        if ($this->unmatched > 0) {
            Log::warning('Backfill fee_master_id: unmatched billings', [
                'count' => $this->unmatched,
                'samples' => array_slice($this->unmatchedBillings, 0, 10),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('billings')
            ->whereNotNull('fee_master_id')
            ->update(['fee_master_id' => null]);
    }

    private function findFeeMasterIdForBilling($billing): ?int
    {
        $student = DB::table('students')->where('id', $billing->student_id)->first();
        if (!$student) {
            return null;
        }

        $baseTitle = $this->extractBaseTitle($billing->title);

        $feeMaster = DB::table('fee_masters')
            ->where('item_name', $baseTitle)
            ->where(function ($q) use ($student) {
                $q->where('unit_target', $student->unit_code)->orWhereNull('unit_target');
            })
            ->where(function ($q) use ($student) {
                $q->where('residence_target', $student->residence_status)->orWhereNull('residence_target');
            })
            ->first();

        return $feeMaster?->id;
    }

    private function extractBaseTitle(string $title): string
    {
        $baseTitle = $title;

        $baseTitle = preg_replace('/[-_\s]+\d{4}$/', '', $baseTitle);

        $baseTitle = preg_replace('/[-_\s]+(Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember)[-_\s]+\d{4}$/i', '', $baseTitle);

        return trim($baseTitle);
    }
};
