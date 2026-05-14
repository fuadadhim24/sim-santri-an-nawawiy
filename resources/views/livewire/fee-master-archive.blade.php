<div>
    <x-slot name="header">
        Archive Master Biaya
    </x-slot>

    <div class="max-w-7xl mx-auto">
        <div class="bg-card rounded-lg shadow-sm border border-border p-6">
            <!-- Search and Filter -->
            <div class="mb-6 flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
                <div class="flex-1">
                    <input wire:model.live="search" type="text" placeholder="Cari Master Biaya..."
                        class="w-full px-4 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                </div>
                <div class="flex gap-2">
                    <select wire:model.live="filter"
                        class="px-4 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        <option value="all">Semua</option>
                        <option value="active">Aktif</option>
                        <option value="archived">Diarsipkan</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto border border-border rounded-md">
                <table class="min-w-full divide-y divide-border">
                    <thead class="bg-muted">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                Nama Item
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                Kategori
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                Jumlah
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                Unit Target
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                Domisili Target
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                Mulai Berlaku
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                Berakhir Pada
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-background divide-y divide-border">
                        @forelse($feeMasters as $fee)
                            <tr class="hover:bg-muted/50">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-foreground">
                                    {{ $fee->item_name }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-muted-foreground">
                                    {{ $fee->category->name ?? 'Tanpa Kategori' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-foreground font-medium">
                                    Rp {{ number_format($fee->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-muted-foreground">
                                    {{ $fee->unit_target ?? 'Semua Unit' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-muted-foreground">
                                    {{ $fee->residence_target ?? 'Semua Status' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-muted-foreground">
                                    {{ $fee->start_date ? $fee->start_date->format('d M Y') : '-' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-muted-foreground">
                                    {{ $fee->end_date ? $fee->end_date->format('d M Y') : '-' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    @if ($fee->is_active)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Diarsipkan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    <div class="flex items-center space-x-2">
                                        <button wire:click="restore({{ $fee->id }})" wire:confirm="Apakah Anda yakin ingin me-restore master biaya ini?" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            Restore
                                        </button>
                                        <button wire:click="forceDelete({{ $fee->id }})" wire:confirm="Apakah Anda yakin ingin menghapus permanen master biaya ini? Tindakan ini tidak dapat dibatalkan." class="text-red-600 hover:text-red-800 text-sm font-medium">
                                            Hapus Permanen
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-sm text-muted-foreground">
                                    Tidak ada data master biaya yang diarsipkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($feeMasters->hasPages())
                <div class="mt-6 flex justify-center">
                    {{ $feeMasters->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
