<div>
    <x-slot name="header">
        {{ $isEdit ? 'Ubah DIskon' : 'Buat Diskon' }}
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-card rounded-lg shadow-sm border border-border p-6">
            <form wire:submit="save" class="space-y-6">

                <!-- Fee Master -->
                <div>
                    <label for="fee_master_id" class="block text-sm font-medium text-foreground">Nama Biaya <span class="text-red-500">*</span></label>
                    <select wire:model.live="fee_master_id" id="fee_master_id"
                        {{ $isEdit ? 'disabled' : '' }}
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm {{ $isEdit ? 'bg-muted/40 cursor-not-allowed opacity-80' : '' }}">
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
                        (Penerima) <span class="text-red-500">*</span></label>
                    <select wire:model.live="target_status" id="target_status"
                        {{ $isEdit ? 'disabled' : '' }}
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm {{ $isEdit ? 'bg-muted/40 cursor-not-allowed opacity-80' : '' }}">
                        <option value="ANAK_GURU">Anak Guru</option>
                        <option value="YATIM">Yatim</option>
                        <option value="PRESTASI">Siswa Berprestasi</option>
                        <option value="LINGKUNGAN">Lingkungan</option>
                    </select>
                    @error('target_status')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Discount Amount -->
                <div>
                    <label for="discount_amount" class="block text-sm font-medium text-foreground mb-1">Jumlah Diskon
                        (Rp) <span class="text-red-500">*</span></label>
                    <div class="relative rounded-md shadow-sm" x-data="{
                        get displayValue() {
                            let val = $wire.discount_amount;
                            if (!val) return '';
                            return val.toString().replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                        }
                    }">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-muted-foreground sm:text-sm font-medium">Rp</span>
                        </div>
                        <input type="text" x-bind:value="displayValue"
                            x-on:input.debounce.500ms="$wire.discount_amount = $event.target.value.replace(/\D/g, ''); $wire.$refresh();" placeholder="50.000"
                            class="block w-full pl-10 pr-4 py-2 border border-input bg-background rounded-md focus:outline-none focus:ring-ring focus:border-ring sm:text-sm font-mono">
                    </div>
                    <p class="mt-1 text-xs text-muted-foreground">Jumlah yang akan dipotong dari biaya asli.</p>
                    @error('discount_amount')
                        <span class="text-destructive text-sm block mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Alert: Perubahan Tidak Berlaku Surut (Only shown in Edit mode) -->
                @if($isEdit)
                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg flex items-start gap-3 mt-6 shadow-sm">
                        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div>
                            <h5 class="text-xs font-bold text-amber-800 uppercase tracking-wider">Perhatian: Perubahan Tidak Berlaku Surut</h5>
                            <p class="text-[11px] text-amber-700 mt-1">Perubahan nominal diskon ini <strong>tidak akan mengubah potongan pada tagihan yang sudah diterbitkan/dihasilkan</strong> sebelumnya. Diskon baru hanya akan diterapkan pada tagihan yang akan dibuat di masa mendatang.</p>
                        </div>
                    </div>
                @endif

                <div class="flex justify-end space-x-3 pt-4 border-t border-border mt-6">
                    <a href="{{ route('admin.discounts') }}"
                        class="px-4 py-2 border border-input rounded-md shadow-sm text-sm font-medium text-foreground bg-background hover:bg-muted focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                        Batal
                    </a>
                    <button type="submit" wire:loading.attr="disabled" wire:target="save"
                        class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="save">{{ $isEdit ? 'Simpan Diskon' : 'Buat Diskon' }}</span>
                        <span wire:loading wire:target="save">Memproses...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
