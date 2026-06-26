<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- Header Section -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-primary/10 rounded-full text-primary mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Pendaftaran Santri Baru</h2>
                        <p class="text-sm text-gray-500">Lengkapi data santri berikut untuk mendaftar</p>
                    </div>
                </div>

                @if ($selectedSchedule)
                    <div class="bg-primary/5 border border-primary/20 rounded-lg p-4">
                        <p class="text-sm text-gray-700">
                            <strong>Jadwal Pendaftaran:</strong> {{ $selectedSchedule->name }}<br>
                            <strong>Periode:</strong> {{ $selectedSchedule->registration_start->locale('id')->isoFormat('D MMMM Y HH:mm') }} - {{ $selectedSchedule->registration_end->locale('id')->isoFormat('D MMMM Y HH:mm') }}
                        </p>
                    </div>
                @endif

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

        <!-- Back Link -->
        <div class="mb-4">
            <a href="{{ route('wali.spmb-schedules') }}"
                class="inline-flex items-center text-primary hover:text-primary/80 font-medium text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Pilihan Jadwal
            </a>
        </div>

        <!-- Registration Form -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 border-b border-gray-200">
                <form wire:submit="save" class="space-y-6">

                    <!-- Full Name -->
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap Santri <span class="text-red-500">*</span></label>
                        <input wire:model="full_name" type="text" id="full_name" placeholder="Masukkan nama lengkap santri"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                        @error('full_name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Unit -->
                        <div>
                            <label for="unit_code" class="block text-sm font-medium text-gray-700">Unit Sekolah <span class="text-red-500">*</span></label>
                            <select wire:model.live="unit_code" id="unit_code"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                                <option value="01">SMP (01)</option>
                                <option value="02">SMA (02)</option>
                                <option value="03">PPTQ (03)</option>
                            </select>
                            @error('unit_code')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Residence Status -->
                        <div>
                            <label for="residence_status" class="block text-sm font-medium text-gray-700">Status Domisili <span class="text-red-500">*</span></label>
                            <select wire:model.live="residence_status" id="residence_status"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                                <option value="MONDOK">Mondok (Menginap)</option>
                                <option value="NON_MONDOK">Non Mondok (Pulang Pergi)</option>
                                <option value="NGAJI_ONLY">Ngaji Only</option>
                            </select>
                            @error('residence_status')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Class Level -->
                        <div>
                            <label for="class_level_id" class="block text-sm font-medium text-gray-700">Pilihan Kelas <span class="text-red-500">*</span></label>
                            <select wire:model="class_level_id" id="class_level_id"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                                <option value="">Pilih Kelas...</option>
                                @foreach($this->classLevels as $level)
                                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                                @endforeach
                            </select>
                            @error('class_level_id')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Special Status -->
                        <div>
                            <label for="special_status" class="block text-sm font-medium text-gray-700">Status Khusus <span class="text-red-500">*</span></label>
                            <select wire:model="special_status" id="special_status"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                                <option value="UMUM">Umum</option>
                                <option value="ANAK_GURU">Anak Guru</option>
                                <option value="YATIM">Yatim</option>
                            </select>
                            @error('special_status')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Info box -->
                    <div class="p-3 bg-gray-50 rounded-md text-sm text-gray-600">
                        <p><strong>Status Domisili:</strong></p>
                        <ul class="list-disc list-inside text-xs mt-1">
                            <li>Mondok: Santri tinggal di asrama pesantren</li>
                            <li>Non Mondok: Santri pulang pergi</li>
                            <li>Ngaji Only: Hanya mengikuti kegiatan mengaji</li>
                        </ul>
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                        <textarea wire:model="address" id="address" rows="3" placeholder="Masukkan alamat lengkap santri"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm"></textarea>
                        @error('address')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Document Uploads -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-800 mb-4">Upload Dokumen</h3>
                        <p class="text-sm text-gray-600 mb-4">Silakan upload dokumen-dokumen yang diperlukan dalam format JPG, JPEG, PNG, atau PDF.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- KK -->
                            <div>
                                <label for="kk" class="block text-sm font-medium text-gray-700">Kartu Keluarga (KK) <span class="text-red-500">*</span></label>
                                <p class="text-xs text-gray-500 mb-2">Format: JPG, JPEG, PNG, WEBP, PDF (Max: 2MB)</p>
                                @if($this->kk && !is_string($this->kk))
                                    <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-md flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-sm text-green-700 font-medium">{{ $this->kk->getClientOriginalName() }}</span>
                                        </div>
                                        <button type="button" wire:click="removeFile('kk')" class="text-gray-400 hover:text-gray-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @else
                                    <input type="file" wire:model="kk" id="kk" accept=".jpg,.jpeg,.png,.webp,.pdf"
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                                @endif
                                @error('kk')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Foto -->
                            <div>
                                <label for="foto" class="block text-sm font-medium text-gray-700">Pas Foto <span class="text-red-500">*</span></label>
                                <p class="text-xs text-gray-500 mb-2">Format: JPG, JPEG, PNG, WEBP (Max: 1MB)</p>
                                @if($this->foto && !is_string($this->foto))
                                    <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-md flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-sm text-green-700 font-medium">{{ $this->foto->getClientOriginalName() }}</span>
                                        </div>
                                        <button type="button" wire:click="removeFile('foto')" class="text-gray-400 hover:text-gray-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @else
                                    <input type="file" wire:model="foto" id="foto" accept=".jpg,.jpeg,.png,.webp"
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                                @endif
                                @error('foto')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- NISN -->
                            <div>
                                <label for="nisn_document" class="block text-sm font-medium text-gray-700">Kartu NISN (Opsional)</label>
                                <p class="text-xs text-gray-500 mb-2">Format: JPG, JPEG, PNG, WEBP, PDF (Max: 2MB)</p>
                                @if($this->nisn_document && !is_string($this->nisn_document))
                                    <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-md flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-sm text-green-700 font-medium">{{ $this->nisn_document->getClientOriginalName() }}</span>
                                        </div>
                                        <button type="button" wire:click="removeFile('nisn_document')" class="text-gray-400 hover:text-gray-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @else
                                    <input type="file" wire:model="nisn_document" id="nisn_document" accept=".jpg,.jpeg,.png,.webp,.pdf"
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                                @endif
                                @error('nisn_document')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Akta -->
                            <div>
                                <label for="akta" class="block text-sm font-medium text-gray-700">Akta Kelahiran <span class="text-red-500">*</span></label>
                                <p class="text-xs text-gray-500 mb-2">Format: JPG, JPEG, PNG, WEBP, PDF (Max: 2MB)</p>
                                @if($this->akta && !is_string($this->akta))
                                    <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-md flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-sm text-green-700 font-medium">{{ $this->akta->getClientOriginalName() }}</span>
                                        </div>
                                        <button type="button" wire:click="removeFile('akta')" class="text-gray-400 hover:text-gray-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @else
                                    <input type="file" wire:model="akta" id="akta" accept=".jpg,.jpeg,.png,.webp,.pdf"
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                                @endif
                                @error('akta')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Ijazah -->
                            <div>
                                <label for="ijazah" class="block text-sm font-medium text-gray-700">Ijazah Terakhir <span class="text-red-500">*</span></label>
                                <p class="text-xs text-gray-500 mb-2">Format: JPG, JPEG, PNG, WEBP, PDF (Max: 2MB)</p>
                                @if($this->ijazah && !is_string($this->ijazah))
                                    <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-md flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-sm text-green-700 font-medium">{{ $this->ijazah->getClientOriginalName() }}</span>
                                        </div>
                                        <button type="button" wire:click="removeFile('ijazah')" class="text-gray-400 hover:text-gray-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @else
                                    <input type="file" wire:model="ijazah" id="ijazah" accept=".jpg,.jpeg,.png,.webp,.pdf"
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                                @endif
                                @error('ijazah')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>


                    <div class="flex justify-end pt-4">
                        <button type="submit" wire:loading.attr="disabled" wire:target="save"
                            class="inline-flex items-center justify-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="save">Daftarkan Santri</span>
                            <span wire:loading wire:target="save">Memproses Pendaftaran...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Information Section -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Informasi Pendaftaran</h3>
                <p class="text-sm text-gray-600 mb-4">Pastikan semua data yang Anda masukkan sudah benar dan lengkap.</p>
                <p class="text-sm text-gray-600">Setelah mendaftar, data Anda akan diproses oleh admin untuk verifikasi.</p>
            </div>
        </div>

    </div>
</div>
