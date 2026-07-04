<div>
    <x-slot name="header">
        {{ $isEdit ? 'Ubah Data Santri' : 'Tambah Santri Baru' }}
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-card rounded-lg shadow-sm border border-border p-6">
            <form wire:submit="save" class="space-y-6">

                @if ($isEdit)
                    <!-- NIS Display (Read Only) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-foreground">NIS</label>
                            <div class="mt-1 p-2 bg-muted rounded-md text-muted-foreground font-mono">
                                {{ $generatedNis }}
                            </div>
                        </div>
                        
                        <!-- NISN Input (Edit mode side-by-side with NIS if desired, but let's just use standard flow) -->
                        <div>
                            <label for="nisn" class="block text-sm font-medium text-foreground">NISN <span class="text-muted-foreground font-normal text-[11px]">(Opsional)</span></label>
                            <input wire:model="nisn" type="text" id="nisn" placeholder="Masukkan NISN (opsional)"
                                class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                            @error('nisn')
                                <span class="text-destructive text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                @else
                    <!-- NISN Input (Create mode) -->
                    <div>
                        <label for="nisn" class="block text-sm font-medium text-foreground">NISN <span class="text-muted-foreground font-normal text-[11px]">(Opsional)</span></label>
                        <input wire:model="nisn" type="text" id="nisn" placeholder="Masukkan NISN (opsional)"
                            class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        @error('nisn')
                            <span class="text-destructive text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

                <!-- Full Name -->
                <div>
                    <label for="full_name" class="block text-sm font-medium text-foreground">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input wire:model="full_name" type="text" id="full_name"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                    @error('full_name')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Guardian -->
                <div>
                    <label for="guardian_id" class="block text-sm font-medium text-foreground">Wali Santri <span class="text-red-500">*</span></label>
                    <select wire:model="guardian_id" id="guardian_id"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        <option value="">Pilih Wali Santri...</option>
                        @foreach ($this->guardians as $guardian)
                            <option value="{{ $guardian->id }}">{{ $guardian->full_name }} ({{ $guardian->whatsapp }})
                            </option>
                        @endforeach
                    </select>
                    @error('guardian_id')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Unit -->
                    <div>
                        <label for="unit_code" class="block text-sm font-medium text-foreground">Unit Sekolah <span class="text-red-500">*</span></label>
                        <select wire:model.live="unit_code" id="unit_code"
                            class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                            <option value="01">SMP (01)</option>
                            <option value="02">SMA (02)</option>
                            <option value="03">PPTQ (03)</option>
                        </select>
                        @error('unit_code')
                            <span class="text-destructive text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Residence Status -->
                    <div>
                        <label for="residence_status" class="block text-sm font-medium text-foreground">Status Domisili <span class="text-red-500">*</span></label>
                        <select wire:model.live="residence_status" id="residence_status"
                            class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                            <option value="MONDOK">Mondok</option>
                            <option value="NON_MONDOK">Non Mondok</option>
                            <option value="NGAJI_ONLY">Ngaji Only</option>
                        </select>
                        @error('residence_status')
                            <span class="text-destructive text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Special Status -->
                    <div>
                        <label for="special_status" class="block text-sm font-medium text-foreground">Status
                            Khusus <span class="text-red-500">*</span></label>
                        <select wire:model="special_status" id="special_status"
                            class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                            <option value="UMUM">Umum</option>
                            <option value="ANAK_GURU">Anak Guru</option>
                            <option value="YATIM">Yatim</option>
                        </select>
                        @error('special_status')
                            <span class="text-destructive text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Class Name -->
                    <div>
                        <label for="class_name" class="block text-sm font-medium text-foreground">Nama Kelas <span class="text-muted-foreground font-normal text-[11px]">(Opsional)</span></label>
                        <input wire:model="class_name" type="text" id="class_name" placeholder="contoh: 7A"
                            class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        @error('class_name')
                            <span class="text-destructive text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Address -->
                <div>
                    <label for="address" class="block text-sm font-medium text-foreground">Alamat <span class="text-muted-foreground font-normal text-[11px]">(Opsional)</span></label>
                    <textarea wire:model="address" id="address" rows="3"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm"></textarea>
                    @error('address')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                @if (!$isEdit && $this->matchingFeeMasters->isNotEmpty())
                    <!-- Auto-generate Billings Checkbox -->
                    <div class="border-t border-border pt-6">
                        <div class="flex items-center space-x-3 mb-4">
                            <input type="checkbox" wire:model.live="autoGenerateBillings"
                                class="h-4 w-4 text-primary focus:ring-primary border-input rounded">
                            <label for="autoGenerateBillings" class="text-sm font-medium text-foreground cursor-pointer">
                                Generate tagihan otomatis untuk santri baru
                            </label>
                        </div>

                        @if ($autoGenerateBillings)
                            <!-- Fee Master Selection -->
                            <div>
                                <h4 class="text-md font-medium text-foreground mb-3">Pilih Tagihan yang Akan Dibuat</h4>
                                <p class="text-sm text-muted-foreground mb-4">
                                    Tagihan yang tersedia berdasarkan:
                                    <span class="font-medium text-foreground">Unit {{ $unit_code == '01' ? 'SMP' : ($unit_code == '02' ? 'SMA' : 'PPTQ') }}</span> •
                                    <span class="font-medium text-foreground">{{ $residence_status == 'MONDOK' ? 'Mondok' : ($residence_status == 'NON_MONDOK' ? 'Non Mondok' : 'Ngaji Only') }}</span>
                                </p>

                                <!-- Select All Checkbox -->
                                <div class="flex items-center justify-between mb-3">
                                    <label class="inline-flex items-center text-xs font-semibold text-foreground cursor-pointer select-none">
                                        <input type="checkbox" 
                                               wire:click="toggleSelectAllFees" 
                                               @if(count(array_intersect(array_map('strval', $selectedFeeMasters), $this->matchingFeeMasters->pluck('id')->map(fn($id) => (string) $id)->toArray())) === count($this->matchingFeeMasters)) checked @endif
                                               class="rounded border-input text-primary focus:ring-primary mr-1.5 h-3.5 w-3.5">
                                        Pilih Semua Tagihan
                                    </label>
                                </div>

                                <!-- List of billings with checkboxes -->
                                <div class="space-y-2 max-h-64 overflow-y-auto border border-border rounded-md p-3 bg-muted/30 mb-4">
                                    @foreach ($this->matchingFeeMasters as $fee)
                                        <label class="flex items-start space-x-3 p-2 hover:bg-muted rounded cursor-pointer">
                                            <input type="checkbox" wire:model.live="selectedFeeMasters" value="{{ $fee->id }}"
                                                class="mt-1 h-4 w-4 text-primary focus:ring-primary border-input rounded">
                                            <div class="flex-1">
                                                <span class="text-sm font-medium text-foreground">{{ $fee->item_name }}</span>
                                                <span class="text-xs text-muted-foreground block">
                                                    {{ $fee->category->name ?? 'Tanpa Kategori' }} • Rp {{ number_format($fee->amount, 0, ',', '.') }}
                                                </span>
                                                <span class="text-xs text-muted-foreground block">
                                                    @if ($fee->start_date)
                                                        Mulai: {{ $fee->start_date->format('d M Y') }}
                                                    @endif
                                                    @if ($fee->end_date)
                                                        {{ $fee->start_date ? ' • ' : '' }}Berakhir: {{ $fee->end_date->format('d M Y') }}
                                                    @endif
                                                </span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                <p class="text-xs text-muted-foreground mb-4">
                                    Total {{ count($selectedFeeMasters) }} tagihan dipilih dari {{ count($this->matchingFeeMasters) }} yang tersedia.
                                </p>

                                @if (count($selectedFeeMasters) > 0)
                                    <!-- Summary table of selected billings -->
                                    <div>
                                        <h5 class="text-sm font-medium text-foreground mb-2">Ringkasan Tagihan yang Akan Dibuat:</h5>
                                        <div class="overflow-x-auto border border-border rounded-md">
                                            <table class="min-w-full divide-y divide-border">
                                                <thead class="bg-muted">
                                                    <tr>
                                                        <th class="px-4 py-2 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                                            Nama Tagihan
                                                        </th>
                                                        <th class="px-4 py-2 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                                            Kategori
                                                        </th>
                                                        <th class="px-4 py-2 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                                            Jumlah
                                                        </th>
                                                        <th class="px-4 py-2 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                                            Mulai Berlaku
                                                        </th>
                                                        <th class="px-4 py-2 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                                            Berakhir Pada
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-background divide-y divide-border">
                                                    @foreach ($this->matchingFeeMasters as $fee)
                                                        @if (in_array((string) $fee->id, $selectedFeeMasters))
                                                            <tr class="hover:bg-muted/50">
                                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-foreground">
                                                                    {{ $fee->item_name }}
                                                                </td>
                                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-muted-foreground">
                                                                    {{ $fee->category->name ?? 'Tanpa Kategori' }}
                                                                </td>
                                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-foreground font-medium">
                                                                    Rp {{ number_format($fee->amount, 0, ',', '.') }}
                                                                </td>
                                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-muted-foreground">
                                                                    {{ $fee->start_date ? $fee->start_date->format('d M Y') : '-' }}
                                                                </td>
                                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-muted-foreground">
                                                                    {{ $fee->end_date ? $fee->end_date->format('d M Y') : '-' }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif

                <div class="flex justify-end space-x-3 pt-4">
                    <a href="{{ route('admin.students') }}"
                        class="px-4 py-2 border border-input rounded-md shadow-sm text-sm font-medium text-foreground bg-background hover:bg-muted focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                        Batal
                    </a>
                    <button type="submit" wire:loading.attr="disabled" wire:target="save"
                        class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="save">{{ $isEdit ? 'Simpan Perubahan' : 'Buat Santri' }}</span>
                        <span wire:loading wire:target="save">Memproses...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
