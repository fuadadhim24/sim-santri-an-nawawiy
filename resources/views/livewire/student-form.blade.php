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
                            <input wire:model="nisn" type="text" id="nisn" placeholder="Contoh: 0123456789 (10 digit)" maxlength="10" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
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
                        <input wire:model="nisn" type="text" id="nisn" placeholder="Contoh: 0123456789 (10 digit)" maxlength="10" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
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

                <!-- Document Upload Section -->
                <div class="border-t border-border pt-6 mt-6">
                    <h4 class="text-sm font-semibold text-foreground mb-4 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Berkas Dokumen Pendukung
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Kartu Keluarga (KK) -->
                        <div class="space-y-1.5">
                            <label for="kk_file" class="block text-xs font-semibold text-foreground flex justify-between items-center">
                                <span>Kartu Keluarga (KK) <span class="text-red-500">*</span></span>
                                @if($isEdit && $student->kk)
                                    <a href="{{ asset('storage/' . $student->kk) }}" target="_blank" class="text-primary hover:underline text-[10px] flex items-center">
                                        <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        Lihat Berkas saat ini
                                    </a>
                                @endif
                            </label>
                            <input type="file" wire:model="kk_file" id="kk_file" accept=".jpg,.jpeg,.png,.webp,.pdf"
                                class="w-full px-3 py-1.5 border border-border bg-background text-foreground rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-ring">
                            @error('kk_file')
                                <span class="text-destructive text-[11px] mt-0.5 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Foto Santri -->
                        <div class="space-y-1.5">
                            <label for="foto_file" class="block text-xs font-semibold text-foreground flex justify-between items-center">
                                <span>Pas Foto <span class="text-red-500">*</span></span>
                                @if($isEdit && $student->foto)
                                    <a href="{{ asset('storage/' . $student->foto) }}" target="_blank" class="text-primary hover:underline text-[10px] flex items-center">
                                        <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        Lihat Berkas saat ini
                                    </a>
                                @endif
                            </label>
                            <input type="file" wire:model="foto_file" id="foto_file" accept=".jpg,.jpeg,.png,.webp"
                                class="w-full px-3 py-1.5 border border-border bg-background text-foreground rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-ring">
                            @error('foto_file')
                                <span class="text-destructive text-[11px] mt-0.5 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Akta Kelahiran -->
                        <div class="space-y-1.5">
                            <label for="akta_file" class="block text-xs font-semibold text-foreground flex justify-between items-center">
                                <span>Akta Kelahiran <span class="text-red-500">*</span></span>
                                @if($isEdit && $student->akta)
                                    <a href="{{ asset('storage/' . $student->akta) }}" target="_blank" class="text-primary hover:underline text-[10px] flex items-center">
                                        <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        Lihat Berkas saat ini
                                    </a>
                                @endif
                            </label>
                            <input type="file" wire:model="akta_file" id="akta_file" accept=".jpg,.jpeg,.png,.webp,.pdf"
                                class="w-full px-3 py-1.5 border border-border bg-background text-foreground rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-ring">
                            @error('akta_file')
                                <span class="text-destructive text-[11px] mt-0.5 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Ijazah Terakhir -->
                        <div class="space-y-1.5">
                            <label for="ijazah_file" class="block text-xs font-semibold text-foreground flex justify-between items-center">
                                <span>Ijazah Terakhir <span class="text-muted-foreground font-normal text-[11px]">(Opsional)</span></span>
                                @if($isEdit && $student->ijazah)
                                    <a href="{{ asset('storage/' . $student->ijazah) }}" target="_blank" class="text-primary hover:underline text-[10px] flex items-center">
                                        <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        Lihat Berkas saat ini
                                    </a>
                                @endif
                            </label>
                            <input type="file" wire:model="ijazah_file" id="ijazah_file" accept=".jpg,.jpeg,.png,.webp,.pdf"
                                class="w-full px-3 py-1.5 border border-border bg-background text-foreground rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-ring">
                            @error('ijazah_file')
                                <span class="text-destructive text-[11px] mt-0.5 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Dokumen NISN -->
                        <div class="space-y-1.5">
                            <label for="nisn_document_file" class="block text-xs font-semibold text-foreground flex justify-between items-center">
                                <span>Dokumen NISN <span class="text-muted-foreground font-normal text-[11px]">(Opsional)</span></span>
                                @if($isEdit && $student->nisn_document)
                                    <a href="{{ asset('storage/' . $student->nisn_document) }}" target="_blank" class="text-primary hover:underline text-[10px] flex items-center">
                                        <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        Lihat Berkas saat ini
                                    </a>
                                @endif
                            </label>
                            <input type="file" wire:model="nisn_document_file" id="nisn_document_file" accept=".jpg,.jpeg,.png,.webp,.pdf"
                                class="w-full px-3 py-1.5 border border-border bg-background text-foreground rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-ring">
                            @error('nisn_document_file')
                                <span class="text-destructive text-[11px] mt-0.5 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                @if (!$isEdit && !empty($availableBillings))
                    <!-- Auto-generate Billings Checkbox -->
                    <div class="border-t border-border pt-6 mt-6">
                        <div class="flex items-center space-x-3 mb-6">
                            <input type="checkbox" wire:model.live="autoGenerateBillings" id="autoGenerateBillings"
                                class="h-4 w-4 text-primary focus:ring-primary border-input rounded">
                            <label for="autoGenerateBillings" class="text-sm font-medium text-foreground cursor-pointer">
                                Generate tagihan otomatis untuk santri baru
                            </label>
                        </div>

                        @if ($autoGenerateBillings)
                            <!-- Fee Category Selection -->
                            <div>
                                <h4 class="text-md font-medium text-foreground mb-4 flex items-center justify-between">
                                    Pilih Kategori Tagihan
                                    <div x-data="{ showInfo: false }" class="relative flex items-center">
                                        <button type="button" @click="showInfo = !showInfo" @click.outside="showInfo = false" class="p-1.5 text-muted-foreground hover:text-primary hover:bg-secondary rounded-md transition-colors focus:outline-none" title="Informasi Tagihan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </button>
                                        <div x-show="showInfo" x-transition style="display: none;" class="absolute right-0 top-full mt-2 px-4 py-3 whitespace-nowrap bg-popover text-popover-foreground text-xs rounded-md shadow-lg border border-border z-50 text-center leading-relaxed font-normal">
                                            Disesuaikan otomatis dengan<br>Unit & Domisili.
                                            <div class="absolute -top-1 right-3 w-2 h-2 bg-popover border-t border-l border-border rotate-45"></div>
                                        </div>
                                    </div>
                                </h4>

                                <!-- Select All Checkbox -->
                                <div class="flex items-center justify-between mb-4">
                                    <label class="inline-flex items-center text-xs font-semibold text-foreground cursor-pointer select-none hover:text-primary transition-colors group">
                                        <input type="checkbox" 
                                               wire:click="toggleSelectAllFees" 
                                               {{ count($selectedBillings) === count($availableBillings) && count($availableBillings) > 0 ? 'checked' : '' }}
                                               class="rounded border-input text-primary focus:ring-primary mr-2 transition-all group-hover:border-primary">
                                        Semua Kategori
                                    </label>
                                    <span class="text-[10px] bg-secondary text-secondary-foreground px-2 py-0.5 rounded-full font-bold">
                                        {{ count($selectedBillings) }}/{{ count($availableBillings) }}
                                    </span>
                                </div>

                                <div class="space-y-3 overflow-y-auto pr-2 border border-border rounded-lg p-2 bg-background shadow-inner" style="max-height: 300px;">
                                    @foreach ($availableBillings as $billing)
                                        <div x-data="{ expanded: false }" class="border border-border rounded-lg hover:border-primary transition-all overflow-hidden"
                                             :class="{ 'ring-1 ring-primary border-primary shadow-sm': $wire.selectedBillings.includes({{ $billing['id'] }}) }">
                                            
                                            <!-- Accordion Header -->
                                            <div class="flex items-start p-3 bg-background transition-colors">
                                                <div class="pt-0.5">
                                                    <input type="checkbox"
                                                           wire:model.live="selectedBillings"
                                                           value="{{ $billing['id'] }}"
                                                           class="rounded border-input text-primary focus:ring-primary transition-all cursor-pointer">
                                                </div>
                                                <div class="ml-3 flex-1 cursor-pointer select-none" @click="expanded = !expanded">
                                                    <div class="flex justify-between items-start">
                                                        <div>
                                                            <div class="font-semibold text-sm text-foreground leading-tight">{{ $billing['name'] }}</div>
                                                        </div>
                                                        <button type="button" class="text-muted-foreground p-1 hover:bg-muted rounded-md transition-colors" :class="{ 'rotate-180': expanded }">
                                                            <svg class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                        </button>
                                                    </div>
                                                    
                                                    <div class="text-xs font-medium text-foreground mt-1 font-mono">
                                                        Total: Rp {{ number_format($billing['total_amount'], 0, ',', '.') }}
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Preview Dropdown List -->
                                            <div class="bg-muted p-3 border-t border-border" x-show="expanded" x-transition style="display: none;">
                                                <p class="text-[10px] font-bold text-muted-foreground uppercase tracking-wider mb-2">Rincian Komponen:</p>
                                                <ul class="space-y-2">
                                                    @foreach ($billing['fees'] as $fee)
                                                        <li class="flex justify-between text-xs py-1.5">
                                                            <div class="flex items-start space-x-2">
                                                                <div class="w-1.5 h-1.5 rounded-full bg-primary mt-1.5"></div>
                                                                <div class="flex flex-col">
                                                                    <span class="text-foreground font-medium">{{ $fee['item_name'] }}</span>
                                                                    <div class="flex flex-row flex-wrap items-center gap-1.5 mt-1">
                                                                        @if ($fee['unit'])
                                                                            <span class="inline-flex px-2 py-0.5 rounded bg-blue-100 text-blue-700 font-medium border border-blue-200" style="font-size: 8px; line-height: 1.2;">
                                                                                UNIT: {{ $fee['unit'] == '01' ? 'SMP' : ($fee['unit'] == '02' ? 'SMA' : ($fee['unit'] == '03' ? 'PPTQ' : $fee['unit'])) }}
                                                                            </span>
                                                                        @else
                                                                            <span class="inline-flex px-2 py-0.5 rounded bg-muted text-muted-foreground font-medium border border-border" style="font-size: 8px; line-height: 1.2;">
                                                                                UNIT: SEMUA
                                                                            </span>
                                                                        @endif
                                                                        
                                                                        @if ($fee['domicile'])
                                                                            <span class="inline-flex px-2 py-0.5 rounded bg-purple-100 text-purple-700 font-medium border border-purple-200" style="font-size: 8px; line-height: 1.2;">
                                                                                DOM: {{ $fee['domicile'] }}
                                                                            </span>
                                                                        @else
                                                                            <span class="inline-flex px-2 py-0.5 rounded bg-muted text-muted-foreground font-medium border border-border" style="font-size: 8px; line-height: 1.2;">
                                                                                DOM: SEMUA
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <span class="font-mono text-muted-foreground mt-0.5">Rp {{ number_format($fee['amount'], 0, ',', '.') }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @if (count($selectedBillings) > 0)
                                    <!-- Summary table of selected billings -->
                                    <div class="mt-6 pt-4 border-t border-border">
                                        <h5 class="text-sm font-medium text-foreground mb-3">Ringkasan Rincian Tagihan yang Akan Dibuat:</h5>
                                        <div class="overflow-x-auto border border-border rounded-md">
                                            <table class="min-w-full divide-y divide-border">
                                                <thead class="bg-muted">
                                                    <tr>
                                                        <th class="px-4 py-2 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                                            Nama Tagihan
                                                        </th>
                                                        <th class="px-4 py-2 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                                            Kategori Paket
                                                        </th>
                                                        <th class="px-4 py-2 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                                            Nominal
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-background divide-y divide-border">
                                                    @foreach ($availableBillings as $billing)
                                                        @if (in_array($billing['id'], $selectedBillings))
                                                            @foreach ($billing['fees'] as $fee)
                                                                <tr class="hover:bg-muted/50">
                                                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-foreground font-medium">
                                                                        {{ $fee['item_name'] }}
                                                                    </td>
                                                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-muted-foreground">
                                                                        {{ $billing['name'] }}
                                                                    </td>
                                                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-foreground font-mono">
                                                                        Rp {{ number_format($fee['amount'], 0, ',', '.') }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
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
