<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- Welcome & Total Unpaid -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800">
                    Selamat Datang, {{ $guardian->full_name }}
                </h2>
                <p class="text-gray-600 mt-1">Berikut adalah ringkasan data santri Anda.</p>

                @if (session('error'))
                    <div class="mt-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('message'))
                    <div class="mt-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                        {{ session('message') }}
                    </div>
                @endif

                @if ($totalUnpaid > 0)
                    <div
                        class="mt-6 bg-destructive/10 border border-destructive/20 rounded-lg p-4 flex items-center justify-between">
                        <div>
                            <p class="text-destructive font-medium">Total Tagihan Belum Lunas</p>
                            <h3 class="text-3xl font-bold text-destructive">Rp
                                {{ number_format($totalUnpaid, 0, ',', '.') }}
                            </h3>
                        </div>
                    </div>
                @else
                    <div class="mt-6 bg-muted border border-border rounded-lg p-4">
                        <p class="text-primary font-medium">Semua tagihan sudah lunas! Terima kasih.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Student Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach ($guardian->students as $student)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">{{ $student->full_name }}</h3>
                                <span class="text-sm text-gray-500">NIS: {{ $student->nis }}</span>
                                <span
                                    class="ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-secondary text-secondary-foreground">{{ $student->unit_code == '01' ? 'SMP' : ($student->unit_code == '02' ? 'SMA' : 'PPTQ') }}</span>
                            </div>
                            <div class="text-right">
                                <span
                                    class="px-2 py-1 rounded text-xs font-semibold {{ $student->is_active ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground' }}">
                                    {{ $student->is_active ? 'AKTIF' : 'TIDAK AKTIF' }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h4 class="font-semibold text-gray-700 mb-2">Tagihan Belum Lunas</h4>
                            @php
                                $unpaidBills = $student->billings->where('status', 'UNPAID');
                            @endphp

                            @if ($unpaidBills->isNotEmpty())
                                <ul class="divide-y divide-gray-100">
                                    @foreach ($unpaidBills as $bill)
                                        <li class="py-3 flex justify-between items-center">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $bill->title }}</p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $bill->created_at->locale('id')->isoFormat('D MMMM Y') }}</p>
                                            </div>
                                            <div class="flex items-center space-x-3">
                                                <span class="text-sm font-bold text-destructive">Rp
                                                    {{ number_format($bill->final_amount, 0, ',', '.') }}</span>
                                                <a href="{{ route('duitku.pay', [$bill->id, 'force' => 1]) }}"
                                                    class="px-3 py-1 bg-primary text-primary-foreground text-xs rounded hover:bg-primary/90 transition inline-block">
                                                    Bayar
                                                </a>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-gray-500 italic">Tidak ada tagihan belum lunas.</p>
                            @endif
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-100">
                            <h4 class="font-semibold text-gray-700 mb-2">Riwayat Terakhir</h4>
                            @php
                                $historyBills = $student->billings->where('status', '!=', 'UNPAID')->take(3);
                            @endphp
                            @if ($historyBills->isNotEmpty())
                                <ul class="divide-y divide-gray-100">
                                    @foreach ($historyBills as $bill)
                                        <li class="py-2 flex justify-between items-center opacity-75">
                                            <div>
                                                <p class="text-sm text-gray-800">{{ $bill->title }}</p>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span
                                                    class="text-xs font-medium px-2 py-0.5 rounded {{ $bill->status == 'PAID' ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground' }}">
                                                    {{ $bill->status == 'PAID' ? 'LUNAS' : ($bill->status == 'UNPAID' ? 'BELUM LUNAS' : $bill->status) }}
                                                </span>
                                                @if ($bill->status == 'PAID')
                                                    <a href="{{ route('admin.receipts.show', $bill->id) }}"
                                                        target="_blank"
                                                        class="text-xs text-primary hover:text-primary/80 underline">
                                                        Kwitansi
                                                    </a>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-xs text-gray-400 italic">Belum ada riwayat.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Guardian Profile & Info Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Profil Wali</h3>

                    <div class="space-y-2">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-primary/10 rounded-full text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-semibold">Nama Lengkap</p>
                                <p class="font-medium text-gray-900">{{ $guardian->full_name }}</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-primary/10 rounded-full text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-semibold">WhatsApp</p>
                                <p class="font-medium text-gray-900">{{ $guardian->whatsapp }}</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-primary/10 rounded-full text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-semibold">Akun Email</p>
                                <p class="font-medium text-gray-900">{{ $guardian->user->email ?? 'Tidak Ada' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100">
                        <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-primary" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Informasi Sekolah
                        </h4>
                        <div class="bg-muted/50 rounded-lg p-4 text-sm text-muted-foreground space-y-2">
                            <p><strong>Kantor Admin:</strong> +62 812-3456-7890</p>
                            <p><strong>Email:</strong> admin@an-nawawiy.sch.id</p>
                            <p><strong>Alamat:</strong> Jl. Pesantren No. 123</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
