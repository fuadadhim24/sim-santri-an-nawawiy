<div>
    <x-slot name="header">
        Entri Pembayaran Tunai
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Search Section -->
        <div class="lg:col-span-1">
            <div class="bg-card rounded-lg shadow-sm border border-border p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Cari Santri</h3>

                <div class="relative">
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Nama atau NIS..."
                        class="w-full px-4 py-2 border border-input bg-background rounded-md focus:outline-none focus:ring-2 focus:ring-ring text-foreground">

                    @if (strlen($search) >= 3)
                        @if (isset($students) && count($students) > 0)
                            <div class="absolute z-10 w-full mt-1 bg-popover border border-border rounded-md shadow-lg">
                                <ul class="py-1">
                                    @foreach ($students as $student)
                                        <li>
                                            <button wire:click="selectStudent({{ $student->id }})"
                                                class="w-full text-left px-4 py-2 text-sm text-popover-foreground hover:bg-muted focus:outline-none">
                                                <span class="font-medium">{{ $student->full_name }}</span>
                                                <span
                                                    class="block text-xs text-muted-foreground">{{ $student->nis }}</span>
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <div
                                class="absolute z-10 w-full mt-1 bg-popover border border-border rounded-md shadow-lg p-4 text-center text-sm text-muted-foreground">
                                Santri tidak ditemukan.
                            </div>
                        @endif
                    @endif
                </div>

                @if ($selectedStudent)
                    <div class="mt-6 p-4 bg-muted rounded-md">
                        <h4 class="font-medium text-foreground">{{ $selectedStudent->full_name }}</h4>
                        <p class="text-sm text-muted-foreground">NIS: {{ $selectedStudent->nis }}</p>
                        <p class="text-sm text-muted-foreground mt-1">
                            {{ $selectedStudent->unit_code == '01' ? 'SMP' : ($selectedStudent->unit_code == '02' ? 'SMA' : 'PPTQ') }}
                            - {{ $selectedStudent->residence_status }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Unpaid Bills Section -->
        <div class="lg:col-span-2">
            <div class="bg-card rounded-lg shadow-sm border border-border p-6 h-full">
                <h3 class="text-lg font-semibold text-foreground mb-4">Tagihan Belum Lunas</h3>

                @if ($selectedStudent)
                    @if (count($unpaidBills) > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs text-muted-foreground uppercase bg-muted">
                                    <tr>
                                        <th class="px-4 py-3">Tagihan</th>
                                        <th class="px-4 py-3">Jumlah</th>
                                        <th class="px-4 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border">
                                    @foreach ($unpaidBills as $bill)
                                        <tr class="hover:bg-muted/50">
                                            <td class="px-4 py-3">
                                                <span
                                                    class="font-medium block text-foreground">{{ $bill->title }}</span>
                                                <span
                                                    class="text-xs text-muted-foreground">{{ $bill->created_at->locale('id')->isoFormat('D MMMM Y') }}</span>
                                            </td>
                                            <td class="px-4 py-3 font-mono">
                                                Rp {{ number_format($bill->final_amount, 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <button wire:click="confirmPayment({{ $bill->id }})"
                                                    class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-xs font-medium">
                                                    Catat Pembayaran
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center h-40 text-muted-foreground">
                            <svg class="h-10 w-10 mb-2 opacity-50" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p>Tidak ada tagihan belum lunas untuk santri ini.</p>
                        </div>
                    @endif
                @else
                    <div class="flex flex-col items-center justify-center h-40 text-muted-foreground">
                        <svg class="h-10 w-10 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <p>Cari dan pilih santri untuk melihat tagihan.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>


    @script
        <script>
            $wire.on('confirm-payment', (event) => {
                const data = event[0] || event;

                window.Swal.fire({
                    title: 'Konfirmasi Pembayaran Tunai',
                    html: `
                    <div class="space-y-3 text-left">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Nama Santri:</span>
                            <span class="text-sm font-semibold">${data.studentName}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Tagihan:</span>
                            <span class="text-sm font-semibold">${data.title}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Tanggal Tagihan:</span>
                            <span class="text-sm font-semibold">${data.date}</span>
                        </div>
                        <div class="flex justify-between border-t border-gray-200 pt-2 mt-2">
                            <span class="text-sm font-semibold text-gray-800">Jumlah:</span>
                            <span class="text-sm font-bold text-blue-600">Rp ${data.amount}</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-2 text-center">Lanjutkan mencatat pembayaran tunai?</p>
                    </div>
                `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Catat Pembayaran',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#16a34a',
                    cancelButtonColor: '#6b7280',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $wire.dispatch('confirmed-payment');
                    }
                });
            });
        </script>
    @endscript
</div>
