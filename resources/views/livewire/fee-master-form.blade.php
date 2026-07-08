<div>
    <x-slot name="header">
        {{ $isEdit ? 'Edit Data Master Biaya' : 'Tambah Data Master Biaya' }}
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-card rounded-lg shadow-sm border border-border p-6">
            <form wire:submit="save" class="space-y-6">
                <!-- Group 1: Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Category -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="fee_category_id" class="block text-sm font-medium text-foreground">Kategori
                                Biaya <span class="text-red-500">*</span></label>
                            <a href="{{ route('admin.fee-categories.create') }}"
                                class="text-xs text-primary hover:underline">+ Kategori Baru</a>
                        </div>
                        <select wire:model.live="fee_category_id" id="fee_category_id"
                            class="mt-1 block w-full pl-3 pr-10 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                            <option value="">Pilih Kategori</option>
                            @foreach ($this->feeCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('fee_category_id')
                            <span class="text-destructive text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Item Name -->
                    <div>
                        <label for="item_name" class="block text-sm font-medium text-foreground mb-1">Nama Item
                            Biaya <span class="text-red-500">*</span></label>
                        <input wire:model.live.debounce.500ms="item_name" type="text" id="item_name" placeholder="contoh: SPP 2026"
                            class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        @error('item_name')
                            <span class="text-destructive text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Amount -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-foreground mb-1">Jumlah (Rp) <span class="text-red-500">*</span></label>
                    <div class="mt-1 relative rounded-md shadow-sm" x-data="{
                        get displayValue() {
                            let val = $wire.amount;
                            if (!val) return '';
                            return val.toString().replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                        }
                    }">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-muted-foreground sm:text-sm font-medium">Rp</span>
                        </div>
                        <input type="text" x-bind:value="displayValue"
                            x-on:input.debounce.500ms="$wire.amount = $event.target.value.replace(/\D/g, ''); $wire.$refresh();" placeholder="500.000"
                            class="block w-full pl-10 pr-4 py-2 border border-input bg-background rounded-md focus:outline-none focus:ring-ring focus:border-ring sm:text-sm font-mono">
                    </div>
                    @error('amount')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Scheduling / Recurrence Rules -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-border">
                    <div>
                        <label for="recurrence_type" class="block text-sm font-medium text-foreground mb-1">Tipe Siklus Tagihan <span class="text-red-500">*</span></label>
                        <select wire:model.live="recurrence_type" id="recurrence_type"
                            class="mt-1 block w-full pl-3 pr-10 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                            <option value="ONE_TIME">Sekali Bayar (Misal: Uang Pangkal)</option>
                            <option value="MONTHLY">Bulanan (Misal: SPP)</option>
                            <option value="EVERY_6_MONTHS">Per 6 Bulan (Semester)</option>
                            <option value="YEARLY">Tahunan (Misal: Daftar Ulang)</option>
                        </select>
                        @error('recurrence_type')
                            <span class="text-destructive text-sm">{{ $message }}</span>
                        @enderror
                        <p class="text-xs text-muted-foreground mt-1">Siklus berulangnya tagihan ini.</p>
                    </div>

                    <div>
                        <label for="due_days" class="block text-sm font-medium text-foreground mb-1">Jatuh Tempo (Hari) <span class="text-red-500">*</span></label>
                        <input wire:model="due_days" type="number" id="due_days" min="0" placeholder="14"
                            class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        @error('due_days')
                            <span class="text-destructive text-sm">{{ $message }}</span>
                        @enderror
                        <p class="text-xs text-muted-foreground mt-1">Batas waktu bayar setelah tagihan terbit.</p>
                    </div>

                    @if($recurrence_type !== 'ONE_TIME')
                    <div>
                        <label for="billing_day" class="block text-sm font-medium text-foreground mb-1">Tanggal Generate <span class="text-red-500">*</span></label>
                        <input wire:model="billing_day" type="number" id="billing_day" min="1" max="31" placeholder="1"
                            class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        @error('billing_day')
                            <span class="text-destructive text-sm">{{ $message }}</span>
                        @enderror
                        <p class="text-xs text-muted-foreground mt-1">Tagihan otomatis dibuat setiap tanggal ini.</p>
                    </div>
                    @endif
                </div>

                <!-- Validity Dates -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-foreground">Mulai Berlaku <span class="text-red-500">*</span></label>
                        <input wire:model="start_date" type="date" id="start_date"
                            class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        @error('start_date')
                            <span class="text-destructive text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-foreground">Berakhir Pada <span class="text-muted-foreground font-normal text-[11px]">(Opsional)</span></label>
                        <input wire:model="end_date" type="date" id="end_date"
                            class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        @error('end_date')
                            <span class="text-destructive text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Targets -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-border">
                    <div>
                        <label for="unit_target" class="block text-sm font-medium text-foreground mb-1">Target
                            Unit <span class="text-muted-foreground font-normal text-[11px]">(Opsional)</span></label>
                        <select wire:model="unit_target" id="unit_target"
                            class="mt-1 block w-full pl-3 pr-10 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                            <option value="">Semua Unit</option>
                            <option value="01">SMP</option>
                            <option value="02">SMA</option>
                            <option value="03">PPTQ</option>
                        </select>
                    </div>
                    <div>
                        <label for="residence_target" class="block text-sm font-medium text-foreground mb-1">Target
                            Domisili <span class="text-muted-foreground font-normal text-[11px]">(Opsional)</span></label>
                        <select wire:model="residence_target" id="residence_target"
                            class="mt-1 block w-full pl-3 pr-10 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                            <option value="">Semua Status Domisili</option>
                            <option value="MONDOK">Mondok</option>
                            <option value="NON_MONDOK">Non Mondok</option>
                            <option value="NGAJI_ONLY">Ngaji Saja</option>
                        </select>
                    </div>
                    <div>
                        <label for="class_level_target_id" class="block text-sm font-medium text-foreground mb-1">Target
                            Kelas <span class="text-muted-foreground font-normal text-[11px]">(Opsional)</span></label>
                        <select wire:model="class_level_target_id" id="class_level_target_id"
                            class="mt-1 block w-full pl-3 pr-10 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                            <option value="">Semua Kelas</option>
                            @foreach($this->classLevels as $level)
                                <option value="{{ $level->id }}">{{ $level->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Recalculation Policy (Only shown in Edit mode) -->
                @if($isEdit)
                    <div class="p-4 bg-muted/30 border border-border rounded-lg space-y-3 pt-4 mt-6">
                        <label class="block text-xs font-semibold text-gray-700 uppercase">Kebijakan Pembaruan Tagihan Terkait:</label>
                        <div class="space-y-2">
                            <label class="flex items-start text-xs text-foreground cursor-pointer select-none">
                                <input type="radio" wire:model.live="update_policy" value="all" class="mt-0.5 text-primary focus:ring-primary border-gray-300 mr-2">
                                <div>
                                    <span class="font-semibold text-slate-800">Ubah Semua Tagihan Belum Lunas</span>
                                    <p class="text-[10px] text-muted-foreground mt-0.5">Semua tagihan belum lunas (termasuk tagihan lama yang sudah terlambat/jatuh tempo) akan diperbarui menggunakan nominal/nama baru.</p>
                                </div>
                            </label>

                            <label class="flex items-start text-xs text-foreground cursor-pointer select-none mt-2">
                                <input type="radio" wire:model.live="update_policy" value="except_current" class="mt-0.5 text-primary focus:ring-primary border-gray-300 mr-2">
                                <div>
                                    <span class="font-semibold text-slate-800">Ubah Tagihan Terkait Kecuali Bulan Ini</span>
                                    <p class="text-[10px] text-muted-foreground mt-0.5">Tagihan belum lunas untuk bulan berjalan ini dan bulan sebelumnya tidak akan diubah. Perubahan hanya berlaku untuk tagihan bulan depan dst.</p>
                                </div>
                            </label>

                            <label class="flex items-start text-xs text-foreground cursor-pointer select-none mt-2">
                                <input type="radio" wire:model.live="update_policy" value="none" class="mt-0.5 text-primary focus:ring-primary border-gray-300 mr-2">
                                <div>
                                    <span class="font-semibold text-slate-800">Jangan Ubah Tagihan Terkait (Hanya Tagihan Baru ke Depan)</span>
                                    <p class="text-[10px] text-muted-foreground mt-0.5">Sama sekali tidak mengubah tagihan belum lunas yang sudah diterbitkan. Perubahan tarif/nama hanya akan berlaku untuk tagihan baru ke depan.</p>
                                </div>
                            </label>
                        </div>
                    </div>
                @endif

                <!-- Affected Billings Preview Section -->
                @if($isEdit)
                    <div class="mt-6 border-t border-border pt-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-bold text-foreground flex items-center gap-2">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Pratinjau Dampak Perubahan Tagihan (Belum Lunas)
                            </h4>
                            @if(count($this->affectedBillings) > 0)
                                <span wire:loading.remove wire:target="amount, item_name, update_policy" class="text-xs px-2.5 py-1 bg-primary/10 text-primary rounded-full font-semibold">
                                    {{ count($this->affectedBillings) }} Tagihan
                                </span>
                            @endif
                        </div>

                        <!-- Loading state -->
                        <div wire:loading wire:target="amount, item_name, update_policy" class="w-full animate-pulse">
                            <div class="flex items-center gap-3 p-3 bg-blue-50/50 border border-blue-200 rounded-lg text-xs text-blue-800">
                                <svg class="animate-spin h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <div class="flex-1">
                                    <span class="font-bold block">Menganalisis Dampak Perubahan...</span>
                                    <span class="text-[10px] text-blue-600 block mt-0.5">Menghitung ulang rincian tagihan santri terdampak berdasarkan kebijakan baru...</span>
                                </div>
                            </div>
                        </div>

                        <!-- Content state -->
                        <div wire:loading.remove wire:target="amount, item_name, update_policy">
                            @if(count($this->affectedBillings) > 0)
                                <div class="border rounded-lg overflow-hidden max-h-[250px] overflow-y-auto">
                                    <table class="min-w-full divide-y divide-border text-xs">
                                        <thead class="bg-muted sticky top-0 z-10">
                                            <tr>
                                                <th class="px-4 py-2.5 text-left font-semibold text-muted-foreground uppercase">Santri</th>
                                                <th class="px-4 py-2.5 text-left font-semibold text-muted-foreground uppercase">Judul Tagihan</th>
                                                <th class="px-4 py-2.5 text-center font-semibold text-muted-foreground uppercase">Jatuh Tempo</th>
                                                <th class="px-4 py-2.5 text-right font-semibold text-red-600 uppercase bg-red-50/30">Sisa Lama</th>
                                                <th class="px-4 py-2.5 text-right font-semibold text-emerald-600 uppercase bg-emerald-50/30">Sisa Baru</th>
                                                <th class="px-4 py-2.5 text-right font-semibold text-muted-foreground uppercase">Selisih</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-border bg-background font-medium">
                                            @foreach($this->affectedBillings as $b)
                                                <tr class="hover:bg-muted/30 transition-colors">
                                                    <td class="px-4 py-2.5">
                                                        <span class="font-semibold block text-foreground">{{ $b['student_name'] }}</span>
                                                        <span class="text-[10px] text-muted-foreground block">{{ $b['student_nis'] }}</span>
                                                    </td>
                                                    <td class="px-4 py-2.5 text-muted-foreground">{{ $b['billing_title'] }}</td>
                                                    <td class="px-4 py-2.5 text-center text-muted-foreground">{{ $b['due_date'] }}</td>
                                                    <td class="px-4 py-2.5 text-right font-mono text-red-600 bg-red-50/10">Rp {{ number_format($b['current_final'], 0, ',', '.') }}</td>
                                                    <td class="px-4 py-2.5 text-right font-mono font-bold text-emerald-600 bg-emerald-50/10">Rp {{ number_format($b['new_final'], 0, ',', '.') }}</td>
                                                    <td class="px-4 py-2.5 text-right font-mono {{ $b['difference'] > 0 ? 'text-red-500' : ($b['difference'] < 0 ? 'text-emerald-500' : 'text-muted-foreground') }}">
                                                        {{ $b['difference'] > 0 ? '+' : '' }}Rp {{ number_format($b['difference'], 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="p-4 bg-muted/20 border border-border border-dashed rounded-lg text-center text-xs text-muted-foreground">
                                    Tidak ada tagihan belum lunas yang terdampak oleh perubahan nominal/nama ini.
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="flex justify-center items-center space-x-3 pt-4 border-t border-border mt-6">
                    <a href="{{ route('admin.fee-masters') }}"
                        class="inline-flex items-center justify-center px-4 py-1.5 border border-input rounded-md shadow-sm text-sm font-medium text-foreground bg-background hover:bg-muted focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                        Batal
                    </a>
                    <button type="submit" wire:loading.attr="disabled" wire:target="save"
                        class="inline-flex items-center justify-center px-4 py-1.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="save">{{ $isEdit ? 'Simpan Perubahan' : 'Tambah Data Biaya' }}</span>
                        <span wire:loading wire:target="save">Memproses...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @script
        <script>
            $wire.on('confirm-fee-creation', (event) => {
                const data = event[0] || event;

                window.Swal.fire({
                    title: 'Konfirmasi Tambah Data Biaya',
                    html: `
                    <div class="space-y-3 text-left">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Nama Item:</span>
                            <span class="text-sm font-semibold">${data.itemName}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Jumlah:</span>
                            <span class="text-sm font-semibold">Rp ${data.amount}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Kategori:</span>
                            <span class="text-sm font-semibold">${data.category}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Target Unit:</span>
                            <span class="text-sm font-semibold">${data.unitTarget}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Target Domisili:</span>
                            <span class="text-sm font-semibold">${data.residenceTarget}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Target Kelas:</span>
                            <span class="text-sm font-semibold">${data.classTarget}</span>
                        </div>
                        <div class="flex justify-between border-t border-gray-200 pt-2 mt-2">
                            <span class="text-sm font-semibold text-gray-800">Total Tagihan Dibuat:</span>
                            <span class="text-sm font-bold text-blue-600">Akan membuat ${data.studentCount} tagihan</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-2 text-center">Lanjutkan?</p>
                    </div>
                `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Buat Tagihan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#6b7280',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $wire.dispatch('confirmedSave');
                    }
                });
            });
        </script>
    @endscript
</div>
