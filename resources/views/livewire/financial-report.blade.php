<div>
    <x-slot name="header">
        Laporan Keuangan
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Summary Cards -->
        <div class="md:col-span-1 bg-card rounded-lg shadow-sm border border-border p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-primary/10 text-primary mr-4">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Total Pendapatan</h4>
                    <div class="mt-1 text-2xl font-bold text-foreground">
                        Rp {{ number_format($totalIncome, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="md:col-span-1 bg-card rounded-lg shadow-sm border border-border p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-secondary text-secondary-foreground mr-4">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Transaksi</h4>
                    <div class="mt-1 text-2xl font-bold text-foreground">
                        {{ $totalTransactions }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="md:col-span-2 bg-card rounded-lg shadow-sm border border-border p-6 flex flex-col justify-center">
            <h4 class="text-xs font-medium text-muted-foreground uppercase tracking-wider mb-3">Filter Rentang Tanggal
            </h4>
            <div class="flex flex-col md:flex-row gap-4">
                <div class="w-full">
                    <input wire:model.live="startDate" type="date"
                        class="w-full px-3 py-2 border border-input bg-background rounded-md text-foreground focus:ring-2 focus:ring-ring focus:border-input transition duration-150 ease-in-out text-sm">
                </div>
                <div class="flex items-center text-muted-foreground">s/d</div>
                <div class="w-full">
                    <input wire:model.live="endDate" type="date"
                        class="w-full px-3 py-2 border border-input bg-background rounded-md text-foreground focus:ring-2 focus:ring-ring focus:border-input transition duration-150 ease-in-out text-sm">
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction List -->
    <div class="bg-card rounded-lg shadow-sm border border-border">
        <div class="p-6 border-b border-border flex flex-col md:flex-row justify-between items-center gap-4">
            <h3 class="text-lg font-semibold text-card-foreground">Riwayat Pembayaran</h3>
            <div class="w-full md:w-64">
                <input wire:model.live="search" type="text" placeholder="Cari santri..."
                    class="w-full px-4 py-2 border border-input bg-background rounded-md focus:outline-none focus:ring-2 focus:ring-ring text-sm text-foreground">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-muted-foreground uppercase bg-muted">
                    <tr>
                        <th class="px-6 py-3">Tanggal Bayar</th>
                        <th class="px-6 py-3">Metode</th>
                        <th class="px-6 py-3">Santri</th>
                        <th class="px-6 py-3">Deskripsi</th>
                        <th class="px-6 py-3 text-right">Jumlah</th>
                        <th class="px-6 py-3 text-center">Kwitansi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($payments as $payment)
                        <tr class="hover:bg-muted/50">
                            <td class="px-6 py-4 text-muted-foreground whitespace-nowrap">
                                {{ $payment->paid_at ? $payment->paid_at->locale('id')->isoFormat('D MMMM Y HH:mm') : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $payment->method === 'cash' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $payment->payment_method }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-foreground">
                                {{ $payment->billing->student->full_name }}
                                <span class="block text-xs text-muted-foreground">{{ $payment->billing->student->nis }}</span>
                            </td>
                            <td class="px-6 py-4 text-foreground">
                                {{ $payment->billing->title }}
                            </td>
                            <td class="px-6 py-4 text-right font-mono font-medium text-foreground">
                                Rp {{ number_format($payment->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.receipts.show', $payment->billing_id) }}" target="_blank"
                                    class="text-xs text-primary hover:text-primary/80 underline font-medium">
                                    Lihat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-muted-foreground">
                                Tidak ada pembayaran ditemukan pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-border">
            {{ $payments->links() }}
        </div>
    </div>
</div>
