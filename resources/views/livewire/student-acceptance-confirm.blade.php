<div>
    <x-slot name="header">
        Konfirmasi Penerimaan Santri
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6" x-data='{ 
        isEditing: false,
        verified: [],
        totalRequired: 5,
        toggleAll() {
            if (this.verified.length === this.totalRequired) {
                this.verified = [];
            } else {
                this.verified = ["kk", "akta", "ijazah", "nisn_document", "foto"];
            }
        },
        toggleVerify(doc) {
            if (this.verified.includes(doc)) {
                this.verified = this.verified.filter(item => item !== doc);
            } else {
                this.verified.push(doc);
            }
        }
    }'>
        <!-- Header Actions (Back & Submit) -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <a href="{{ route('admin.student-acceptance') }}" class="text-primary hover:text-primary/80 text-sm font-medium inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Daftar Penerimaan
            </a>

            <div class="flex items-center gap-3">
                <button type="button"
                        onclick="confirmRejectionConfirmPage()"
                        wire:loading.attr="disabled" wire:target="rejectAcceptance"
                        class="inline-flex justify-center items-center px-4 bg-red-50 text-red-600 border border-red-200 rounded-lg text-sm font-bold hover:bg-red-600 hover:text-white transition-all shadow-sm"
                        style="height: 38px;">
                    <span wire:loading.remove wire:target="rejectAcceptance">Tolak</span>
                    <span wire:loading wire:target="rejectAcceptance">Proses...</span>
                </button>
                <button type="button" 
                        @click="if(verified.length !== totalRequired) { Swal.fire({icon: 'warning', title: 'Validasi Belum Lengkap', text: 'Harap selesaikan proses validasi (centang) semua berkas fisik terlebih dahulu sebelum menerima santri.'}) } else { $wire.confirmAcceptance() }"
                        :class="{'opacity-50 cursor-not-allowed': verified.length !== totalRequired}"
                        wire:loading.attr="disabled" wire:target="confirmAcceptance, rejectAcceptance"
                        class="inline-flex justify-center items-center px-6 bg-primary text-primary-foreground text-sm rounded-lg font-bold hover:bg-primary/90 transition-all shadow-sm border border-transparent"
                        style="height: 38px;">
                    <svg wire:loading wire:target="confirmAcceptance" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="confirmAcceptance">Terima Santri</span>
                    <span wire:loading wire:target="confirmAcceptance">Proses...</span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">
            <!-- Kolom 1: Data Santri & Koreksi (Kiri) -->
            <div class="flex flex-col space-y-6">
                <div class="bg-card rounded-xl shadow-sm border border-border overflow-hidden flex flex-col h-full max-h-[600px]">
                    <div class="border-b border-border bg-background px-5 py-4">
                        <div class="flex items-center justify-between w-full">
                            <h2 class="text-base font-semibold text-card-foreground flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Data Santri
                            </h2>
                            <button @click="isEditing = !isEditing" type="button" class="text-[10px] font-bold px-2 py-1.5 rounded-md bg-background border border-border shadow-sm text-muted-foreground flex items-center gap-1 hover:bg-muted transition-colors">
                                <svg x-show="!isEditing" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                <svg x-show="isEditing" style="display: none;" class="w-3 h-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                                <span x-text="isEditing ? 'Tutup Edit' : 'Buka & Edit'"></span>
                            </button>
                        </div>
                    </div>
                    <div class="p-5 flex-1 overflow-y-auto" x-data="{
                            full_name: @entangle('full_name'),
                            nis: @entangle('nis'),
                            nisn: @entangle('nisn'),
                            unit_code: @entangle('unit_code'),
                            residence_status: @entangle('residence_status'),
                            spmb_schedule_id: @entangle('spmb_schedule_id'),
                            class_level_id: @entangle('class_level_id')
                        }">
                        
                        @if (session()->has('success_field'))
                            <div class="p-3 mb-4 text-xs text-green-800 bg-green-100 border border-green-200 rounded-lg flex items-center shadow-sm">
                                <svg class="w-4 h-4 mr-2 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>{{ session('success_field') }}</span>
                            </div>
                        @endif

                        <!-- View Mode (Locked) -->
                        <div x-show="!isEditing" class="space-y-4">
                            <div>
                                <label class="text-[10px] text-muted-foreground font-semibold uppercase tracking-wider">Nama Lengkap</label>
                                <p class="font-semibold text-foreground text-sm border-b border-dashed border-border pb-1" x-text="full_name || '-'"></p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-[10px] text-muted-foreground font-semibold uppercase tracking-wider">NIS</label>
                                    <p class="font-mono text-foreground text-sm border-b border-dashed border-border pb-1" x-text="nis || '-'"></p>
                                </div>
                                <div>
                                    <label class="text-[10px] text-muted-foreground font-semibold uppercase tracking-wider">NISN</label>
                                    <p class="font-mono text-foreground text-sm border-b border-dashed border-border pb-1" x-text="nisn || '-'"></p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-[10px] text-muted-foreground font-semibold uppercase tracking-wider">Unit</label>
                                    <p class="font-medium text-foreground text-sm border-b border-dashed border-border pb-1">
                                        <span x-text="unit_code === '01' ? 'SMP' : (unit_code === '02' ? 'SMA' : 'PPTQ')"></span>
                                    </p>
                                </div>
                                <div>
                                    <label class="text-[10px] text-muted-foreground font-semibold uppercase tracking-wider">Domisili</label>
                                    <p class="font-medium text-foreground text-sm border-b border-dashed border-border pb-1">
                                        <span x-text="residence_status === 'MONDOK' ? 'Mondok' : (residence_status === 'NON_MONDOK' ? 'Non-Mondok' : 'Ngaji Only')"></span>
                                    </p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-[10px] text-muted-foreground font-semibold uppercase tracking-wider">Tingkat Kelas</label>
                                    <p class="font-medium text-foreground text-sm border-b border-dashed border-border pb-1">
                                        {{ $classLevels->firstWhere('id', $class_level_id)?->name ?? ($student->classLevel?->name ?? '-') }}
                                    </p>
                                </div>
                                <div>
                                    <label class="text-[10px] text-muted-foreground font-semibold uppercase tracking-wider">Jadwal SPMB</label>
                                    <p class="font-medium text-foreground text-sm border-b border-dashed border-border pb-1">
                                        {{ $student->spmbSchedule ? $student->spmbSchedule->name : '-' }}
                                    </p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-[10px] text-muted-foreground font-semibold uppercase tracking-wider">No. Pendaftaran</label>
                                    <p class="font-medium text-foreground text-sm border-b border-dashed border-border pb-1">
                                        {{ $student->registration_number ?? '-' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="text-[10px] text-muted-foreground font-semibold uppercase tracking-wider">Wali Santri</label>
                                    <p class="font-medium text-foreground text-sm border-b border-dashed border-border pb-1">
                                        {{ $student->guardian?->full_name ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Mode (Unlocked) -->
                        <div x-show="isEditing" style="display: none;" class="space-y-4">
                            <div class="p-2 mb-2 bg-primary/10 border border-primary/20 rounded-md">
                                <p class="text-[10px] text-primary font-medium flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Silakan edit data sesuai dengan dokumen fisik.
                                </p>
                            </div>
                            <div>
                                <label class="text-xs text-muted-foreground font-semibold uppercase tracking-wider">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="full_name" class="mt-1.5 block w-full rounded-lg border-input bg-background text-sm text-foreground shadow-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                                @error('full_name') <span class="text-xs text-destructive mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                 <label class="text-xs text-muted-foreground font-semibold uppercase tracking-wider">NIS <span class="text-red-500">*</span></label>
                                 <div class="mt-1.5 flex rounded-lg shadow-sm">
                                     <input type="text" wire:model="nis" class="flex-1 block w-full rounded-none rounded-l-lg border-input bg-background text-sm font-mono text-foreground shadow-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" placeholder="Contoh: 2627.01.0001">
                                     <button type="button" wire:click="checkNis" class="inline-flex items-center px-4 rounded-r-lg border border-l-0 border-input bg-muted hover:bg-secondary text-sm font-semibold transition-colors focus:outline-none">
                                         Cek NIS
                                     </button>
                                 </div>
                                 @if ($nisCheckStatus === 'available')
                                     <p class="mt-1 text-xs text-emerald-600 font-semibold flex items-center gap-1">
                                         <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                         {{ $nisCheckMessage }}
                                     </p>
                                 @elseif ($nisCheckStatus === 'taken' || $nisCheckStatus === 'empty')
                                     <p class="mt-1 text-xs text-destructive font-semibold flex items-center gap-1">
                                         <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                         {{ $nisCheckMessage }}
                                     </p>
                                 @endif
                                 @error('nis') <span class="text-xs text-destructive mt-1 block">{{ $message }}</span> @enderror
                             </div>

                             <div>
                                 <label class="text-xs text-muted-foreground font-semibold uppercase tracking-wider">NISN <span class="text-muted-foreground font-normal text-[10px]">(Opsional)</span></label>
                                 <input type="text" wire:model="nisn" maxlength="10" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="mt-1.5 block w-full rounded-lg border-input bg-background text-sm font-mono text-foreground shadow-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" placeholder="10 digit angka">
                                 @error('nisn') <span class="text-xs text-destructive mt-1 block">{{ $message }}</span> @enderror
                             </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs text-muted-foreground font-semibold uppercase tracking-wider">Unit <span class="text-red-500">*</span></label>
                                    <select wire:model.live="unit_code" class="mt-1.5 block w-full rounded-lg border-input bg-background text-sm text-foreground shadow-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                                        <option value="01">SMP</option>
                                        <option value="02">SMA</option>
                                        <option value="03">PPTQ</option>
                                    </select>
                                    @error('unit_code') <span class="text-xs text-destructive mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="text-xs text-muted-foreground font-semibold uppercase tracking-wider">Domisili <span class="text-red-500">*</span></label>
                                    <select wire:model.live="residence_status" class="mt-1.5 block w-full rounded-lg border-input bg-background text-sm text-foreground shadow-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                                        <option value="MONDOK">Mondok</option>
                                        <option value="NON_MONDOK">Non-Mondok</option>
                                        <option value="NGAJI_ONLY">Ngaji Only</option>
                                    </select>
                                    @error('residence_status') <span class="text-xs text-destructive mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            
                            <div>
                                <label class="text-xs text-muted-foreground font-semibold uppercase tracking-wider">Tingkat Kelas <span class="text-red-500">*</span></label>
                                <select wire:model.live="class_level_id" class="mt-1.5 block w-full rounded-lg border-input bg-background text-sm text-foreground shadow-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                                    <option value="">-- Pilih Tingkat Kelas --</option>
                                    @foreach($classLevels as $level)
                                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                                    @endforeach
                                </select>
                                @error('class_level_id') <span class="text-xs text-destructive mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="text-xs text-muted-foreground font-semibold uppercase tracking-wider">Jadwal Seleksi SPMB <span class="text-muted-foreground font-normal text-[10px]">(Opsional)</span></label>
                                <select wire:model="spmb_schedule_id" class="mt-1.5 block w-full rounded-lg border-input bg-background text-sm text-foreground shadow-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                                    <option value="">-- Pilih Jadwal --</option>
                                    @foreach($schedules as $schedule)
                                        <option value="{{ $schedule->id }}">{{ $schedule->name }}</option>
                                    @endforeach
                                </select>
                                @error('spmb_schedule_id') <span class="text-xs text-destructive mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2 border-t border-border pt-4">
                                <label class="text-xs text-muted-foreground font-semibold uppercase tracking-wider block mb-2">Status Khusus / Golongan Diskon</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-muted/20 p-4 rounded-lg border border-border">
                                    @foreach($this->specialStatuses as $status)
                                        @php
                                            $originalStatus = $student->specialStatuses->firstWhere('code', $status->code);
                                            $isPending = $originalStatus && !$originalStatus->pivot->is_approved;
                                        @endphp
                                        <label class="flex items-center gap-2.5 cursor-pointer p-2 rounded-md hover:bg-muted/40 transition-colors">
                                            <input type="checkbox"
                                                wire:model="special_statuses"
                                                value="{{ $status->code }}"
                                                class="w-4 h-4 rounded border-input text-primary focus:ring-ring">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-medium text-foreground">
                                                    {{ $status->name }}
                                                </span>
                                                <span class="text-xs text-muted-foreground">
                                                    {{ $status->description }}
                                                </span>
                                                @if($isPending)
                                                    <span class="mt-1 self-start inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                        Ajuan Wali (Pending)
                                                    </span>
                                                @elseif($originalStatus && $originalStatus->pivot->is_approved)
                                                    <span class="mt-1 self-start inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                        Disetujui
                                                    </span>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                    @if(count($this->specialStatuses) === 0)
                                        <span class="text-xs text-muted-foreground col-span-2">Tidak ada status khusus tersedia</span>
                                    @endif
                                </div>
                                <p class="text-[10px] text-muted-foreground mt-2">Daftar status khusus di atas disinkronisasikan sebagai status yang "Disetujui" (Approved) begitu pendaftaran calon santri diterima. Tagihan awal yang diterbitkan di bawah ini akan otomatis dikurangi diskon yang disetujui.</p>
                                @error('special_statuses') <span class="text-xs text-destructive mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom 2: Verifikasi Berkas Fisik (Tengah) -->
            <div class="flex flex-col space-y-6">
                <div class="bg-card rounded-xl shadow-sm border border-border overflow-hidden flex flex-col h-full max-h-[600px]">
                    <div class="border-b border-border bg-muted/30 px-5 py-4">
                        <div class="flex items-center justify-between w-full">
                            <h2 class="text-base font-semibold text-card-foreground flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                Validasi Berkas
                            </h2>
                            <button @click="toggleAll()" type="button" class="px-2 py-1 bg-primary hover:bg-primary/90 text-primary-foreground text-[10px] font-bold rounded-md shadow-sm transition-all focus:ring-2 focus:ring-offset-1 focus:ring-primary flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Centang Semua
                            </button>
                        </div>
                    </div>

                    <div class="p-5 space-y-3 flex-1 overflow-y-auto">
                        <!-- KK -->
                        <div class="p-3 bg-background border border-border rounded-lg flex items-center justify-between hover:border-primary/30 hover:shadow-sm transition-all">
                            <div class="flex items-center space-x-3">
                                <div class="p-1.5 bg-blue-50 text-blue-600 rounded-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <div class="text-left">
                                    <h4 class="text-sm font-semibold text-foreground">Kartu Keluarga</h4>
                                    <p class="text-[10px] text-muted-foreground mt-0.5">Validasi Nama</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($student->kk)
                                    <a href="{{ $student->kk_url }}" target="_blank" class="px-2.5 py-1.5 text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-md transition-colors" title="Lihat Berkas">
                                        Lihat
                                    </a>
                                @else
                                    <span class="text-[9px] text-muted-foreground bg-muted px-2.5 py-0.5 rounded font-semibold">Fisik</span>
                                @endif
                                <button type="button" @click="toggleVerify('kk')" 
                                    :class="verified.includes('kk') ? 'bg-green-600 text-white border-green-600 shadow-sm' : 'bg-background text-muted-foreground border-border hover:border-green-600 hover:text-green-600'"
                                    class="border px-2.5 py-1.5 text-xs font-semibold rounded-md transition-all flex items-center justify-center min-w-[70px]">
                                    <span x-text="verified.includes('kk') ? 'Sesuai' : 'Validasi'"></span>
                                </button>
                            </div>
                        </div>

                        <!-- Akta Kelahiran -->
                        <div class="p-3 bg-background border border-border rounded-lg flex items-center justify-between hover:border-primary/30 hover:shadow-sm transition-all">
                            <div class="flex items-center space-x-3">
                                <div class="p-1.5 bg-pink-50 text-pink-600 rounded-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c0.563-0.186 1-0.701 1-1.303V4.281c0-0.818-0.812-1.428-1.597-1.18L12.5 5.5v15l8.5-4.954zM2.062 19.32L10.5 20.5v-15L2.597 3.101C1.812 2.853 1 3.463 1 4.281v9.962c0 0.602 0.437 1.117 1 1.303l0.062 0.02z"/></svg>
                                </div>
                                <div class="text-left">
                                    <h4 class="text-sm font-semibold text-foreground">Akta Kelahiran</h4>
                                    <p class="text-[10px] text-muted-foreground mt-0.5">TTL & Ejaan</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($student->akta)
                                    <a href="{{ $student->akta_url }}" target="_blank" class="px-2.5 py-1.5 text-xs font-semibold text-pink-600 bg-pink-50 hover:bg-pink-100 rounded-md transition-colors" title="Lihat Berkas">
                                        Lihat
                                    </a>
                                @else
                                    <span class="text-[9px] text-muted-foreground bg-muted px-2.5 py-0.5 rounded font-semibold">Fisik</span>
                                @endif
                                <button type="button" @click="toggleVerify('akta')" 
                                    :class="verified.includes('akta') ? 'bg-green-600 text-white border-green-600 shadow-sm' : 'bg-background text-muted-foreground border-border hover:border-green-600 hover:text-green-600'"
                                    class="border px-2.5 py-1.5 text-xs font-semibold rounded-md transition-all flex items-center justify-center min-w-[70px]">
                                    <span x-text="verified.includes('akta') ? 'Sesuai' : 'Validasi'"></span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Dokumen NISN -->
                        <div class="p-3 bg-background border border-border rounded-lg flex items-center justify-between hover:border-primary/30 hover:shadow-sm transition-all">
                            <div class="flex items-center space-x-3">
                                <div class="p-1.5 bg-indigo-50 text-indigo-600 rounded-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div class="text-left">
                                    <h4 class="text-sm font-semibold text-foreground">Dokumen NISN</h4>
                                    <p class="text-[10px] text-muted-foreground mt-0.5">Validasi NISN</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($student->nisn_document)
                                    <a href="{{ $student->nisn_document_url }}" target="_blank" class="px-2.5 py-1.5 text-xs font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-md transition-colors" title="Lihat Berkas">
                                        Lihat
                                    </a>
                                @else
                                    <span class="text-[9px] text-muted-foreground bg-muted px-2.5 py-0.5 rounded font-semibold">Fisik</span>
                                @endif
                                <button type="button" @click="toggleVerify('nisn_document')" 
                                    :class="verified.includes('nisn_document') ? 'bg-green-600 text-white border-green-600 shadow-sm' : 'bg-background text-muted-foreground border-border hover:border-green-600 hover:text-green-600'"
                                    class="border px-2.5 py-1.5 text-xs font-semibold rounded-md transition-all flex items-center justify-center min-w-[70px]">
                                    <span x-text="verified.includes('nisn_document') ? 'Sesuai' : 'Validasi'"></span>
                                </button>
                            </div>
                        </div>

                        <!-- Ijazah Terakhir -->
                        <div class="p-3 bg-background border border-border rounded-lg flex items-center justify-between hover:border-primary/30 hover:shadow-sm transition-all">
                            <div class="flex items-center space-x-3">
                                <div class="p-1.5 bg-purple-50 text-purple-600 rounded-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path>
                                </div>
                                <div class="text-left">
                                    <h4 class="text-sm font-semibold text-foreground">Ijazah</h4>
                                    <p class="text-[10px] text-muted-foreground mt-0.5">Asal Sekolah</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($student->ijazah)
                                    <a href="{{ $student->ijazah_url }}" target="_blank" class="px-2.5 py-1.5 text-xs font-semibold text-purple-600 bg-purple-50 hover:bg-purple-100 rounded-md transition-colors" title="Lihat Berkas">
                                        Lihat
                                    </a>
                                @else
                                    <span class="text-[9px] text-muted-foreground bg-muted px-2.5 py-0.5 rounded font-semibold">Fisik</span>
                                @endif
                                <button type="button" @click="toggleVerify('ijazah')" 
                                    :class="verified.includes('ijazah') ? 'bg-green-600 text-white border-green-600 shadow-sm' : 'bg-background text-muted-foreground border-border hover:border-green-600 hover:text-green-600'"
                                    class="border px-2.5 py-1.5 text-xs font-semibold rounded-md transition-all flex items-center justify-center min-w-[70px]">
                                    <span x-text="verified.includes('ijazah') ? 'Sesuai' : 'Validasi'"></span>
                                </button>
                            </div>
                        </div>

                        <!-- Pas Foto -->
                        <div class="p-3 bg-background border border-border rounded-lg flex items-center justify-between hover:border-primary/30 hover:shadow-sm transition-all">
                            <div class="flex items-center space-x-3">
                                <div class="p-1.5 bg-yellow-50 text-yellow-600 rounded-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div class="text-left">
                                    <h4 class="text-sm font-semibold text-foreground">Pas Foto</h4>
                                    <p class="text-[10px] text-muted-foreground mt-0.5">Kerapian Wajah</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($student->foto)
                                    <a href="{{ $student->foto_url }}" target="_blank" class="px-2.5 py-1.5 text-xs font-semibold text-yellow-600 bg-yellow-50 hover:bg-yellow-100 rounded-md transition-colors" title="Lihat Berkas">
                                        Lihat
                                    </a>
                                @else
                                    <span class="text-[9px] text-muted-foreground bg-muted px-2.5 py-0.5 rounded font-semibold">Fisik</span>
                                @endif
                                <button type="button" @click="toggleVerify('foto')" 
                                    :class="verified.includes('foto') ? 'bg-green-600 text-white border-green-600 shadow-sm' : 'bg-background text-muted-foreground border-border hover:border-green-600 hover:text-green-600'"
                                    class="border px-2.5 py-1.5 text-xs font-semibold rounded-md transition-all flex items-center justify-center min-w-[70px]">
                                    <span x-text="verified.includes('foto') ? 'Sesuai' : 'Validasi'"></span>
                                </button>
                            </div>
                        </div>

                        <!-- Dokumen Pendukung Tambahan (Jika Ada) -->
                        @if($student->supporting_documents && count($student->supporting_documents) > 0)
                            <div class="mt-4 pt-4 border-t border-border space-y-2">
                                <h4 class="text-xs font-semibold text-foreground uppercase tracking-wider">Dokumen Pendukung Tambahan:</h4>
                                <div class="space-y-2">
                                    @foreach($student->supporting_documents as $doc)
                                        <div class="p-2.5 bg-muted/40 border border-border rounded-lg flex items-center justify-between hover:bg-muted/80 transition-all">
                                            <div class="flex items-center min-w-0">
                                                <svg class="h-4 w-4 text-primary mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                                <span class="text-xs text-muted-foreground truncate" title="{{ $doc['name'] }}">{{ $doc['name'] }}</span>
                                            </div>
                                            <a href="{{ Storage::url($doc['path']) }}" target="_blank" class="px-2.5 py-1.5 text-xs font-semibold text-primary bg-primary/10 hover:bg-primary/20 rounded-md transition-colors whitespace-nowrap ml-2">
                                                Lihat
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Kolom 3: Pilihan Tagihan & Aksi (Kanan) -->
            <div class="flex flex-col space-y-6">
                <div class="bg-card rounded-xl shadow-sm border border-border overflow-hidden flex flex-col h-full max-h-[600px]">
                    <div class="border-b border-border bg-background px-5 py-4 flex items-center justify-between relative z-10">
                        <h2 class="text-base font-semibold text-card-foreground flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Pilih Kategori Tagihan
                        </h2>
                        <div x-data="{ showInfo: false }" class="relative flex items-center">
                            <button type="button" @click="showInfo = !showInfo" @click.outside="showInfo = false" class="p-1.5 text-muted-foreground hover:text-primary hover:bg-secondary rounded-md transition-colors focus:outline-none" title="Informasi Tagihan">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </button>
                            <div x-show="showInfo" x-transition style="display: none;" class="absolute right-0 top-full mt-2 px-4 py-3 whitespace-nowrap bg-popover text-popover-foreground text-xs rounded-md shadow-lg border border-border z-50 text-center leading-relaxed">
                                Disesuaikan otomatis dengan<br>Unit & Domisili.
                                <div class="absolute -top-1 right-3 w-2 h-2 bg-popover border-t border-l border-border rotate-45"></div>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 flex-1 overflow-y-auto">
                        @if (empty($availableBillings))
                            <div class="py-8 bg-muted border border-dashed border-border rounded-lg text-center h-full flex flex-col items-center justify-center">
                                <div class="w-12 h-12 bg-muted rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 4v2M7.08 6.47A9 9 0 1119.02 19.02M7.08 6.47L5.6 5"></path>
                                    </svg>
                                </div>
                                <p class="font-semibold text-sm text-foreground">Tidak ada tagihan</p>
                                <p class="text-muted-foreground text-xs mt-1">Ganti Unit atau Domisili untuk memuat ulang.</p>
                            </div>
                        @else
                            <form wire:submit.prevent="confirmAcceptance" class="flex flex-col h-full">
                                <div class="flex items-center justify-between mb-4">
                                    <label class="inline-flex items-center text-xs font-semibold text-foreground cursor-pointer select-none hover:text-primary transition-colors group">
                                        <input type="checkbox" 
                                               wire:click="toggleSelectAll" 
                                               {{ count($selectedBillings) === count($availableBillings) && count($availableBillings) > 0 ? 'checked' : '' }}
                                               class="rounded border-input text-primary focus:ring-primary mr-2 transition-all group-hover:border-primary">
                                        Semua Kategori
                                    </label>
                                    <span class="text-[10px] bg-secondary text-secondary-foreground px-2 py-0.5 rounded-full font-bold">
                                        {{ count($selectedBillings) }}/{{ count($availableBillings) }}
                                    </span>
                                </div>

                                <div class="space-y-4 flex-1">
                                    @foreach ($availableBillings as $billing)
                                        <div x-data="{ expanded: false }" class="border border-border rounded-lg hover:border-primary transition-all overflow-hidden"
                                             :class="{ 'ring-1 ring-primary border-primary shadow-sm': $wire.selectedBillings.includes({{ $billing['id'] }}) }">
                                            
                                            <!-- Accordion Header -->
                                            <div class="flex items-start p-3 bg-background transition-colors">
                                                <div class="pt-0.5">
                                                    <input type="checkbox"
                                                           wire:model.live="selectedBillings"
                                                           value="{{ $billing['id'] }}"
                                                           class="rounded border-input text-primary focus:ring-primary transition-all cursor-pointer">
                                                </div>
                                                <div class="ml-3 flex-1 cursor-pointer select-none" @click="expanded = !expanded">
                                                    <div class="flex justify-between items-start">
                                                        <div>
                                                            <div class="font-semibold text-sm text-foreground leading-tight">{{ $billing['name'] }}</div>
                                                        </div>
                                                        <button type="button" class="text-muted-foreground p-1 hover:bg-muted rounded-md transition-colors" :class="{ 'rotate-180': expanded }">
                                                            <svg class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                        </button>
                                                    </div>
                                                    
                                                    <div class="text-xs font-medium text-foreground mt-2 font-mono">
                                                        Total: Rp {{ number_format($billing['total_amount'], 0, ',', '.') }}
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Preview Dropdown List -->
                                            <div class="bg-muted p-3 border-t border-border" x-show="expanded" x-transition style="display: none;">
                                                <p class="text-[10px] font-bold text-muted-foreground uppercase tracking-wider mb-2">Rincian Komponen:</p>
                                                <ul class="space-y-2">
                                                    @foreach ($billing['fees'] as $fee)
                                                        <li class="flex justify-between text-xs py-1.5">
                                                            <div class="flex items-start space-x-2">
                                                                <div class="w-1.5 h-1.5 rounded-full bg-primary mt-1.5"></div>
                                                                <div class="flex flex-col">
                                                                    <span class="text-foreground font-medium flex items-center">
                                                                        <svg class="w-3 h-3 mr-1 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                                                        {{ $fee['item_name'] }}
                                                                    </span>
                                                                    <div class="flex flex-row flex-wrap items-center gap-1.5 mt-1 pl-4">
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
                                                            <span class="font-mono font-semibold text-muted-foreground whitespace-nowrap mt-0.5">
                                                                Rp {{ number_format($fee['amount'], 0, ',', '.') }}
                                                            </span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </form>
                        @endif
                    </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>

@script
<script>
    const _studentName     = '{{ addslashes($student->full_name) }}';
    const _guardianName    = '{{ addslashes($student->guardian->full_name ?? '') }}';
    const _guardianPhone   = '{{ $student->guardian->whatsapp ?? '' }}';
    const _waLink          = '{{ $student->guardian->wa_link ?? '' }}';
    const _spmbName        = '{{ addslashes($student->spmbSchedule->name ?? 'Tidak ada jadwal') }}';
    const _acceptanceUrl   = '{{ route('admin.student-acceptance') }}';

    function showWaPreviewAfterReject(whatsapp, reason, message) {
        let _rejected = false;
        window.Swal.fire({
            title: 'Preview Pesan WhatsApp',
            icon: 'info',
            allowOutsideClick: false,
            showCloseButton: false,
            html: `
                <div class="text-left mt-2">
                    <p class="text-xs text-gray-500 mb-3">Pendaftaran <strong>belum ditolak</strong> — selesaikan dengan klik Copy atau Kirim.</p>
                    <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">Preview Pesan WhatsApp:</label>
                    <div class="p-3 bg-gray-50 border border-gray-200 rounded text-sm font-mono whitespace-pre-wrap mb-4" style="max-height: 200px; overflow-y: auto;">${message}</div>
                    <div class="flex flex-col gap-2">
                        <button type="button" id="copy-btn" class="w-full py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 font-medium">Copy Pesan</button>
                        <button type="button" id="edit-wa-btn" class="w-full py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 font-medium">Edit Pesan</button>
                        <button type="button" id="send-wa-btn" class="w-full py-2 bg-green-600 text-white rounded hover:bg-green-700 font-medium">Kirim ke WhatsApp</button>
                    </div>
                </div>
            `,
            showConfirmButton: false,
            didOpen: () => {
                const copyBtn = Swal.getHtmlContainer().querySelector('#copy-btn');
                const editBtn = Swal.getHtmlContainer().querySelector('#edit-wa-btn');
                const sendBtn = Swal.getHtmlContainer().querySelector('#send-wa-btn');

                copyBtn.addEventListener('click', async () => {
                    if (!_rejected) {
                        copyBtn.disabled = true;
                        copyBtn.innerText = 'Menyimpan...';
                        await $wire.rejectAcceptance(reason);
                        _rejected = true;
                        copyBtn.disabled = false;
                    }
                    navigator.clipboard.writeText(message);
                    copyBtn.innerText = 'Tersalin!';
                    setTimeout(() => { copyBtn.innerText = 'Copy Pesan'; }, 2000);
                });

                editBtn.addEventListener('click', () => {
                    Swal.fire({
                        title: 'Edit Pesan WhatsApp',
                        input: 'textarea',
                        inputValue: message,
                        allowOutsideClick: false,
                        showCloseButton: false,
                        inputAttributes: { 'rows': '12', 'style': 'height: 300px; font-family: monospace; font-size: 14px;' },
                        showCancelButton: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Batal',
                        preConfirm: (val) => {
                            if (!val) return Swal.showValidationMessage('Pesan tidak boleh kosong!');
                            return val;
                        }
                    }).then((editResult) => {
                        showWaPreviewAfterReject(whatsapp, reason, editResult.isConfirmed ? editResult.value : message);
                    });
                });

                sendBtn.addEventListener('click', async () => {
                    if (!_rejected) {
                        sendBtn.disabled = true;
                        sendBtn.innerText = 'Menyimpan...';
                        await $wire.rejectAcceptance(reason);
                        _rejected = true;
                    }
                    const encodedMsg = encodeURIComponent(message);
                    window.open(`https://wa.me/${whatsapp}?text=${encodedMsg}`, '_blank');
                    Swal.close();
                    window.location.href = _acceptanceUrl;
                });
            },
            footer: `<button type="button" class="text-gray-500 hover:underline" id="close-no-wa-btn">Tutup (Tanpa Kirim WA)</button>`,
            didRender: () => {
                const closeNoWaBtn = Swal.getFooter().querySelector('#close-no-wa-btn');
                if (closeNoWaBtn) {
                    closeNoWaBtn.addEventListener('click', async () => {
                        if (!_rejected) {
                            closeNoWaBtn.disabled = true;
                            closeNoWaBtn.innerText = 'Menyimpan...';
                            await $wire.rejectAcceptance(reason);
                            _rejected = true;
                        }
                        Swal.close();
                        window.location.href = _acceptanceUrl;
                    });
                }
            }
        });
    }

    window.confirmRejectionConfirmPage = function() {
        window.Swal.fire({
            title: 'Konfirmasi Penolakan',
            allowOutsideClick: false,
            showCloseButton: false,
            html: `
                <div class="text-left">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Alasan Template:</label>
                    <select id="rejection-template" class="swal2-select w-full border border-gray-300 rounded p-2 mb-3" style="display: flex; margin: 0 0 12px 0;">
                        <option value="Berkas Kartu Keluarga (KK) tidak lengkap atau buram.">Berkas KK tidak lengkap/buram</option>
                        <option value="Berkas Akta Kelahiran tidak lengkap atau buram.">Berkas Akta Lahir tidak lengkap/buram</option>
                        <option value="Berkas Ijazah terakhir tidak lengkap atau buram.">Berkas Ijazah tidak lengkap/buram</option>
                        <option value="Data NISN tidak valid atau tidak terdaftar di sistem.">Data NISN tidak valid/tidak terdaftar</option>
                        <option value="Pas foto tidak sesuai ketentuan.">Pas foto tidak sesuai ketentuan</option>
                        <option value="custom">Tulis Alasan Kustom...</option>
                    </select>

                    <label class="block text-sm font-medium text-gray-700 mb-1">Detail Alasan Penolakan:</label>
                    <textarea id="rejection-reason" class="swal2-textarea w-full border border-gray-300 rounded p-2" style="margin: 0; width: 100%; box-sizing: border-box;" placeholder="Detail alasan penolakan..."></textarea>

                    <div class="mt-4 flex items-center">
                        <input type="checkbox" id="send-wa" class="w-4 h-4 text-primary border-gray-300 rounded" checked>
                        <label for="send-wa" class="ml-2 text-sm text-gray-700">Kirim pesan ke WhatsApp Wali Santri</label>
                    </div>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Tolak',
            cancelButtonText: 'Batal',
            didOpen: () => {
                const select   = Swal.getHtmlContainer().querySelector('#rejection-template');
                const textarea = Swal.getHtmlContainer().querySelector('#rejection-reason');
                textarea.value = select.value;
                select.addEventListener('change', (e) => {
                    textarea.value = e.target.value === 'custom' ? '' : e.target.value;
                    if (e.target.value === 'custom') textarea.focus();
                });
            },
            preConfirm: () => {
                const textarea = Swal.getHtmlContainer().querySelector('#rejection-reason');
                const sendWa   = Swal.getHtmlContainer().querySelector('#send-wa').checked;
                const value    = textarea.value.trim();
                if (!value) {
                    Swal.showValidationMessage('Alasan penolakan wajib diisi!');
                    return false;
                }
                return { reason: value, sendWa: sendWa };
            }
        }).then((result) => {
            if (!result.isConfirmed) return;

            const { reason, sendWa } = result.value;

            if (sendWa && _waLink) {
                const defaultMessage = `*Pemberitahuan Pendaftaran Santri Baru*\n*An-Nawawiy*\n\nAssalamu'alaikum Warahmatullahi Wabarakatuh,\n\nYth. Bapak/Ibu *${_guardianName}*\n(${_guardianPhone})\n\nMohon maaf, berdasarkan hasil verifikasi dokumen pada jadwal:\n*${_spmbName}*\n\nPendaftaran santri atas nama:\n*${_studentName}*\n\n*Belum dapat kami terima* dengan alasan:\n\n_"${reason}"_\n\nSilakan melengkapi berkas atau menghubungi bagian administrasi untuk informasi lebih lanjut.\n\nSyukron, Jazakumullahu Khairan.\n\n---\n_Pesan otomatis dari Sistem Informasi Santri An-Nawawiy_`;

                showWaPreviewAfterReject(_waLink, reason, defaultMessage);
            } else {
                $wire.rejectAcceptance(reason).then(() => {
                    Swal.fire({
                        title: 'Berhasil Ditolak!',
                        text: 'Pendaftaran santri telah ditolak.',
                        icon: 'success',
                        confirmButtonText: 'Tutup'
                    }).then(() => { window.location.href = _acceptanceUrl; });
                });
            }
        });
    };

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
</script>
@endscript
