<div class="space-y-6">
    <!-- Back Link -->
    <div class="flex items-center justify-between">
        <a href="{{ route('wali.spmb-schedules') }}"
            class="inline-flex items-center text-sm font-semibold text-primary hover:opacity-85 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Pilihan Jadwal
        </a>
    </div>

    <!-- Header Section -->
    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
        <div class="flex items-center">
            <div class="p-3 bg-primary/10 rounded-full text-primary mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-foreground">Formulir Pendaftaran Santri Baru</h2>
                <p class="text-xs text-muted-foreground mt-0.5">Lengkapi formulir di bawah ini dengan data calon santri yang valid.</p>
            </div>
        </div>

        @if ($selectedSchedule)
            <div class="mt-4 p-4 rounded-lg text-xs" style="background-color: var(--secondary); color: var(--secondary-foreground); border: 1px solid var(--accent);">
                <p>
                    <strong>Gelombang:</strong> {{ $selectedSchedule->name }}<br>
                    <strong>Periode Pendaftaran:</strong> {{ $selectedSchedule->registration_start->locale('id')->isoFormat('D MMMM Y HH:mm') }} s/d {{ $selectedSchedule->registration_end->locale('id')->isoFormat('D MMMM Y HH:mm') }}
                </p>
            </div>
        @endif

        @if (session('error'))
            <div class="mt-4 p-3 text-sm text-red-750 bg-red-50 border border-red-200 rounded-lg" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @if (session('message'))
            <div class="mt-4 p-3 text-sm text-green-750 bg-green-50 border border-green-200 rounded-lg" role="alert">
                {{ session('message') }}
            </div>
        @endif
    </div>

    <!-- Registration Form -->
    <div class="bg-card border border-border rounded-xl shadow-sm overflow-hidden">
        <div class="p-6">
            <form wire:submit="save" class="space-y-6">

                <!-- Full Name -->
                <div>
                    <label for="full_name" class="block text-sm font-semibold text-foreground mb-1.5">Nama Lengkap Santri <span class="text-red-500">*</span></label>
                    <input wire:model="full_name" type="text" id="full_name" placeholder="Masukkan nama lengkap calon santri"
                        class="w-full px-4 py-2 border border-border bg-background text-foreground rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-ring transition-colors @error('full_name') border-destructive @enderror">
                    @error('full_name')
                        <span class="text-destructive text-xs mt-1 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <!-- NISN (Opsional) -->
                <div>
                    <label for="nisn" class="block text-sm font-semibold text-foreground mb-1.5">
                        NISN <span class="text-muted-foreground font-normal text-xs">(opsional)</span>
                    </label>
                    <input wire:model="nisn" type="text" inputmode="numeric" pattern="\d{10}" id="nisn"
                        placeholder="Contoh: 1234567890"
                        maxlength="10"
                        class="w-full px-4 py-2 border border-border bg-background text-foreground rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-ring transition-colors @error('nisn') border-destructive @enderror">
                    <p class="text-xs text-muted-foreground mt-1">Harus tepat 10 digit angka. Kosongkan jika belum memiliki NISN.</p>
                    @error('nisn')
                        <span class="text-destructive text-xs mt-1 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Unit -->
                    <div>
                        <label for="unit_code" class="block text-sm font-semibold text-foreground mb-1.5">Unit Sekolah <span class="text-red-500">*</span></label>
                        <select wire:model.live="unit_code" id="unit_code"
                            class="w-full px-3 py-2 border border-border bg-background text-foreground rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-ring transition-colors">
                            <option value="01">SMP (01)</option>
                            <option value="02">SMA (02)</option>
                            <option value="03">PPTQ (03)</option>
                        </select>
                        @error('unit_code')
                            <span class="text-destructive text-xs mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Residence Status -->
                    <div>
                        <label for="residence_status" class="block text-sm font-semibold text-foreground mb-1.5">Status Domisili <span class="text-red-500">*</span></label>
                        <select wire:model.live="residence_status" id="residence_status"
                            class="w-full px-3 py-2 border border-border bg-background text-foreground rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-ring transition-colors">
                            <option value="MONDOK">Mondok (Menginap)</option>
                            <option value="NON_MONDOK">Non Mondok (Pulang Pergi)</option>
                            <option value="NGAJI_ONLY">Ngaji Only</option>
                        </select>
                        @error('residence_status')
                            <span class="text-destructive text-xs mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Class Level -->
                <div>
                    <label for="class_level_id" class="block text-sm font-semibold text-foreground mb-1.5">Pilihan Kelas <span class="text-red-500">*</span></label>
                    <select wire:model="class_level_id" id="class_level_id"
                        class="w-full px-3 py-2 border border-border bg-background text-foreground rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-ring transition-colors">
                        <option value="">Pilih Tingkat Kelas...</option>
                        @foreach($this->classLevels as $level)
                            <option value="{{ $level->id }}">{{ $level->name }}</option>
                        @endforeach
                    </select>
                    @error('class_level_id')
                        <span class="text-destructive text-xs mt-1 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Info Box -->
                <div class="p-3 bg-muted rounded-lg border border-border/40 text-xs text-muted-foreground space-y-1">
                    <p class="font-bold text-foreground">Keterangan Domisili:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        <li>Mondok: Santri tinggal di lingkungan asrama pesantren.</li>
                        <li>Non Mondok: Santri pulang pergi dari rumah tinggal/wali.</li>
                        <li>Ngaji Only: Hanya mengikuti program mengaji/tahfidz non-formal.</li>
                    </ul>
                </div>

                <!-- Address -->
                <div>
                    <label for="address" class="block text-sm font-semibold text-foreground mb-1.5">Alamat Lengkap</label>
                    <textarea wire:model="address" id="address" rows="3" placeholder="Tulis alamat rumah lengkap dengan RT/RW, kelurahan, kecamatan, kabupaten..."
                        class="w-full px-4 py-2 border border-border bg-background text-foreground rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-ring transition-colors resize-none"></textarea>
                    @error('address')
                        <span class="text-destructive text-xs mt-1 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Document Uploads -->
                <div class="p-5 bg-muted/30 border border-border rounded-xl space-y-4">
                    <h3 class="font-bold text-foreground text-sm">Unggah Berkas Pendukung</h3>
                    <p class="text-xs text-muted-foreground">Silakan unggah dokumen persyaratan dalam format JPG, JPEG, PNG, WEBP, atau PDF (ukuran maks 2MB).</p>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem 2.5rem; margin-top: 1rem;">
                        <!-- KK -->
                        <div class="space-y-1.5" style="margin-bottom: 0.5rem;">
                            <label for="kk" class="block text-xs font-semibold text-foreground">Kartu Keluarga (KK) <span class="text-red-500">*</span></label>
                            @if($this->kk && !is_string($this->kk))
                                <div class="p-2.5 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between">
                                    <div class="flex items-center min-w-0">
                                        <svg class="h-4 w-4 text-green-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-xs text-green-800 font-medium truncate">{{ $this->kk->getClientOriginalName() }}</span>
                                    </div>
                                    <button type="button" wire:click="removeFile('kk')" class="text-muted-foreground hover:text-foreground ml-2">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @else
                                <input type="file" wire:model="kk" id="kk" accept=".jpg,.jpeg,.png,.webp,.pdf"
                                    class="w-full px-3 py-1.5 border border-border bg-background text-foreground rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-ring">
                            @endif
                            @error('kk')
                                <span class="text-destructive text-xs mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Foto -->
                        <div class="space-y-1.5" style="margin-bottom: 0.5rem;">
                            <label for="foto" class="block text-xs font-semibold text-foreground">Pas Foto Santri <span class="text-red-500">*</span></label>
                            @if($this->foto && !is_string($this->foto))
                                <div class="p-2.5 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between">
                                    <div class="flex items-center min-w-0">
                                        <svg class="h-4 w-4 text-green-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-xs text-green-800 font-medium truncate">{{ $this->foto->getClientOriginalName() }}</span>
                                    </div>
                                    <button type="button" wire:click="removeFile('foto')" class="text-muted-foreground hover:text-foreground ml-2">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @else
                                <input type="file" wire:model="foto" id="foto" accept=".jpg,.jpeg,.png,.webp"
                                    class="w-full px-3 py-1.5 border border-border bg-background text-foreground rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-ring">
                            @endif
                            @error('foto')
                                <span class="text-destructive text-xs mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Akta -->
                        <div class="space-y-1.5" style="margin-bottom: 0.5rem;">
                            <label for="akta" class="block text-xs font-semibold text-foreground">Akta Kelahiran <span class="text-red-500">*</span></label>
                            @if($this->akta && !is_string($this->akta))
                                <div class="p-2.5 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between">
                                    <div class="flex items-center min-w-0">
                                        <svg class="h-4 w-4 text-green-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-xs text-green-800 font-medium truncate">{{ $this->akta->getClientOriginalName() }}</span>
                                    </div>
                                    <button type="button" wire:click="removeFile('akta')" class="text-muted-foreground hover:text-foreground ml-2">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @else
                                <input type="file" wire:model="akta" id="akta" accept=".jpg,.jpeg,.png,.webp,.pdf"
                                    class="w-full px-3 py-1.5 border border-border bg-background text-foreground rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-ring">
                            @endif
                            @error('akta')
                                <span class="text-destructive text-xs mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Ijazah -->
                        <div class="space-y-1.5" style="margin-bottom: 0.5rem;">
                            <label for="ijazah" class="block text-xs font-semibold text-foreground">Ijazah Terakhir <span class="text-red-500">*</span></label>
                            @if($this->ijazah && !is_string($this->ijazah))
                                <div class="p-2.5 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between">
                                    <div class="flex items-center min-w-0">
                                        <svg class="h-4 w-4 text-green-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-xs text-green-800 font-medium truncate">{{ $this->ijazah->getClientOriginalName() }}</span>
                                    </div>
                                    <button type="button" wire:click="removeFile('ijazah')" class="text-muted-foreground hover:text-foreground ml-2">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @else
                                <input type="file" wire:model="ijazah" id="ijazah" accept=".jpg,.jpeg,.png,.webp,.pdf"
                                    class="w-full px-3 py-1.5 border border-border bg-background text-foreground rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-ring">
                            @endif
                            @error('ijazah')
                                <span class="text-destructive text-xs mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- NISN Document (Opsional) -->
                        <div class="space-y-1.5" style="margin-bottom: 0.5rem;">
                            <label for="nisn_document" class="block text-xs font-semibold text-foreground">Dokumen NISN <span class="text-muted-foreground font-normal">(Opsional)</span></label>
                            @if($this->nisn_document && !is_string($this->nisn_document))
                                <div class="p-2.5 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between">
                                    <div class="flex items-center min-w-0">
                                        <svg class="h-4 w-4 text-green-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-xs text-green-800 font-medium truncate">{{ $this->nisn_document->getClientOriginalName() }}</span>
                                    </div>
                                    <button type="button" wire:click="removeFile('nisn_document')" class="text-muted-foreground hover:text-foreground ml-2">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @else
                                <input type="file" wire:model="nisn_document" id="nisn_document" accept=".jpg,.jpeg,.png,.webp,.pdf"
                                    class="w-full px-3 py-1.5 border border-border bg-background text-foreground rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-ring">
                            @endif
                            @error('nisn_document')
                                <span class="text-destructive text-xs mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Lampiran Pendukung Tambahan (Multiple) -->
                        <div class="space-y-1.5 md:col-span-2" style="margin-top: 0.5rem; margin-bottom: 0.5rem;">
                            <label for="supporting_documents" class="block text-xs font-semibold text-foreground">Dokumen Pendukung Tambahan <span class="text-muted-foreground font-normal">(Opsional - bisa pilih beberapa berkas sekaligus, maks 4MB per berkas)</span></label>
                            <p class="text-[11px] text-muted-foreground/90 leading-relaxed bg-primary/5 p-2 rounded-lg border border-primary/10">
                                💡 <strong>Tips Potongan Biaya:</strong> Jika Anda ingin mengajukan keringanan biaya/diskon pendidikan kepada yayasan, silakan unggah berkas bukti yang sah di sini (contoh: sertifikat prestasi/piagam santri, surat keterangan kurang mampu, bukti kartu PKH/KPS, slip gaji wali, atau KK untuk diskon saudara kandung).
                            </p>
                            
                            @if(!empty($supporting_documents))
                                <div class="space-y-2 mt-1.5">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        @foreach($supporting_documents as $index => $doc)
                                            <div class="p-2.5 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between">
                                                <div class="flex items-center min-w-0">
                                                    <svg class="h-4 w-4 text-green-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                    <span class="text-xs text-green-800 font-medium truncate">{{ $doc->getClientOriginalName() }}</span>
                                                </div>
                                                <button type="button" wire:click="removeSupportingDoc({{ $index }})" class="text-muted-foreground hover:text-foreground ml-2">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="mt-2">
                                        <input type="file" wire:model="supporting_documents" id="supporting_documents_more" accept=".jpg,.jpeg,.png,.webp,.pdf" multiple
                                            class="w-full px-3 py-1.5 border border-border bg-background text-foreground rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-ring">
                                    </div>
                                </div>
                            @else
                                <input type="file" wire:model="supporting_documents" id="supporting_documents" accept=".jpg,.jpeg,.png,.webp,.pdf" multiple
                                    class="w-full px-3 py-1.5 border border-border bg-background text-foreground rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-ring">
                            @endif
                            @error('supporting_documents')
                                <span class="text-destructive text-xs mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                            @error('supporting_documents.*')
                                <span class="text-destructive text-xs mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Submit Button -->
                <div class="flex justify-end pt-4 border-t border-border">
                    <button type="submit" wire:loading.attr="disabled" wire:target="save"
                        class="inline-flex items-center justify-center px-6 py-2 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-primary-foreground bg-primary hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                        <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="save">Kirim Pendaftaran</span>
                        <span wire:loading wire:target="save">Mengunggah & Memproses...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
