<div>
    <x-slot name="header">
        Manajemen Santri
    </x-slot>

    <div class="bg-card rounded-lg shadow-sm border border-border">
        <div class="p-6 border-b border-border flex justify-between items-center">
            <h3 class="text-lg font-semibold text-card-foreground">Daftar Santri</h3>
            <div class="flex space-x-4">
                <input wire:model.live="search" type="text" placeholder="Cari santri..."
                    class="px-4 py-2 border border-input bg-background rounded-md focus:outline-none focus:ring-2 focus:ring-ring text-foreground">
                <a href="{{ route('admin.students.create') }}"
                    class="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 text-sm font-medium">
                    + Tambah Santri
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-muted-foreground uppercase bg-muted">
                    <tr>
                        <th class="px-6 py-3">NIS</th>
                        <th class="px-6 py-3">Nama Lengkap</th>
                        <th class="px-6 py-3">Unit</th>
                        <th class="px-6 py-3">Tempat Tinggal</th>
                        <th class="px-6 py-3">Kelas</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach ($students as $student)
                        <tr class="hover:bg-muted/50 transition-colors">
                            <td class="px-6 py-4 font-mono text-muted-foreground">{{ $student->nis }}</td>
                            <td class="px-6 py-4 font-medium text-foreground">{{ $student->full_name }}</td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 rounded-full text-xs font-medium bg-secondary text-secondary-foreground">
                                    {{ $student->unit_code == '01' ? 'SMP' : ($student->unit_code == '02' ? 'SMA' : 'PPTQ') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-muted-foreground">{{ $student->residence_status }}</td>
                            <td class="px-6 py-4 text-muted-foreground">{{ $student->class_name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 rounded-full text-xs font-medium {{ $student->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $student->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.students.edit', $student) }}"
                                    class="text-primary hover:text-primary/80 font-medium mr-2">Ubah</a>
                                <a href="#"
                                    class="text-muted-foreground hover:text-foreground font-medium">Lihat</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-border">
            {{ $students->links() }}
        </div>
    </div>
</div>
