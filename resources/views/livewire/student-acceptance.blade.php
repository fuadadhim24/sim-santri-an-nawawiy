<div>
    <x-slot name="header">
        Penerimaan Santri
    </x-slot>

    @if (session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-card rounded-lg shadow-sm border border-border">
        <div class="p-6 border-b border-border flex items-center justify-between gap-4 overflow-x-auto no-scrollbar">
            <h3 class="text-lg font-semibold text-card-foreground whitespace-nowrap">Santri Menunggu Konfirmasi</h3>
            <div class="flex items-center space-x-2">
                <input wire:model.live="search" type="text" placeholder="Cari santri..."
                    class="py-2 px-4 border border-input bg-background rounded-md focus:outline-none focus:ring-2 focus:ring-ring text-foreground">
            </div>
        </div>

        <div class="overflow-x-auto">
            @forelse ($studentsBySchedule as $scheduleId => $students)
                @php
                    $schedule = $spmbSchedules->firstWhere('id', $scheduleId);
                    $scheduleName = $schedule ? $schedule->name : 'Tanpa Jadwal SPMB';
                    $scheduleKey = 'schedule-'.$scheduleId;
                    $studentsCount = count($students);
                @endphp

                @php
                    $expandedScheduleIds = session('expanded_schedule_ids', []);
                    $isExpanded = is_array($expandedScheduleIds) && in_array((string) $scheduleId, array_map('strval', $expandedScheduleIds), true);
                @endphp
                <div x-data="{
    isOpen: {{ $isExpanded ? 'true' : 'false' }},
    toggle() {
        this.isOpen = !this.isOpen;
        if (this.isOpen) {
            this.$el.dispatchEvent(new CustomEvent('schedule:opened', { detail: { id: '{{ $scheduleId }}' } }));
        }
    },
    open() {
        this.isOpen = true;
        this.$el.dispatchEvent(new CustomEvent('schedule:opened', { detail: { id: '{{ $scheduleId }}' } }));
    }
}" data-schedule-id="{{ $scheduleId }}"
    class="border border-border rounded-md overflow-hidden">
        <div class="bg-muted/50 px-6 py-3 cursor-pointer flex justify-between items-center" @click="toggle()">
                        <h4 class="font-medium text-foreground flex items-center gap-2">
                            <span>{{ $scheduleName }}</span>
                            <svg class="w-4 h-4 mt-1 inline-block text-gray-500"
                                 :class="{ 'transform rotate(180)': !isOpen }"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                 style="transition: transform 0.2s;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </h4>
                        <div class="text-sm text-muted-foreground">
                            {{ $studentsCount }} santri
                        </div>
                    </div>

                    <div x-show="isOpen" x-transition class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-muted-foreground uppercase bg-muted">
                                <tr>
                                    <th class="px-6 py-3">No. Pendaftaran</th>
                                    <th class="px-6 py-3">Nama</th>
                                    <th class="px-6 py-3">Wali</th>
                                    <th class="px-6 py-3">Tanggal Daftar</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @foreach ($students as $student)
                                    <tr class="hover:bg-muted/50 transition-colors">
                                        <td class="px-6 py-4 font-mono text-muted-foreground">{{ $student->registration_number ?? '-' }}</td>
                                        <td class="px-6 py-4 font-medium text-foreground">{{ $student->full_name }}</td>
                                        <td class="px-6 py-4">
                                            <span class="block font-medium text-foreground text-sm">{{ $student->guardian->full_name ?? '-' }}</span>
                                            @if($student->guardian?->wa_link)
                                                <a href="https://wa.me/{{ $student->guardian->wa_link }}" target="_blank"
                                                    class="text-xs text-green-600 hover:text-green-700 font-mono">
                                                    📱 {{ $student->guardian->whatsapp }}
                                                </a>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-muted-foreground">{{ $student->created_at->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                                {{ $student->getStatusEnum()?->getLabel() ?? 'Menunggu' }}
                                            </span>
                                        </td>
                                         <td class="px-6 py-4">
                                             <button type="button"
                                                 class="text-blue-600 hover:text-blue-800 font-medium mr-3"
                                                 onclick="showDocumentsModal('{{ addslashes($student->full_name) }}', '{{ $student->kk_url }}', '{{ $student->akta_url }}', '{{ $student->ijazah_url }}', '{{ $student->nisn_document_url }}', '{{ $student->foto_url }}')">
                                                 Lihat Berkas
                                             </button>
                                             <a href="{{ route('admin.student-acceptance-confirm', $student) }}"
                                                 class="text-primary hover:text-primary/80 font-medium mr-3">
                                                 Tinjau & Terima
                                             </a>
                                             <form method="POST" action="{{ route('admin.students.reject', $student) }}" style="display:inline;">
                                                 @csrf
                                                 <button type="button" class="text-red-600 hover:text-red-800 font-medium"
                                                    onclick="confirmRejection(this, '{{ addslashes($student->full_name) }}', '{{ $student->guardian->wa_link ?? '' }}', '{{ addslashes($student->guardian->full_name ?? '') }}', '{{ $student->guardian->whatsapp ?? '' }}', '{{ addslashes($student->spmbSchedule->name ?? 'Tidak ada jadwal') }}', '{{ $student->spmb_schedule_id ?? '' }}')">
                                                     Tolak
                                                 </button>
                                             </form>
                                         </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-muted-foreground">
                    Tidak ada santri yang menunggu konfirmasi.
                </div>
            @endforelse
        </div>
    </div>



    @script
    <script>
        window.showDocumentsModal = function(name, kk, akta, ijazah, nisn, foto) {
            let htmlContent = `
                <div class="text-left space-y-4">
                    <p class="text-sm text-gray-600 mb-4">Berikut adalah berkas lampiran yang telah diunggah oleh calon santri <strong>${name}</strong>:</p>
                    <div class="space-y-3">
                        <!-- KK -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-150" style="display: flex; align-items: center; justify-content: space-between; padding: 12px; margin-bottom: 8px;">
                            <div class="flex items-center space-x-3" style="display: flex; align-items: center; gap: 12px;">
                                <span class="p-1.5 bg-blue-50 text-blue-600 rounded" style="padding: 6px; background-color: #eff6ff; color: #2563eb; border-radius: 4px; display: inline-flex;">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-850" style="font-size: 14px; font-weight: 600; margin: 0;">Kartu Keluarga (KK)</p>
                                </div>
                            </div>
                            <div>
                                ${kk ? `<a href="${kk}" target="_blank" class="inline-flex items-center justify-center px-3 py-1 bg-primary text-primary-foreground text-xs font-semibold rounded hover:bg-primary/95 transition shadow-sm" style="padding: 4px 12px; background-color: #2563eb; color: #ffffff; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: 600;">Lihat File</a>` : `<span class="px-2.5 py-1 bg-gray-150 text-gray-500 text-xs rounded font-semibold" style="padding: 4px 10px; background-color: #f3f4f6; color: #6b7280; border-radius: 4px; font-size: 12px;">Belum Ada</span>`}
                            </div>
                        </div>

                        <!-- Akta -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-150" style="display: flex; align-items: center; justify-content: space-between; padding: 12px; margin-bottom: 8px;">
                            <div class="flex items-center space-x-3" style="display: flex; align-items: center; gap: 12px;">
                                <span class="p-1.5 bg-blue-50 text-blue-600 rounded" style="padding: 6px; background-color: #eff6ff; color: #2563eb; border-radius: 4px; display: inline-flex;">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-850" style="font-size: 14px; font-weight: 600; margin: 0;">Akta Kelahiran</p>
                                </div>
                            </div>
                            <div>
                                ${akta ? `<a href="${akta}" target="_blank" class="inline-flex items-center justify-center px-3 py-1 bg-primary text-primary-foreground text-xs font-semibold rounded hover:bg-primary/95 transition shadow-sm" style="padding: 4px 12px; background-color: #2563eb; color: #ffffff; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: 600;">Lihat File</a>` : `<span class="px-2.5 py-1 bg-gray-150 text-gray-500 text-xs rounded font-semibold" style="padding: 4px 10px; background-color: #f3f4f6; color: #6b7280; border-radius: 4px; font-size: 12px;">Belum Ada</span>`}
                            </div>
                        </div>

                        <!-- Ijazah -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-150" style="display: flex; align-items: center; justify-content: space-between; padding: 12px; margin-bottom: 8px;">
                            <div class="flex items-center space-x-3" style="display: flex; align-items: center; gap: 12px;">
                                <span class="p-1.5 bg-blue-50 text-blue-600 rounded" style="padding: 6px; background-color: #eff6ff; color: #2563eb; border-radius: 4px; display: inline-flex;">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-850" style="font-size: 14px; font-weight: 600; margin: 0;">Ijazah Terakhir</p>
                                </div>
                            </div>
                            <div>
                                ${ijazah ? `<a href="${ijazah}" target="_blank" class="inline-flex items-center justify-center px-3 py-1 bg-primary text-primary-foreground text-xs font-semibold rounded hover:bg-primary/95 transition shadow-sm" style="padding: 4px 12px; background-color: #2563eb; color: #ffffff; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: 600;">Lihat File</a>` : `<span class="px-2.5 py-1 bg-gray-150 text-gray-500 text-xs rounded font-semibold" style="padding: 4px 10px; background-color: #f3f4f6; color: #6b7280; border-radius: 4px; font-size: 12px;">Belum Ada</span>`}
                            </div>
                        </div>

                        <!-- NISN Document -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-150" style="display: flex; align-items: center; justify-content: space-between; padding: 12px; margin-bottom: 8px;">
                            <div class="flex items-center space-x-3" style="display: flex; align-items: center; gap: 12px;">
                                <span class="p-1.5 bg-blue-50 text-blue-600 rounded" style="padding: 6px; background-color: #eff6ff; color: #2563eb; border-radius: 4px; display: inline-flex;">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-850" style="font-size: 14px; font-weight: 600; margin: 0;">Dokumen NISN</p>
                                </div>
                            </div>
                            <div>
                                ${nisn ? `<a href="${nisn}" target="_blank" class="inline-flex items-center justify-center px-3 py-1 bg-primary text-primary-foreground text-xs font-semibold rounded hover:bg-primary/95 transition shadow-sm" style="padding: 4px 12px; background-color: #2563eb; color: #ffffff; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: 600;">Lihat File</a>` : `<span class="px-2.5 py-1 bg-gray-150 text-gray-500 text-xs rounded font-semibold" style="padding: 4px 10px; background-color: #f3f4f6; color: #6b7280; border-radius: 4px; font-size: 12px;">Belum Ada</span>`}
                            </div>
                        </div>

                        <!-- Foto -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-150" style="display: flex; align-items: center; justify-content: space-between; padding: 12px; margin-bottom: 8px;">
                            <div class="flex items-center space-x-3" style="display: flex; align-items: center; gap: 12px;">
                                <span class="p-1.5 bg-blue-50 text-blue-600 rounded" style="padding: 6px; background-color: #eff6ff; color: #2563eb; border-radius: 4px; display: inline-flex;">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-850" style="font-size: 14px; font-weight: 600; margin: 0;">Pas Foto</p>
                                </div>
                            </div>
                            <div>
                                ${foto ? `<a href="${foto}" target="_blank" class="inline-flex items-center justify-center px-3 py-1 bg-primary text-primary-foreground text-xs font-semibold rounded hover:bg-primary/95 transition shadow-sm" style="padding: 4px 12px; background-color: #2563eb; color: #ffffff; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: 600;">Lihat File</a>` : `<span class="px-2.5 py-1 bg-gray-150 text-gray-500 text-xs rounded font-semibold" style="padding: 4px 10px; background-color: #f3f4f6; color: #6b7280; border-radius: 4px; font-size: 12px;">Belum Ada</span>`}
                            </div>
                        </div>
                    </div>
                </div>
            `;

            window.Swal.fire({
                title: 'Dokumen Lampiran',
                html: htmlContent,
                showConfirmButton: false,
                showCloseButton: true,
                cancelButtonText: 'Tutup',
                showCancelButton: true,
                cancelButtonColor: '#6c757d',
                width: '500px'
            });
        };

        window.confirmRejection = function(button, studentName, whatsapp, guardianName, guardianPhone, spmbName, scheduleId) {
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
                    const select = Swal.getHtmlContainer().querySelector('#rejection-template');
                    const textarea = Swal.getHtmlContainer().querySelector('#rejection-reason');

                    textarea.value = select.value;

                    select.addEventListener('change', (e) => {
                        if (e.target.value === 'custom') {
                            textarea.value = '';
                            textarea.focus();
                        } else {
                            textarea.value = e.target.value;
                        }
                    });
                },
                preConfirm: () => {
                    const textarea = Swal.getHtmlContainer().querySelector('#rejection-reason');
                    const sendWa = Swal.getHtmlContainer().querySelector('#send-wa').checked;
                    const value = textarea.value.trim();
                    if (!value) {
                        Swal.showValidationMessage('Alasan penolakan wajib diisi!');
                        return false;
                    }
                    return { reason: value, sendWa: sendWa };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const { reason, sendWa } = result.value;

                    submitSystemRejection(button, reason, scheduleId).then((success) => {
                        if (!success) {
                            return;
                        }

                        if (sendWa && whatsapp) {
                            const defaultMessage = `*Pemberitahuan Pendaftaran Santri Baru*\n*An-Nawawiy*\n\nAssalamu'alaikum Warahmatullahi Wabarakatuh,\n\nYth. Bapak/Ibu *${guardianName}*\n(${guardianPhone})\n\nMohon maaf, berdasarkan hasil verifikasi dokumen pada jadwal:\n*${spmbName}*\n\nPendaftaran santri atas nama:\n*${studentName}*\n\n*Belum dapat kami terima* dengan alasan:\n\n_"${reason}"_\n\nSilakan melengkapi berkas atau menghubungi bagian administrasi untuk informasi lebih lanjut.\n\nSyukron, Jazakumullahu Khairan.\n\n---\n_Pesan otomatis dari Sistem Informasi Santri An-Nawawiy_`;

                            showWaPreviewModal(button, reason, whatsapp, defaultMessage);
                        } else {
                            Swal.fire({
                                title: 'Berhasil Ditolak!',
                                text: 'Pendaftaran santri telah ditolak dan tersimpan di sistem.',
                                icon: 'success',
                                confirmButtonText: 'Tutup'
                            });
                        }
                    });
                }
            });
        };

        function showWaPreviewModal(button, reason, whatsapp, message) {
            window.Swal.fire({
                title: 'Berhasil Ditolak!',
                text: 'Data telah diupdate di sistem. Silakan tinjau pesan WhatsApp berikut:',
                icon: 'success',
                allowOutsideClick: false,
                showCloseButton: false,
                html: `
                    <div class="text-left mt-4">
                        <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">Preview Pesan WhatsApp:</label>
                        <div class="p-3 bg-gray-50 border border-gray-200 rounded text-sm font-mono whitespace-pre-wrap max-h-60 overflow-y-auto mb-4" id="wa-preview">${message}</div>

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

                    copyBtn.addEventListener('click', () => {
                        navigator.clipboard.writeText(message);
                        copyBtn.innerText = 'Tersalin!';
                        setTimeout(() => copyBtn.innerText = 'Copy Pesan', 2000);
                    });

                    editBtn.addEventListener('click', () => {
                        Swal.fire({
                            title: 'Edit Pesan WhatsApp',
                            input: 'textarea',
                            inputValue: message,
                            allowOutsideClick: false,
                            showCloseButton: false,
                            inputAttributes: {
                                'rows': '12',
                                'style': 'height: 300px; font-family: monospace; font-size: 14px;'
                            },
                            showCancelButton: true,
                            confirmButtonText: 'Simpan',
                            preConfirm: (val) => {
                                if (!val) return Swal.showValidationMessage('Pesan tidak boleh kosong!');
                                return val;
                            }
                        }).then((editResult) => {
                            if (editResult.isConfirmed) {
                                showWaPreviewModal(button, reason, whatsapp, editResult.value);
                            } else {
                                showWaPreviewModal(button, reason, whatsapp, message);
                            }
                        });
                    });

                    sendBtn.addEventListener('click', () => {
                        const encodedMsg = encodeURIComponent(message);
                        window.open(`https://wa.me/${whatsapp}?text=${encodedMsg}`, '_blank');
                        Swal.close();
                    });
                },
                footer: '<button type="button" class="text-gray-500 hover:underline" onclick="Swal.close(); location.reload();">Tutup (Tanpa Kirim WA)</button>'
            });
        }

        window.expandSchedulePanel = function(scheduleId) {
            if (!scheduleId) return;

            const panel = document.querySelector(`[data-schedule-id="${scheduleId}"]`);
            if (!panel) return;

            const data = Alpine.$data(panel);
            if (data && typeof data.open === 'function') {
                data.open();
            }
        };

        function syncExpandedScheduleState() {
            const panels = Array.from(document.querySelectorAll('[data-schedule-id]'));
            const current = JSON.parse(sessionStorage.getItem('expanded_schedule_ids') || '[]');
            const visibleIds = panels.map((panel) => panel.getAttribute('data-schedule-id'));

            const activeIds = current.filter((id) => visibleIds.includes(id));
            sessionStorage.setItem('expanded_schedule_ids', JSON.stringify(activeIds));

            if (panels.length > 0) {
                const shouldAutoExpand = panels.length <= 2;
                panels.forEach((panel, index) => {
                    const id = panel.getAttribute('data-schedule-id');
                    const data = Alpine.$data(panel);
                    if (!data) return;

                    const isPreferred = shouldAutoExpand && index === 0;
                    const shouldOpen = isPreferred || activeIds.includes(id);

                    if (shouldOpen && !data.isOpen) {
                        data.open();
                    } else if (!shouldOpen && data.isOpen) {
                        data.isOpen = false;
                    }
                });
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-schedule-id]').forEach((panel) => {
                panel.addEventListener('schedule:opened', (event) => {
                    const id = event.detail?.id;
                    if (!id) return;

                    const current = JSON.parse(sessionStorage.getItem('expanded_schedule_ids') || '[]');
                    const next = Array.from(new Set([...current, id]));
                    sessionStorage.setItem('expanded_schedule_ids', JSON.stringify(next));
                });
            });

            syncExpandedScheduleState();
        });

        document.addEventListener('livewire:navigated', syncExpandedScheduleState);
        document.addEventListener('livewire:load', syncExpandedScheduleState);

        async function submitSystemRejection(button, reason, scheduleId) {
            let form = button.closest('form');
            let csrfToken = form.querySelector('input[name="_token"]').value;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: new URLSearchParams({
                        _token: csrfToken,
                        reason: reason
                    })
                });

                const data = await response.json().catch(() => null);

                if (!response.ok || !data?.success) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menolak',
                        text: data?.message || 'Penolakan gagal diproses.',
                        confirmButtonText: 'Tutup'
                    });
                    return false;
                }

                if (window.Livewire) {
                    window.Livewire.dispatch('refreshStudentAcceptance');
                }

                if (scheduleId) {
                    window.expandSchedulePanel(scheduleId);
                }

                return true;
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menolak',
                    text: 'Penolakan gagal diproses karena terjadi kesalahan jaringan.',
                    confirmButtonText: 'Tutup'
                });
                return false;
            }
        }
    </script>
    @endscript
</div>