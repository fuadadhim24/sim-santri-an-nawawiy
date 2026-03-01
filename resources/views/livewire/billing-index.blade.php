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
            </div>
        </div>

        @if(session()->has('message'))
            <div class="p-4 bg-green-100 text-green-700 border-b border-border">
                {{ session('message') }}
            </div>
        @endif

        @if(session()->has('error'))
            <div class="p-4 bg-red-100 text-red-700 border-b border-border">
                {{ session('error') }}
            </div>
        @endif

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
                                @if ($billing->status == 'PAID')
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        LUNAS
                                    </span>
                                @elseif ($billing->status == 'UNPAID')
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                        BELUM LUNAS
                                    </span>
                                @elseif ($billing->status == 'PENDING')
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                        PENDING
                                    </span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                        {{ $billing->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('admin.students.show', $billing->student_id) }}"
                                        class="text-primary hover:text-primary/80 font-medium text-sm">Detail</a>

                                    @if ($billing->status == 'UNPAID')
                                        <button wire:click="openPaymentModal({{ $billing->id }})"
                                            class="px-2 py-1 bg-primary text-primary-foreground rounded hover:bg-primary/90 text-xs font-medium ml-2">
                                            Bayar
                                        </button>
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
                                        <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded text-xs font-medium ml-2">
                                            Read-only
                                        </span>
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

    <x-modal name="payment-modal" wire:model="showPaymentModal">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-card-foreground mb-4">Konfirmasi Pembayaran</h2>

            @if($selectedBilling)
                <div class="bg-muted rounded-lg p-4 mb-4">
                    <p class="text-sm text-muted-foreground">Santri:</p>
                    <p class="font-medium text-foreground">{{ $selectedBilling->student->full_name ?? '-' }}</p>
                    <p class="text-sm text-muted-foreground mt-2">Tagihan:</p>
                    <p class="font-medium text-foreground">{{ $selectedBilling->title }}</p>
                    <p class="text-sm text-muted-foreground mt-2">Jumlah:</p>
                    <p class="font-bold text-lg text-foreground">Rp {{ number_format($selectedBilling->final_amount, 0, ',', '.') }}</p>
                </div>

                <div class="space-y-3">
                    <p class="text-sm font-medium text-card-foreground">Pilih Metode Pembayaran:</p>
                    <div class="flex gap-3">
                        <button wire:click="processCashPayment({{ $selectedBilling->id ?? null }})"
                            class="flex-1 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                            💵 Cash
                        </button>
                        <a href="{{ route('duitku.pay', [$selectedBilling->id ?? 0, 'force' => 1]) }}"
                            class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-center">
                            💳 Duitku
                        </a>
                    </div>
                </div>
            @endif

            <div class="mt-6 flex justify-end">
                <button wire:click="$set('showPaymentModal', false)"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    Batal
                </button>
            </div>
        </div>
    </x-modal>
</div>
