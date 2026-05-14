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

                @if ($hasActiveStudents)
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
                @elseif ($hasPendingStudents)
                    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <p class="text-yellow-800 font-medium">Pendaftaran santri Anda sedang menunggu persetujuan admin.</p>
                    </div>
                @else
                    <div class="mt-6 bg-muted border border-border rounded-lg p-4">
                        <p class="text-primary font-medium">Anda belum memiliki data santri.</p>
                    </div>
                @endif
            </div>
        </div>

        @if ($hasActiveStudents)
            <!-- Student Accordions & Profile -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Students Accordion (Spans 2 columns on large screens) -->
                <div class="lg:col-span-2 space-y-4" x-data="{ activeStudent: null }">
                    @foreach ($guardian->students->where('status', 'diterima') as $student)
                    @php
                        $unpaidBills = $student->billings->where('status', 'UNPAID');
                        $studentTotalUnpaid = $unpaidBills->sum('final_amount');
                    @endphp
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                        <!-- Accordion Header -->
                        <button @click="activeStudent = activeStudent === {{ $student->id }} ? null : {{ $student->id }}" 
                                class="w-full text-left p-6 flex justify-between items-center focus:outline-none hover:bg-gray-50 transition border-b border-transparent"
                                :class="{'border-gray-200': activeStudent === {{ $student->id }}}">
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">{{ $student->full_name }}</h3>
                                <div class="mt-1 space-x-2">
                                    <span class="text-sm text-gray-500">NIS: {{ $student->nis }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-secondary text-secondary-foreground">
                                        {{ $student->unit_code == '01' ? 'SMP' : ($student->unit_code == '02' ? 'SMA' : 'PPTQ') }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $student->is_active ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground' }}">
                                        {{ $student->is_active ? 'AKTIF' : 'TIDAK AKTIF' }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-6">
                                <div class="text-right hidden sm:block">
                                    <p class="text-xs text-gray-500 font-medium uppercase">Total Tagihan</p>
                                    <p class="text-lg font-bold text-destructive">Rp {{ number_format($studentTotalUnpaid, 0, ',', '.') }}</p>
                                </div>
                                <div class="text-gray-400 bg-gray-100 p-2 rounded-full">
                                    <svg class="w-5 h-5 transform transition-transform duration-200" :class="{'rotate-180': activeStudent === {{ $student->id }}}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </button>

                        <!-- Accordion Body -->
                        <div x-show="activeStudent === {{ $student->id }}" style="display: none;"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform -translate-y-2"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             class="p-6 bg-gray-50/50">
                            
                            <div class="mb-6">
                                <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Tagihan Belum Lunas
                                </h4>
                                
                                @if ($unpaidBills->isNotEmpty())
                                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                                        <ul class="divide-y divide-gray-100">
                                            @foreach ($unpaidBills as $bill)
                                                <li class="p-4 flex flex-col sm:flex-row sm:justify-between sm:items-center hover:bg-gray-50 transition">
                                                    <div class="mb-3 sm:mb-0">
                                                        <p class="text-sm font-medium text-gray-900">{{ $bill->title }}</p>
                                                        <p class="text-xs text-gray-500 mt-1">
                                                            Tenggat: {{ $bill->created_at->locale('id')->isoFormat('D MMMM Y') }}
                                                        </p>
                                                    </div>
                                                    <div class="flex items-center justify-between sm:justify-end w-full sm:w-auto space-x-4">
                                                        <span class="text-sm font-bold text-destructive">
                                                            Rp {{ number_format($bill->final_amount, 0, ',', '.') }}
                                                        </span>
                                                        <a href="{{ route('duitku.pay', [$bill->id, 'force' => 1]) }}"
                                                            onclick="return confirm('Anda akan diarahkan ke halaman pembayaran otomatis Duitku. Lanjutkan?')"
                                                            class="px-4 py-2 bg-primary text-primary-foreground text-xs font-bold rounded hover:bg-primary/90 transition shadow-sm">
                                                            Bayar
                                                        </a>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <div class="bg-white p-4 rounded-lg border border-gray-200 text-center">
                                        <p class="text-sm text-gray-500 italic">Tidak ada tagihan belum lunas.</p>
                                    </div>
                                @endif
                            </div>

                            <div>
                                <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    Riwayat Terakhir (3 Transaksi)
                                </h4>
                                @php
                                    $historyBills = $student->billings->where('status', '!=', 'UNPAID')->take(3);
                                @endphp
                                @if ($historyBills->isNotEmpty())
                                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                                        <ul class="divide-y divide-gray-100">
                                            @foreach ($historyBills as $bill)
                                                <li class="p-3 flex justify-between items-center opacity-80 hover:opacity-100 transition">
                                                    <div>
                                                        <p class="text-sm text-gray-800 font-medium">{{ $bill->title }}</p>
                                                        <p class="text-xs text-gray-500 mt-0.5">{{ $bill->updated_at->locale('id')->isoFormat('D MMMM Y') }}</p>
                                                    </div>
                                                    <div class="flex items-center space-x-3">
                                                        <span class="text-xs font-medium px-2 py-1 rounded {{ $bill->status == 'PAID' ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground' }}">
                                                            {{ $bill->status == 'PAID' ? 'LUNAS' : $bill->status }}
                                                        </span>
                                                        @if ($bill->status == 'PAID')
                                                            <a href="{{ route('admin.receipts.show', $bill->id) }}"
                                                                target="_blank"
                                                                class="p-1.5 text-primary bg-primary/5 hover:bg-primary/10 rounded transition" title="Lihat Kwitansi">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <div class="bg-white p-3 rounded-lg border border-gray-200 text-center">
                                        <p class="text-xs text-gray-400 italic">Belum ada riwayat pembayaran.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Guardian Profile & Info Card (Right Column) -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg sticky top-6">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Profil Wali Santri</h3>

                            <div class="space-y-4 mt-4">
                                <div class="flex items-start space-x-3">
                                    <div class="p-2 bg-primary/10 rounded-full text-primary mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase font-semibold">Nama Lengkap</p>
                                        <p class="font-medium text-gray-900">{{ $guardian->full_name }}</p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-3">
                                    <div class="p-2 bg-primary/10 rounded-full text-primary mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase font-semibold">WhatsApp</p>
                                        <p class="font-medium text-gray-900">{{ $guardian->whatsapp }}</p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-3">
                                    <div class="p-2 bg-primary/10 rounded-full text-primary mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase font-semibold">Akun Email</p>
                                        <p class="font-medium text-gray-900 truncate max-w-[150px] sm:max-w-xs" title="{{ $guardian->user->email ?? 'Tidak Ada' }}">{{ $guardian->user->email ?? 'Tidak Ada' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            @if ($hasPendingStudents)
                <!-- Pending Students Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($guardian->students->where('status', 'menunggu') as $pendingStudent)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 border-b border-gray-200">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-800">{{ $pendingStudent->full_name }}</h3>
                                        <span class="text-sm text-gray-500">NIS: {{ $pendingStudent->nis }}</span>
                                        <span
                                            class="ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-secondary text-secondary-foreground">{{ $pendingStudent->unit_code == '01' ? 'SMP' : ($pendingStudent->unit_code == '02' ? 'SMA' : 'PPTQ') }}</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="px-2 py-1 rounded text-xs font-semibold bg-yellow-100 text-yellow-800">
                                            Menunggu Persetujuan
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <p class="text-sm text-gray-600">
                                        <strong>Status Domisili:</strong> {{ $pendingStudent->residence_status == 'MONDOK' ? 'Mondok' : ($pendingStudent->residence_status == 'NON_MONDOK' ? 'Non Mondok' : 'Ngaji Only') }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        <strong>Status Khusus:</strong> {{ $pendingStudent->special_status == 'UMUM' ? 'Umum' : ($pendingStudent->special_status == 'ANAK_GURU' ? 'Anak Guru' : 'Yatim') }}
                                    </p>
                                    @if ($pendingStudent->address)
                                        <p class="text-sm text-gray-600">
                                            <strong>Alamat:</strong> {{ $pendingStudent->address }}
                                        </p>
                                    @endif
                                </div>

                                <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <p class="text-sm text-yellow-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Pendaftaran Anda sedang diproses. Silakan tunggu konfirmasi dari admin.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Guardian Profile Card -->
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
                            </div>

                        </div>
                    </div>
                </div>
            @else
                <!-- SPMB Registration Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- SPMB Information Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center mb-4">
                            <div class="p-3 bg-primary/10 rounded-full text-primary mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">Pendaftaran Santri Baru (SPMB)</h3>
                                <p class="text-sm text-gray-500">Daftarkan putra/putri Anda sekarang</p>
                            </div>
                        </div>

                        <div class="bg-primary/5 border border-primary/20 rounded-lg p-4 mb-4">
                            <p class="text-sm text-gray-700">
                                <strong>Informasi Pendaftaran:</strong><br>
                                Saat ini Anda belum memiliki data santri. Silakan daftarkan putra/putri Anda melalui proses Penerimaan Murid Baru (SPMB).
                            </p>
                        </div>

                        <div class="space-y-3">
                            <h4 class="font-semibold text-gray-700">Biaya Pendaftaran:</h4>
                            @if ($spmbFeeMasters->isNotEmpty())
                                <ul class="space-y-2">
                                    @foreach ($spmbFeeMasters as $fee)
                                        <li class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $fee->item_name }}</p>
                                                <p class="text-xs text-gray-500">{{ $fee->description ?? 'Biaya pendaftaran' }}</p>
                                            </div>
                                            <span class="font-bold text-primary">Rp {{ number_format($fee->amount, 0, ',', '.') }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-gray-500 italic">Belum ada informasi biaya pendaftaran.</p>
                            @endif
                        </div>

                        <div class="mt-6">
                            <a href="{{ route('wali.spmb-schedules') }}" class="w-full py-3 px-4 bg-primary text-primary-foreground text-center font-bold rounded-lg hover:bg-primary/90 transition inline-block">
                                Lihat Jadwal Pendaftaran
                            </a>
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-100">
                            <h4 class="font-semibold text-gray-700 mb-3">Persyaratan Pendaftaran:</h4>
                            <ul class="space-y-2 text-sm text-gray-600">
                                <li class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Kartu Keluarga (KK)
                                </li>
                                <li class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Pas Foto Terbaru
                                </li>
                                <li class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    NISN (Operator)
                                </li>
                                <li class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Akta Kelahiran
                                </li>
                                <li class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Ijazah
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

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

                    </div>
                </div>
            </div>
            @endif
        @endif

        <!-- SPMB Schedules Section - Always Show -->
        <div class="mt-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-gray-800">Jadwal SPMB Aktif</h3>
                        <span class="text-sm text-gray-500">Informasi pendaftaran santri baru</span>
                    </div>

                    @if ($schedulesWithStudents && count($schedulesWithStudents) > 0)
                        <div class="space-y-6">
                            @foreach ($schedulesWithStudents as $scheduleData)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h4 class="font-bold text-lg text-gray-800">{{ $scheduleData['schedule']->name }}</h4>
                                            <p class="text-sm text-gray-600">
                                                Periode: {{ $scheduleData['schedule']->registration_start->locale('id')->isoFormat('D MMMM Y') }}
                                                - {{ $scheduleData['schedule']->registration_end->locale('id')->isoFormat('D MMMM Y') }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            @if ($scheduleData['schedule']->isOpen())
                                                <span class="px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-800">
                                                    Buka
                                                </span>
                                            @else
                                                <span class="px-2 py-1 rounded text-xs font-semibold bg-red-100 text-red-800">
                                                    Ditutup
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    @if ($scheduleData['schedule']->description)
                                        <p class="text-sm text-gray-600 mb-3">{{ $scheduleData['schedule']->description }}</p>
                                    @endif

                                    @if ($scheduleData['students']->count() > 0)
                                        <div class="mt-4">
                                            <h5 class="font-semibold text-gray-700 mb-2">Santri Terdaftar:</h5>
                                            <div class="space-y-2">
                                                @foreach ($scheduleData['students'] as $student)
                                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                                        <div>
                                                            <p class="font-medium text-gray-900">{{ $student->full_name }}</p>
                                                            <p class="text-xs text-gray-500">NIS: {{ $student->nis }}</p>
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            <span class="px-2 py-1 rounded text-xs font-semibold
                                                                @if ($student->status == 'menunggu')
                                                                    bg-yellow-100 text-yellow-800
                                                                @elseif ($student->status == 'diterima')
                                                                    bg-green-100 text-green-800
                                                                @else
                                                                    bg-red-100 text-red-800
                                                                @endif">
                                                                {{ $student->status == 'menunggu' ? 'Menunggu' : ($student->status == 'diterima' ? 'Diterima' : 'Ditolak') }}
                                                            </span>
                                                            <a href="{{ route('wali.students.show', $student->id) }}" class="text-xs text-primary hover:text-primary/80 underline">
                                                                Lihat Detail
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="mt-4 pt-3 border-t border-gray-100">
                                            <a href="{{ route('wali.spmb-schedules') }}" class="text-sm text-primary hover:text-primary/80 font-medium">
                                                Lihat Jadwal Lainnya →
                                            </a>
                                        </div>
                                    @else
                                        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                            <p class="text-sm text-blue-800">
                                                Belum ada santri terdaftar pada jadwal ini.
                                            </p>
                                        </div>

                                        @if ($scheduleData['schedule']->isOpen())
                                            <div class="mt-4">
                                                <a href="{{ route('wali.spmb-schedules') }}" class="inline-block py-2 px-4 bg-primary text-primary-foreground text-sm font-medium rounded-lg hover:bg-primary/90 transition">
                                                    Pilih Jadwal
                                                </a>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                            <p class="text-sm text-gray-600">Tidak ada jadwal SPMB aktif saat ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        {{-- FAQ & Informasi Section --}}
        @if($faqs->isNotEmpty())
        <div class="mt-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center mb-6">
                        <div class="p-2 bg-primary/10 rounded-full text-primary mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Informasi & FAQ</h3>
                            <p class="text-sm text-gray-500">Program, biaya, dan informasi penting lainnya</p>
                        </div>
                    </div>

                    @foreach($faqs as $category => $categoryFaqs)
                        @php
                            $categoryLabels = [
                                'program' => 'Info Program',
                                'biaya' => 'Informasi Biaya',
                                'fasilitas' => 'Fasilitas',
                                'pendaftaran' => 'Pendaftaran',
                                'umum' => 'Umum',
                            ];
                        @endphp
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
                                <span class="w-2 h-2 bg-primary rounded-full mr-2"></span>
                                {{ $categoryLabels[$category] ?? ucfirst($category) }}
                            </h4>
                            <div class="space-y-3">
                                @foreach($categoryFaqs as $faq)
                                    <details class="group border border-gray-200 rounded-lg overflow-hidden">
                                        <summary class="flex items-center justify-between p-4 cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                                            <span class="font-medium text-gray-800 text-sm">{{ $faq->title }}</span>
                                            <svg class="w-4 h-4 text-gray-500 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </summary>
                                        <div class="p-4 border-t border-gray-200">
                                            <div class="prose prose-sm max-w-none text-gray-600">
                                                {!! nl2br(e($faq->content)) !!}
                                            </div>

                                            @if($faq->image_path)
                                                <div class="mt-4">
                                                    <img src="{{ asset('storage/' . $faq->image_path) }}" alt="{{ $faq->title }}"
                                                        class="rounded-lg border border-gray-200 max-w-full h-auto max-h-64 object-contain">
                                                </div>
                                            @endif

                                            @if($faq->pdf_path)
                                                <div class="mt-4">
                                                    <p class="text-sm font-medium text-gray-700 mb-2">Dokumen Terlampir:</p>
                                                    <iframe src="{{ asset('storage/' . $faq->pdf_path) }}" 
                                                        class="w-full h-96 rounded-lg border border-gray-200"
                                                        title="{{ $faq->title }}"></iframe>
                                                    <a href="{{ asset('storage/' . $faq->pdf_path) }}" target="_blank"
                                                        class="inline-flex items-center mt-2 text-sm text-primary hover:text-primary/80 font-medium">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                        </svg>
                                                        Buka di tab baru
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </details>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

    </div>


</div>
