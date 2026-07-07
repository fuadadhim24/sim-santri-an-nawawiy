<div>
    <x-slot name="header">
        {{ $isEdit ? 'Ubah DIskon' : 'Buat Diskon' }}
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-card rounded-lg shadow-sm border border-border p-6">
            <form wire:submit="save" class="space-y-6">

                <!-- Fee Master -->
                <div>
                    <label for="fee_master_id" class="block text-sm font-medium text-foreground">Nama Biaya <span class="text-red-500">*</span></label>
                    <select wire:model.live="fee_master_id" id="fee_master_id"
                        {{ $isEdit ? 'disabled' : '' }}
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm {{ $isEdit ? 'bg-muted/40 cursor-not-allowed opacity-80' : '' }}">
                        <option value="">Pilih Nama Biaya...</option>
                        @foreach ($this->feeMasters as $fee)
                            <option value="{{ $fee->id }}">
                                {{ $fee->item_name }} - {{ $fee->category->name ?? 'N/A' }} (Rp
                                {{ number_format($fee->amount, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                    @error('fee_master_id')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Target Status -->
                <div>
                    <label for="target_status" class="block text-sm font-medium text-foreground">Status Target
                        (Penerima) <span class="text-red-500">*</span></label>
                    <select wire:model.live="target_status" id="target_status"
                        {{ $isEdit ? 'disabled' : '' }}
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm {{ $isEdit ? 'bg-muted/40 cursor-not-allowed opacity-80' : '' }}">
                        <option value="ANAK_GURU">Anak Guru</option>
                        <option value="YATIM">Yatim</option>
                        <option value="PRESTASI">Siswa Berprestasi</option>
                    </select>
                    @error('target_status')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Discount Amount -->
                <div>
                    <label for="discount_amount" class="block text-sm font-medium text-foreground">Jumlah Diskon
                        (Rp) <span class="text-red-500">*</span></label>
                    <input wire:model.live="discount_amount" type="number" id="discount_amount" placeholder="e.g. 50000"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                    <p class="mt-1 text-xs text-muted-foreground">Jumlah yang akan dipotong dari biaya asli.</p>
                    @error('discount_amount')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Recalculation Policy (Only shown in Edit mode) -->
                @if($isEdit)
                    <div class="p-4 bg-muted/30 border border-border rounded-lg space-y-3">
                        <label class="block text-xs font-semibold text-gray-700 uppercase">Kebijakan Rekalkulasi Tagihan Aktif:</label>
                        <div class="space-y-2">
                            <label class="flex items-start text-xs text-foreground cursor-pointer select-none">
                                <input type="radio" wire:model.live="recalculate_policy" value="all" class="mt-0.5 text-primary focus:ring-primary border-gray-300 mr-2">
                                <div>
                                    <span class="font-semibold text-slate-800">Ubah Semua Tagihan Belum Lunas</span>
                                    <p class="text-[10px] text-muted-foreground mt-0.5">Semua tagihan (termasuk tagihan lama yang sudah terlambat/jatuh tempo) akan dihitung ulang menggunakan diskon baru.</p>
                                </div>
                            </label>

                            <label class="flex items-start text-xs text-foreground cursor-pointer select-none mt-2">
                                <input type="radio" wire:model.live="recalculate_policy" value="future" class="mt-0.5 text-primary focus:ring-primary border-gray-300 mr-2">
                                <div>
                                    <span class="font-semibold text-slate-800">Hanya Ubah Tagihan Masa Depan (Belum Jatuh Tempo)</span>
                                    <p class="text-[10px] text-muted-foreground mt-0.5">Hanya tagihan yang belum jatuh tempo (jatuh tempo hari ini atau di masa depan) yang akan dihitung ulang. Tagihan yang sudah terlambat tetap menggunakan diskon lama.</p>
                                </div>
                            </label>

                            <label class="flex items-start text-xs text-foreground cursor-pointer select-none mt-2">
                                <input type="radio" wire:model.live="recalculate_policy" value="next_month" class="mt-0.5 text-primary focus:ring-primary border-gray-300 mr-2">
                                <div>
                                    <span class="font-semibold text-slate-800">Mulai Bulan Depan (Kecuali Bulan Ini)</span>
                                    <p class="text-[10px] text-muted-foreground mt-0.5">Hanya tagihan yang jatuh tempo mulai bulan depan yang akan dihitung ulang. Tagihan untuk bulan berjalan ini dan bulan-bulan sebelumnya tidak akan diubah.</p>
                                </div>
                            </label>
                        </div>
                    </div>
                @endif

                <!-- Affected Billings Preview Section -->
                @if($fee_master_id && $target_status)
                    <div class="mt-8 border-t border-border pt-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-bold text-foreground flex items-center gap-2">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Preview Tagihan Terdampak (Belum Lunas)
                            </h4>
                            <span wire:loading.remove wire:target="fee_master_id, target_status, discount_amount, recalculate_policy" class="text-xs px-2.5 py-1 bg-primary/10 text-primary rounded-full font-semibold">
                                {{ count($this->affectedBillings) }} Tagihan
                            </span>
                        </div>

                        <!-- Loading state -->
                        <div wire:loading wire:target="fee_master_id, target_status, discount_amount, recalculate_policy" class="w-full animate-pulse">
                            <div class="flex items-center gap-3 p-3 bg-blue-50/50 border border-blue-200 rounded-lg text-xs text-blue-800">
                                <svg class="animate-spin h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <div class="flex-1">
                                    <span class="font-bold block">Menganalisis Tagihan Terdampak...</span>
                                    <span class="text-[10px] text-blue-600 block mt-0.5">Menghitung ulang nominal sisa tagihan lama & baru untuk santri penerima diskon...</span>
                                </div>
                            </div>
                        </div>

                        <!-- Content state -->
                        <div wire:loading.remove wire:target="fee_master_id, target_status, discount_amount, recalculate_policy" class="border rounded-lg overflow-hidden max-h-[250px] overflow-y-auto">
                            <table class="min-w-full divide-y divide-border text-xs">
                                <thead class="bg-muted sticky top-0 z-10">
                                    <tr>
                                        <th class="px-4 py-2.5 text-left font-semibold text-muted-foreground uppercase">Santri</th>
                                        <th class="px-4 py-2.5 text-left font-semibold text-muted-foreground uppercase">Judul Tagihan</th>
                                        <th class="px-4 py-2.5 text-right font-semibold text-muted-foreground uppercase">Tagihan Asli</th>
                                        <th class="px-4 py-2.5 text-right font-semibold text-muted-foreground uppercase">Diskon Lama</th>
                                        <th class="px-4 py-2.5 text-right font-semibold text-red-600 uppercase bg-red-50/30">Sisa Lama</th>
                                        <th class="px-4 py-2.5 text-right font-semibold text-emerald-600 uppercase bg-emerald-50/30">Sisa Baru</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border bg-background">
                                    @forelse($this->affectedBillings as $b)
                                        <tr class="hover:bg-muted/30 transition-colors">
                                            <td class="px-4 py-2.5">
                                                <span class="font-semibold block text-foreground">{{ $b['student_name'] }}</span>
                                                <span class="text-[10px] text-muted-foreground block">{{ $b['student_nis'] }}</span>
                                            </td>
                                            <td class="px-4 py-2.5 text-muted-foreground">{{ $b['billing_title'] }}</td>
                                            <td class="px-4 py-2.5 text-right font-mono">Rp {{ number_format($b['original_amount'], 0, ',', '.') }}</td>
                                            <td class="px-4 py-2.5 text-right font-mono text-muted-foreground">Rp {{ number_format($b['current_discount'], 0, ',', '.') }}</td>
                                            <td class="px-4 py-2.5 text-right font-mono font-semibold text-red-600 bg-red-50/10">Rp {{ number_format($b['current_final'], 0, ',', '.') }}</td>
                                            <td class="px-4 py-2.5 text-right font-mono font-bold text-emerald-600 bg-emerald-50/10">
                                                Rp {{ number_format($b['new_final'], 0, ',', '.') }}
                                                @if($b['diff'] != 0)
                                                    <span class="block text-[9px] font-semibold {{ $b['diff'] < 0 ? 'text-emerald-500' : 'text-red-500' }}">
                                                        ({{ $b['diff'] < 0 ? '-' : '+' }} Rp {{ number_format(abs($b['diff']), 0, ',', '.') }})
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-8 text-center text-muted-foreground">
                                                Tidak ada tagihan belum lunas yang terdampak oleh aturan diskon ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <div class="flex justify-end space-x-3 pt-4 border-t border-border mt-6">
                    <a href="{{ route('admin.discounts') }}"
                        class="px-4 py-2 border border-input rounded-md shadow-sm text-sm font-medium text-foreground bg-background hover:bg-muted focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                        Batal
                    </a>
                    <button type="submit" wire:loading.attr="disabled" wire:target="save"
                        class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="save">{{ $isEdit ? 'Simpan Diskon' : 'Buat Diskon' }}</span>
                        <span wire:loading wire:target="save">Memproses...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
