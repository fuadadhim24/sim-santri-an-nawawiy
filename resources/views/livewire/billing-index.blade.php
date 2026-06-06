<div>
    <x-slot name="header">
        Tagihan & Pembayaran
    </x-slot>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-card rounded-lg shadow-sm border border-border p-4">
            <p class="text-xs text-muted-foreground uppercase tracking-wide">Belum Lunas</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $countUnpaid }}</p>
            <p class="text-sm text-muted-foreground">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</p>
        </div>
        <div class="bg-card rounded-lg shadow-sm border border-border p-4">
            <p class="text-xs text-muted-foreground uppercase tracking-wide">Lunas</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $countPaid }}</p>
            <p class="text-sm text-muted-foreground">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
        </div>
        <div class="bg-card rounded-lg shadow-sm border border-border p-4">
            <p class="text-xs text-muted-foreground uppercase tracking-wide">Total Tagihan</p>
            <p class="text-2xl font-bold text-foreground mt-1">{{ $countUnpaid + $countPaid }}</p>
            <p class="text-sm text-muted-foreground">Rp {{ number_format($totalUnpaid + $totalPaid, 0, ',', '.') }}</p>
        </div>
        <div class="bg-card rounded-lg shadow-sm border border-border p-4">
            <p class="text-xs text-muted-foreground uppercase tracking-wide">Tingkat Lunas</p>
            <p class="text-2xl font-bold text-primary mt-1">
                {{ ($countUnpaid + $countPaid) > 0 ? round(($countPaid / ($countUnpaid + $countPaid)) * 100) : 0 }}%
            </p>
            <p class="text-sm text-muted-foreground">dari total tagihan</p>
        </div>
    </div>

    <div class="bg-card rounded-lg shadow-sm border border-border">
        <!-- Filter Bar -->
        <div class="p-6 border-b border-border space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-card-foreground whitespace-nowrap">Daftar Tagihan</h3>
                <div class="flex items-center space-x-2">
                    <a href="{{ route('admin.billings.create') }}"
                        class="inline-flex items-center justify-center py-2 px-4 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 font-semibold whitespace-nowrap flex-none shrink-0">
                        Buat Tagihan Manual</a>
                    <a href="{{ route('admin.billings.archive') }}"
                        class="inline-flex items-center justify-center py-2 px-4 bg-secondary text-secondary-foreground rounded-md hover:bg-secondary/80 font-semibold whitespace-nowrap flex-none shrink-0">
                        Arsip Tagihan</a>
                </div>
            </div>

            <!-- Filters Row -->
            <div class="flex flex-wrap items-center gap-3">
                <!-- Filter Jenjang -->
                <select wire:model.live="unitFilter" id="filter-unit"
                    class="py-2 px-3 pr-8 border border-input bg-background rounded-md focus:outline-none focus:ring-2 focus:ring-ring text-foreground text-sm">
                    <option value="">Semua Jenjang</option>
                    <option value="01">SMP</option>
                    <option value="02">SMA</option>
                    <option value="03">PPTQ</option>
                </select>

                <!-- Filter Kelas (dynamic based on unit) -->
                <select wire:model.live="classFilter" id="filter-class"
                    class="py-2 px-3 pr-8 border border-input bg-background rounded-md focus:outline-none focus:ring-2 focus:ring-ring text-foreground text-sm">
                    <option value="">Semua Kelas</option>
                    @foreach($this->classOptions as $className)
                        <option value="{{ $className }}">{{ $className }}</option>
                    @endforeach
                </select>

                <!-- Filter Golongan -->
                <select wire:model.live="specialFilter" id="filter-special"
                    class="py-2 px-3 pr-8 border border-input bg-background rounded-md focus:outline-none focus:ring-2 focus:ring-ring text-foreground text-sm">
                    <option value="">Semua Golongan</option>
                    <option value="UMUM">Umum</option>
                    <option value="ANAK_GURU">Anak Guru</option>
                    <option value="YATIM">Yatim</option>
                </select>

                <!-- Filter Status -->
                <select wire:model.live="statusFilter" id="filter-status"
                    class="py-2 px-3 pr-8 border border-input bg-background rounded-md focus:outline-none focus:ring-2 focus:ring-ring text-foreground text-sm">
                    <option value="">Semua Status</option>
                    <option value="UNPAID">Belum Lunas</option>
                    <option value="PAID">Lunas</option>
                    <option value="EXPIRED">Kadaluarsa</option>
                    <option value="VOID">Dibatalkan</option>
                </select>

                <!-- Search -->
                <input wire:model.live="search" type="text" placeholder="Cari santri / tagihan..."
                    class="flex-1 min-w-[200px] py-2 px-4 border border-input bg-background rounded-md focus:outline-none focus:ring-2 focus:ring-ring text-foreground text-sm">
            </div>
        </div>

        @if (session()->has('message'))
            <div class="p-4 bg-green-100 text-green-700 border-b border-border">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="p-4 bg-red-100 text-red-700 border-b border-border">
                {{ session('error') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-muted-foreground uppercase bg-muted">
                    <tr>
                        <th class="px-6 py-3">Tanggal Tagihan</th>
                        <th class="px-6 py-3">Santri</th>
                        <th class="px-6 py-3">Jenjang</th>
                        <th class="px-6 py-3">Deskripsi</th>
                        <th class="px-6 py-3 text-right">Jumlah</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($billings as $billing)
                        <tr class="hover:bg-muted/50 transition-colors">
                            <td class="px-6 py-4 text-muted-foreground">
                                {{ $billing->created_at->locale('id')->isoFormat('D MMMM Y') }}</td>
                            <td class="px-6 py-4 font-medium text-foreground">
                                {{ $billing->student->full_name }}
                                <span class="text-xs text-muted-foreground block">{{ $billing->student->nis }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $unitLabels = ['01' => 'SMP', '02' => 'SMA', '03' => 'PPTQ'];
                                @endphp
                                <span class="text-xs font-medium px-2 py-0.5 rounded bg-primary/10 text-primary">
                                    {{ $unitLabels[$billing->student->unit_code] ?? $billing->student->unit_code }}
                                </span>
                                @if($billing->student->class_name)
                                    <span class="text-xs text-muted-foreground block mt-0.5">{{ $billing->student->class_name }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-foreground">{{ $billing->title }}</td>
                            <td class="px-6 py-4 text-right font-mono font-medium text-foreground">
                                Rp {{ number_format($billing->final_amount, 0, ',', '.') }}
                                @if ($billing->discount_applied > 0)
                                    <span class="block text-xs text-green-600 line-through">
                                        Rp {{ number_format($billing->original_amount, 0, ',', '.') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if ($billing->status == 'PAID')
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        LUNAS
                                    </span>
                                @elseif ($billing->status == 'UNPAID')
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                        BELUM LUNAS
                                    </span>
                                @elseif ($billing->status == 'EXPIRED')
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                        KADALUARSA
                                    </span>
                                @elseif ($billing->status == 'VOID')
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                        DIBATALKAN
                                    </span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                        {{ $billing->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('admin.students.show', $billing->student_id) }}"
                                        class="text-primary hover:text-primary/80 font-medium text-sm">Detail</a>

                                    @if ($billing->status == 'UNPAID')
                                        <button wire:click="processCashPayment({{ $billing->id }})"
                                            wire:swal="Apakah Anda yakin ingin memproses pembayaran ini secara Cash? Status tagihan akan langsung menjadi LUNAS."
                                            class="px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-xs font-medium ml-2">
                                            Bayar Cash
                                        </button>
                                        <a href="{{ route('duitku.pay', [$billing->id, 'force' => 1]) }}"
                                            onclick="event.preventDefault(); Swal.fire({title: 'Konfirmasi', text: 'Anda akan diarahkan ke halaman pembayaran. Lanjutkan?', icon: 'question', showCancelButton: true, confirmButtonText: 'Ya, Lanjutkan', cancelButtonText: 'Batal'}).then((result) => { if(result.isConfirmed) window.location.href = this.href; })"
                                            class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs font-medium ml-2">
                                            Bayar Cashless
                                        </a>
                                        <button wire:click="openSplitModal({{ $billing->id }})"
                                            class="px-2 py-1 text-white rounded hover:brightness-90 text-xs font-medium ml-2 transition-all"
                                            style="background-color: #d97706;">
                                            Pecah Cicilan
                                        </button>
                                        <button wire:click="delete({{ $billing->id }})"
                                            wire:swal="Yakin ingin menghapus / mengarsipkan tagihan ini?"
                                            class="px-2 py-1 bg-red-100 text-red-600 rounded hover:bg-red-200 text-xs font-medium ml-2">
                                            Hapus / Arsip
                                        </button>
                                    @endif

                                    @if ($billing->status == 'PAID')
                                        <a href="{{ route('admin.receipts.show', $billing->id) }}" target="_blank"
                                            class="px-2 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-xs font-medium flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                                </path>
                                            </svg>
                                            Kwitansi
                                        </a>
                                        <span
                                            class="px-2 py-1 bg-blue-50 text-blue-600 rounded text-xs font-medium ml-2">
                                            Read-only
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-muted-foreground">
                                Tidak ada tagihan ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-border">
            {{ $billings->links() }}
        </div>
    </div>

    <!-- Modal Pecah Cicilan -->
    @if($showSplitModal && $billingToSplit)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('showSplitModal', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-card border border-border rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="processSplit">
                    <div class="bg-card px-4 pt-5 pb-4 sm:p-6 sm:pb-4 space-y-4">
                        <div class="flex justify-between items-center pb-3 border-b border-border">
                            <h3 class="text-lg font-bold text-foreground" id="modal-title">
                                Pecah Tagihan Menjadi Cicilan
                            </h3>
                            <button type="button" wire:click="$set('showSplitModal', false)" class="text-muted-foreground hover:text-foreground">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Billing Details -->
                        <div class="p-3 bg-muted rounded-md text-sm text-foreground space-y-1">
                            <p><strong>Santri:</strong> {{ $billingToSplit->student->full_name }}</p>
                            <p><strong>Tagihan:</strong> {{ $billingToSplit->title }}</p>
                            <p><strong>Total Nominal:</strong> Rp {{ number_format($billingToSplit->final_amount, 0, ',', '.') }}</p>
                        </div>

                        <!-- Split Count -->
                        <div>
                            <label class="block text-sm font-semibold text-foreground mb-1">Jumlah Cicilan</label>
                            <select wire:model.live="splitCount" class="w-full py-2 px-3 border border-input bg-background rounded-md text-foreground text-sm focus:outline-none focus:ring-2 focus:ring-ring">
                                <option value="2">2 Kali</option>
                                <option value="3">3 Kali</option>
                                <option value="4">4 Kali</option>
                                <option value="5">5 Kali</option>
                                <option value="6">6 Kali</option>
                                <option value="12">12 Kali</option>
                            </select>
                        </div>

                        <!-- Installment Details -->
                        <div class="space-y-3 max-h-60 overflow-y-auto pr-1">
                            <h4 class="text-sm font-semibold text-foreground border-b border-border pb-1">Detail Rencana Cicilan</h4>
                            @for ($i = 0; $i < $splitCount; $i++)
                                <div class="grid grid-cols-3 gap-2 items-center">
                                    <div class="col-span-2">
                                        <label class="block text-xs text-muted-foreground">Judul Cicilan {{ $i + 1 }}</label>
                                        <input type="text" wire:model="splitTitles.{{ $i }}" class="w-full py-1 px-2 border border-input bg-background rounded text-foreground text-xs focus:outline-none focus:ring-1 focus:ring-ring">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-muted-foreground">Nominal (Rp)</label>
                                        <input type="number" wire:model.live="splitAmounts.{{ $i }}" class="w-full py-1 px-2 border border-input bg-background rounded text-foreground text-xs focus:outline-none focus:ring-1 focus:ring-ring">
                                    </div>
                                </div>
                            @endfor
                        </div>

                        <!-- Verification Summary -->
                        @php
                            $totalActual = array_sum(array_map('floatval', $splitAmounts));
                            $totalExpected = (float)$billingToSplit->final_amount;
                            $diff = $totalExpected - $totalActual;
                        @endphp
                        <div class="pt-2 border-t border-border flex justify-between items-center text-xs">
                            <span class="text-muted-foreground">Total Input: <strong class="text-foreground">Rp {{ number_format($totalActual, 0, ',', '.') }}</strong></span>
                            @if (abs($diff) > 0.01)
                                <span class="text-red-500 font-semibold">Selisih: Rp {{ number_format($diff, 0, ',', '.') }}</span>
                            @else
                                <span class="text-green-600 font-semibold">✓ Jumlah Sesuai</span>
                            @endif
                        </div>
                    </div>
                    <div class="bg-muted px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-border">
                        <button type="submit" wire:loading.attr="disabled" class="w-full inline-flex justify-center items-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-primary-foreground hover:bg-primary/90 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                            @if (abs($diff) > 0.01) disabled @endif>
                            <svg wire:loading class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Pecah Tagihan</span>
                        </button>
                        <button type="button" wire:click="$set('showSplitModal', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-input shadow-sm px-4 py-2 bg-background text-foreground hover:bg-muted focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
