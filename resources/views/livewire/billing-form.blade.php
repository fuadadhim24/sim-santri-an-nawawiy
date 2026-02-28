<div>
    <x-slot name="header">
        Buat Tagihan Manual
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-card rounded-lg shadow-sm border border-border p-6">
            <div class="mb-6">
                <h3 class="text-lg font-medium text-foreground">Buat Tagihan Baru</h3>
                <p class="text-sm text-muted-foreground">
                    Pilih santri dan jenis biaya untuk membuat tagihan secara manual.
                </p>
            </div>

            <form wire:submit="save" class="space-y-6">
                <!-- Student Selection -->
                <div>
                    <label for="student_id" class="block text-sm font-medium text-foreground">Santri</label>
                    <select wire:model.live="student_id" id="student_id"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        <option value="">Pilih Santri</option>
                        @foreach ($this->students as $student)
                            <option value="{{ $student->id }}">
                                {{ $student->full_name }} ({{ $student->nis }})
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Fee Master Selection -->
                <div>
                    <label for="fee_master_id" class="block text-sm font-medium text-foreground">Jenis Biaya</label>
                    <select wire:model.live="fee_master_id" id="fee_master_id"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        <option value="">Pilih Jenis Biaya</option>
                        @foreach ($this->feeMasters as $fee)
                            <option value="{{ $fee->id }}">
                                {{ $fee->item_name }} - {{ $fee->category->name ?? 'Tanpa Kategori' }} (Rp {{ number_format($fee->amount, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                    @error('fee_master_id')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-foreground">Judul Tagihan</label>
                    <input wire:model="title" type="text" id="title"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm"
                        placeholder="contoh: SPP Januari 2026">
                    @error('title')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Amounts -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="original_amount" class="block text-sm font-medium text-foreground">Jumlah Asli</label>
                        <input wire:model.live="original_amount" type="number" id="original_amount" step="0"
                            class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        @error('original_amount')
                            <span class="text-destructive text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="discount_applied" class="block text-sm font-medium text-foreground">Diskon</label>
                        <input wire:model.live="discount_applied" type="number" id="discount_applied" step="0"
                            class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        @error('discount_applied')
                            <span class="text-destructive text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="final_amount" class="block text-sm font-medium text-foreground">Jumlah Akhir</label>
                        <input wire:model="final_amount" type="number" id="final_amount" step="0" readonly
                            class="mt-1 block w-full px-3 py-2 border border-input bg-muted rounded-md shadow-sm sm:text-sm text-muted-foreground">
                        @error('final_amount')
                            <span class="text-destructive text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <a href="{{ route('admin.billings') }}"
                        class="px-4 py-2 border border-input rounded-md shadow-sm text-sm font-medium text-foreground bg-background hover:bg-muted focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                        Buat Tagihan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
