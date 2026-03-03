<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- Header Section -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-primary/10 rounded-full text-primary mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Jadwal Pendaftaran Santri Baru (SPMB)</h2>
                        <p class="text-sm text-gray-500">Pilih jadwal pendaftaran yang tersedia</p>
                    </div>
                </div>

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
            </div>
        </div>

        <!-- Back to Dashboard Link -->
        <div class="mb-4">
            <a href="{{ route('wali.dashboard') }}"
                class="inline-flex items-center text-primary hover:text-primary/80 font-medium text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Dasbor
            </a>
        </div>

        <!-- SPMB Schedules List -->
        @forelse ($schedules as $schedule)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-800">{{ $schedule->name }}</h3>
                            @if ($schedule->description)
                                <p class="text-sm text-gray-600 mt-2">{{ $schedule->description }}</p>
                            @endif
                        </div>
                        <div class="flex items-center space-x-2 ml-4">
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                Dibuka
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                            <div class="p-2 bg-primary/10 rounded-full text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-semibold">Mulai Pendaftaran</p>
                                <p class="font-medium text-gray-900">{{ $schedule->registration_start->locale('id')->isoFormat('D MMMM Y HH:mm') }}</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                            <div class="p-2 bg-primary/10 rounded-full text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-semibold">Selesai Pendaftaran</p>
                                <p class="font-medium text-gray-900">{{ $schedule->registration_end->locale('id')->isoFormat('D MMMM Y HH:mm') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Registered Students Section -->
                    @if ($schedule->registered_students->count() > 0)
                        <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <h4 class="text-sm font-semibold text-blue-800 mb-3">Santri yang sudah terdaftar</h4>
                            <div class="space-y-2">
                                @foreach ($schedule->registered_students as $student)
                                    <div class="flex items-center justify-between p-2 bg-white rounded border border-blue-100">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <span class="text-xs font-medium text-blue-800">{{ strtoupper(substr($student->full_name, 0, 1)) }}</span>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $student->full_name }}</p>
                                                <p class="text-xs text-gray-500">NIS: {{ $student->nis }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            @if ($student->status === 'menunggu')
                                                <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                                    Menunggu
                                                </span>
                                            @elseif ($student->status === 'diterima')
                                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                                    Diterima
                                                </span>
                                            @elseif ($student->status === 'ditolak')
                                                <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                                    Ditolak
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mt-6">
                        <button wire:click="selectSchedule({{ $schedule->id }})"
                            class="w-full py-3 px-4 bg-primary text-primary-foreground text-center font-bold rounded-lg hover:bg-primary/90 transition">
                            Pilih Jadwal Ini
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-12 text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-primary/10 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Jadwal Tersedia</h3>
                    <p class="text-gray-500 mb-6">Saat ini belum ada jadwal pendaftaran yang aktif. Silakan cek kembali nanti.</p>
                    <a href="{{ route('wali.dashboard') }}"
                        class="inline-flex items-center px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 text-sm font-medium">
                        Kembali ke Dasbor
                    </a>
                </div>
            </div>
        @endforelse

        <!-- Requirements Section -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Persyaratan Pendaftaran</h3>
                <p class="text-sm text-gray-600 mb-4">Pastikan Anda telah menyiapkan dokumen-dokumen berikut sebelum mendaftar:</p>

                <ul class="space-y-3 text-sm text-gray-700">
                    <li class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary mr-2 mt-0.5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium">Akta Kelahiran</p>
                            <p class="text-xs text-gray-500">Fotokopi akta kelahiran yang sah dan masih berlaku</p>
                        </div>
                    </li>
                    <li class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary mr-2 mt-0.5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium">Kartu Keluarga</p>
                            <p class="text-xs text-gray-500">Fotokopi kartu keluarga yang terbaru</p>
                        </div>
                    </li>
                    <li class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary mr-2 mt-0.5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium">Pas Foto Terbaru</p>
                            <p class="text-xs text-gray-500">Pas foto berwarna ukuran 3x4 (2 lembar)</p>
                        </div>
                    </li>
                    <li class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary mr-2 mt-0.5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium">Ijazah Terakhir</p>
                            <p class="text-xs text-gray-500">Fotokopi ijazah atau SKL yang dilegalisir</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</div>
