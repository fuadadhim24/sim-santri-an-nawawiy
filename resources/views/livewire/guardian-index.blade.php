<div>
    <x-slot name="header">
        Manajemen Wali Santri
    </x-slot>

    <div class="bg-card rounded-lg shadow-sm border border-border">
        <div class="p-6 border-b border-border flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h3 class="text-lg font-semibold text-card-foreground">Daftar Wali Santri</h3>
                <p class="text-xs text-muted-foreground mt-0.5">Manajemen akun pengguna wali santri dan data profil mereka.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <!-- Filter checkbox -->
                <label class="inline-flex items-center text-xs font-medium text-muted-foreground hover:text-foreground cursor-pointer bg-muted/50 px-3 py-2 rounded-md border border-border transition-colors select-none">
                    <input type="checkbox" wire:model.live="filterNoStudents" class="rounded border-input text-primary focus:ring-primary mr-2">
                    Wali Tanpa Santri
                </label>
                
                <!-- Search input -->
                <input wire:model.live="search" type="text" placeholder="Cari wali..."
                    class="py-2 px-3 border border-input bg-background rounded-md focus:outline-none focus:ring-2 focus:ring-ring text-xs text-foreground w-40 sm:w-48">
                
                <!-- Clean up empty guardians -->
                @if ($filterNoStudents && $this->hasGuardiansWithoutStudents)
                    <button wire:click="deleteAllWithoutStudents" 
                        wire:swal="Apakah Anda yakin ingin menghapus SEMUA akun wali santri yang tidak memiliki santri terdaftar? Tindakan ini tidak dapat dibatalkan."
                        class="inline-flex items-center justify-center py-2 px-3 bg-destructive/10 text-destructive border border-destructive/20 hover:bg-destructive/20 text-xs font-medium rounded-md transition shadow-sm"
                        title="Hapus Semua Wali Tanpa Santri">
                        Bersihkan Semua Wali Tanpa Santri
                    </button>
                @endif

                <!-- Add new -->
                <a href="{{ route('admin.guardians.create') }}"
                    class="inline-flex items-center justify-center py-2 px-4 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 text-xs font-semibold whitespace-nowrap">
                    + Tambah Wali
                </a>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="p-4 mx-6 mt-4 bg-green-150 text-green-800 rounded-md border border-green-200">
                {{ session('message') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-muted-foreground uppercase bg-muted">
                    <tr>
                        <th class="px-6 py-3">Nama Lengkap</th>
                        <th class="px-6 py-3">WhatsApp</th>
                        <th class="px-6 py-3">Akun Pengguna</th>
                        <th class="px-6 py-3">Santri</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($guardians as $guardian)
                        <tr class="hover:bg-muted/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-foreground">{{ $guardian->full_name }}</td>
                            <td class="px-6 py-4 text-muted-foreground">{{ $guardian->whatsapp }}</td>
                            <td class="px-6 py-4 text-muted-foreground">{{ $guardian->user->email ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <span class="bg-secondary text-secondary-foreground px-2 py-1 rounded-full text-xs">
                                    {{ $guardian->students->count() }} Santri
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('admin.guardians.edit', $guardian) }}"
                                        class="text-primary hover:text-primary/80 font-medium">Ubah</a>
                                    @if ($guardian->students->count() === 0)
                                        <button wire:click="deleteSingle({{ $guardian->id }})"
                                            wire:swal="Apakah Anda yakin ingin menghapus akun wali '{{ $guardian->full_name }}' beserta akun pengguna mereka? Tindakan ini tidak dapat dibatalkan."
                                            class="text-destructive hover:text-destructive/80 font-medium">Hapus</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-muted-foreground">
                                Tidak ada wali santri ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-border">
            {{ $guardians->links() }}
        </div>
    </div>
</div>
