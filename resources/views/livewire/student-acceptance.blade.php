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

                @if (!empty($selectedStudents))
                    <button wire:click="confirmAccept"
                        class="inline-flex items-center justify-center py-2 px-4 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium transition-colors">
                        Terima Terpilih ({{ count($selectedStudents) }})
                    </button>
                    <button wire:click="confirmReject"
                        class="inline-flex items-center justify-center py-2 px-4 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium transition-colors">
                        Tolak Terpilih ({{ count($selectedStudents) }})
                    </button>
                @endif
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
                                <th class="px-6 py-3 w-10">
                                    <input type="checkbox" wire:model.live="selectAll" wire:click="toggleSelectAll"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </th>
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
                                    <td class="px-6 py-4">
                                        <input type="checkbox" wire:model="selectedStudents.{{ $student->id }}" wire:click="toggleStudent({{ $student->id }})"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    </td>
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
                                        <button wire:click="confirmAccept({{ $student->id }})"
                                            class="inline-flex items-center justify-center py-1.5 px-3 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium mr-2 transition-colors">
                                            Terima
                                        </button>
                                        <button wire:click="confirmReject({{ $student->id }})"
                                            class="inline-flex items-center justify-center py-1.5 px-3 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium transition-colors">
                                            Tolak
                                        </button>
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

    <!-- Single Student Accept Confirmation Modal -->
    <x-modal name="confirm-accept" :show="$confirmingAccept" maxWidth="md">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-green-100 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-center text-gray-900 mb-2">Terima Santri?</h3>
            <p class="text-sm text-center text-gray-500 mb-4">
                Apakah Anda yakin ingin menerima santri <strong>{{ $selectedStudent?->full_name }}</strong>?
                <br>
                Tagihan SPMB akan dibuat secara otomatis setelah santri diterima.
            </p>
            <div class="flex justify-center space-x-3">
                <button wire:click="cancelAction"
                    class="inline-flex items-center justify-center py-2 px-4 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium transition-colors">
                    Batal
                </button>
                <button wire:click="acceptStudent"
                    class="inline-flex items-center justify-center py-2 px-4 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium transition-colors">
                    Ya, Terima
                </button>
            </div>
        </div>
    </x-modal>

    <!-- Single Student Reject Confirmation Modal -->
    <x-modal name="confirm-reject" :show="$confirmingReject" maxWidth="md">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-center text-gray-900 mb-2">Tolak Santri?</h3>
            <p class="text-sm text-center text-gray-500 mb-4">
                Apakah Anda yakin ingin menolak santri <strong>{{ $selectedStudent?->full_name }}</strong>?
                <br>
                Tidak ada tagihan yang akan dibuat untuk santri ini.
            </p>
            <div class="flex justify-center space-x-3">
                <button wire:click="cancelAction"
                    class="inline-flex items-center justify-center py-2 px-4 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium transition-colors">
                    Batal
                </button>
                <button wire:click="rejectStudent"
                    class="inline-flex items-center justify-center py-2 px-4 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium transition-colors">
                    Ya, Tolak
                </button>
            </div>
        </div>
    </x-modal>

    <!-- Bulk Reject Confirmation Modal -->
    <x-modal name="confirm-bulk-reject" :show="$confirmingReject && !$selectedStudent" maxWidth="md">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-center text-gray-900 mb-2">Tolak Santri Terpilih?</h3>
            <p class="text-sm text-center text-gray-500 mb-4">
                Apakah Anda yakin ingin menolak <strong>{{ count($selectedStudents) }}</strong> santri terpilih?
                <br>
                Data santri akan dihapus secara permanen.
            </p>
            <div class="flex justify-center space-x-3">
                <button wire:click="cancelAction"
                    class="inline-flex items-center justify-center py-2 px-4 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium transition-colors">
                    Batal
                </button>
                <button wire:click="bulkRejectStudents"
                    class="inline-flex items-center justify-center py-2 px-4 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium transition-colors">
                    Ya, Tolak
                </button>
            </div>
        </div>
    </x-modal>

    <!-- Billing Selection Modal -->
    <x-modal name="billing-selection" :show="$showBillingModal" maxWidth="2xl">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-green-100 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-center text-gray-900 mb-2">Pilih Tagihan</h3>
            <p class="text-sm text-center text-gray-500 mb-6">
                Pilih tagihan yang akan dibuat untuk <strong>{{ count($selectedStudents) }}</strong> santri terpilih.
            </p>

            <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
                @forelse ($billingCategories as $category)
                    <div class="border border-border rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-medium text-foreground">{{ $category->name }}</h4>
                            <input type="checkbox"
                                wire:click="toggleBillingCategory({{ $category->id }})"
                                {{ $this->isCategorySelected($category->id) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>

                        <div class="space-y-2 pl-4">
                            @forelse ($billingFees->get($category->id, collect()) as $fee)
                                <div class="flex items-center justify-between">
                                    <label class="flex items-center space-x-2 cursor-pointer">
                                        <input type="checkbox"
                                            wire:model="selectedBillings"
                                            value="{{ $fee->id }}"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="text-sm text-foreground">{{ $fee->item_name }}</span>
                                    </label>
                                    <span class="text-sm text-muted-foreground">Rp {{ number_format($fee->amount, 0, ',', '.') }}</span>
                                </div>
                            @empty
                                <p class="text-sm text-muted-foreground">Tidak ada tagihan tersedia untuk kategori ini.</p>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted-foreground">Tidak ada kategori tagihan tersedia.</p>
                @endforelse
            </div>

            <div class="flex justify-center space-x-3 mt-6">
                <button wire:click="cancelAction"
                    class="inline-flex items-center justify-center py-2 px-4 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium transition-colors">
                    Batal
                </button>
                <button wire:click="bulkAcceptStudents"
                    class="inline-flex items-center justify-center py-2 px-4 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium transition-colors">
                    Terima Santri & Buat Tagihan
                </button>
            </div>
        </div>
    </x-modal>
</div>
