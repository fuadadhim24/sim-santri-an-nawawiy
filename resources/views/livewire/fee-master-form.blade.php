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
                        <input wire:model="item_name" type="text" id="item_name"
                            placeholder="contoh: SPP 2026"
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
</div>
