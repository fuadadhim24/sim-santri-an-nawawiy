<div>
    <x-slot name="header">
        Arsip Tagihan
    </x-slot>

    <div class="bg-card rounded-lg shadow-sm border border-border">
        <div class="p-6 border-b border-border flex flex-col md:flex-row justify-between items-center gap-4">
            <h3 class="text-lg font-semibold text-card-foreground">Data Tagihan (Diarsipkan)</h3>
            <div class="flex items-center space-x-2">
                <input wire:model.live="search" type="text" placeholder="Cari santri atau judul..."
                    class="py-2 px-4 border border-input bg-background rounded-md text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-ring">
                <a href="{{ route('admin.billings') }}"
                    class="px-4 py-2 bg-secondary text-secondary-foreground rounded-md text-sm font-medium hover:bg-secondary/80">
                    Kembali ke Daftar
                </a>
            </div>
        </div>

        @if (session('message'))
            <div class="p-4 bg-green-100 text-green-700">
                {{ session('message') }}
            </div>
        @endif
        @if (session('error'))
            <div class="p-4 bg-red-100 text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-muted-foreground uppercase bg-muted">
                    <tr>
                        <th class="px-6 py-3">Tanggal Dihapus</th>
                        <th class="px-6 py-3">Santri</th>
                        <th class="px-6 py-3">Judul Tagihan</th>
                        <th class="px-6 py-3 text-right">Jumlah</th>
                        <th class="px-6 py-3 text-center">Status Asal</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($billings as $billing)
                        <tr class="hover:bg-muted/50 transition-colors">
                            <td class="px-6 py-4 text-muted-foreground">
                                {{ $billing->deleted_at ? $billing->deleted_at->format('d M Y H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 font-medium text-foreground">
                                {{ $billing->student->full_name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-foreground">{{ $billing->title }}</td>
                            <td class="px-6 py-4 text-right font-mono font-medium text-foreground">
                                Rp {{ number_format($billing->final_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="px-2 py-1 rounded-full text-xs font-medium {{ $billing->status === 'PAID' ? 'bg-primary/20 text-primary' : 'bg-destructive/20 text-destructive' }}">
                                    {{ $billing->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button type="button" wire:click="restore({{ $billing->id }})"
                                    wire:confirm="Yakin ingin memulihkan tagihan ini?"
                                    class="text-primary hover:text-primary/80 font-medium mr-2">Restore</button>
                                <button type="button" wire:click="forceDelete({{ $billing->id }})"
                                    wire:confirm="Yakin menghapus permanen? Data yang hilang tidak dapat kembali."
                                    class="text-destructive hover:text-destructive/80 font-medium">Hapus
                                    Permanen</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-muted-foreground">
                                Tidak ada data tagihan di arsip.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-border">
            {{ $billings->links() }}
        </div>
    </div>
</div>
