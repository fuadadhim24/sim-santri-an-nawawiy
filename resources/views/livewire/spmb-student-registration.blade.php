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

                        <!-- Info about status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Informasi</label>
                            <div class="mt-1 p-3 bg-gray-50 rounded-md text-sm text-gray-600">
                                <p><strong>Status Domisili:</strong></p>
                                <ul class="list-disc list-inside text-xs mt-1">
                                    <li>Mondok: Santri tinggal di asrama pesantren</li>
                                    <li>Non Mondok: Santri pulang pergi</li>
                                    <li>Ngaji Only: Hanya mengikuti kegiatan mengaji</li>
                                </ul>
                            </div>
                        </div>
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


                    <div class="flex justify-end space-x-3 pt-4">
                        <a href="{{ route('wali.spmb-schedules') }}"
                            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            Daftarkan Santri
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Requirements Section -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Persyaratan Pendaftaran</h3>
                <p class="text-sm text-gray-600 mb-4">Pastikan Anda telah menyiapkan dokumen-dokumen berikut:</p>

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
                            <p class="text-xs text-gray-500">Fotokopi akta kelahiran yang sah</p>
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
