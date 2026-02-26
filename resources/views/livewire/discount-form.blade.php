<div>
    <x-slot name="header">
        {{ $isEdit ? 'Ubah DIskon' : 'Buat Diskon' }}
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-card rounded-lg shadow-sm border border-border p-6">
            <form wire:submit="save" class="space-y-6">

                <!-- Fee Master -->
                <div>
                    <label for="fee_master_id" class="block text-sm font-medium text-foreground">Nama Biaya</label>
                    <select wire:model="fee_master_id" id="fee_master_id"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        <option value="">Pilih Nama Biaya...</option>
                        @foreach ($this->feeMasters as $fee)
                            <option value="{{ $fee->id }}">
                                {{ $fee->item_name }} - {{ $fee->category->name ?? 'N/A' }} (Rp
                                {{ number_format($fee->amount, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                    @error('fee_master_id')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Target Status -->
                <div>
                    <label for="target_status" class="block text-sm font-medium text-foreground">Status Target
                        (Penerima)</label>
                    <select wire:model="target_status" id="target_status"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        <option value="ANAK_GURU">Anak Guru</option>
                        <option value="YATIM">Yatim</option>
                    </select>
                    @error('target_status')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Discount Amount -->
                <div>
                    <label for="discount_amount" class="block text-sm font-medium text-foreground">Jumlah Diskon
                        (Rp)</label>
                    <input wire:model="discount_amount" type="number" id="discount_amount" placeholder="e.g. 50000"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                    <p class="mt-1 text-xs text-muted-foreground">Jumlah yang akan dipotong dari biaya asli.</p>
                    @error('discount_amount')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <a href="{{ route('admin.discounts') }}"
                        class="px-4 py-2 border border-input rounded-md shadow-sm text-sm font-medium text-foreground bg-background hover:bg-muted focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                        {{ $isEdit ? 'Simpan Diskon' : 'Buat Diskon' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
