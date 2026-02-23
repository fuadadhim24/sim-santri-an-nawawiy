<div>
    <x-slot name="header">
        Ringkasan Dasbor
    </x-slot>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-16">
        <div class="bg-card rounded-lg shadow-sm border border-border p-6 flex items-center">
            <div class="p-3 rounded-full bg-primary/10 text-primary mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-muted-foreground uppercase">Total Santri</p>
                <p class="text-2xl font-bold text-foreground">{{ $totalStudents }}</p>
            </div>
        </div>

        <div class="bg-card rounded-lg shadow-sm border border-border p-6 flex items-center">
            <div class="p-3 rounded-full bg-accent text-accent-foreground mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-muted-foreground uppercase">Total Wali Santri</p>
                <p class="text-2xl font-bold text-foreground">{{ $totalGuardians }}</p>
            </div>
        </div>

        <div class="bg-card rounded-lg shadow-sm border border-border p-6 flex items-center">
            <div class="p-3 rounded-full bg-destructive/10 text-destructive mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-muted-foreground uppercase">Tagihan Belum Lunas</p>
                <p class="text-2xl font-bold text-destructive">{{ $unpaidInvoices }}</p>
            </div>
        </div>

        <div class="bg-card rounded-lg shadow-sm border border-border p-6 flex items-center">
            <div class="p-3 rounded-full bg-primary/20 text-primary mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                    </path>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-muted-foreground uppercase">Total Pemasukan</p>
                <p class="text-xl font-bold text-primary">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Monthly Income Chart -->
        <div class="bg-card rounded-lg shadow-sm border border-border p-6">
            <h3 class="text-lg font-semibold text-foreground mb-4">Grafik Pemasukan (12 Bulan Terakhir)</h3>
            <div id="incomeChart" style="min-height: 350px;"></div>
        </div>

        <div class="bg-card rounded-lg shadow-sm border border-border p-6">
            <h3 class="text-lg font-semibold text-foreground mb-4">Distribusi Status Pembayaran</h3>
            <div id="statusChart" style="min-height: 350px;"></div>
        </div>
    </div>

    <!-- Recent Payments Table -->
    <div class="bg-card rounded-lg shadow-sm border border-border overflow-hidden mb-8">
        <div class="p-6 border-b border-border flex justify-between items-center">
            <h3 class="text-lg font-semibold text-foreground">Riwayat Pembayaran Terbaru</h3>
            <a href="{{ route('admin.billings') }}" class="text-sm font-medium text-primary hover:underline">
                Lihat Semua &rarr;
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-muted text-muted-foreground uppercase text-xs font-semibold">
                    <tr>
                        <th class="px-6 py-3">Tanggal Lunas</th>
                        <th class="px-6 py-3">Santri</th>
                        <th class="px-6 py-3">Tagihan</th>
                        <th class="px-6 py-3 text-right">Jumlah</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($recentPayments as $payment)
                        <tr class="hover:bg-muted/50 transition-colors">
                            <td class="px-6 py-4 text-muted-foreground">
                                {{ $payment->updated_at->locale('id')->isoFormat('D MMMM Y HH:mm') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-foreground">{{ $payment->student->full_name }}</span>
                                <span class="block text-xs text-muted-foreground">{{ $payment->student->nis }}</span>
                            </td>
                            <td class="px-6 py-4 text-foreground">
                                {{ $payment->title }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-mono font-bold text-primary">Rp
                                    {{ number_format($payment->final_amount, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.receipts.show', $payment->id) }}" target="_blank"
                                    class="text-xs font-medium text-primary hover:underline">
                                    Kwitansi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-muted-foreground">
                                Belum ada riwayat pembayaran terbaru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            var incomeOptions = {
                series: [{
                    name: 'Pemasukan',
                    data: @json($incomeData)
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: @json($months),
                },
                yaxis: {
                    title: {
                        text: 'Rupiah (Rp)'
                    },
                    labels: {
                        formatter: function(value) {
                            return new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR',
                                maximumSignificantDigits: 3
                            }).format(value);
                        }
                    }
                },
                fill: {
                    opacity: 1,
                    colors: ['#2e7d32'] // Primary
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return "Rp " + new Intl.NumberFormat('id-ID').format(val)
                        }
                    }
                }
            };

            var incomeChart = new ApexCharts(document.querySelector("#incomeChart"), incomeOptions);
            incomeChart.render();


            // Status Chart
            var statusOptions = {
                series: [@json($paidCount), @json($unpaidCount)],
                chart: {
                    width: 380,
                    type: 'pie',
                },
                labels: ['Lunas', 'Belum Lunas'],
                colors: ['#2e7d32', '#c62828'], // Primary, Destructive
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var statusChart = new ApexCharts(document.querySelector("#statusChart"), statusOptions);
            statusChart.render();
        });
    </script>
</div>
