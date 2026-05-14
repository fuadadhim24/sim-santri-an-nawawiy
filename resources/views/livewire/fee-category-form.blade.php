<div>
    <x-slot name="header">
        {{ $isEdit ? 'Edit Kategori Biaya' : 'Tambah Kategori Biaya' }}
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-card rounded-lg shadow-sm border border-border p-6">
            <form wire:submit="save" class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-foreground">Nama Kategori</label>
                    <input wire:model="name" type="text" id="name" placeholder="contoh: SPP"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                    @error('name')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-foreground">Kode Kategori (Slug)</label>
                    <input wire:model="code" type="text" id="code" placeholder="contoh: SPP_BULANAN"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm uppercase">
                    <p class="mt-1 text-xs text-muted-foreground">Gunakan huruf besar, angka, dan underscore saja.</p>
                    @error('code')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Activation Mode -->
                <div>
                    <label for="activation_mode" class="block text-sm font-medium text-foreground">Mode Aktivasi</label>
                    <select wire:model="activation_mode" id="activation_mode"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        @foreach($this->activationModeOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-muted-foreground">
                        Mengatur bagaimana tagihan di-generate saat santri diterima:<br><br>
                        <strong>Otomatis (1 tagihan aktif):</strong> Tagihan lama dinonaktifkan saat tagihan baru dibuat (contoh: SPP bulanan)<br>
                        <strong>Otomatis (boleh banyak tagihan):</strong> Bisa punya beberapa tagihan aktif bersamaan (contoh: Daftar Ulang, Uang Saku)<br>
                        <strong>Tidak Otomatis (manual):</strong> Tidak ikut di-generate saat santri diterima, admin harus buat tagihan sendiri (contoh: Biaya SPMB)
                    </p>
                    @error('activation_mode')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Is Locked -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input wire:model="is_locked" type="checkbox" id="is_locked"
                            class="h-4 w-4 text-primary focus:ring-ring border-input rounded">
                    </div>
                    <div class="ml-3">
                        <label for="is_locked" class="block text-sm font-medium text-foreground">Kunci Kategori</label>
                        <p class="mt-1 text-xs text-muted-foreground">Jika dicentang, kategori tidak dapat dihapus atau dimodifikasi.</p>
                    </div>
                    @error('is_locked')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Can Generate Before Acceptance -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input wire:model="can_generate_before_acceptance" type="checkbox" id="can_generate_before_acceptance"
                            class="h-4 w-4 text-primary focus:ring-ring border-input rounded">
                    </div>
                    <div class="ml-3">
                        <label for="can_generate_before_acceptance" class="block text-sm font-medium text-foreground">Buat Tagihan Sebelum Diterima</label>
                        <p class="mt-1 text-xs text-muted-foreground">Jika dicentang, tagihan dapat dibuat untuk siswa sebelum status diterima.</p>
                    </div>
                    @error('can_generate_before_acceptance')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <a href="{{ route('admin.fee-categories') }}"
                        class="px-4 py-2 border border-input rounded-md shadow-sm text-sm font-medium text-foreground bg-background hover:bg-muted focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                        Batal
                    </a>
                    <button type="submit" wire:loading.attr="disabled" wire:target="save"
                        class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="save">{{ $isEdit ? 'Simpan Perubahan' : 'Tambah Kategori' }}</span>
                        <span wire:loading wire:target="save">Memproses...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
