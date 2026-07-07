<div class="space-y-6">

    {{-- ═══════════════════════════════════════════════════════
         CASE A: Santri aktif (diterima)
    ═══════════════════════════════════════════════════════ --}}
    @if ($hasActiveStudents)

        {{-- Flash messages --}}
        @if (session('error'))
            <div class="p-4 text-sm text-red-700 bg-red-100 rounded-xl border border-red-200">{{ session('error') }}</div>
        @endif
        @if (session('message'))
            <div class="p-4 text-sm text-green-700 bg-green-100 rounded-xl border border-green-200">{{ session('message') }}</div>
        @endif

        {{-- ── Banner SPMB Dibuka ── --}}
        @if (count($schedulesWithStudents) > 0)
            @php
                $activeSchedulesCount = count($schedulesWithStudents);
                $activeSched = $schedulesWithStudents[0]['schedule'] ?? null;
            @endphp
            @if ($activeSched)
                <div class="border rounded-xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4" 
                    style="background-color: var(--secondary); border-color: var(--accent); color: var(--secondary-foreground);">
                    <div class="flex items-start space-x-3">
                        <div class="p-2 bg-primary/10 rounded-full text-primary mt-0.5" style="background-color: rgba(46, 125, 50, 0.1);">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-base" style="color: var(--secondary-foreground);">Pendaftaran Santri Baru Dibuka!</p>
                            <p class="text-sm opacity-90 mt-0.5">
                                @if ($activeSchedulesCount > 1)
                                    Terdapat <strong>{{ $activeSchedulesCount }} gelombang pendaftaran aktif</strong> yang sedang dibuka saat ini.
                                @else
                                    Jadwal pendaftaran <strong>{{ $activeSched->name }}</strong> sedang berlangsung hingga {{ $activeSched->registration_end->locale('id')->isoFormat('D MMMM Y') }}.
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="{{ route('wali.spmb-schedules') }}" 
                            class="inline-flex items-center px-4 py-2 bg-primary text-primary-foreground text-xs font-bold rounded-lg shadow-sm hover:opacity-90 transition-colors">
                            Daftar Calon Santri
                            <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            @endif
        @endif

        {{-- ── Banner Terlambat ── --}}
        @if ($countOverdue > 0)
            <div class="border rounded-xl p-4" style="background-color: #fcfaf7; border-color: #e6dfd5; color: #3e2723;">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-red-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="font-bold text-red-800">{{ $countOverdue }} tagihan melewati batas waktu bayar — harap segera dilunasi.</p>
                </div>
            </div>
        @endif

        {{-- ── Ringkasan saldo --}}
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-card border border-border rounded-xl p-4 shadow-sm">
                <p class="text-xs text-muted-foreground uppercase tracking-wide font-medium">Total Belum Lunas</p>
                <p class="text-2xl font-bold mt-1" style="color: #c62828;">
                    Rp {{ number_format($totalUnpaid, 0, ',', '.') }}
                </p>
                @if ($totalUnpaid == 0)
                    <p class="text-xs text-green-750 font-medium mt-1">Semua tagihan sudah lunas 🎉</p>
                @endif
            </div>
            <div class="bg-card border border-border rounded-xl p-4 shadow-sm flex flex-col justify-between">
                <p class="text-xs text-muted-foreground uppercase tracking-wide font-medium">Riwayat Pembayaran</p>
                <a href="{{ route('wali.payment-history') }}"
                    class="mt-2 inline-flex items-center text-sm font-semibold text-primary hover:opacity-80 transition">
                    Lihat semua riwayat
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        {{-- ── Daftar Tagihan Belum Lunas ── --}}
        @php
            $allUnpaidBills = $guardian->students->where('status', 'diterima')->flatMap(fn($s) => $s->billings->where('status', 'UNPAID')->map(fn($b) => ['bill' => $b, 'student' => $s]))->values();
            $multiStudent   = $guardian->students->where('status', 'diterima')->count() > 1;
        @endphp

        <div class="bg-card border border-border rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-border flex items-center justify-between bg-muted/20">
                <h3 class="font-bold text-foreground">Tagihan Yang Perlu Dibayar</h3>
                @if ($allUnpaidBills->count() > 0)
                    <span class="text-xs font-bold px-2..5 py-0.5 rounded-full bg-red-100 text-red-800" style="color: #c62828; background-color: #fbebeb;">
                        {{ $allUnpaidBills->count() }} tagihan
                    </span>
                @endif
            </div>

            @if ($allUnpaidBills->isNotEmpty())
                <ul class="divide-y divide-border">
                    @foreach ($allUnpaidBills as $item)
                        @php
                            $bill     = $item['bill'];
                            $student  = $item['student'];
                            $dueDate  = $bill->due_date ?? $bill->created_at->addDays(14);
                            $isOverdue = $dueDate->isPast();
                        @endphp
                        <li class="px-5 py-4 flex items-center gap-4 hover:bg-muted/10 transition
                            {{ $isOverdue ? 'border-l-4 border-red-500 bg-red-50/10' : '' }}">

                            {{-- Icon status --}}
                            <div class="flex-shrink-0 w-9 h-9 rounded-full flex items-center justify-center
                                {{ $isOverdue ? 'bg-red-50 text-red-700' : 'bg-green-50 text-primary' }}" style="{{ $isOverdue ? 'background-color:#fbebeb; color:#c62828;' : 'background-color:#e8f5e9; color:#2e7d32;' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="{{ $isOverdue ? 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' : 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z' }}"/>
                                </svg>
                            </div>

                            {{-- Info tagihan --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="text-sm font-bold text-foreground">{{ $bill->title }}</p>
                                    @if ($isOverdue)
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-red-100 text-red-800 uppercase tracking-wide">
                                            Terlambat
                                        </span>
                                    @endif
                                </div>
                                @if ($multiStudent)
                                    <p class="text-xs text-muted-foreground mt-0.5">{{ $student->full_name }}</p>
                                @endif
                                <p class="text-xs mt-0.5 {{ $isOverdue ? 'text-red-700' : 'text-muted-foreground' }}">
                                    Tenggat: {{ $dueDate->locale('id')->isoFormat('D MMMM Y') }}
                                </p>
                            </div>

                            {{-- Nominal + Tombol --}}
                            <div class="flex-shrink-0 text-right flex flex-col items-end gap-2">
                                <p class="text-sm font-bold text-foreground">
                                    Rp {{ number_format($bill->final_amount, 0, ',', '.') }}
                                </p>
                                <a href="{{ route('duitku.pay', [$bill->id, 'force' => 1]) }}"
                                    onclick="event.preventDefault(); Swal.fire({title:'Konfirmasi Pembayaran', text:'Apakah Anda yakin ingin membayar {{ $bill->title }} sebesar Rp {{ number_format($bill->final_amount, 0, ',', '.') }}?', icon:'question', showCancelButton:true, confirmButtonText:'Ya, Bayar', cancelButtonText:'Batal', confirmButtonColor:'{{ $isOverdue ? '#c62828' : '#2e7d32' }}'}).then((r)=>{ if(r.isConfirmed) window.location.href=this.href; })"
                                    style="background-color: {{ $isOverdue ? '#c62828' : '#2e7d32' }}; color: #ffffff;"
                                    class="inline-flex items-center px-4 py-1.5 text-xs font-bold rounded-lg transition shadow-sm hover:opacity-90">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                    Bayar
                                </a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="p-10 text-center">
                    <svg class="w-12 h-12 mx-auto mb-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="font-semibold text-green-700">Semua tagihan sudah lunas!</p>
                    <p class="text-sm text-muted-foreground mt-1">Tidak ada tagihan yang perlu dibayar saat ini.</p>
                </div>
            @endif
        </div>

        {{-- ── Info Santri (ringkas, tanpa accordion) ── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach ($guardian->students->where('status', 'diterima') as $student)
                <div class="bg-card border border-border rounded-xl p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm flex-shrink-0">
                                {{ mb_strtoupper(mb_substr($student->full_name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-foreground text-sm truncate">{{ $student->full_name }}</p>
                                <p class="text-xs text-muted-foreground">NIS: {{ $student->nis }}</p>
                            </div>
                        </div>
                        <a href="{{ route('wali.students.show', $student->id) }}"
                            class="text-xs font-semibold text-primary hover:opacity-80 transition flex-shrink-0">
                            Detail →
                        </a>
                    </div>
                    <div class="flex gap-2 flex-wrap">
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium bg-secondary text-secondary-foreground">
                            {{ ['01'=>'SMP','02'=>'SMA','03'=>'PPTQ'][$student->unit_code] ?? $student->unit_code }}
                        </span>
                        @if ($student->class_name)
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium bg-muted text-muted-foreground">
                                {{ $student->class_name }}
                            </span>
                        @endif
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $student->is_active ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground' }}">
                            {{ $student->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                        @if ($student->special_status !== 'UMUM')
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium bg-blue-100 text-blue-700">
                                @if ($student->special_status === 'ANAK_GURU')
                                    Anak Guru
                                @elseif ($student->special_status === 'YATIM')
                                    Yatim
                                @elseif ($student->special_status === 'PRESTASI')
                                    Siswa Berprestasi
                                @else
                                    {{ str_replace('_', ' ', $student->special_status) }}
                                @endif
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach

            {{-- Profil wali --}}
            <div class="bg-card border border-border rounded-xl p-4 shadow-sm {{ $guardian->students->where('status','diterima')->count() % 2 == 0 ? 'sm:col-span-2' : '' }}">
                <div class="flex items-center justify-between mb-3">
                    <p class="font-semibold text-foreground text-sm">Profil Anda</p>
                    <a href="{{ route('wali.profile.edit') }}"
                        class="text-xs font-medium text-primary hover:text-primary/80 transition">Edit →</a>
                </div>
                <div class="space-y-1 text-sm text-muted-foreground">
                    <p><span class="font-medium text-foreground">{{ $guardian->full_name }}</span></p>
                    <p>{{ $guardian->whatsapp }}</p>
                    @if ($guardian->user->email)
                        <p class="truncate">{{ $guardian->user->email }}</p>
                    @endif
                </div>
            </div>
        </div>

    {{-- ═══════════════════════════════════════════════════════
         CASE B: Santri pending / ditolak (belum diterima)
    ═══════════════════════════════════════════════════════ --}}
    @elseif ($hasPendingStudents || $hasRejectedStudents)

        @if (session('error'))
            <div class="p-4 text-sm text-red-700 bg-red-100 rounded-xl border border-red-200">{{ session('error') }}</div>
        @endif

        @php
            $activeSchedulesCount = count($schedulesWithStudents);
            $activeSched = $schedulesWithStudents[0]['schedule'] ?? null;
        @endphp

        @if ($activeSched)
            <div class="border rounded-xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4" 
                style="background-color: var(--secondary); border-color: var(--accent); color: var(--secondary-foreground);">
                <div class="flex items-start space-x-3">
                    <div class="p-2 bg-primary/10 rounded-full text-primary mt-0.5" style="background-color: rgba(46, 125, 50, 0.1);">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-base" style="color: var(--secondary-foreground);">Pendaftaran Santri Baru Dibuka!</p>
                        <p class="text-sm opacity-90 mt-0.5">
                            @if ($activeSchedulesCount > 1)
                                Terdapat <strong>{{ $activeSchedulesCount }} gelombang pendaftaran aktif</strong> yang sedang dibuka saat ini.
                            @else
                                Jadwal pendaftaran <strong>{{ $activeSched->name }}</strong> sedang berlangsung hingga {{ $activeSched->registration_end->locale('id')->isoFormat('D MMMM Y') }}.
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <a href="{{ route('wali.spmb-schedules') }}" 
                        class="inline-flex items-center px-4 py-2 bg-primary text-primary-foreground text-xs font-bold rounded-lg shadow-sm hover:opacity-90 transition-colors">
                        Daftar Calon Santri
                        <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        @endif

        <div class="bg-card border border-border rounded-xl p-6 shadow-sm">
            <h2 class="text-xl font-bold text-foreground mb-1">Selamat Datang, {{ $guardian->full_name }}</h2>
            <p class="text-muted-foreground text-sm">Status pendaftaran santri Anda:</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($guardian->students->whereIn('status', ['menunggu', 'ditolak']) as $ps)
                <div class="bg-card border rounded-xl p-5 shadow-sm {{ $ps->status == 'ditolak' ? 'border-red-200' : 'border-yellow-200' }}">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <p class="font-bold text-foreground">{{ $ps->full_name }}</p>
                            <p class="text-xs text-muted-foreground">No. Pendaftaran: {{ $ps->registration_number }}</p>
                        </div>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $ps->status == 'menunggu' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700' }}">
                            {{ $ps->status == 'menunggu' ? 'Menunggu' : 'Belum Disetujui' }}
                        </span>
                    </div>
                    @if ($ps->status == 'menunggu')
                        <p class="text-sm text-yellow-700 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            Pendaftaran sedang diproses. Silakan tunggu konfirmasi dari admin pesantren.
                        </p>
                    @else
                        <div class="text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg p-3">
                            <p class="font-semibold mb-1">Pendaftaran Belum Disetujui</p>
                            <p class="text-xs">{{ $ps->rejection_note ?? 'Persyaratan berkas belum lengkap.' }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

    {{-- ═══════════════════════════════════════════════════════
         CASE C: Belum ada santri sama sekali
    ═══════════════════════════════════════════════════════ --}}
    @else
        <div class="bg-card border border-border rounded-xl p-8 text-center shadow-sm">
            <svg class="w-10 h-10 mx-auto mb-3 text-muted-foreground/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <h2 class="text-xl font-bold text-foreground mb-1">Selamat Datang, {{ $guardian->full_name }}</h2>
            <p class="text-muted-foreground text-sm mb-5">Anda belum memiliki santri yang terdaftar.</p>

            @php
                $activeSchedulesCount = count($schedulesWithStudents);
                $activeSched = $schedulesWithStudents[0]['schedule'] ?? null;
            @endphp

            @if ($activeSched)
                <div class="border rounded-lg p-4 mb-5 text-left flex items-start space-x-3"
                    style="background-color: var(--secondary); border-color: var(--accent); color: var(--secondary-foreground);">
                    <div class="p-1.5 bg-primary/10 rounded-full text-primary flex-shrink-0 mt-0.5" style="background-color: rgba(46, 125, 50, 0.1);">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-sm" style="color: var(--secondary-foreground);">Pendaftaran Santri Baru Dibuka!</p>
                        <p class="text-xs opacity-90 mt-0.5">
                            @if ($activeSchedulesCount > 1)
                                Terdapat <strong>{{ $activeSchedulesCount }} gelombang pendaftaran aktif</strong> yang sedang dibuka saat ini.
                            @else
                                Jadwal <strong>{{ $activeSched->name }}</strong> berlangsung hingga {{ $activeSched->registration_end->locale('id')->isoFormat('D MMMM Y') }}.
                            @endif
                        </p>
                    </div>
                </div>
            @endif

            <a href="{{ route('wali.spmb-schedules') }}"
                class="inline-flex items-center px-5 py-2.5 bg-primary text-primary-foreground text-sm font-semibold rounded-lg hover:bg-primary/90 transition shadow">
                Daftarkan Santri Sekarang →
            </a>
        </div>
    @endif

</div>
