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
                        Single Active Per Key: Hanya satu tagihan aktif per kunci<br>
                        Multi Active: Beberapa tagihan dapat aktif sekaligus<br>
                        Manual Only: Tagihan hanya bisa dibuat secara manual
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
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                        {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Kategori' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
