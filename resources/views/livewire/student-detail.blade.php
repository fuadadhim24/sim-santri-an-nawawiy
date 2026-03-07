<div>
    <x-slot name="header">
        Detail Santri
    </x-slot>

    <div class="space-y-6">
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
                    @if (Auth::user()->role === 'SUPER_ADMIN' || Auth::user()->role === 'ADMIN_TU')
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
                    @if (Auth::user()->role === 'SUPER_ADMIN' || Auth::user()->role === 'ADMIN_TU')
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
