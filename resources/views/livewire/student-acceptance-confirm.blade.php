<div>
    <x-slot name="header">
        Konfirmasi Penerimaan Santri
    </x-slot>

    <div class="max-w-4xl">
        <!-- Back Button -->
        <div class="mb-8">
            <a href="{{ route('admin.student-acceptance') }}" class="text-primary hover:text-primary/80 text-sm font-medium mb-4 inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Daftar Penerimaan
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Student Info Card -->
            <div class="lg:col-span-1">
                <div class="bg-card rounded-lg shadow-sm border border-border p-6">
                    <h2 class="text-lg font-semibold text-card-foreground mb-4">Data Santri</h2>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-muted-foreground font-medium">Nama</p>
                            <p class="font-semibold text-foreground mt-1">{{ $student->full_name }}</p>
                        </div>
                        <div class="border-t border-border pt-3">
                            <p class="text-sm text-muted-foreground font-medium">NIS</p>
                            <p class="font-semibold text-foreground mt-1">{{ $student->nis ?? '-' }}</p>
                        </div>
                        <div class="border-t border-border pt-3">
                            <p class="text-sm text-muted-foreground font-medium">Unit</p>
                            <p class="font-semibold text-foreground mt-1">
                                @switch($student->unit_code)
                                    @case('01') SMP @break
                                    @case('02') SMA @break
                                    @case('03') PPTQ @break
                                    @default -
                                @endswitch
                            </p>
                        </div>
                        <div class="border-t border-border pt-3">
                            <p class="text-sm text-muted-foreground font-medium">Domisili</p>
                            <p class="font-semibold text-foreground mt-1">
                                @switch($student->residence_status)
                                    @case('MONDOK') Mondok @break
                                    @case('NON_MONDOK') Non-Mondok @break
                                    @case('NGAJI_ONLY') Ngaji Only @break
                                    @default -
                                @endswitch
                            </p>
                        </div>
                        <div class="border-t border-border pt-3">
                            <p class="text-sm text-muted-foreground font-medium">Penjaga</p>
                            <p class="font-semibold text-foreground mt-1">{{ $student->guardian?->full_name ?? '-' }}</p>
                        </div>
                        <div class="border-t border-border pt-3">
                            <p class="text-sm text-muted-foreground font-medium">Jadwal SPMB</p>
                            <p class="font-semibold text-foreground mt-1">{{ $student->spmbSchedule?->name ?? '-' }}</p>
                        </div>
                        <div class="border-t border-border pt-3">
                            <p class="text-sm text-muted-foreground font-medium mb-2">Dokumen Lampiran</p>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-muted-foreground">Kartu Keluarga (KK)</span>
                                    @if ($student->kk)
                                        <a href="{{ $student->kk_url }}" target="_blank" class="text-primary hover:underline font-semibold">Lihat</a>
                                    @else
                                        <span class="text-gray-400">Belum Ada</span>
                                    @endif
                                </div>
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-muted-foreground">Akta Kelahiran</span>
                                    @if ($student->akta)
                                        <a href="{{ $student->akta_url }}" target="_blank" class="text-primary hover:underline font-semibold">Lihat</a>
                                    @else
                                        <span class="text-gray-400">Belum Ada</span>
                                    @endif
                                </div>
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-muted-foreground">Ijazah Terakhir</span>
                                    @if ($student->ijazah)
                                        <a href="{{ $student->ijazah_url }}" target="_blank" class="text-primary hover:underline font-semibold">Lihat</a>
                                    @else
                                        <span class="text-gray-400">Belum Ada</span>
                                    @endif
                                </div>
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-muted-foreground">Dokumen NISN</span>
                                    @if ($student->nisn_document)
                                        <a href="{{ $student->nisn_document_url }}" target="_blank" class="text-primary hover:underline font-semibold">Lihat</a>
                                    @else
                                        <span class="text-gray-400">Belum Ada</span>
                                    @endif
                                </div>
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-muted-foreground">Pas Foto</span>
                                    @if ($student->foto)
                                        <a href="{{ $student->foto_url }}" target="_blank" class="text-primary hover:underline font-semibold">Lihat</a>
                                    @else
                                        <span class="text-gray-400">Belum Ada</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Billing Selection Card -->
            <div class="lg:col-span-2">
                <div class="bg-card rounded-lg shadow-sm border border-border p-6">
                    <h2 class="text-lg font-semibold text-card-foreground mb-2">
                        Pilih Tagihan yang Akan Dibuat
                    </h2>
                    <p class="text-sm text-muted-foreground mb-6">
                        Tagihan yang tersedia berdasarkan: <span class="font-semibold text-foreground">Unit dan Domisili</span>
                    </p>

                    @if (empty($availableBillings))
                        <div class="p-6 bg-muted/50 border border-border rounded-lg text-center">
                            <svg class="w-12 h-12 mx-auto text-muted-foreground mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 4v2M7.08 6.47A9 9 0 1119.02 19.02M7.08 6.47L5.6 5"></path>
                            </svg>
                            <p class="font-semibold text-foreground">Tidak ada tagihan yang tersedia</p>
                            <p class="text-muted-foreground text-sm mt-2">Tidak ada kategori tagihan yang sesuai dengan unit dan domisili santri ini.</p>
                        </div>
                    @else
                        <form wire:submit.prevent="confirmAcceptance">
                            <div class="space-y-3 mb-6">
                                @foreach ($availableBillings as $billing)
                                    <label class="flex items-start p-4 border border-border rounded-lg hover:bg-muted/50 cursor-pointer transition-colors"
                                           :class="{ 'bg-accent/10 border-accent': $selectedBillings.includes({{ $billing['id'] }}) }">
                                        <input type="checkbox"
                                               wire:model.live="selectedBillings"
                                               value="{{ $billing['id'] }}"
                                               class="mt-1 rounded border-input">
                                        <div class="ml-3 flex-1">
                                            <div class="font-semibold text-foreground">{{ $billing['name'] }}</div>
                                            @if ($billing['description'])
                                                <p class="text-sm text-muted-foreground mt-1">{{ $billing['description'] }}</p>
                                            @endif
                                            <div class="flex gap-2 mt-2 flex-wrap">
                                                @if ($billing['unit'])
                                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-primary/10 text-primary">
                                                        Unit: {{ $billing['unit'] }}
                                                    </span>
                                                @endif
                                                @if ($billing['domicile'])
                                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-accent/10 text-accent">
                                                        Domisili: {{ $billing['domicile'] }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            <div class="flex gap-3 pt-6 border-t border-border">
                                <button type="button"
                                        wire:click="cancel"
                                        class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-border rounded-lg text-foreground font-medium hover:bg-muted transition-colors">
                                    Batal
                                </button>
                                <button type="submit" wire:loading.attr="disabled" wire:target="confirmAcceptance"
                                        class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-primary text-primary-foreground rounded-lg font-medium hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg wire:loading.remove wire:target="confirmAcceptance" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <svg wire:loading wire:target="confirmAcceptance" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span wire:loading.remove wire:target="confirmAcceptance">Terima & Buat Tagihan</span>
                                    <span wire:loading wire:target="confirmAcceptance">Memproses...</span>
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
