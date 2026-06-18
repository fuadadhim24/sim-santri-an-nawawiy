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


    <script>
        function confirmRejection(button, studentName) {
            Swal.fire({
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
        }
    </script>
</div>
