<div class="space-y-6">
    <!-- Back Link -->
    <div class="flex items-center justify-between">
        <a href="{{ route('wali.dashboard') }}"
            class="inline-flex items-center text-sm font-semibold text-primary hover:opacity-85 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Dasbor
        </a>
    </div>

    <!-- Header Section -->
    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
        <div class="flex items-center">
            <div class="p-3 bg-primary/10 rounded-full text-primary mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-foreground">Jadwal Penerimaan Santri Baru (SPMB)</h2>
                <p class="text-xs text-muted-foreground mt-0.5">Pilih salah satu gelombang pendaftaran aktif di bawah.</p>
            </div>
        </div>

        @if (session('error'))
            <div class="mt-4 p-3 text-sm text-red-700 bg-red-100 rounded-lg border border-red-200" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @if (session('message'))
            <div class="mt-4 p-3 text-sm text-green-700 bg-green-100 rounded-lg border border-green-200" role="alert">
                {{ session('message') }}
            </div>
        @endif
    </div>

    <!-- SPMB Schedules List -->
    @forelse ($schedules as $schedule)
        <div class="bg-card border border-border rounded-xl shadow-sm overflow-hidden">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-foreground">{{ $schedule->name }}</h3>
                        @if ($schedule->description)
                            <p class="text-xs text-muted-foreground mt-2 leading-relaxed">{{ $schedule->description }}</p>
                        @endif
                    </div>
                    <div class="flex items-center space-x-2 ml-4">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                            Pendaftaran Dibuka
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div class="flex items-center space-x-3 p-3 bg-muted rounded-lg border border-border/40">
                        <div class="p-2 bg-primary/10 rounded-full text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[10px] text-muted-foreground uppercase font-semibold">Mulai Pendaftaran</p>
                            <p class="text-sm font-medium text-foreground">{{ $schedule->registration_start->locale('id')->isoFormat('D MMMM Y HH:mm') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3 p-3 bg-muted rounded-lg border border-border/40">
                        <div class="p-2 bg-primary/10 rounded-full text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[10px] text-muted-foreground uppercase font-semibold">Selesai Pendaftaran</p>
                            <p class="text-sm font-medium text-foreground">{{ $schedule->registration_end->locale('id')->isoFormat('D MMMM Y HH:mm') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Registered Students Section -->
                @if ($schedule->registered_students->count() > 0)
                    <div class="mt-6 p-4 bg-muted border border-border rounded-lg">
                        <h4 class="text-xs font-bold text-foreground mb-3">Santri yang sudah Anda daftarkan di gelombang ini:</h4>
                        <div class="space-y-2">
                            @foreach ($schedule->registered_students as $student)
                                <div class="flex items-center justify-between p-2.5 bg-card border border-border rounded-lg shadow-2xs">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-bold">
                                            {{ strtoupper(substr($student->full_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold text-foreground">{{ $student->full_name }}</p>
                                            <p class="text-[10px] text-muted-foreground">{{ $student->nis ? 'NIS: ' . $student->nis : 'No. Pendaftaran: ' . $student->registration_number }}</p>
                                        </div>
                                    </div>
                                    <div>
                                        @if ($student->status === 'menunggu')
                                            <span class="px-2 py-0.5 text-[10px] font-bold bg-yellow-100 text-yellow-800 rounded-full">
                                                Menunggu
                                            </span>
                                        @elseif ($student->status === 'diterima')
                                            <span class="px-2 py-0.5 text-[10px] font-bold bg-green-100 text-green-800 rounded-full">
                                                Diterima
                                            </span>
                                        @elseif ($student->status === 'ditolak')
                                            <span class="px-2 py-0.5 text-[10px] font-bold bg-red-100 text-red-800 rounded-full">
                                                Ditolak
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="mt-5">
                    <button wire:click="selectSchedule({{ $schedule->id }})" wire:loading.attr="disabled"
                        class="w-full py-2 px-4 bg-primary text-primary-foreground text-center font-bold text-sm rounded-lg hover:opacity-90 transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center">
                        <svg wire:loading wire:target="selectSchedule({{ $schedule->id }})" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="selectSchedule({{ $schedule->id }})">Pilih Jadwal & Mulai Isi Formulir</span>
                        <span wire:loading wire:target="selectSchedule({{ $schedule->id }})">Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-card border border-border rounded-xl shadow-sm p-12 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-primary/10 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <h3 class="text-base font-bold text-foreground mb-1">Tidak Ada Jadwal Pendaftaran Aktif</h3>
            <p class="text-xs text-muted-foreground mb-5">Saat ini pendaftaran santri baru belum dibuka. Silakan hubungi admin.</p>
            <a href="{{ route('wali.dashboard') }}"
                class="inline-flex items-center px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:opacity-90 text-xs font-bold transition">
                Kembali ke Dasbor
            </a>
        </div>
    @endforelse

    <!-- Requirements Section -->
    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
        <h3 class="text-base font-bold text-foreground mb-3">Persyaratan Berkas Pendaftaran</h3>
        <p class="text-xs text-muted-foreground mb-4">Mohon persiapkan beberapa berkas berikut sebelum melakukan pengisian formulir:</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <div class="p-3 bg-muted rounded-lg border border-border/40 flex items-start space-x-2.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-xs font-semibold text-foreground">Kartu Keluarga (KK)</p>
                    <p class="text-[10px] text-muted-foreground">Fotokopi/scan kartu keluarga terbaru</p>
                </div>
            </div>
            <div class="p-3 bg-muted rounded-lg border border-border/40 flex items-start space-x-2.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-xs font-semibold text-foreground">Pas Foto Terbaru</p>
                    <p class="text-[10px] text-muted-foreground">Berwarna dengan latar belakang polos</p>
                </div>
            </div>
            <div class="p-3 bg-muted rounded-lg border border-border/40 flex items-start space-x-2.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-xs font-semibold text-foreground">Akta Kelahiran</p>
                    <p class="text-[10px] text-muted-foreground">Fotokopi akta kelahiran yang sah</p>
                </div>
            </div>
            <div class="p-3 bg-muted rounded-lg border border-border/40 flex items-start space-x-2.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-xs font-semibold text-foreground">Ijazah Terakhir</p>
                    <p class="text-[10px] text-muted-foreground">Fotokopi/scan ijazah pendidikan terakhir</p>
                </div>
            </div>
            <div class="p-3 bg-muted rounded-lg border border-border/40 flex items-start space-x-2.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-xs font-semibold text-foreground">Dokumen NISN <span class="text-muted-foreground font-normal">(Opsional)</span></p>
                    <p class="text-[10px] text-muted-foreground">Scan kartu/bukti NISN jika ada</p>
                </div>
            </div>
        </div>
    </div>
</div>
