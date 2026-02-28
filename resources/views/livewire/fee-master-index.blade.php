<div>
    <x-slot name="header">
        Manajemen Data Biaya
    </x-slot>

    <div class="bg-card rounded-lg shadow-sm border border-border">
        <div class="p-6 border-b border-border flex items-center justify-between gap-4">
            <h3 class="text-lg font-semibold text-card-foreground whitespace-nowrap">Daftar Biaya</h3>
            <div class="flex items-center space-x-2 flex-nowrap">
                <select wire:model.live="categoryFilter"
                    class="w-40 md:w-48 py-2 px-8 pr-10 border border-input bg-background rounded-md focus:outline-none focus:ring-2 focus:ring-ring text-foreground appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_0.75rem_center] bg-no-repeat transition-all">
                    <option value="">Semua Kategori</option>
                    @foreach ($this->feeCategories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                <input wire:model.live="search" type="text" placeholder="Cari biaya..."
                    class="w-40 md:w-48 py-2 px-4 border border-input bg-background rounded-md focus:outline-none focus:ring-2 focus:ring-ring text-foreground">
                <a href="{{ route('admin.fee-masters.create') }}"
                    class="inline-flex items-center justify-center py-2 px-4 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 font-semibold whitespace-nowrap flex-none shrink-0">+
                    Tambah Biaya</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-muted-foreground uppercase bg-muted">
                    <tr>
                        <th class="px-6 py-3">Kategori</th>
                        <th class="px-6 py-3">Nama Biaya</th>
                        <th class="px-6 py-3">Target Unit</th>
                        <th class="px-6 py-3">Target Tempat Tinggal</th>
                        <th class="px-6 py-3 text-right">Jumlah</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($fees as $fee)
                        <tr class="hover:bg-muted/50 transition-colors">
                            <td class="px-6 py-4">
                                <span
                                    class="text-xs font-semibold px-2 py-1 rounded bg-primary/10 text-primary uppercase">
                                    {{ $fee->category->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-foreground">{{ $fee->item_name }}</td>
                            <td class="px-6 py-4 text-muted-foreground">
                                {{ $fee->unit_target ? ($fee->unit_target == '01' ? 'SMP' : ($fee->unit_target == '02' ? 'SMA' : 'PPTQ')) : 'Semua Unit' }}
                            </td>
                            <td class="px-6 py-4 text-muted-foreground">
                                {{ $fee->residence_target ? str_replace('_', ' ', $fee->residence_target) : 'Semua Tempat Tinggal' }}
                            </td>
                            <td class="px-6 py-4 text-right font-mono font-medium text-foreground">
                                Rp {{ number_format($fee->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.fee-masters.edit', $fee) }}"
                                    class="text-primary hover:text-primary/80 font-medium mr-2">Ubah</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-muted-foreground">
                                Tidak ada data biaya ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-border">
            {{ $fees->links() }}
        </div>
    </div>
</div>
