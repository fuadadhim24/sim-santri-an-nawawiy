<div>
    <x-slot name="header">
        Manajemen Kategori Biaya
    </x-slot>

    <div class="bg-card rounded-lg shadow-sm border border-border">
        <div class="p-6 border-b border-border flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h3 class="text-lg font-semibold text-card-foreground">Daftar Kategori</h3>
            <div class="flex items-center space-x-2">
                <input wire:model.live="search" type="text" placeholder="Cari kategori..."
                    class="w-full md:w-64 py-2 px-4 border border-input bg-background rounded-md focus:outline-none focus:ring-2 focus:ring-ring text-foreground">
                <a href="{{ route('admin.fee-categories.create') }}"
                    class="inline-flex items-center justify-center py-2 px-4 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 font-semibold whitespace-nowrap">
                    + Tambah Kategori
                </a>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="p-4 mx-6 mt-4 bg-green-100 text-green-700 rounded-md">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="p-4 mx-6 mt-4 bg-red-100 text-red-700 rounded-md">
                {{ session('error') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-muted-foreground uppercase bg-muted">
                    <tr>
                        <th class="px-6 py-3">Kode</th>
                        <th class="px-6 py-3">Nama Kategori</th>
                        <th class="px-6 py-3">Mode Aktivasi</th>
                        <th class="px-6 py-3 text-center">Kunci</th>
                        <th class="px-6 py-3 text-center">Sebelum Diterima</th>
                        <th class="px-6 py-3 text-center">Status</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($categories as $category)
                        <tr class="hover:bg-muted/50 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-primary">{{ $category->code }}</td>
                            <td class="px-6 py-4 font-medium text-foreground">{{ $category->name }}</td>
                            <td class="px-6 py-4">
                                @if($category->activation_mode === 'single_active_per_key')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Otomatis (1 aktif)
                                    </span>
                                @elseif($category->activation_mode === 'multi_active')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Otomatis (banyak)
                                    </span>
                                @elseif($category->activation_mode === 'manual_only')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Tidak Otomatis
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($category->is_locked)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Terkunci
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Terbuka
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($category->can_generate_before_acceptance)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Ya
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Tidak
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($category->is_active)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Non-aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-3">
                                    <a href="{{ route('admin.fee-categories.edit', $category->id) }}"
                                        class="text-primary hover:text-primary/80 font-medium">Edit</a>
                                    @if(!$category->is_locked)
                                        <button wire:click="confirmDelete({{ $category->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="confirmDelete({{ $category->id }})"
                                            class="text-destructive hover:text-destructive/80 font-medium inline-flex items-center">
                                            <span wire:loading.remove wire:target="confirmDelete({{ $category->id }})">Hapus</span>
                                            <span wire:loading wire:target="confirmDelete({{ $category->id }})">
                                                <svg class="animate-spin inline-block align-middle h-3 w-3 mr-1 text-destructive" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                <span class="align-middle">Analisis...</span>
                                            </span>
                                        </button>
                                    @else
                                        <span class="text-muted-foreground text-xs">Terkunci</span>
                                    @endif
                                </div>
                             </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-muted-foreground">
                                Tidak ada kategori ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-border">
            {{ $categories->links() }}
        </div>
    </div>

    @script
    <script>
        $wire.on('swal:confirm-simple-delete', (event) => {
            const data = event[0] || event;
            window.Swal.fire({
                title: 'Hapus Kategori?',
                text: `Apakah Anda yakin ingin menghapus kategori "${data.name}" secara permanen?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.deleteCategoryDirect(data.id);
                }
            });
        });

        $wire.on('swal:confirm-complex-delete', (event) => {
            const data = event[0] || event;

            const feeListHtml = (data.feeNames && data.feeNames.length)
                ? `<details style="margin-top:8px;text-align:left;">
                        <summary style="cursor:pointer;font-size:12px;color:#6b7280;user-select:none;">
                            Lihat daftar master biaya (${data.feeNames.length})
                        </summary>
                        <ul style="margin-top:6px;padding-left:16px;font-size:12px;color:#374151;list-style:disc;">
                            ${data.feeNames.map(n => `<li>${n}</li>`).join('')}
                        </ul>
                    </details>`
                : '';
            
            window.Swal.fire({
                title: 'Tidak Bisa Dihapus',
                html: `
                    <div class="text-left space-y-3">
                        <p>Kategori <b>${data.name}</b> memiliki <b>${data.feeCount} Master Biaya</b> terkait.</p>
                        ${feeListHtml}
                        <div class="bg-amber-50 border border-amber-200 p-3 rounded text-amber-800 text-xs mt-2">
                            ⚠️ Kategori yang memiliki data master biaya terkait tidak dapat dihapus. Silakan nonaktifkan kategori ini jika tidak ingin digunakan lagi.
                        </div>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#eab308',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Nonaktifkan Kategori',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.deactivateCategory(data.id);
                }
            });
        });
    </script>
    @endscript
</div>
