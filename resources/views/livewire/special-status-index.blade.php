<div>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 pb-4 border-b border-border">
                <div>
                    <h1 class="text-2xl font-bold text-foreground">Golongan / Status Khusus</h1>
                    <p class="text-sm text-muted-foreground mt-1">
                        Kelola status khusus santri secara dinamis (seperti Anak Guru, Anak Kurang Mampu, Siswa Berprestasi) untuk kustomisasi pemotongan biaya.
                    </p>
                </div>
                <div class="mt-4 md:mt-0">
                    <button wire:click="create" 
                        class="inline-flex items-center px-4 py-2 bg-primary text-primary-foreground text-sm font-medium rounded-md shadow hover:bg-primary/95 focus:outline-none transition">
                        <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Golongan Baru
                    </button>
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-card rounded-lg border border-border shadow-sm overflow-hidden">
                <!-- Search & Filters -->
                <div class="p-4 bg-muted/40 border-b border-border flex flex-col md:flex-row gap-4 items-center justify-between">
                    <div class="relative w-full md:w-80">
                        <!-- <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span> -->
                        <input wire:model.live="search" type="text" placeholder="Cari golongan..." 
                            class="pl-9 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring text-sm" />
                    </div>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-border">
                        <thead class="bg-muted/50">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-muted-foreground uppercase tracking-wider">Kode Database</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-muted-foreground uppercase tracking-wider">Nama Golongan</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-muted-foreground uppercase tracking-wider">Deskripsi</th>
                                <th scope="col" class="relative px-6 py-3.5">
                                    <span class="sr-only">Aksi</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border bg-card">
                            @forelse($statuses as $status)
                                <tr class="hover:bg-muted/10 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-medium text-foreground">
                                        {{ $status->code }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground">
                                        {{ $status->name }}
                                        @if($status->is_system)
                                            <span class="text-xs text-red-500 ml-1" title="Bawaan Sistem (Terkunci)">🔒</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-muted-foreground">
                                        {{ $status->description ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end gap-3">
                                            <button wire:click="edit({{ $status->id }})" 
                                                class="text-primary hover:text-primary/80 transition-colors">
                                                Edit
                                            </button>
                                            
                                            @if(!$status->is_system)
                                                <button wire:click="confirmDelete({{ $status->id }})" 
                                                    class="text-destructive hover:text-destructive/80 transition-colors">
                                                    Hapus
                                                </button>
                                            @else
                                                <span class="text-muted-foreground/40 cursor-not-allowed select-none">Hapus</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-sm text-muted-foreground">
                                        <svg class="mx-auto h-8 w-8 text-muted-foreground/60 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Tidak ada data golongan yang ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($statuses->hasPages())
                    <div class="px-6 py-4 bg-muted/20 border-t border-border">
                        {{ $statuses->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div></div>

@script
<script>
    $wire.on('swal:show-form', (event) => {
        const data = event[0] || event;
        const isEdit = data.isEdit;
        const id = data.id;
        const code = data.code || '';
        const name = data.name || '';
        const description = data.description || '';
        const isSystem = data.isSystem;
        const isVisible = data.isVisible !== false;

        window.Swal.fire({
            title: isEdit ? 'Edit Golongan' : 'Tambah Golongan Baru',
            html: `
                <div class="text-left font-sans space-y-4 p-1">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kode Database <span class="text-red-500">*</span></label>
                        <input id="swal_code" type="text" 
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm text-black bg-white disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed" 
                            placeholder="Contoh: PRESTASI_1 atau YATIM" value="${code}" ${isSystem ? 'disabled' : ''}>
                        ${isSystem ? '<p class="text-xs text-gray-500 mt-1">🔒 Kode database bawaan sistem terkunci.</p>' : '<p class="text-xs text-gray-500 mt-1">Akan otomatis menjadi format UPPERCASE (spasi diganti underscore).</p>'}
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Golongan <span class="text-red-500">*</span></label>
                        <input id="swal_name" type="text" 
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm text-black bg-white disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed" 
                            placeholder="Contoh: Siswa Berprestasi 1" value="${name}" ${isSystem ? 'disabled' : ''}>
                        ${isSystem ? '<p class="text-xs text-gray-500 mt-1">🔒 Nama golongan bawaan sistem terkunci.</p>' : ''}
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi / Catatan</label>
                        <textarea id="swal_desc" 
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm text-black bg-white" 
                            placeholder="Tulis kriteria singkat..." rows="3">${description}</textarea>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#6b7280',
            allowOutsideClick: false,
            focusConfirm: false,
            preConfirm: () => {
                const inputCode = document.getElementById('swal_code').value.trim();
                const inputName = document.getElementById('swal_name').value.trim();
                const inputDesc = document.getElementById('swal_desc').value.trim();

                if (!isSystem) {
                    if (!inputCode) {
                        window.Swal.showValidationMessage('Kode Database wajib diisi!');
                        return false;
                    }
                    if (!inputName) {
                        window.Swal.showValidationMessage('Nama Golongan wajib diisi!');
                        return false;
                    }
                }

                return { code: inputCode, name: inputName, description: inputDesc };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $wire.saveData(id, result.value.code, result.value.name, result.value.description, true);
            }
        });
    });

    $wire.on('confirm-delete-special-status', (event) => {
        const data = event[0] || event;
        window.Swal.fire({
            title: 'Hapus Golongan?',
            text: `Apakah Anda yakin ingin menghapus golongan "${data.name}" secara permanen?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $wire.executeDelete(data.id);
            }
        });
    });

    $wire.on('swal:error-in-use', (event) => {
        const data = event[0] || event;
        
        let html = `
            <div class="text-left space-y-3 font-sans">
                <p class="text-sm text-gray-600 leading-relaxed">
                    Golongan <strong>${data.name}</strong> tidak dapat dihapus karena masih digunakan di dalam sistem.
                </p>
        `;

        if (data.students && data.students.length > 0) {
            html += `
                <div class="mt-3">
                    <span class="text-xs font-semibold text-gray-700 block mb-1">
                        📌 Digunakan oleh ${data.students.length} santri berikut:
                    </span>
                    <div class="border border-gray-200 rounded-md bg-gray-50 p-2 text-xs text-gray-600 overflow-y-auto" style="max-height: 150px;">
                        <ul class="list-disc pl-4 space-y-1">
                            ${data.students.map(name => `<li>${name}</li>`).join('')}
                        </ul>
                    </div>
                    <p class="text-xs text-gray-500 mt-2 leading-normal">
                        💡 <strong>Langkah yang harus dilakukan:</strong> Masuk ke menu <strong>Santri</strong>, edit santri-santri di atas, lalu ubah status golongannya ke golongan lain (misal: <em>Umum</em>). Setelah semua santri diganti golongannya, Anda baru bisa menghapus golongan ini.
                    </p>
                </div>
            `;
        }

        if (data.discountCount > 0) {
            html += `
                <div class="mt-3 bg-amber-50 border border-amber-200 p-2.5 rounded text-xs text-amber-800 leading-normal">
                    ⚠️ <strong>Digunakan pada ${data.discountCount} aturan diskon biaya:</strong><br>
                    Hapus terlebih dahulu aturan diskon yang menargetkan golongan ini di menu <strong>Diskon</strong> sebelum menghapus golongan ini.
                </div>
            `;
        }

        html += `</div>`;

        window.Swal.fire({
            title: 'Tidak Bisa Dihapus',
            html: html,
            icon: 'warning',
            confirmButtonText: 'Mengerti',
            confirmButtonColor: '#3b82f6'
        });
    });

    $wire.on('swal:success', (event) => {
        const data = event[0] || event;
        window.Swal.fire({
            icon: 'success',
            title: data.title || 'Berhasil',
            text: data.text || '',
            timer: 2000,
            showConfirmButton: false
        });
    });

    $wire.on('swal:error', (event) => {
        const data = event[0] || event;
        window.Swal.fire({
            icon: 'error',
            title: data.title || 'Gagal',
            text: data.text || ''
        });
    });
</script>
@endscript
