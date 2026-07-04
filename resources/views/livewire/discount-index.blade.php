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
                                    wire:click="delete({{ $discount->id }})"
                                    wire:confirm="Hapus diskon ini secara permanen?"
                                    wire:loading.attr="disabled"
                                    wire:target="delete({{ $discount->id }})"
                                    class="text-destructive hover:text-destructive/80 font-medium">
                                    <span wire:loading.remove wire:target="delete({{ $discount->id }})">Hapus</span>
                                    <span wire:loading wire:target="delete({{ $discount->id }})">
                                        <svg class="animate-spin inline-block align-middle h-3 w-3 mr-1 text-destructive" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span class="align-middle">Menghapus...</span>
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
</div>
