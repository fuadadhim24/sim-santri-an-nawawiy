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
                <a href="{{ route('admin.fee-masters.archive') }}"
                    class="inline-flex items-center justify-center py-2 px-4 bg-secondary text-secondary-foreground rounded-md hover:bg-secondary/80 font-semibold whitespace-nowrap flex-none shrink-0">
                    Arsip Biaya</a>
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
                        <th class="px-6 py-3">Siklus</th>
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
                                @if($fee->recurrence_type == 'MONTHLY')
                                    <span class="text-xs font-semibold px-2 py-1 rounded bg-blue-100 text-blue-800">Bulanan (Tgl {{ $fee->billing_day }})</span>
                                @elseif($fee->recurrence_type == 'YEARLY')
                                    <span class="text-xs font-semibold px-2 py-1 rounded bg-purple-100 text-purple-800">Tahunan (Tgl {{ $fee->billing_day }})</span>
                                @else
                                    <span class="text-xs font-semibold px-2 py-1 rounded bg-gray-100 text-gray-800">Sekali Bayar</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-muted-foreground">
                                {{ $fee->unit_target ? ($fee->unit_target == '01' ? 'SMP' : ($fee->unit_target == '02' ? 'SMA' : 'PPTQ')) : 'Semua Unit' }}
                            </td>
                            <td class="px-6 py-4 text-muted-foreground">
                                {{ $fee->residence_target ? str_replace('_', ' ', $fee->residence_target) : 'Semua Tempat Tinggal' }}
                            </td>
                            <td class="px-6 py-4 text-right font-mono font-medium text-foreground">
                                Rp {{ number_format($fee->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center space-x-2">
                                <button type="button" 
                                    wire:click="confirmSync({{ $fee->id }})"
                                    class="text-green-600 hover:text-green-800 font-medium" title="Sync Tagihan Susulan">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </button>
                                <a href="{{ route('admin.fee-masters.edit', $fee) }}"
                                    class="text-primary hover:text-primary/80 font-medium" title="Ubah Biaya">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <button type="button" wire:confirm="Apakah Anda yakin ingin mengarsipkan data ini?"
                                    wire:click="delete({{ $fee->id }})"
                                    class="text-destructive hover:text-destructive/80 font-medium" title="Hapus/Arsip">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-muted-foreground">
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

    @script
    <script>
        $wire.on('confirm-sync-billings', (event) => {
            const data = event[0] || event;
            
            window.Swal.fire({
                title: 'Konfirmasi Sync Tagihan',
                html: `
                    <p class="mb-4">Anda akan men-generate tagihan <b>${data.itemName}</b></p>
                    <div class="bg-blue-50 p-4 rounded-lg mb-4 text-left">
                        <span class="block text-sm text-blue-800">Ditemukan santri yang belum mendapat tagihan ini:</span>
                        <span class="block text-2xl font-bold text-blue-900 mt-1">${data.missingCount} Santri</span>
                    </div>
                    <p class="text-sm text-gray-500">Tagihan akan dibuat dengan jatuh tempo yang sesuai.</p>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Generate Tagihan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading screen sementara proses generate
                    window.Swal.fire({
                        title: 'Memproses...',
                        html: 'Mohon tunggu selagi sistem membuat tagihan.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            window.Swal.showLoading();
                        }
                    });
                    
                    $wire.dispatch('processSync', { id: data.id });
                }
            });
        });

        $wire.on('swal:success', (event) => {
            const data = event[0] || event;
            window.Swal.fire({
                icon: 'success',
                title: data.title,
                text: data.text,
                confirmButtonColor: '#3b82f6'
            });
        });

        $wire.on('swal:info', (event) => {
            const data = event[0] || event;
            window.Swal.fire({
                icon: 'info',
                title: data.title,
                text: data.text,
                confirmButtonColor: '#3b82f6'
            });
        });
    </script>
    @endscript
</div>
