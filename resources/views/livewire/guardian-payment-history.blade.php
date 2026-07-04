<div>
    <x-slot name="header">Riwayat Pembayaran</x-slot>

    <div class="space-y-6">

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-card border border-border rounded-xl p-5 shadow-sm">
                <p class="text-xs text-muted-foreground uppercase tracking-wide font-medium">Total Terbayar</p>
                <p class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
                <p class="text-xs text-muted-foreground mt-1">{{ $countPaid }} transaksi lunas</p>
            </div>
            <div class="bg-card border border-border rounded-xl p-5 shadow-sm">
                <p class="text-xs text-muted-foreground uppercase tracking-wide font-medium">Transaksi Lunas</p>
                <p class="text-2xl font-bold text-foreground mt-1">{{ $countPaid }}</p>
                <p class="text-xs text-muted-foreground mt-1">pembayaran berhasil</p>
            </div>
            <div class="bg-card border border-border rounded-xl p-5 shadow-sm">
                <p class="text-xs text-muted-foreground uppercase tracking-wide font-medium">Dibatalkan</p>
                <p class="text-2xl font-bold text-muted-foreground mt-1">{{ $countVoid }}</p>
                <p class="text-xs text-muted-foreground mt-1">tagihan void/dibatalkan</p>
            </div>
        </div>

        {{-- Filter & Table --}}
        <div class="bg-card border border-border rounded-xl shadow-sm overflow-hidden">

            {{-- Filter Bar --}}
            <div class="p-5 border-b border-border flex flex-wrap gap-3 items-center">
                <h3 class="font-semibold text-foreground text-base mr-auto">Riwayat Transaksi</h3>

                {{-- Filter santri jika lebih dari 1 --}}
                @if ($students->count() > 1)
                    <select wire:model.live="studentFilter"
                        class="py-2 pl-3 pr-8 border border-input bg-background rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-ring">
                        <option value="">Semua Santri</option>
                        @foreach ($students as $s)
                            <option value="{{ $s->id }}">{{ $s->full_name }}</option>
                        @endforeach
                    </select>
                @endif

                <select wire:model.live="statusFilter"
                    class="py-2 pl-3 pr-8 border border-input bg-background rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-ring">
                    <option value="">Semua Status</option>
                    <option value="PAID">Lunas</option>
                    <option value="VOID">Dibatalkan</option>
                </select>

                <input wire:model.live="search" type="text" placeholder="Cari tagihan..."
                    class="py-2 px-3 border border-input bg-background rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-ring min-w-[180px]">
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-muted text-muted-foreground text-xs uppercase">
                        <tr>
                            <th class="px-5 py-3 text-left">Tanggal</th>
                            @if ($students->count() > 1)
                                <th class="px-5 py-3 text-left">Santri</th>
                            @endif
                            <th class="px-5 py-3 text-left">Tagihan</th>
                            <th class="px-5 py-3 text-right">Nominal</th>
                            <th class="px-5 py-3 text-left">Metode</th>
                            <th class="px-5 py-3 text-left">Status</th>
                            <th class="px-5 py-3 text-center">Kwitansi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse ($billings as $billing)
                            @php
                                $payment = $billing->payments->firstWhere('status', 'paid');
                            @endphp
                            <tr class="hover:bg-muted/40 transition-colors">
                                <td class="px-5 py-4 text-muted-foreground whitespace-nowrap">
                                    {{ $billing->created_at->locale('id')->isoFormat('D MMM Y') }}
                                    @if ($payment?->paid_at)
                                        <span class="block text-[10px] text-green-600 mt-0.5">
                                            Bayar: {{ $payment->paid_at->locale('id')->isoFormat('D MMM Y') }}
                                        </span>
                                    @endif
                                </td>
                                @if ($students->count() > 1)
                                    <td class="px-5 py-4 font-medium text-foreground">
                                        {{ $billing->student->full_name }}
                                    </td>
                                @endif
                                <td class="px-5 py-4 text-foreground">
                                    {{ $billing->title }}
                                    @if ($billing->status === 'VOID' && $billing->archive_reason)
                                        <span class="block text-[10px] text-muted-foreground italic mt-0.5">
                                            {{ $billing->archive_reason }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right font-mono font-semibold text-foreground">
                                    Rp {{ number_format($billing->final_amount, 0, ',', '.') }}
                                    @if ($billing->discount_applied > 0)
                                        <span class="block text-xs text-green-600 line-through font-normal">
                                            Rp {{ number_format($billing->original_amount, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-muted-foreground text-xs capitalize">
                                    {{ $payment?->payment_method ?? ($payment?->method ?? '-') }}
                                </td>
                                <td class="px-5 py-4">
                                    @if ($billing->status === 'PAID')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            Lunas
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                            Dibatalkan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @if ($billing->status === 'PAID')
                                        <a href="{{ route('admin.receipts.show', $billing->id) }}" target="_blank"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium bg-primary/10 text-primary hover:bg-primary/20 rounded-lg transition">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            Unduh
                                        </a>
                                    @else
                                        <span class="text-xs text-muted-foreground">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $students->count() > 1 ? 7 : 6 }}"
                                    class="px-5 py-12 text-center text-muted-foreground">
                                    <svg class="w-10 h-10 mx-auto mb-3 text-muted-foreground/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-sm">Belum ada riwayat pembayaran.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($billings->hasPages())
                <div class="p-4 border-t border-border">
                    {{ $billings->links() }}
                </div>
            @endif
        </div>

        {{-- Back link --}}
        <div class="text-center">
            <a href="{{ route('wali.dashboard') }}"
                class="inline-flex items-center text-sm text-muted-foreground hover:text-foreground transition">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>
