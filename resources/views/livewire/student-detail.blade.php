<div>
    <x-slot name="header">
        Detail Santri
    </x-slot>

    <div class="space-y-6">
        <!-- Status Notification Banner -->
        @if ($student->status === 'menunggu')
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zm-1 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Pendaftaran untuk <strong class="font-semibold">{{ $student->full_name }}</strong> saat ini sedang dalam proses peninjauan oleh panitia penerimaan. Silakan periksa halaman ini secara berkala untuk pembaruan status.
                        </p>
                    </div>
                </div>
            </div>
        @elseif ($student->status === 'diterima')
            <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">
                            Selamat! Pendaftaran untuk <strong class="font-semibold">{{ $student->full_name }}</strong> telah **diterima**. Silakan periksa dan selesaikan tagihan awal di bagian bawah untuk melengkapi proses registrasi.
                        </p>
                    </div>
                </div>
            </div>
        @elseif ($student->status === 'ditolak')
            <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm text-red-750">
                            Terima kasih telah mendaftar di pondok kami. Mohon maaf, saat ini pendaftaran untuk <strong class="font-semibold">{{ $student->full_name }}</strong> belum dapat kami setujui.
                        </p>
                        <div class="mt-2 text-xs text-red-800 bg-red-100/50 p-2 rounded border border-red-200">
                            <strong>Catatan Panitia:</strong> {{ $student->rejection_note ?? 'Persyaratan administrasi belum terpenuhi.' }}
                        </div>
                        <p class="mt-2 text-xs text-red-600">
                            Silakan hubungi panitia pendaftaran untuk informasi lebih lanjut atau melakukan pendaftaran ulang di periode berikutnya.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Student & Guardian Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Student Info -->
            <div class="bg-card rounded-lg shadow-sm border border-border p-6">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg font-semibold text-card-foreground">Informasi Santri</h3>
                    <span
                        class="px-2 py-1 rounded-full text-xs font-medium {{ $student->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $student->is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                </div>
                <div class="space-y-3 text-sm">
                    <div class="grid grid-cols-3">
                        <span class="text-muted-foreground">NIS</span>
                        <span class="col-span-2 font-mono text-foreground">{{ $student->nis }}</span>
                    </div>
                    <div class="grid grid-cols-3">
                        <span class="text-muted-foreground">Nama Lengkap</span>
                        <span class="col-span-2 font-medium text-foreground">{{ $student->full_name }}</span>
                    </div>
                    <div class="grid grid-cols-3">
                        <span class="text-muted-foreground">Unit</span>
                        <span class="col-span-2 text-foreground">
                            {{ $student->unit_code == '01' ? 'SMP' : ($student->unit_code == '02' ? 'SMA' : 'PPTQ') }}
                        </span>
                    </div>
                    <div class="grid grid-cols-3">
                        <span class="text-muted-foreground">Kelas</span>
                        <span class="col-span-2 text-foreground">{{ $student->class_name ?? '-' }}</span>
                    </div>
                    <div class="grid grid-cols-3">
                        <span class="text-muted-foreground">Tempat Tinggal</span>
                        <span class="col-span-2 text-foreground">{{ $student->residence_status }}</span>
                    </div>
                    <div class="grid grid-cols-3">
                        <span class="text-muted-foreground">Status Khusus</span>
                        <span class="col-span-2 text-foreground">{{ $student->special_status }}</span>
                    </div>
                    <div class="grid grid-cols-3">
                        <span class="text-muted-foreground">Alamat</span>
                        <span class="col-span-2 text-foreground">{{ $student->address ?? '-' }}</span>
                    </div>
                </div>
                @auth
                    @if (in_array(Auth::user()->role, ['SUPER_ADMIN', 'ADMINISTRASI']))
                        <div class="mt-6">
                            <a href="{{ route('admin.students.edit', $student) }}"
                                class="inline-flex items-center px-4 py-2 bg-secondary text-secondary-foreground text-sm font-medium rounded-md hover:bg-secondary/80 transition-colors shadow-sm">
                                Ubah Santri
                            </a>
                        </div>
                    @endif
                @endauth
            </div>

            <!-- Guardian Info -->
            <div class="bg-card rounded-lg shadow-sm border border-border p-6">
                <h3 class="text-lg font-semibold text-card-foreground mb-4">Informasi Wali</h3>
                <div class="space-y-3 text-sm">
                    <div class="grid grid-cols-3">
                        <span class="text-muted-foreground">Nama</span>
                        <span class="col-span-2 font-medium text-foreground">{{ $student->guardian->full_name }}</span>
                    </div>
                    <div class="grid grid-cols-3">
                        <span class="text-muted-foreground">WhatsApp</span>
                        <span class="col-span-2 text-foreground">{{ $student->guardian->whatsapp }}</span>
                    </div>
                    <div class="grid grid-cols-3">
                        <span class="text-muted-foreground">Email</span>
                        <span class="col-span-2 text-foreground">{{ $student->guardian->user->email ?? '-' }}</span>
                    </div>
                </div>
                @auth
                    @if (in_array(Auth::user()->role, ['SUPER_ADMIN', 'ADMINISTRASI']))
                        <div class="mt-6">
                            <a href="{{ route('admin.guardians.edit', $student->guardian) }}"
                                class="inline-flex items-center px-4 py-2 bg-secondary text-secondary-foreground text-sm font-medium rounded-md hover:bg-secondary/80 transition-colors shadow-sm">
                                Ubah Wali
                            </a>
                        </div>
                    @endif
                @endauth
            </div>
        </div>

        <!-- Attachment Documents -->
        <div class="bg-card rounded-lg shadow-sm border border-border p-6">
            <h3 class="text-lg font-semibold text-card-foreground mb-4">Dokumen Lampiran</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <!-- KK -->
                <div class="border border-border rounded-lg p-4 flex flex-col justify-between items-center text-center bg-muted/20 hover:bg-muted/50 transition-colors">
                    <div class="mb-3">
                        <svg class="w-10 h-10 text-muted-foreground mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-medium text-foreground text-sm">Kartu Keluarga (KK)</h4>
                        <span class="inline-block mt-2 px-2 py-0.5 rounded-full text-xs font-medium {{ $student->kk ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $student->kk ? 'Sudah Diunggah' : 'Belum Ada' }}
                        </span>
                    </div>
                    @if ($student->kk)
                        <a href="{{ $student->kk_url }}" target="_blank" class="mt-4 w-full text-center py-1.5 px-3 bg-primary text-primary-foreground text-xs font-medium rounded hover:bg-primary/90 transition-colors">
                            Lihat Dokumen
                        </a>
                    @endif
                </div>

                <!-- Akta Kelahiran -->
                <div class="border border-border rounded-lg p-4 flex flex-col justify-between items-center text-center bg-muted/20 hover:bg-muted/50 transition-colors">
                    <div class="mb-3">
                        <svg class="w-10 h-10 text-muted-foreground mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-medium text-foreground text-sm">Akta Kelahiran</h4>
                        <span class="inline-block mt-2 px-2 py-0.5 rounded-full text-xs font-medium {{ $student->akta ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $student->akta ? 'Sudah Diunggah' : 'Belum Ada' }}
                        </span>
                    </div>
                    @if ($student->akta)
                        <a href="{{ $student->akta_url }}" target="_blank" class="mt-4 w-full text-center py-1.5 px-3 bg-primary text-primary-foreground text-xs font-medium rounded hover:bg-primary/90 transition-colors">
                            Lihat Dokumen
                        </a>
                    @endif
                </div>

                <!-- Ijazah -->
                <div class="border border-border rounded-lg p-4 flex flex-col justify-between items-center text-center bg-muted/20 hover:bg-muted/50 transition-colors">
                    <div class="mb-3">
                        <svg class="w-10 h-10 text-muted-foreground mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-medium text-foreground text-sm">Ijazah Terakhir</h4>
                        <span class="inline-block mt-2 px-2 py-0.5 rounded-full text-xs font-medium {{ $student->ijazah ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $student->ijazah ? 'Sudah Diunggah' : 'Belum Ada' }}
                        </span>
                    </div>
                    @if ($student->ijazah)
                        <a href="{{ $student->ijazah_url }}" target="_blank" class="mt-4 w-full text-center py-1.5 px-3 bg-primary text-primary-foreground text-xs font-medium rounded hover:bg-primary/90 transition-colors">
                            Lihat Dokumen
                        </a>
                    @endif
                </div>

                <!-- Dokumen NISN -->
                <div class="border border-border rounded-lg p-4 flex flex-col justify-between items-center text-center bg-muted/20 hover:bg-muted/50 transition-colors">
                    <div class="mb-3">
                        <svg class="w-10 h-10 text-muted-foreground mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-medium text-foreground text-sm">Dokumen NISN</h4>
                        <span class="inline-block mt-2 px-2 py-0.5 rounded-full text-xs font-medium {{ $student->nisn_document ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $student->nisn_document ? 'Sudah Diunggah' : 'Belum Ada' }}
                        </span>
                    </div>
                    @if ($student->nisn_document)
                        <a href="{{ $student->nisn_document_url }}" target="_blank" class="mt-4 w-full text-center py-1.5 px-3 bg-primary text-primary-foreground text-xs font-medium rounded hover:bg-primary/90 transition-colors">
                            Lihat Dokumen
                        </a>
                    @endif
                </div>

                <!-- Pas Foto -->
                <div class="border border-border rounded-lg p-4 flex flex-col justify-between items-center text-center bg-muted/20 hover:bg-muted/50 transition-colors">
                    <div class="mb-3">
                        @if ($student->foto)
                            <img src="{{ $student->foto_url }}" alt="Pas Foto" class="w-12 h-12 object-cover rounded-full mx-auto border border-border">
                        @else
                            <svg class="w-10 h-10 text-muted-foreground mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @endif
                    </div>
                    <div>
                        <h4 class="font-medium text-foreground text-sm">Pas Foto</h4>
                        <span class="inline-block mt-2 px-2 py-0.5 rounded-full text-xs font-medium {{ $student->foto ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $student->foto ? 'Sudah Diunggah' : 'Belum Ada' }}
                        </span>
                    </div>
                    @if ($student->foto)
                        <a href="{{ $student->foto_url }}" target="_blank" class="mt-4 w-full text-center py-1.5 px-3 bg-primary text-primary-foreground text-xs font-medium rounded hover:bg-primary/90 transition-colors">
                            Lihat Dokumen
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Billing History -->
        <div class="bg-card rounded-lg shadow-sm border border-border">
            <div class="p-6 border-b border-border">
                <h3 class="text-lg font-semibold text-card-foreground">Riwayat Tagihan</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-muted-foreground uppercase bg-muted">
                        <tr>
                            <th class="px-6 py-3">Judul</th>
                            <th class="px-6 py-3">Jumlah</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Dibuat Pada</th>
                            <th class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse ($student->billings as $billing)
                            <tr class="hover:bg-muted/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-foreground">{{ $billing->title }}</td>
                                <td class="px-6 py-4 text-foreground">Rp
                                    {{ number_format($billing->final_amount, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-medium
                                        {{ $billing->status == 'PAID' ? 'bg-green-100 text-green-700' : ($billing->status == 'PENDING' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                        {{ $billing->status == 'PAID' ? 'LUNAS' : ($billing->status == 'UNPAID' ? 'BELUM LUNAS' : $billing->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-muted-foreground">
                                    {{ $billing->created_at->locale('id')->isoFormat('D MMMM Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if ($billing->status == 'PAID')
                                        <a href="{{ route('admin.receipts.show', $billing->id) }}" target="_blank"
                                            class="text-primary hover:text-primary/80 font-medium">Kwitansi</a>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-muted-foreground">
                                    Tidak ada riwayat tagihan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
