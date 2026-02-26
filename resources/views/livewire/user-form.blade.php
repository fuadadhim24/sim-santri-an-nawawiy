<div>
    <x-slot name="header">
        {{ $isEdit ? 'Ubah Pengguna' : 'Buat Pengguna' }}
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-card rounded-lg shadow-sm border border-border p-6">
            <form wire:submit="save" class="space-y-6">

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-foreground">Nama</label>
                    <input wire:model="name" type="text" id="name"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                    @error('name')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-foreground">Alamat Email</label>
                    <input wire:model="email" type="email" id="email"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                    @error('email')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-foreground">
                        Kata Sandi {{ $isEdit ? '(Kosongkan jika tidak ingin mengubah)' : '' }}
                    </label>
                    <input wire:model="password" type="password" id="password"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                    @error('password')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-foreground">Peran</label>
                    <select wire:model="role" id="role"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        <option value="ADMIN_TU">Admin TU</option>
                        <option value="SUPER_ADMIN">Super Admin</option>
                    </select>
                    @error('role')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <a href="{{ route('admin.users') }}"
                        class="px-4 py-2 border border-input rounded-md shadow-sm text-sm font-medium text-foreground bg-background hover:bg-muted focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                        {{ $isEdit ? 'Simpan Pengguna' : 'Buat Pengguna' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
