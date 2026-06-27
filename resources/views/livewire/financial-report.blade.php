<div>
    <x-slot name="header">
        Laporan Keuangan
    </x-slot>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Cash Income Card -->
        <div class="bg-card rounded-lg shadow-sm border border-border p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-800 mr-4">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Pendapatan Tunai</h4>
                    <div class="mt-1 text-2xl font-bold text-foreground">
                        Rp {{ number_format($cashIncome, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Cashless Income Card -->
        <div class="bg-card rounded-lg shadow-sm border border-border p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-800 mr-4">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Pendapatan Cashless</h4>
                    <div class="mt-1 text-2xl font-bold text-foreground">
                        Rp {{ number_format($cashlessIncome, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Combined Income Card -->
        <div class="bg-card rounded-lg shadow-sm border border-border p-6">
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

        <!-- Total Transactions Card -->
        <div class="bg-card rounded-lg shadow-sm border border-border p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-secondary text-secondary-foreground mr-4">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Transaksi Sukses</h4>
                    <div class="mt-1 text-2xl font-bold text-foreground">
                        {{ $totalTransactions }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction List -->
    <div class="bg-card rounded-lg shadow-sm border border-border">
        <div class="p-6 border-b border-border flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h3 class="text-lg font-semibold text-card-foreground">Riwayat Pembayaran</h3>
                <p class="text-xs text-muted-foreground mt-0.5">Daftar transaksi pembayaran santri yang telah diverifikasi.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <!-- Date Filters -->
                <div class="flex items-center gap-2 bg-muted/50 border border-border px-3 py-1.5 rounded-md text-xs select-none">
                    <span class="text-muted-foreground font-medium">Periode:</span>
                    <input wire:model.live="startDate" type="date"
                        class="bg-transparent border-none p-0 text-foreground focus:ring-0 text-xs w-28 focus:outline-none">
                    <span class="text-muted-foreground">-</span>
                    <input wire:model.live="endDate" type="date"
                        class="bg-transparent border-none p-0 text-foreground focus:ring-0 text-xs w-28 focus:outline-none">
                </div>
                
                <!-- Search Input -->
                <input wire:model.live="search" type="text" placeholder="Cari santri..."
                    class="px-3 py-1.5 border border-input bg-background rounded-md focus:outline-none focus:ring-2 focus:ring-ring text-xs text-foreground w-40 sm:w-48">
                
                <!-- Export Actions Group -->
                <div class="flex items-center gap-2">
                    <!-- Print PDF Link -->
                    <a href="{{ route('admin.reports.financial.print', ['startDate' => $startDate, 'endDate' => $endDate, 'search' => $search]) }}" 
                        target="_blank"
                        class="inline-flex items-center justify-center px-3 py-1.5 bg-primary text-primary-foreground text-xs font-semibold rounded-md hover:bg-primary/90 transition shadow-sm gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak PDF
                    </a>
                    
                    <!-- Export Excel Button -->
                    <button wire:click="exportExcel"
                        class="inline-flex items-center justify-center px-3 py-1.5 bg-emerald-600/10 text-emerald-600 border border-emerald-600/20 hover:bg-emerald-600/20 text-xs font-semibold rounded-md transition shadow-sm gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Ekspor Excel
                    </button>
                </div>
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
