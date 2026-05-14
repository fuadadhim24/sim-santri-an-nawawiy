<div>
    <x-slot name="header">
        {{ $isEdit ? 'Ubah Jadwal SPMB' : 'Tambah Jadwal SPMB' }}
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-card rounded-lg shadow-sm border border-border p-6">
            <form wire:submit="save" class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-foreground">Nama Jadwal</label>
                    <input wire:model="name" type="text" id="name" placeholder="contoh: SPMB 2026/2027"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                    @error('name')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-foreground">Deskripsi</label>
                    <textarea wire:model="description" id="description" rows="3" placeholder="Deskripsi jadwal SPMB..."
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm"></textarea>
                    @error('description')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Registration Start -->
                    <div>
                        <label for="registration_start" class="block text-sm font-medium text-foreground">Tanggal Mulai Pendaftaran</label>
                        <input wire:model.blur="registration_start" type="datetime-local" id="registration_start"
                            class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        @error('registration_start')
                            <span class="text-destructive text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Registration End -->
                    <div>
                        <label for="registration_end" class="block text-sm font-medium text-foreground">Tanggal Selesai Pendaftaran</label>
                        <input wire:model.blur="registration_end" type="datetime-local" id="registration_end"
                            class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        @error('registration_end')
                            <span class="text-destructive text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Is Active -->
                <div>
                    <div class="flex items-center space-x-3">
                        <input type="checkbox" wire:model="is_active" id="is_active"
                            class="h-4 w-4 text-primary focus:ring-primary border-input rounded">
                        <label for="is_active" class="text-sm font-medium text-foreground cursor-pointer">
                            Aktifkan jadwal ini
                        </label>
                    </div>
                    <p class="text-xs text-muted-foreground mt-1">
                        Hanya satu jadwal yang dapat diaktifkan pada satu waktu.
                    </p>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <a href="{{ route('admin.spmb-schedules') }}"
                        class="px-4 py-2 border border-input rounded-md shadow-sm text-sm font-medium text-foreground bg-background hover:bg-muted focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                        Batal
                    </a>
                    <button type="submit" wire:loading.attr="disabled" wire:target="save"
                        class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="save">{{ $isEdit ? 'Simpan Perubahan' : 'Buat Jadwal' }}</span>
                        <span wire:loading wire:target="save">Memproses...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
