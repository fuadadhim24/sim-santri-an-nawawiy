<div>
    <x-slot name="header">
        Tagihan & Pembayaran
    </x-slot>

    <div class="bg-card rounded-lg shadow-sm border border-border">
        <div class="p-6 border-b border-border flex items-center justify-between gap-4 overflow-x-auto no-scrollbar">
            <h3 class="text-lg font-semibold text-card-foreground whitespace-nowrap">Daftar Tagihan</h3>
            <div class="flex items-center space-x-2">
                <select wire:model.live="statusFilter"
                    class="w-full md:w-56 py-2 px-8 pr-10 border border-input bg-background rounded-md focus:outline-none focus:ring-2 focus:ring-ring text-foreground appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_0.75rem_center] bg-no-repeat transition-all">
                    <option value="">Semua Status</option>
                    <option value="UNPAID">Belum Lunas</option>
                    <option value="PAID">Lunas</option>
                    <option value="EXPIRED">Kadaluarsa</option>
                    <option value="VOID">Dibatalkan</option>
                </select>
                <input wire:model.live="search" type="text" placeholder="Cari tagihan..."
                    class="w-full md:w-64 py-2 px-4 border border-input bg-background rounded-md focus:outline-none focus:ring-2 focus:ring-ring text-foreground">
                <a href="{{ route('admin.billings.create') }}"
                    class="inline-flex items-center justify-center py-2 px-4 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 font-semibold whitespace-nowrap flex-none shrink-0">+
                    Buat Tagihan</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-muted-foreground uppercase bg-muted">
                    <tr>
                        <th class="px-6 py-3">Tanggal Tagihan</th>
                        <th class="px-6 py-3">Santri</th>
                        <th class="px-6 py-3">Deskripsi</th>
                        <th class="px-6 py-3 text-right">Jumlah</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($billings as $billing)
                        <tr class="hover:bg-muted/50 transition-colors">
                            <td class="px-6 py-4 text-muted-foreground">
                                {{ $billing->created_at->locale('id')->isoFormat('D MMMM Y') }}</td>
                            <td class="px-6 py-4 font-medium text-foreground">
                                {{ $billing->student->full_name }}
                                <span class="text-xs text-muted-foreground block">{{ $billing->student->nis }}</span>
                            </td>
                            <td class="px-6 py-4 text-foreground">{{ $billing->title }}</td>
                            <td class="px-6 py-4 text-right font-mono font-medium text-foreground">
                                Rp {{ number_format($billing->final_amount, 0, ',', '.') }}
                                @if ($billing->discount_applied > 0)
                                    <span class="block text-xs text-green-600 line-through">
                                        Rp {{ number_format($billing->original_amount, 0, ',', '.') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 rounded-full text-xs font-medium
                                    {{ $billing->status == 'PAID' ? 'bg-green-100 text-green-700' : ($billing->status == 'PENDING' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                    {{ $billing->status == 'PAID' ? 'LUNAS' : ($billing->status == 'UNPAID' ? 'BELUM LUNAS' : $billing->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('admin.students.show', $billing->student_id) }}"
                                        class="text-primary hover:text-primary/80 font-medium text-sm">Detail</a>

                                    @if ($billing->status == 'UNPAID')
                                        <a href="{{ route('duitku.pay', [$billing->id, 'force' => 1]) }}"
                                            class="px-2 py-1 bg-primary text-primary-foreground rounded hover:bg-primary/90 text-xs font-medium ml-2">
                                            Bayar
                                        </a>
                                    @endif

                                    @if ($billing->status == 'PAID')
                                        <a href="{{ route('admin.receipts.show', $billing->id) }}" target="_blank"
                                            class="px-2 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-xs font-medium flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                                </path>
                                            </svg>
                                            Kwitansi
                                        </a>
                                        @if (Auth::user()->role === 'SUPER_ADMIN')
                                            <button wire:click="cancelPayment({{ $billing->id }})"
                                                wire:confirm="Anda yakin ingin MEMBATALKAN pembayaran ini? Status akan kembali menjadi BELUM LUNAS."
                                                class="px-2 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200 text-xs font-medium ml-2">
                                                Batal
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-muted-foreground">
                                Tidak ada tagihan ditemukan.
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
