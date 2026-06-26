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
                @endphp

                <div class="border-b border-border">
                    <div class="bg-muted/50 px-6 py-3">
                        <h4 class="font-medium text-foreground">{{ $scheduleName }}</h4>
                        <p class="text-sm text-muted-foreground">{{ count($students) }} santri</p>
                    </div>

                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-muted-foreground uppercase bg-muted">
                            <tr>
                                <th class="px-6 py-3">NIS</th>
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
                                    <td class="px-6 py-4 font-mono text-muted-foreground">{{ $student->nis }}</td>
                                    <td class="px-6 py-4 font-medium text-foreground">{{ $student->full_name }}</td>
                                    <td class="px-6 py-4 text-muted-foreground">{{ $student->guardian->full_name ?? '-' }}</td>
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
                                             <button type="button" class="text-muted-foreground hover:text-foreground font-medium" onclick="confirmRejection(this, '{{ addslashes($student->full_name) }}')">
                                                 Tolak
                                             </button>
                                         </form>
                                     </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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

        window.confirmRejection = function(button, studentName) {
            window.Swal.fire({
                title: 'Konfirmasi Penolakan',
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
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tolak',
                cancelButtonText: 'Batal',
                didOpen: () => {
                    const select = Swal.getHtmlContainer().querySelector('#rejection-template');
                    const textarea = Swal.getHtmlContainer().querySelector('#rejection-reason');
                    
                    // Initialize textarea with first template value
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
                    const value = textarea.value.trim();
                    if (!value) {
                        Swal.showValidationMessage('Alasan penolakan wajib diisi!');
                        return false;
                    }
                    return value;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = button.closest('form');
                    let input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'reason';
                    input.value = result.value;
                    form.appendChild(input);
                    form.submit();
                }
            });
        };
    </script>
    @endscript
</div>
