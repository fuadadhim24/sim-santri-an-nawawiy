<div>
    <x-slot name="header">
        Manajemen Jadwal SPMB
    </x-slot>

    <div class="bg-card rounded-lg shadow-sm border border-border">
        <div class="p-6 border-b border-border flex items-center justify-between gap-4 overflow-x-auto no-scrollbar">
            <h3 class="text-lg font-semibold text-card-foreground whitespace-nowrap">Daftar Jadwal SPMB</h3>
            <a href="{{ route('admin.spmb-schedules.create') }}"
                class="inline-flex items-center justify-center py-2 px-4 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 text-sm font-medium whitespace-nowrap flex-shrink-0">+
                Tambah Jadwal</a>
        </div>
        @if (session()->has('message'))
            <div class="p-4 mx-6 mt-4 bg-green-150 text-green-800 rounded-md border border-green-200">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="p-4 mx-6 mt-4 bg-red-100 text-red-800 rounded-md border border-red-200">
                {{ session('error') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-muted-foreground uppercase bg-muted">
                    <tr>
                        <th class="px-6 py-3">Nama Jadwal</th>
                        <th class="px-6 py-3">Deskripsi</th>
                        <th class="px-6 py-3">Mulai Pendaftaran</th>
                        <th class="px-6 py-3">Selesai Pendaftaran</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($schedules as $schedule)
                        <tr class="hover:bg-muted/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-foreground">{{ $schedule->name }}</td>
                            <td class="px-6 py-4 text-muted-foreground">{{ $schedule->description ?? '-' }}</td>
                            <td class="px-6 py-4 text-muted-foreground">{{ $schedule->registration_start->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4 text-muted-foreground">{{ $schedule->registration_end->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    @if ($schedule->is_active)
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                            Tidak Aktif
                                        </span>
                                    @endif
                                    @if ($schedule->isOpen())
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                            Dibuka
                                        </span>
                                    @elseif ($schedule->isClosed())
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                            Ditutup
                                        </span>
                                    @else
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                            Belum Dibuka
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <button wire:click="toggleActive({{ $schedule->id }})"
                                        class="text-primary hover:text-primary/80 font-medium text-xs">
                                        {{ $schedule->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                    <span class="text-muted-foreground">|</span>
                                    <a href="{{ route('admin.spmb-schedules.edit', $schedule) }}"
                                        class="text-primary hover:text-primary/80 font-medium text-xs">Ubah</a>
                                    <span class="text-muted-foreground">|</span>
                                    <button wire:click="confirmDelete({{ $schedule->id }})"
                                        class="text-destructive hover:text-destructive/80 font-medium text-xs">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-muted-foreground">
                                Belum ada jadwal SPMB. Klik tombol "Tambah Jadwal" untuk membuat jadwal baru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <x-modal name="delete-confirm-modal" :show="false">
        <div class="p-6">
            <h3 class="text-lg font-medium text-foreground mb-2">Konfirmasi Hapus</h3>
            <p class="text-sm text-muted-foreground mb-4">Apakah Anda yakin ingin menghapus jadwal SPMB ini? Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="Livewire.dispatch('closeModal', 'delete-confirm-modal')"
                    class="px-4 py-2 border border-input rounded-md shadow-sm text-sm font-medium text-foreground bg-background hover:bg-muted focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                    Batal
                </button>
                <button type="button" onclick="confirmDeleteAction()"
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-destructive-foreground bg-destructive hover:bg-destructive/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                    Hapus
                </button>
            </div>
        </div>
    </x-modal>

    @script
    <script>
        let deleteId = null;

        function confirmDelete(id) {
            deleteId = id;
            Livewire.dispatch('openModal', 'delete-confirm-modal');
        }

        function confirmDeleteAction() {
            if (deleteId) {
                @this.delete(deleteId);
                deleteId = null;
                Livewire.dispatch('closeModal', 'delete-confirm-modal');
            }
        }
    </script>
    @endscript
</div>
