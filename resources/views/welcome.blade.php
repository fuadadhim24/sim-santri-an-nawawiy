<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Manajemen Santri - An-Nawawiy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        background: 'var(--background, #f8f5f0)',
                        foreground: 'var(--foreground, #3e2723)',
                        card: 'var(--card, #f8f5f0)',
                        'card-foreground': 'var(--card-foreground, #3e2723)',
                        primary: 'var(--primary, #2e7d32)',
                        'primary-foreground': 'var(--primary-foreground, #ffffff)',
                        secondary: 'var(--secondary, #e8f5e9)',
                        'secondary-foreground': 'var(--secondary-foreground, #1b5e20)',
                        muted: 'var(--muted, #f0e9e0)',
                        'muted-foreground': 'var(--muted-foreground, #6d4c41)',
                        destructive: 'var(--destructive, #c62828)',
                        'destructive-foreground': 'var(--destructive-foreground, #ffffff)',
                        border: 'var(--border, #e0d6c9)',
                        input: 'var(--input, #e0d6c9)',
                        chart: {
                            1: 'var(--chart-1, #4caf50)',
                            2: 'var(--chart-2, #388e3c)'
                        }
                    },
                    fontFamily: {
                        sans: ['Montserrat', 'sans-serif'],
                        serif: ['Merriweather', 'serif']
                    }
                }
            }
        }
    </script>
    <style>
        html {
            scroll-behavior: smooth;
        }

        .hero-pattern {
            background-color: var(--background);
            background-image: radial-gradient(var(--border) 1px, transparent 1px);
            background-size: 24px 24px;
        }

        .text-gradient {
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-image: linear-gradient(to right, var(--primary), var(--chart-2));
        }
    </style>
</head>

<body class="antialiased min-h-screen flex flex-col bg-background text-foreground font-sans">
    @php
        $spmbTotal =
            (\App\Models\FeeMaster::find(1)->amount ?? 150000) + (\App\Models\FeeMaster::find(2)->amount ?? 1250000);
        $sppMondok = \App\Models\FeeMaster::find(5)->amount ?? 450000;
        $sppLaju = \App\Models\FeeMaster::find(4)->amount ?? 200000;
    @endphp

    <!-- Navbar -->
    <nav class="fixed w-full z-50 bg-background/90 backdrop-blur-md border-b border-border shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex-shrink-0 flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-lg bg-primary flex items-center justify-center font-bold text-primary-foreground shadow-md">
                        AN
                    </div>
                    <span class="font-bold text-xl tracking-tight text-foreground hidden sm:block font-serif">SIM
                        An-Nawawiy</span>
                </div>
                <div>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="text-sm font-semibold text-muted-foreground hover:text-primary transition-colors">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}"
                                class="text-sm font-semibold px-5 py-2.5 rounded-md bg-primary text-primary-foreground shadow-sm hover:bg-primary/90 transition-all">Login</a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 flex-grow flex items-center hero-pattern">
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10 w-full">
            <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight mb-6 font-serif text-foreground">
                Portal Resmi <br class="hidden md:block" />
                <span class="text-gradient">Pendidikan & Tagihan</span>
            </h1>
            <p class="mt-4 max-w-2xl mx-auto text-lg md:text-xl text-muted-foreground mb-10 leading-relaxed">
                Kelola administrasi, pendaftaran, dan rincian biaya pendidikan santri Pondok Pesantren An-Nawawiy secara
                terpadu, transparan, dan mudah.
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-4 mt-8">
                <a href="#biaya"
                    class="px-8 py-4 rounded-md bg-primary text-primary-foreground font-bold text-lg shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all w-full sm:w-auto text-center border border-transparent">
                    Lihat Rincian Biaya
                </a>
                <a href="{{ route('login') }}"
                    class="px-8 py-4 rounded-md bg-card text-card-foreground font-bold text-lg shadow-sm hover:shadow-md transition-all w-full sm:w-auto text-center border border-border">
                    Masuk ke Sistem
                </a>
            </div>
        </div>
    </div>

    <!-- Pricing / Layanan Section -->
    <div id="biaya" class="relative py-24 bg-card border-t border-border z-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <h2 class="text-3xl md:text-4xl font-bold text-foreground font-serif">Struktur Biaya & Layanan</h2>
                <div class="w-24 h-1 bg-primary mx-auto mt-6 mb-4 rounded-full"></div>
                <p class="mt-4 text-muted-foreground max-w-2xl mx-auto text-lg mb-8">Silakan pelajari paket pendaftaran
                    awal
                    dan biaya pendidikan bulanan kami. Modul simulasi checkout tersedia untuk proses verifikasi
                    integrasi pembayaran Duitku.</p>

                <!-- Demo Accounts Alert -->
                <div
                    class="inline-block bg-primary/10 border border-primary/20 rounded-xl p-6 text-left max-w-2xl shadow-sm">
                    <div class="flex items-center gap-3 mb-3">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h4 class="font-bold text-foreground text-lg font-serif">Akses Akun Evaluasi (Demo)</h4>
                    </div>
                    <p class="text-sm text-muted-foreground mb-4">Untuk keperluan verifikasi dan peninjauan flow
                        pembayaran secara menyeluruh dari sisi Wali Santri maupun Dashboard Admin, gunakan kredensial
                        berikut:</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-background rounded-lg p-3 border border-border">
                            <span class="text-xs font-bold text-primary uppercase tracking-wider block mb-1">Akses Wali
                                Santri</span>
                            <div class="text-sm text-foreground"><span class="font-semibold">Email:</span> wali@test.com
                            </div>
                            <div class="text-sm text-foreground"><span class="font-semibold">Pass:</span> password</div>
                        </div>
                        <div class="bg-background rounded-lg p-3 border border-border">
                            <span class="text-xs font-bold text-primary uppercase tracking-wider block mb-1">Akses
                                Admin</span>
                            <div class="text-sm text-foreground"><span class="font-semibold">Email:</span>
                                admin@annawawiy.ac.id</div>
                            <div class="text-sm text-foreground"><span class="font-semibold">Pass:</span> password</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Package 1: Pendaftaran Baru / SPMB -->
                <div
                    class="bg-background rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-border flex flex-col relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity">
                        <svg class="w-32 h-32 text-primary" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z">
                            </path>
                        </svg>
                    </div>

                    <div class="p-8 flex-grow flex flex-col">
                        <div class="mb-4">
                            <span
                                class="px-3 py-1 text-xs font-bold uppercase tracking-wider bg-secondary text-secondary-foreground rounded-full border border-secondary">Paket
                                Unggulan</span>
                        </div>
                        <h3 class="text-2xl font-bold text-foreground mb-3 font-serif">SPMB (Pendaftaran Baru)</h3>
                        <p class="text-muted-foreground text-sm mb-6 flex-grow">Mencakup biaya pendaftaran seleksi awal
                            dan daftar ulang untuk hak asrama serta fasilitas madrasah.</p>

                        <div class="mb-8 pb-8 border-b border-border">
                            <span class="text-4xl font-extrabold text-foreground">Rp
                                {{ number_format($spmbTotal, 0, ',', '.') }}</span>
                        </div>

                        <ul class="space-y-4 text-sm text-foreground mb-8">
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-primary mt-0.5 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Biaya Pendaftaran SPMB</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-primary mt-0.5 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Biaya Daftar Ulang SPMB</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-primary mt-0.5 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Kitab Pegangan Dasar</span>
                            </li>
                        </ul>
                    </div>
                    <div class="p-8 pt-0 mt-auto">
                        <a href="{{ route('checkout.test', ['package' => 'SPMB (Pendaftaran Santri Baru)', 'price' => $spmbTotal]) }}"
                            class="w-full py-3 rounded-md bg-secondary text-secondary-foreground text-center font-bold transition-all flex justify-center items-center shadow-sm hover:brightness-110 border border-secondary/50">
                            Simulasi Checkout
                        </a>
                    </div>
                </div>

                <!-- Package 2: SPP Bulanan (Mondok) -->
                <div
                    class="bg-background rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border-2 border-primary flex flex-col relative overflow-hidden transform lg:-translate-y-4">
                    <div class="absolute top-0 inset-x-0 h-2 bg-primary"></div>

                    <div class="p-8 flex-grow flex flex-col">
                        <h3 class="text-2xl font-bold text-foreground mb-3 font-serif mt-2">SPP Bulanan (Mondok)</h3>
                        <p class="text-muted-foreground text-sm mb-6 flex-grow">Iuran komprehensif bulanan untuk
                            mendukung seluruh fasilitas asrama, konsumsi, dan operasional mengaji santri mukim.</p>

                        <div class="mb-8 pb-8 border-b border-border text-primary">
                            <span class="text-4xl font-extrabold text-primary">Rp
                                {{ number_format($sppMondok, 0, ',', '.') }}</span><span
                                class="text-muted-foreground text-lg ml-1 font-normal">/bulan</span>
                        </div>

                        <ul class="space-y-4 text-sm text-foreground mb-8">
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-primary mt-0.5 shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Fasilitas Asrama Penuh</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-primary mt-0.5 shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Makan 3x Sehari (Dapur Umum)</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-primary mt-0.5 shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Program Tahfidz & Ekstrakurikuler</span>
                            </li>
                        </ul>
                    </div>
                    <div class="p-8 pt-0 mt-auto bg-primary/5">
                        <a href="{{ route('checkout.test', ['package' => 'SPP Bulanan (Mondok)', 'price' => $sppMondok]) }}"
                            class="w-full py-3 rounded-md bg-primary hover:bg-primary/90 text-primary-foreground text-center font-bold transition-all flex justify-center items-center shadow-md border border-transparent">
                            Simulasi Checkout
                        </a>
                    </div>
                </div>

                <!-- Package 3: SPP Bulanan (Laju) -->
                <div
                    class="bg-background rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-border flex flex-col relative overflow-hidden">
                    <div class="p-8 flex-grow flex flex-col">
                        <h3 class="text-2xl font-bold text-foreground mb-3 font-serif mt-2">SPP Bulanan (Laju)</h3>
                        <p class="text-muted-foreground text-sm mb-6 flex-grow">Biaya SPP bulanan yang dirancang khusus
                            untuk santri non-mukim (laju) guna menunjang fasilitas belajar madrasah diniyah.</p>

                        <div class="mb-8 pb-8 border-b border-border">
                            <span class="text-4xl font-extrabold text-foreground">Rp
                                {{ number_format($sppLaju, 0, ',', '.') }}</span><span
                                class="text-muted-foreground text-lg ml-1 font-normal">/bulan</span>
                        </div>

                        <ul class="space-y-4 text-sm text-foreground mb-8">
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-primary mt-0.5 shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Fasilitas Ruang Kelas & Belajar</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-primary mt-0.5 shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Akses Perpustakaan & Laboratorium</span>
                            </li>
                            <li class="flex items-start gap-3 opacity-60">
                                <svg class="w-5 h-5 text-muted-foreground mt-0.5 shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <span class="text-muted-foreground">Tidak Termasuk Makan/Asrama</span>
                            </li>
                        </ul>
                    </div>
                    <div class="p-8 pt-0 mt-auto">
                        <a href="{{ route('checkout.test', ['package' => 'SPP Bulanan (Laju)', 'price' => $sppLaju]) }}"
                            class="w-full py-3 rounded-md bg-card hover:bg-muted text-card-foreground text-center font-bold transition-all flex justify-center items-center shadow-sm border border-border">
                            Simulasi Checkout
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="py-10 border-t border-border bg-background text-center flex-shrink-0 z-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-2 text-foreground">
                    <div
                        class="w-8 h-8 rounded bg-primary flex items-center justify-center font-bold text-xs text-primary-foreground">
                        AN</div>
                    <span class="font-bold font-serif">SIM An-Nawawiy</span>
                </div>
                <p class="text-sm text-muted-foreground">
                    &copy; 2026 PP An-Nawawiy. Seluruh hak cipta dilindungi. <span class="hidden sm:inline">|</span>
                    <br class="sm:hidden" /> Payment Gateway by <span
                        class="font-semibold text-primary">Duitku</span>.
                </p>
            </div>
        </div>
    </footer>
</body>

</html>
