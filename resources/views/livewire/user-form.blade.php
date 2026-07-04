<div>
    <x-slot name="header">
        {{ $isEdit ? 'Ubah Pengguna' : 'Buat Pengguna' }}
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-card rounded-lg shadow-sm border border-border p-6">
            <form wire:submit="save" class="space-y-6">

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-foreground">Nama <span class="text-red-500">*</span></label>
                    <input wire:model="name" type="text" id="name"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                    @error('name')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- WhatsApp -->
                <div>
                    <label for="whatsapp" class="block text-sm font-medium text-foreground">Nomor WhatsApp <span class="text-red-500">*</span></label>
                    <input wire:model="whatsapp" type="tel" id="whatsapp" placeholder="08xxxxxxxxxx"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                    @error('whatsapp')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email (Optional) -->
                <div>
                    <label for="email" class="block text-sm font-medium text-foreground">Email <span class="text-muted-foreground font-normal text-[11px]">(Opsional)</span></label>
                    <input wire:model="email" type="email" id="email" placeholder="contoh@email.com"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                    @error('email')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-foreground">
                        @if($isEdit)
                            Kata Sandi <span class="text-muted-foreground font-normal text-[11px]">(Opsional - Kosongkan jika tidak ingin mengubah)</span>
                        @else
                            Kata Sandi <span class="text-red-500">*</span>
                        @endif
                    </label>
                    <input wire:model="password" type="password" id="password"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                    @error('password')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-foreground">Peran <span class="text-red-500">*</span></label>
                    <select wire:model="role" id="role"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        <option value="ADMINISTRASI">Administrasi</option>
                        <option value="BENDAHARA">Bendahara</option>
                        <option value="SUPER_ADMIN">Super Admin</option>
                    </select>
                    @error('role')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Status -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input wire:model="is_active" type="checkbox" id="is_active"
                            class="h-4 w-4 text-primary focus:ring-ring border-input rounded">
                    </div>
                    <div class="ml-3">
                        <label for="is_active" class="block text-sm font-medium text-foreground">Status Aktif</label>
                        <p class="mt-1 text-xs text-muted-foreground">Jika dinonaktifkan, user tidak akan bisa login ke aplikasi.</p>
                    </div>
                </div>
                @error('is_active')
                    <span class="text-destructive text-sm block mt-1">{{ $message }}</span>
                @enderror

                <div class="flex justify-end space-x-3 pt-4">
                    <a href="{{ route('admin.users') }}"
                        class="px-4 py-2 border border-input rounded-md shadow-sm text-sm font-medium text-foreground bg-background hover:bg-muted focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                        Batal
                    </a>
                    <button type="submit" wire:loading.attr="disabled" wire:target="save"
                        class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="save">{{ $isEdit ? 'Simpan Pengguna' : 'Buat Pengguna' }}</span>
                        <span wire:loading wire:target="save">Memproses...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
