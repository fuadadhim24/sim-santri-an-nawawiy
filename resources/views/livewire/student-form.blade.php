<div>
    <x-slot name="header">
        {{ $isEdit ? 'Edit Santri' : 'Tambah Santri Baru' }}
    </x-slot>

    <div class="bg-card rounded-lg shadow-sm border border-border max-w-4xl mx-auto">
        <div class="p-6 border-b border-border">
            <h3 class="text-lg font-semibold text-card-foreground">Formulir Data Santri</h3>
            <p class="text-sm text-muted-foreground mt-1">Lengkapi informasi di bawah ini untuk membuat atau memperbarui profil santri.</p>
        </div>

        <div class="p-6">
            @if (session()->has('error'))
                <div class="p-3 mb-6 text-xs text-red-800 bg-red-100 border border-red-200 rounded-lg shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <form wire:submit.prevent="save" class="space-y-6">
                <!-- Guardian Search -->
                <div>
                    <label for="guardian_id" class="block text-sm font-medium text-foreground">Wali Santri <span class="text-red-500">*</span></label>
                    <select wire:model="guardian_id" id="guardian_id"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        <option value="">Pilih Wali Santri...</option>
                        @foreach ($this->guardians as $g)
                            <option value="{{ $g->id }}">{{ $g->full_name }} (WA: {{ $g->whatsapp }})</option>
                        @endforeach
                    </select>
                    @error('guardian_id')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Full Name & NISN -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-foreground">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input wire:model="full_name" type="text" id="full_name" placeholder="Nama Lengkap Santri"
                            class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        @error('full_name')
                            <span class="text-destructive text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="nisn" class="block text-sm font-medium text-foreground">NISN <span class="text-muted-foreground font-normal text-[11px]">(10 Digit - Opsional)</span></label>
                        <input wire:model="nisn" type="text" id="nisn" placeholder="Contoh: 0123456789"
                            class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        @error('nisn')
                            <span class="text-destructive text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Address -->
                <div>
                    <label for="address" class="block text-sm font-medium text-foreground">Alamat <span class="text-muted-foreground font-normal text-[11px]">(Opsional)</span></label>
                    <textarea wire:model="address" id="address" rows="3"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm"></textarea>
                    @error('address')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Document Upload Section -->
                <div class="border-t border-border pt-6">
                    <h4 class="text-sm font-semibold text-foreground mb-4">Unggah Berkas Fisik Dokumen</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- KK File -->
                        <div class="p-4 border border-dashed border-border rounded-lg bg-muted/40 flex flex-col justify-between min-h-[120px]">
                            <div>
                                <div class="flex justify-between items-start gap-2">
                                    <label class="block text-xs font-bold text-muted-foreground uppercase tracking-wider">Kartu Keluarga (PDF/Gambar - Max 2MB)</label>
                                    @if ($isEdit && $student->kk)
                                        <span class="inline-flex flex-shrink-0 items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            ✓ Sudah Ada
                                        </span>
                                    @endif
                                </div>
                                <input type="file" wire:model="kk_file" class="mt-2 text-xs w-full">
                                @error('kk_file') <span class="text-destructive text-xs block mt-1">{{ $message }}</span> @enderror
                            </div>
                            @if ($isEdit && $student->kk)
                                <div class="mt-3 flex items-center justify-between text-xs border-t border-border/60 pt-2">
                                    <span class="text-[10px] text-muted-foreground italic">Kosongkan jika tak ingin diubah</span>
                                    <a href="{{ Storage::url($student->kk) }}" target="_blank" class="inline-flex items-center gap-1 font-bold text-primary hover:underline">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Lihat KK saat ini
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Foto File -->
                        <div class="p-4 border border-dashed border-border rounded-lg bg-muted/40 flex flex-col justify-between min-h-[120px]">
                            <div>
                                <div class="flex justify-between items-start gap-2">
                                    <label class="block text-xs font-bold text-muted-foreground uppercase tracking-wider">Foto Santri (Gambar - Max 2MB)</label>
                                    @if ($isEdit && $student->foto)
                                        <span class="inline-flex flex-shrink-0 items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            ✓ Sudah Ada
                                        </span>
                                    @endif
                                </div>
                                <input type="file" wire:model="foto_file" class="mt-2 text-xs w-full">
                                @error('foto_file') <span class="text-destructive text-xs block mt-1">{{ $message }}</span> @enderror
                            </div>
                            @if ($isEdit && $student->foto)
                                <div class="mt-3 flex items-center justify-between text-xs border-t border-border/60 pt-2">
                                    <span class="text-[10px] text-muted-foreground italic">Kosongkan jika tak ingin diubah</span>
                                    <a href="{{ Storage::url($student->foto) }}" target="_blank" class="inline-flex items-center gap-1 font-bold text-primary hover:underline">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Lihat Foto saat ini
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Akta Lahir -->
                        <div class="p-4 border border-dashed border-border rounded-lg bg-muted/40 flex flex-col justify-between min-h-[120px]">
                            <div>
                                <div class="flex justify-between items-start gap-2">
                                    <label class="block text-xs font-bold text-muted-foreground uppercase tracking-wider">Akta Kelahiran (PDF/Gambar - Max 2MB)</label>
                                    @if ($isEdit && $student->akta)
                                        <span class="inline-flex flex-shrink-0 items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            ✓ Sudah Ada
                                        </span>
                                    @endif
                                </div>
                                <input type="file" wire:model="akta_file" class="mt-2 text-xs w-full">
                                @error('akta_file') <span class="text-destructive text-xs block mt-1">{{ $message }}</span> @enderror
                            </div>
                            @if ($isEdit && $student->akta)
                                <div class="mt-3 flex items-center justify-between text-xs border-t border-border/60 pt-2">
                                    <span class="text-[10px] text-muted-foreground italic">Kosongkan jika tak ingin diubah</span>
                                    <a href="{{ Storage::url($student->akta) }}" target="_blank" class="inline-flex items-center gap-1 font-bold text-primary hover:underline">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Lihat Akta saat ini
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Ijazah -->
                        <div class="p-4 border border-dashed border-border rounded-lg bg-muted/40 flex flex-col justify-between min-h-[120px]">
                            <div>
                                <div class="flex justify-between items-start gap-2">
                                    <label class="block text-xs font-bold text-muted-foreground uppercase tracking-wider">Ijazah Terakhir (PDF/Gambar - Max 2MB - Opsional)</label>
                                    @if ($isEdit && $student->ijazah)
                                        <span class="inline-flex flex-shrink-0 items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            ✓ Sudah Ada
                                        </span>
                                    @endif
                                </div>
                                <input type="file" wire:model="ijazah_file" class="mt-2 text-xs w-full">
                                @error('ijazah_file') <span class="text-destructive text-xs block mt-1">{{ $message }}</span> @enderror
                            </div>
                            @if ($isEdit && $student->ijazah)
                                <div class="mt-3 flex items-center justify-between text-xs border-t border-border/60 pt-2">
                                    <span class="text-[10px] text-muted-foreground italic">Kosongkan jika tak ingin diubah</span>
                                    <a href="{{ Storage::url($student->ijazah) }}" target="_blank" class="inline-flex items-center gap-1 font-bold text-primary hover:underline">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Lihat Ijazah saat ini
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- ACADEMIC PROFILE & BILLING SECTION (SEPARATED CARD - PLACED BELOW DOCUMENTS) -->
                <div class="p-6 rounded-lg border {{ $isEdit && $isAcademicLocked ? 'bg-secondary/40 border-dashed border-muted-foreground/30 opacity-85' : 'bg-card border-border shadow-sm' }} transition-all pt-6 mt-8">
                    <!-- Card Header / Controller -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 border-b border-border pb-4">
                        <div>
                            <h4 class="text-sm font-bold text-foreground flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                Profil Akademik & Tagihan Keuangan
                            </h4>
                            <p class="text-xs text-muted-foreground mt-1">
                                @if ($isEdit && $isAcademicLocked)
                                    Profil akademik terkunci karena setiap perubahan (Unit, Domisili, Status, Kelas) berdampak langsung pada tagihan santri (menghapus tagihan lama & menerbitkan tagihan baru otomatis).
                                @else
                                    Perubahan terdeteksi. Kebijakan tagihan lama diset ke: <span class="font-bold text-amber-700 uppercase">{{ $oldUnpaidPolicy === 'keep_all' ? 'Jangan Ubah' : ($oldUnpaidPolicy === 'delete_all' ? 'Hapus Semua' : ($oldUnpaidPolicy === 'delete_except_current_month' ? 'Hapus Kecuali Bulan Ini' : 'Hapus Pilihan')) }}</span>. Harap tinjau preview before-after di bawah.
                                @endif
                            </p>
                        </div>
                        
                        @if ($isEdit)
                            <div>
                                @if ($isAcademicLocked)
                                    <button type="button" wire:click="triggerUnlock" wire:loading.attr="disabled" wire:target="triggerUnlock" class="inline-flex items-center justify-center px-4 py-2 bg-primary text-primary-foreground text-xs font-bold rounded-md shadow hover:bg-primary/90 transition duration-150">
                                        <svg wire:loading wire:target="triggerUnlock" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <svg wire:loading.remove wire:target="triggerUnlock" class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                                        </svg>
                                        <span wire:loading.remove wire:target="triggerUnlock">Buka Kunci</span>
                                        <span wire:loading wire:target="triggerUnlock">Memproses...</span>
                                    </button>
                                @else
                                    <button type="button" wire:click="triggerUnlock" wire:loading.attr="disabled" wire:target="triggerUnlock" class="inline-flex items-center justify-center px-4 py-2 border border-amber-600 bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-950/40 text-xs font-bold rounded-md shadow-sm transition duration-150">
                                        <svg wire:loading wire:target="triggerUnlock" class="animate-spin -ml-1 mr-2 h-4 w-4 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <svg wire:loading.remove wire:target="triggerUnlock" class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        <span wire:loading.remove wire:target="triggerUnlock">Ubah Kebijakan Tagihan</span>
                                        <span wire:loading wire:target="triggerUnlock">Memproses...</span>
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Academic Inputs Grid with Absolute Overlay -->
                    <div class="relative p-6 bg-muted/25 dark:bg-muted/10 rounded-lg border border-border/80">
                        @if ($isEdit && $isAcademicLocked)
                            <div class="absolute inset-0 backdrop-blur-[1.5px] rounded-lg z-10 flex items-center justify-center cursor-not-allowed" style="background-color: rgba(15, 23, 42, 0.45);">
                                <div wire:loading.remove wire:target="confirmUnlock" class="bg-card border border-border shadow-lg rounded-md px-4 py-2.5 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-amber-500 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    <span class="text-xs font-bold text-foreground">Klik "Buka Kunci Profil" di atas untuk mengubah data akademik</span>
                                </div>
                                <div wire:loading wire:target="confirmUnlock" class="bg-card border border-border shadow-lg rounded-md px-4 py-2.5 flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span class="text-xs font-bold text-foreground animate-pulse">Sedang membuka kunci & memproses tagihan...</span>
                                </div>
                            </div>
                        @endif

                        <!-- Academic Inputs Grid -->
                        <div class="space-y-6">
                            <!-- Unit & Residence Status -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="unit_code" class="block text-sm font-medium text-foreground">Unit Sekolah <span class="text-red-500">*</span></label>
                                    <select wire:model.live="unit_code" id="unit_code" @disabled($isEdit && $isAcademicLocked)
                                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm disabled:bg-muted disabled:opacity-60 disabled:cursor-not-allowed">
                                        <option value="01">SMP (01)</option>
                                        <option value="02">SMA (02)</option>
                                        <option value="03">PPTQ (03)</option>
                                    </select>
                                    @error('unit_code')
                                        <span class="text-destructive text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label for="residence_status" class="block text-sm font-medium text-foreground">Status Domisili <span class="text-red-500">*</span></label>
                                    <select wire:model.live="residence_status" id="residence_status" @disabled($isEdit && $isAcademicLocked)
                                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm disabled:bg-muted disabled:opacity-60 disabled:cursor-not-allowed">
                                        <option value="MONDOK">Mondok</option>
                                        <option value="NON_MONDOK">Non Mondok</option>
                                        <option value="NGAJI_ONLY">Ngaji Only</option>
                                    </select>
                                    @error('residence_status')
                                        <span class="text-destructive text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Special Status & Class Level -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="special_status" class="block text-sm font-medium text-foreground">Status Khusus <span class="text-red-500">*</span></label>
                                    <select wire:model.live="special_status" id="special_status" @disabled($isEdit && $isAcademicLocked)
                                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm disabled:bg-muted disabled:opacity-60 disabled:cursor-not-allowed">
                                        <option value="UMUM">Umum</option>
                                        <option value="ANAK_GURU">Anak Guru</option>
                                        <option value="YATIM">Yatim</option>
                                    </select>
                                    @error('special_status')
                                        <span class="text-destructive text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label for="class_level_id" class="block text-sm font-medium text-foreground">Tingkat Kelas <span class="text-red-500">*</span></label>
                                    <select wire:model.live="class_level_id" id="class_level_id" @disabled($isEdit && $isAcademicLocked)
                                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm disabled:bg-muted disabled:opacity-60 disabled:cursor-not-allowed">
                                        <option value="">Pilih Tingkat Kelas...</option>
                                        @foreach ($this->classLevels as $level)
                                            <option value="{{ $level->id }}">{{ $level->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('class_level_id')
                                        <span class="text-destructive text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- LOADING INDICATOR FOR TRANSITION PREVIEW -->
                <div wire:loading wire:target="unit_code, residence_status, special_status, class_level_id" class="mt-8">
                    <div class="bg-amber-50/30 border border-amber-200 border-dashed rounded-lg p-8 flex flex-col items-center justify-center space-y-3">
                        <svg class="animate-spin h-8 w-8 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-xs font-bold text-amber-800 animate-pulse">Menghitung Ulang Transisi Tagihan...</span>
                        <p class="text-[10px] text-muted-foreground">Menganalisis perbedaan tagihan lama dan tagihan baru yang cocok untuk profil baru santri.</p>
                    </div>
                </div>

                <!-- WARNING PANEL FOR INCOMPLETE ACADEMIC FIELDS -->
                @if ($isEdit && !$isAcademicLocked)
                    @php
                        $incompleteFields = $this->incompleteAcademicFields;
                    @endphp
                    @if (!empty($incompleteFields))
                        <div wire:loading.remove wire:target="unit_code, residence_status, special_status, class_level_id" class="mt-8 p-6 bg-amber-50/50 border border-amber-200 rounded-lg flex items-start gap-3.5">
                            <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <div>
                                <h4 class="text-xs font-bold text-amber-800 uppercase tracking-wider">Lengkapi Profil Akademik</h4>
                                <p class="text-xs text-amber-700 mt-1">Sistem menyembunyikan preview transisi tagihan sementara karena terdapat data yang belum dipilih atau masih default. Harap lengkapi field berikut:</p>
                                <ul class="list-disc list-inside text-xs text-amber-700 mt-2.5 space-y-1.5 font-bold">
                                    @foreach ($incompleteFields as $field)
                                        <li>{{ $field }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                @endif

                <!-- BEFORE-AFTER BILLING TRANSITION PREVIEW (EDIT MODE ONLY) -->
                @if ($isEdit && $showTransitionModal)
                    <div wire:loading.remove wire:target="unit_code, residence_status, special_status, class_level_id" class="mt-8 bg-card border border-amber-200 rounded-lg shadow-sm overflow-hidden">
                        <div class="bg-amber-50 px-6 py-4 border-b border-amber-200 flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-600 flex-shrink-0 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            <div>
                                <h3 class="text-sm font-bold text-amber-800">Preview Transisi Tagihan (Sebelum vs Sesudah)</h3>
                                <p class="text-[11px] text-amber-700">Berikut adalah rincian sisa tagihan lama yang akan dihapus/dipertahankan serta tagihan baru yang akan dibuat.</p>
                            </div>
                        </div>

                        <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6 bg-amber-50/10">
                            <!-- Left: Tagihan Sebelum (Before) -->
                            <div class="space-y-4 lg:border-r lg:border-dashed lg:border-amber-200 lg:pr-6">
                                <h4 class="text-xs font-bold text-foreground uppercase tracking-wider flex items-center justify-between">
                                    <span>Tagihan Sebelum (Lama)</span>
                                    <span class="text-[10px] bg-amber-100 text-amber-800 px-2 py-0.5 rounded font-bold">Kebijakan: {{ $oldUnpaidPolicy === 'keep_all' ? 'Jangan Ubah' : ($oldUnpaidPolicy === 'delete_all' ? 'Hapus Semua' : ($oldUnpaidPolicy === 'delete_except_current_month' ? 'Hapus Kecuali Bulan Ini' : 'Hapus Pilihan')) }}</span>
                                </h4>

                                @if (empty($oldBillings))
                                    <p class="text-xs text-muted-foreground bg-muted/50 p-3 rounded">Tidak ada tagihan lama.</p>
                                @else
                                    <div class="space-y-2 max-h-[300px] overflow-y-auto">
                                        @foreach ($oldBillings as $billing)
                                            @php
                                                $isDeleted = $this->isBillingDeleted($billing['id'], $billing['due_date'], $billing['created_at']);
                                            @endphp
                                            <div class="p-3 border rounded-lg bg-card {{ $isDeleted ? 'border-red-200 bg-red-50/10 opacity-75' : 'border-border' }} transition-all flex items-center justify-between">
                                                <div>
                                                    <div class="flex items-center gap-1 flex-wrap">
                                                        <span class="text-xs font-semibold {{ $isDeleted ? 'line-through text-red-500 font-medium' : 'text-foreground' }}">
                                                            {{ $billing['title'] }}
                                                        </span>
                                                        @if (isset($billing['fee_master']['category']['name']))
                                                            <span class="text-[10px] text-muted-foreground font-normal">({{ $billing['fee_master']['category']['name'] }})</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-[10px] text-muted-foreground mt-0.5 flex items-center gap-1.5">
                                                        <span>Due: {{ \Carbon\Carbon::parse($billing['due_date'])->isoFormat('DD MMMM Y') }}</span>
                                                        @if ($billing['payment_reference'])
                                                            <span class="text-red-500 font-semibold text-[10px]">(VA Aktif)</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <span class="font-mono text-xs font-bold {{ $isDeleted ? 'line-through text-red-400' : 'text-foreground' }}">
                                                        Rp {{ number_format($billing['final_amount'], 0, ',', '.') }}
                                                    </span>
                                                    <div class="text-[9px] mt-0.5 font-bold uppercase {{ $isDeleted ? 'text-red-600' : 'text-emerald-600' }}">
                                                        {{ $isDeleted ? 'Hapus' : 'Pertahankan' }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <!-- Right: Tagihan Sesudah (After) -->
                            <div class="space-y-4">
                                <h4 class="text-xs font-bold text-foreground uppercase tracking-wider flex items-center justify-between">
                                    <span>Tagihan Sesudah (Baru)</span>
                                    <span class="text-[10px] bg-green-100 text-green-800 px-2 py-0.5 rounded font-bold">Tersedia: {{ count($availableNewBillings) }}</span>
                                </h4>

                                @if (empty($availableNewBillings))
                                    <p class="text-xs text-muted-foreground bg-muted/50 p-3 rounded">Tidak ada tagihan baru untuk profil target.</p>
                                @else
                                    <div class="space-y-3 max-h-[300px] overflow-y-auto">
                                        @foreach ($availableNewBillings as $billing)
                                            <div x-data="{ expanded: false }" class="border rounded-lg hover:border-primary transition-all overflow-hidden bg-card"
                                                 :class="$wire.newCategoriesToGenerate.map(String).includes('{{ $billing['id'] }}') ? 'border-primary ring-1 ring-primary shadow-sm' : 'border-border'">
                                                
                                                <!-- Accordion Header -->
                                                <div class="flex items-start p-3 bg-background transition-colors">
                                                    <div class="pt-0.5">
                                                        <input type="checkbox" wire:model.live="newCategoriesToGenerate" value="{{ $billing['id'] }}" class="rounded border-input text-primary focus:ring-primary transition-all cursor-pointer">
                                                    </div>
                                                    <div class="ml-3 flex-1 cursor-pointer select-none" @click="expanded = !expanded">
                                                        <div class="flex justify-between items-start">
                                                            <div class="font-semibold text-xs text-foreground leading-tight">{{ $billing['name'] }}</div>
                                                            <button type="button" class="text-muted-foreground p-0.5 hover:bg-muted rounded-md transition-colors" :class="{ 'rotate-180': expanded }">
                                                                <svg class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                            </button>
                                                        </div>
                                                        <div class="text-[10px] font-mono font-semibold text-muted-foreground mt-1">
                                                            Total: Rp {{ number_format($billing['total_amount'], 0, ',', '.') }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Accordion Content (Fees List) -->
                                                <div class="bg-muted p-3 border-t border-border" x-show="expanded" x-transition style="display: none;">
                                                    <p class="text-[9px] font-bold text-muted-foreground uppercase tracking-wider mb-2">Rincian Komponen:</p>
                                                    <ul class="space-y-2">
                                                        @foreach ($billing['fees'] as $fee)
                                                            <li class="flex flex-col py-1 first:pt-0 last:pb-0 border-b last:border-0 border-dashed border-border/50">
                                                                <div class="flex justify-between text-[11px] text-muted-foreground">
                                                                    <span>• {{ $fee['item_name'] }}</span>
                                                                    <span class="font-mono text-foreground font-semibold">Rp {{ number_format($fee['amount'], 0, ',', '.') }}</span>
                                                                </div>
                                                                <div class="flex flex-row flex-wrap items-center gap-1.5 mt-1">
                                                                    @if ($fee['unit'])
                                                                        <span class="inline-flex px-2.5 py-0.5 rounded bg-blue-50 dark:bg-blue-950/20 text-blue-700 dark:text-blue-400 font-semibold border border-blue-200/50" style="font-size: 8px; line-height: 1.2;">
                                                                            UNIT: {{ $fee['unit'] == '01' ? 'SMP' : ($fee['unit'] == '02' ? 'SMA' : ($fee['unit'] == '03' ? 'PPTQ' : $fee['unit'])) }}
                                                                        </span>
                                                                    @else
                                                                        <span class="inline-flex px-2.5 py-0.5 rounded bg-muted text-muted-foreground font-semibold border border-border" style="font-size: 8px; line-height: 1.2;">
                                                                            UNIT: SEMUA
                                                                        </span>
                                                                    @endif
                                                                    
                                                                    @if ($fee['domicile'])
                                                                        <span class="inline-flex px-2.5 py-0.5 rounded bg-purple-50 dark:bg-purple-950/20 text-purple-700 dark:text-purple-400 font-semibold border border-purple-200/50" style="font-size: 8px; line-height: 1.2;">
                                                                            DOM: {{ $fee['domicile'] }}
                                                                        </span>
                                                                    @else
                                                                        <span class="inline-flex px-2.5 py-0.5 rounded bg-muted text-muted-foreground font-semibold border border-border" style="font-size: 8px; line-height: 1.2;">
                                                                            DOM: SEMUA
                                                                        </span>
                                                                    @endif

                                                                    @if (isset($fee['class_level_target_name']))
                                                                        <span class="inline-flex px-2.5 py-0.5 rounded bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 font-semibold border border-amber-200/50" style="font-size: 8px; line-height: 1.2;">
                                                                            KELAS: {{ $fee['class_level_target_name'] }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- BILLINGS PREVIEW FOR CREATE MODE -->
                @if (!$isEdit)
                    <div class="border-t border-border pt-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-semibold text-foreground">Pembuatan Tagihan Otomatis (Santri Baru)</h4>
                                <p class="text-xs text-muted-foreground mt-0.5">Biaya pendaftaran dan SPP bulanan awal yang dicentang akan otomatis digenerate.</p>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" wire:model.live="autoGenerateBillings" id="autoGenerate" class="rounded border-input text-primary focus:ring-primary h-4 w-4 mr-2">
                                <label for="autoGenerate" class="text-xs font-medium text-foreground">Aktifkan Autogenerate</label>
                            </div>
                        </div>

                        @if ($autoGenerateBillings && !empty($availableBillings))
                            <div class="space-y-3">
                                <div class="flex justify-between items-center bg-muted px-4 py-2 rounded-lg border">
                                    <button type="button" wire:click="toggleSelectAllFees" class="text-xs font-semibold text-primary hover:underline">
                                        {{ count($selectedBillings) === count($availableBillings) ? 'Deselect All' : 'Select All' }}
                                    </button>
                                    <span class="text-xs text-muted-foreground font-semibold">
                                        Terpilih: {{ count($selectedBillings) }}/{{ count($availableBillings) }}
                                    </span>
                                </div>

                                <div class="space-y-3 overflow-y-auto pr-2 border border-border rounded-lg p-2 bg-background shadow-inner" style="max-height: 300px;">
                                    @foreach ($availableBillings as $billing)
                                        <div x-data="{ expanded: false }" class="border rounded-lg hover:border-primary transition-all overflow-hidden bg-card"
                                             :class="$wire.selectedBillings.map(String).includes('{{ $billing['id'] }}') ? 'border-primary ring-1 ring-primary shadow-sm' : 'border-border'">
                                            
                                            <div class="flex items-start p-3 bg-background transition-colors">
                                                <div class="pt-0.5">
                                                    <input type="checkbox" wire:model.live="selectedBillings" value="{{ $billing['id'] }}" class="rounded border-input text-primary focus:ring-primary transition-all cursor-pointer">
                                                </div>
                                                <div class="ml-3 flex-1 cursor-pointer select-none" @click="expanded = !expanded">
                                                    <div class="flex justify-between items-start">
                                                        <div class="font-semibold text-sm text-foreground leading-tight">{{ $billing['name'] }}</div>
                                                        <button type="button" class="text-muted-foreground p-1 hover:bg-muted rounded-md transition-colors" :class="{ 'rotate-180': expanded }">
                                                            <svg class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                        </button>
                                                    </div>
                                                    <div class="text-xs font-medium text-foreground mt-1 font-mono">
                                                        Total: Rp {{ number_format($billing['total_amount'], 0, ',', '.') }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="bg-muted p-3 border-t border-border" x-show="expanded" x-transition style="display: none;">
                                                <p class="text-[10px] font-bold text-muted-foreground uppercase tracking-wider mb-2">Rincian Komponen:</p>
                                                <ul class="space-y-2">
                                                    @foreach ($billing['fees'] as $fee)
                                                        <li class="flex justify-between text-xs py-1.5">
                                                            <div class="flex items-start space-x-2">
                                                                <div class="w-1.5 h-1.5 rounded-full bg-primary mt-1.5"></div>
                                                                <div class="flex flex-col">
                                                                    <span class="text-foreground font-medium">{{ $fee['item_name'] }}</span>
                                                                    <div class="flex flex-row flex-wrap items-center gap-1.5 mt-1">
                                                                        @if ($fee['unit'])
                                                                            <span class="inline-flex px-2 py-0.5 rounded bg-blue-100 text-blue-700 font-medium border border-blue-200" style="font-size: 8px; line-height: 1.2;">
                                                                                UNIT: {{ $fee['unit'] == '01' ? 'SMP' : ($fee['unit'] == '02' ? 'SMA' : ($fee['unit'] == '03' ? 'PPTQ' : $fee['unit'])) }}
                                                                            </span>
                                                                        @else
                                                                            <span class="inline-flex px-2 py-0.5 rounded bg-muted text-muted-foreground font-medium border border-border" style="font-size: 8px; line-height: 1.2;">
                                                                                UNIT: SEMUA
                                                                            </span>
                                                                        @endif
                                                                        
                                                                        @if ($fee['domicile'])
                                                                            <span class="inline-flex px-2 py-0.5 rounded bg-purple-100 text-purple-700 font-medium border border-purple-200" style="font-size: 8px; line-height: 1.2;">
                                                                                DOM: {{ $fee['domicile'] }}
                                                                            </span>
                                                                        @else
                                                                            <span class="inline-flex px-2 py-0.5 rounded bg-muted text-muted-foreground font-medium border border-border" style="font-size: 8px; line-height: 1.2;">
                                                                                DOM: SEMUA
                                                                            </span>
                                                                        @endif

                                                                        @if (isset($fee['class_level_target_name']))
                                                                            <span class="inline-flex px-2 py-0.5 rounded bg-amber-100 text-amber-700 font-medium border border-amber-200" style="font-size: 8px; line-height: 1.2;">
                                                                                KELAS: {{ $fee['class_level_target_name'] }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <span class="font-mono text-muted-foreground mt-0.5">Rp {{ number_format($fee['amount'], 0, ',', '.') }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Form Submit Actions -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-border">
                    <a href="{{ route('admin.students') }}"
                        class="px-4 py-2 border border-input rounded-md shadow-sm text-sm font-medium text-foreground bg-background hover:bg-muted focus:outline-none">
                        Batal
                    </a>
                    <button type="submit" wire:loading.attr="disabled" wire:target="save"
                        class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="save">{{ $isEdit ? 'Simpan Perubahan' : 'Buat Santri' }}</span>
                        <span wire:loading wire:target="save">Memproses...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- SWEETALERT POPUP SCRIPT FOR TRANSITION POLICY -->
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('swal:choose-unlock-policy', (event) => {
                const oldBillings = event[0].oldBillings || [];
                const currentMonthYear = new Date().toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
                const hasOldBillings = oldBillings.length > 0;

                // Build custom HTML card layout for policies
                let htmlContent = `
                    <div class="text-left space-y-3 mt-4">
                        <!-- Keep All -->
                        <label class="block p-3 border border-slate-200 rounded-lg hover:border-blue-500 hover:bg-blue-50/5 cursor-pointer select-none transition-all">
                            <div class="flex items-start gap-3">
                                <input type="radio" name="swal-policy" value="keep_all" checked class="mt-1 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                <div>
                                    <span class="text-sm font-semibold text-slate-800 block">Jangan Ubah Tagihan Lama (Keep All)</span>
                                    <p class="text-[11px] text-slate-500 mt-1 leading-normal">Semua tagihan lama tetap dibiarkan aktif tanpa ada yang diarsipkan atau dihapus.</p>
                                </div>
                            </div>
                        </label>

                        <!-- Delete All -->
                        <label class="block p-3 border border-slate-200 rounded-lg hover:border-red-500 hover:bg-red-50/5 cursor-pointer select-none transition-all">
                            <div class="flex items-start gap-3">
                                <input type="radio" name="swal-policy" value="delete_all" class="mt-1 text-red-600 focus:ring-red-500 cursor-pointer">
                                <div>
                                    <span class="text-sm font-semibold text-slate-800 block text-red-700">Hapus Semua Tagihan Lama (Delete All)</span>
                                    <p class="text-[11px] text-slate-500 mt-1 leading-normal">Semua tagihan kelas/profil lama santri yang belum dibayar akan langsung diarsipkan.</p>
                                </div>
                            </div>
                        </label>

                        <!-- Delete Except Current Month -->
                        <label class="block p-3 border border-slate-200 rounded-lg hover:border-indigo-500 hover:bg-indigo-50/5 cursor-pointer select-none transition-all">
                            <div class="flex items-start gap-3">
                                <input type="radio" name="swal-policy" value="delete_except_current_month" class="mt-1 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                <div>
                                    <span class="text-sm font-semibold text-slate-800 block text-indigo-700">Hapus Kecuali Bulan Ini</span>
                                    <p class="text-[11px] text-slate-500 mt-1 leading-normal">Hapus semua sisa tagihan, kecuali tagihan bulan berjalan (<strong>${currentMonthYear}</strong>).</p>
                                </div>
                            </div>
                        </label>

                        <!-- Delete Selected -->
                        ${hasOldBillings ? `
                        <label class="block p-3 border border-slate-200 rounded-lg hover:border-amber-500 hover:bg-amber-50/5 cursor-pointer select-none transition-all">
                            <div class="flex items-start gap-3">
                                <input type="radio" name="swal-policy" value="delete_selected" class="mt-1 text-amber-600 focus:ring-amber-500 cursor-pointer">
                                <div>
                                    <span class="text-sm font-semibold text-slate-800 block text-amber-700">Pilih Manual Tagihan yang Dihapus</span>
                                    <p class="text-[11px] text-slate-500 mt-1 leading-normal">Tentukan secara spesifik daftar tagihan lama santri mana saja yang akan dihapus.</p>
                                </div>
                            </div>
                        </label>
                        ` : ''}
                    </div>
                `;

                Swal.fire({
                    title: 'Kebijakan Transisi Tagihan Lama',
                    html: htmlContent,
                    showCancelButton: true,
                    confirmButtonText: 'Lanjutkan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    preConfirm: () => {
                        const checkedRadio = document.querySelector('input[name="swal-policy"]:checked');
                        if (!checkedRadio) {
                            Swal.showValidationMessage('Anda harus memilih salah satu kebijakan!');
                            return false;
                        }
                        return checkedRadio.value;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const policy = result.value;
                        
                        if (policy === 'delete_selected') {
                            let listContent = '<div class="text-left space-y-2 mt-4 max-h-[300px] overflow-y-auto p-1 bg-slate-50 rounded-lg border border-slate-200 shadow-inner">';
                            oldBillings.forEach(billing => {
                                const formattedAmount = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(billing.final_amount);
                                const vaBadge = billing.payment_reference ? ' <span class="text-red-500 font-semibold text-[10px] ml-1.5 whitespace-nowrap">(VA Aktif)</span>' : '';
                                const categoryName = billing.fee_master && billing.fee_master.category ? billing.fee_master.category.name : 'Umum/Lainnya';
                                listContent += `
                                    <label class="block p-3 border border-slate-200 rounded-lg hover:border-blue-500 hover:bg-blue-50/5 cursor-pointer select-none bg-white transition-all mb-2">
                                        <div class="flex items-start gap-3">
                                            <input type="checkbox" class="swal-billing-checkbox mt-1 text-blue-600 focus:ring-blue-500 rounded border-slate-300 cursor-pointer" value="${billing.id}" id="chk_${billing.id}">
                                            <div class="flex-1">
                                                <div class="flex justify-between items-start gap-2">
                                                    <div class="flex items-center gap-1 flex-wrap">
                                                        <span class="text-xs font-bold text-slate-800">${billing.title}</span>
                                                        <span class="text-slate-400 text-[10px] font-normal">(${categoryName})</span>
                                                    </div>
                                                    <span class="font-mono text-xs font-bold text-slate-700 whitespace-nowrap">${formattedAmount}</span>
                                                </div>
                                                <div class="text-[10px] text-slate-500 mt-1 flex items-center justify-between">
                                                    <span>Batas: ${new Date(billing.due_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}</span>
                                                    ${vaBadge}
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                `;
                            });
                            listContent += '</div>';

                            Swal.fire({
                                title: 'Pilih Tagihan yang Dihapus',
                                text: 'Beri tanda centang pada tagihan lama santri yang ingin dihapus/diarsipkan:',
                                html: listContent,
                                showCancelButton: true,
                                confirmButtonText: 'Buka Kunci Profil',
                                cancelButtonText: 'Batal',
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                preConfirm: () => {
                                    const checkedBoxes = document.querySelectorAll('.swal-billing-checkbox:checked');
                                    return Array.from(checkedBoxes).map(cb => cb.value);
                                }
                            }).then((selectResult) => {
                                if (selectResult.isConfirmed) {
                                    @this.set('oldUnpaidPolicy', 'delete_selected');
                                    @this.set('oldBillingsToDelete', selectResult.value);
                                    @this.call('confirmUnlock');
                                }
                            });
                        } else {
                            @this.set('oldUnpaidPolicy', policy);
                            @this.set('oldBillingsToDelete', []);
                            @this.call('confirmUnlock');
                        }
                    }
                });
            });
            Livewire.on('swal:validation-error', (event) => {
                const errors = event[0].errors || [];
                let errorHtml = '<ul class="text-left list-disc pl-5 text-sm space-y-1">';
                errors.forEach(err => {
                    errorHtml += `<li>${err}</li>`;
                });
                errorHtml += '</ul>';

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan Data',
                    html: errorHtml,
                    confirmButtonText: 'Perbaiki',
                    confirmButtonColor: '#3085d6'
                });
            });
        });
    </script>
</div>
