<div>
    <x-slot name="header">
        Manajemen Wali Santri
    </x-slot>

    <div class="bg-card rounded-lg shadow-sm border border-border">
        <div class="p-6 border-b border-border flex items-center justify-between gap-4 overflow-x-auto no-scrollbar">
            <h3 class="text-lg font-semibold text-card-foreground whitespace-nowrap">Daftar Wali Santri</h3>
            <div class="flex items-center space-x-2">
                <input wire:model.live="search" type="text" placeholder="Cari wali..."
                    class="py-2 px-4 border border-input bg-background rounded-md focus:outline-none focus:ring-2 focus:ring-ring text-foreground">
                <a href="{{ route('admin.guardians.create') }}"
                    class="inline-flex items-center justify-center py-2 px-4 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 text-sm font-medium whitespace-nowrap flex-shrink-0">+
                    Tambah Wali</a>
            </div>
        </div>
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
                    @foreach ($guardians as $guardian)
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
                                <a href="{{ route('admin.guardians.edit', $guardian) }}"
                                    class="text-primary hover:text-primary/80 font-medium">Ubah</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-border">
            {{ $guardians->links() }}
        </div>
    </div>
</div>
