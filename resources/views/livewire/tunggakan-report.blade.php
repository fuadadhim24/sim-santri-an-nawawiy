<div class="space-y-6">

    {{-- ── Header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-foreground">Laporan Tunggakan Santri</h1>
            <p class="text-sm text-muted-foreground mt-0.5">Rekapan santri yang memiliki tagihan belum lunas (UNPAID)</p>
        </div>
        <button wire:click="exportExcel" wire:loading.attr="disabled"
            class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-primary-foreground text-sm font-semibold rounded-lg hover:bg-primary/90 transition shadow-sm disabled:opacity-60">
            <svg wire:loading wire:target="exportExcel" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg wire:loading.remove wire:target="exportExcel" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            <span wire:loading.remove wire:target="exportExcel">Ekspor Excel</span>
            <span wire:loading wire:target="exportExcel">Memproses...</span>
        </button>
    </div>

    {{-- ── Stat Cards ── --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-card border border-border rounded-xl p-5 shadow-sm">
            <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1">Jumlah Santri Menunggak</p>
            <p class="text-3xl font-bold text-foreground">{{ number_format($totalSiswa) }}</p>
            <p class="text-xs text-muted-foreground mt-1">santri</p>
        </div>
        <div class="bg-card border border-destructive/30 rounded-xl p-5 shadow-sm">
            <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1">Total Tunggakan</p>
            <p class="text-2xl font-bold text-destructive">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</p>
            <p class="text-xs text-muted-foreground mt-1">belum lunas</p>
        </div>
    </div>

    {{-- ── Filter Bar ── --}}
    <div class="bg-card border border-border rounded-xl p-4 shadow-sm">
        <div class="flex flex-col sm:flex-row gap-3">
            {{-- Search --}}
            <div class="flex-1">
                <input wire:model.live.debounce.300ms="search" id="search-tunggakan"
                    type="text" placeholder="Cari nama santri atau NIS..."
                    class="w-full pl-4 pr-4 py-2 text-sm border border-border rounded-lg bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-primary/30">
            </div>

            {{-- Filter Unit --}}
            <select wire:model.live="unitFilter" id="filter-unit"
                class="text-sm border border-border rounded-lg bg-background text-foreground pl-4 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30">
                <option value="">Semua Unit</option>
                <option value="01">SMP</option>
                <option value="02">SMA</option>
                <option value="03">PPTQ</option>
            </select>

            {{-- Filter Kelas --}}
            <select wire:model.live="kelasFilter" id="filter-kelas"
                class="text-sm border border-border rounded-lg bg-background text-foreground pl-4 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30">
                <option value="">Semua Kelas</option>
                @foreach ($classLevels as $kelas)
                    <option value="{{ $kelas->id }}">{{ $kelas->name }}</option>
                @endforeach
            </select>

            {{-- Reset --}}
            @if ($search || $unitFilter || $kelasFilter)
                <button wire:click="$set('search', ''); $set('unitFilter', ''); $set('kelasFilter', '')"
                    class="text-sm text-muted-foreground hover:text-foreground px-3 py-2 rounded-lg border border-border hover:bg-muted transition">
                    Reset Filter
                </button>
            @endif
        </div>
    </div>

    {{-- ── Tabel ── --}}
    <div class="bg-card border border-border rounded-xl shadow-sm overflow-hidden">
        @if ($students->isEmpty())
            <div class="p-16 text-center">
                <svg class="w-12 h-12 mx-auto text-muted-foreground/30 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-lg font-semibold text-foreground">Tidak Ada Tunggakan!</p>
                <p class="text-sm text-muted-foreground mt-1">Semua santri sudah melunasi tagihan mereka 🎉</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-muted border-b border-border text-xs font-bold text-muted-foreground uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Santri</th>
                            <th class="px-4 py-3">Unit / Kelas</th>
                            <th class="px-4 py-3">Rincian Tagihan Belum Lunas</th>
                            <th class="px-4 py-3 text-right">Total Tunggakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @php $no = ($students->currentPage() - 1) * $students->perPage() + 1; @endphp
                        @foreach ($students as $student)
                            <tr class="hover:bg-muted/30 transition-colors">
                                <td class="px-4 py-3 text-muted-foreground font-medium">{{ $no++ }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-foreground">{{ $student->full_name }}</p>
                                    <p class="text-xs text-muted-foreground">{{ $student->nis }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-primary/10 text-primary">
                                        {{ $unitLabels[$student->unit_code] ?? $student->unit_code }}
                                    </span>
                                    <p class="text-xs text-muted-foreground mt-1">{{ $student->class_name ?: 'Kelas belum ditentukan' }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="space-y-1">
                                        @foreach ($student->billings as $bill)
                                            <div class="flex items-start gap-2">
                                                <span class="mt-0.5 flex-shrink-0 w-1.5 h-1.5 rounded-full bg-destructive/60 mt-1.5"></span>
                                                <div>
                                                    <p class="text-xs text-foreground">{{ $bill->title }}</p>
                                                    <p class="text-xs text-muted-foreground">{{ $bill->created_at->locale('id')->isoFormat('D MMM Y') }} · Rp {{ number_format($bill->final_amount, 0, ',', '.') }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <p class="font-bold text-destructive">
                                        Rp {{ number_format($student->billings->sum('final_amount'), 0, ',', '.') }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">{{ $student->billings->count() }} tagihan</p>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-4 py-3 border-t border-border">
                {{ $students->links() }}
            </div>
        @endif
    </div>

</div>
