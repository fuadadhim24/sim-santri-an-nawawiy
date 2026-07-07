<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi #{{ str_pad($billing->id, 6, '0', STR_PAD_LEFT) }} - {{ $billing->student->full_name }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Montserrat', 'sans-serif'],
                    },
                    colors: {
                        background: '#f8f5f0',
                        foreground: '#3e2723',
                        primary: {
                            DEFAULT: '#2e7d32',
                            foreground: '#ffffff',
                        },
                        secondary: {
                            DEFAULT: '#e8f5e9',
                            foreground: '#1b5e20',
                        },
                        muted: {
                            DEFAULT: '#f0e9e0',
                            foreground: '#6d4c41',
                        },
                        border: '#e0d6c9',
                        destructive: '#c62828',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f8f5f0;
            /* var(--background) */
            color: #3e2723;
            /* var(--foreground) */
        }

        @media print {
            body {
                background-color: white !important;
                color: black !important;
            }

            .no-print {
                display: none !important;
            }

            .print-border {
                border: 1px solid #e0d6c9 !important;
            }
        }
    </style>
</head>

<body class="p-4 md:p-10">
    <div id="receipt-card" class="max-w-4xl mx-auto bg-white shadow-lg rounded-xl overflow-hidden print-border">
        <!-- Header -->
        <div class="bg-primary p-8 text-white flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">KWITANSI PEMBAYARAN</h1>
                <p class="text-white/70 mt-1">Nomor: #{{ str_pad($billing->id, 6, '0', STR_PAD_LEFT) }}</p>
            </div>
            <div class="text-right">
                <h2 class="text-xl font-bold text-white leading-tight">PP. AN-NAWAWIY</h2>
                <p class="text-sm text-white/70">Sistem Informasi Manajemen Santri</p>
            </div>
        </div>

        <div class="p-8">
            <!-- Info Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                <div>
                    <h3 class="text-xs font-bold text-muted-foreground uppercase tracking-wider mb-2">Informasi Santri
                    </h3>
                    <p class="text-lg font-bold text-foreground">{{ $billing->student->full_name }}</p>
                    <p class="text-muted-foreground">NIS: {{ $billing->student->nis }}</p>
                    <p class="text-muted-foreground">Unit:
                        {{ $billing->student->unit_code == '01' ? 'SMP' : ($billing->student->unit_code == '02' ? 'SMA' : 'PPTQ') }}
                    </p>
                    <p class="text-muted-foreground">Kelas: {{ $billing->student->class_name ?? '-' }}</p>
                </div>
                <div class="md:text-right">
                    <h3 class="text-xs font-bold text-muted-foreground uppercase tracking-wider mb-2">Rincian Pembayaran
                    </h3>
                    <p class="text-muted-foreground">Tanggal Tagihan:
                        {{ $billing->created_at->locale('id')->isoFormat('D MMMM Y') }}</p>
                    @if ($billing->payments->isNotEmpty() && $billing->payments->first()->paid_at)
                        <p class="text-muted-foreground">Tanggal Pembayaran:
                            {{ $billing->payments->first()->paid_at->locale('id')->isoFormat('D MMMM Y H:mm') }} WIB</p>
                    @endif
                    <p class="text-muted-foreground">Metode:
                        @if ($billing->payments->isNotEmpty())
                            {{ $billing->payments->first()->payment_method }}
                        @else
                            -
                        @endif
                    </p>
                    <p class="mt-1 font-bold {{ $billing->status == 'PAID' ? 'text-primary' : 'text-destructive' }}">
                        STATUS: {{ $billing->status == 'PAID' ? 'LUNAS' : 'BELUM LUNAS' }}
                    </p>
                </div>
            </div>

            <!-- Table -->
            <div class="border border-border rounded-lg overflow-hidden mb-8">
                <table class="w-full text-left">
                    <thead class="bg-muted border-b border-border">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-muted-foreground uppercase">Deskripsi Tagihan
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-muted-foreground uppercase">Jumlah
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr>
                            <td class="px-6 py-4">
                                <p class="font-medium text-foreground">{{ $billing->title }}</p>
                            </td>
                            <td class="px-6 py-4 text-right text-foreground font-semibold">
                                Rp {{ number_format($billing->original_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Calculation -->
            <div class="flex justify-end list-none">
                <div class="w-full md:w-64 space-y-3">
                    <div class="flex justify-between text-muted-foreground">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($billing->original_amount, 0, ',', '.') }}</span>
                    </div>
                    @if ($billing->discount_applied > 0)
                        <div class="flex justify-between text-destructive">
                            <span>Diskon</span>
                            <span>- Rp {{ number_format($billing->discount_applied, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-xl font-bold text-foreground pt-3 border-t border-border">
                        <span>TOTAL</span>
                        <span>Rp {{ number_format($billing->final_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Footer Info -->
            <div class="mt-16 pt-8 border-t border-border text-center text-muted-foreground text-sm">
                <p class="font-bold text-foreground mb-1 font-sans">Terima Kasih Atas Pembayaran Anda</p>
                <p>Kwitansi ini adalah bukti pembayaran yang sah yang dihasilkan secara elektronik oleh sistem.</p>
                <p class="mt-4">Jl. Pesantren No. 123 | admin@an-nawawiy.sch.id | +62 812-3456-7890</p>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="max-w-4xl mx-auto mt-8 flex justify-center gap-4 no-print print:hidden font-medium">
        <a href="{{ route('wali.dashboard') }}"
            class="px-6 py-3 bg-white text-foreground border border-border rounded-lg hover:bg-muted transition shadow-sm flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                    clip-rule="evenodd" />
            </svg>
            Kembali ke Dasbor
        </a>
        <button onclick="downloadReceiptAsImage()"
            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-sm flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Unduh Gambar
        </button>
        <button onclick="window.print()"
            class="px-6 py-3 bg-primary text-white rounded-lg hover:opacity-90 transition shadow-sm flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z"
                    clip-rule="evenodd" />
            </svg>
            Cetak Struk Bukti
        </button>
    </div>

    <script>
        function downloadReceiptAsImage() {
            const button = event.currentTarget;
            const originalContent = button.innerHTML;
            button.disabled = true;
            button.innerHTML = `
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Memproses...
            `;

            const element = document.getElementById('receipt-card');
            html2canvas(element, {
                scale: 3, // Ultra HD Quality
                useCORS: true,
                logging: false,
                backgroundColor: '#ffffff'
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'Kwitansi_Pembayaran_{{ str_pad($billing->id, 6, "0", STR_PAD_LEFT) }}_{{ Str::slug($billing->student->full_name) }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();

                button.disabled = false;
                button.innerHTML = originalContent;
            }).catch(err => {
                console.error('Failed to generate image:', err);
                alert('Gagal mengunduh gambar kwitansi.');
                button.disabled = false;
                button.innerHTML = originalContent;
            });
        }
    </script>
</body>

</html>
