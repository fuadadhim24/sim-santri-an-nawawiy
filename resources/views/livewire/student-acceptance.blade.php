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
                                        <a href="{{ route('admin.student-acceptance-confirm', $student) }}"
                                           class="text-primary hover:text-primary/80 font-medium mr-2">
                                            Terima
                                        </a>
                                        <form method="POST" action="{{ route('admin.students.reject', $student) }}" style="display:inline;">
                                            @csrf
                                            <button type="button" class="text-muted-foreground hover:text-foreground font-medium" onclick="Swal.fire({title: 'Konfirmasi', text: 'Tolak santri {{ addslashes($student->full_name) }}?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Ya, Tolak', cancelButtonText: 'Batal'}).then((result) => { if(result.isConfirmed) this.closest('form').submit(); })">
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

</div>
