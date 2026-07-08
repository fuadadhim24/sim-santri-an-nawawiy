<div>
    <x-slot name="header">
        Manajemen Diskon
    </x-slot>

    <div class="bg-card rounded-lg shadow-sm border border-border">
        <div class="p-6 border-b border-border flex items-center justify-between gap-4 overflow-x-auto no-scrollbar">
            <h3 class="text-lg font-semibold text-card-foreground whitespace-nowrap">Daftar Diskon</h3>
            <div class="flex items-center space-x-2">
                <input wire:model.live="search" type="text" placeholder="Cari Berdasarkan Nama Biaya..."
                    class="py-2 px-4 border border-input bg-background rounded-md focus:outline-none focus:ring-2 focus:ring-ring text-foreground">
                <a href="{{ route('admin.discounts.create') }}"
                    class="inline-flex items-center justify-center py-2 px-4 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 text-sm font-medium whitespace-nowrap flex-shrink-0">+
                    Tambah Diskon</a>
            </div>
        </div>
        @if (session()->has('message'))
            <div class="p-4 mx-6 mt-4 bg-green-100 text-green-700 rounded-md">
                {{ session('message') }}
            </div>
        @endif
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-muted-foreground uppercase bg-muted">
                    <tr>
                        <th class="px-6 py-3">Nama Biaya</th>
                        <th class="px-6 py-3">Target Status</th>
                        <th class="px-6 py-3 text-right">Jumlah Diskon</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($discounts as $discount)
                        <tr class="hover:bg-muted/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-foreground">
                                {{ $discount->feeMaster?->item_name ?? 'N/A' }}
                                @if($discount->feeMaster?->trashed())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-150 text-red-700 ml-1">Terhapus</span>
                                @endif
                                <span class="text-xs text-muted-foreground block">
                                    {{ $discount->feeMaster?->category?->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                    {{ str_replace('_', ' ', $discount->target_status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-mono font-medium text-foreground">
                                Rp {{ number_format($discount->discount_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.discounts.edit', $discount) }}"
                                    class="text-primary hover:text-primary/80 font-medium mr-3">Ubah</a>
                                <button
                                    wire:click="confirmDelete({{ $discount->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="confirmDelete({{ $discount->id }})"
                                    class="text-destructive hover:text-destructive/80 font-medium">
                                    <span wire:loading.remove wire:target="confirmDelete({{ $discount->id }})">Hapus</span>
                                    <span wire:loading wire:target="confirmDelete({{ $discount->id }})">
                                        <svg class="animate-spin inline-block align-middle h-3 w-3 mr-1 text-destructive" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span class="align-middle">Memproses...</span>
                                    </span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-muted-foreground">
                                Tidak ada diskon ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-border">
            {{ $discounts->links() }}
        </div>
    </div>

    @script
        <script>
            $wire.on('show-delete-discount-confirmation', (event) => {
                const data = event[0] || event;
                
                window.Swal.fire({
                    title: 'Hapus Aturan Diskon?',
                    html: `
                        <div class="text-sm text-left space-y-2 text-foreground">
                            <p>Aturan diskon untuk biaya <strong>${data.feeMasterName}</strong> (Target: <span class="px-1.5 py-0.5 bg-purple-100 text-purple-700 text-xs rounded-full font-semibold">${data.targetStatus}</span>) akan dihapus.</p>
                            <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-amber-800 font-semibold flex items-center gap-2 mt-2">
                                <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                <span>Terdeteksi ${data.affectedCount} tagihan belum lunas yang terkait. Penghapusan tidak berlaku surut dan tidak akan mengubah potongan pada tagihan tersebut.</span>
                            </div>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $wire.dispatch('execute-delete-discount', { id: data.id });
                    }
                });
            });

            $wire.on('show-simple-delete-discount-confirmation', (event) => {
                const data = event[0] || event;
                window.Swal.fire({
                    title: 'Hapus Aturan Diskon?',
                    text: 'Apakah Anda yakin ingin menghapus aturan diskon ini secara permanen?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $wire.dispatch('execute-delete-discount', { id: data.id });
                    }
                });
            });
        </script>
    @endscript
</div>
