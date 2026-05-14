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
                                Biaya</label>
                            <a href="{{ route('admin.fee-categories.create') }}"
                                class="text-xs text-primary hover:underline">+ Kategori Baru</a>
                        </div>
                        <select wire:model.live="fee_category_id" id="fee_category_id"
                            class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
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
                            Biaya</label>
                        <input wire:model="item_name" type="text" id="item_name" placeholder="contoh: SPP 2026"
                            class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        @error('item_name')
                            <span class="text-destructive text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Amount -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-foreground mb-1">Jumlah (Rp)</label>
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
                            x-on:input="$wire.amount = $event.target.value.replace(/\D/g, '')" placeholder="500.000"
                            class="block w-full pl-10 pr-4 py-2 border border-input bg-background rounded-md focus:outline-none focus:ring-ring focus:border-ring sm:text-sm font-mono">
                    </div>
                    @error('amount')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Scheduling / Recurrence Rules -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-border">
                    <div>
                        <label for="recurrence_type" class="block text-sm font-medium text-foreground mb-1">Tipe Siklus Tagihan</label>
                        <select wire:model.live="recurrence_type" id="recurrence_type"
                            class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                            <option value="ONE_TIME">Sekali Bayar (Misal: Uang Pangkal)</option>
                            <option value="MONTHLY">Bulanan (Misal: SPP)</option>
                            <option value="YEARLY">Tahunan (Misal: Daftar Ulang)</option>
                        </select>
                        @error('recurrence_type')
                            <span class="text-destructive text-sm">{{ $message }}</span>
                        @enderror
                        <p class="text-xs text-muted-foreground mt-1">Siklus berulangnya tagihan ini.</p>
                    </div>

                    <div>
                        <label for="due_days" class="block text-sm font-medium text-foreground mb-1">Jatuh Tempo (Hari)</label>
                        <input wire:model="due_days" type="number" id="due_days" min="0" placeholder="14"
                            class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        @error('due_days')
                            <span class="text-destructive text-sm">{{ $message }}</span>
                        @enderror
                        <p class="text-xs text-muted-foreground mt-1">Batas waktu bayar setelah tagihan terbit.</p>
                    </div>

                    @if($recurrence_type !== 'ONE_TIME')
                    <div>
                        <label for="billing_day" class="block text-sm font-medium text-foreground mb-1">Tanggal Generate</label>
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
                        <label for="start_date" class="block text-sm font-medium text-foreground">Mulai Berlaku</label>
                        <input wire:model="start_date" type="date" id="start_date"
                            class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        @error('start_date')
                            <span class="text-destructive text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-foreground">Berakhir Pada
                            (Opsional)</label>
                        <input wire:model="end_date" type="date" id="end_date"
                            class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        @error('end_date')
                            <span class="text-destructive text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Targets -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-border">
                    <div>
                        <label for="unit_target" class="block text-sm font-medium text-foreground mb-1">Target
                            Unit</label>
                        <select wire:model="unit_target" id="unit_target"
                            class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                            <option value="">Semua Unit</option>
                            <option value="01">SMP</option>
                            <option value="02">SMA</option>
                            <option value="03">PPTQ</option>
                        </select>
                    </div>
                    <div>
                        <label for="residence_target" class="block text-sm font-medium text-foreground mb-1">Target
                            Domisili</label>
                        <select wire:model="residence_target" id="residence_target"
                            class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                            <option value="">Semua Status Domisili</option>
                            <option value="MONDOK">Mondok</option>
                            <option value="NON_MONDOK">Non Mondok</option>
                            <option value="NGAJI_ONLY">Ngaji Saja</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t border-border">
                    <a href="{{ route('admin.fee-masters') }}"
                        class="px-4 py-2 border border-input rounded-md shadow-sm text-sm font-medium text-foreground bg-background hover:bg-muted focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                        {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Data Biaya' }}
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
