<div>
    <x-slot name="header">
        Buat Tagihan Manual
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-card rounded-lg shadow-sm border border-border p-6">
            <div class="mb-6">
                <h3 class="text-lg font-medium text-foreground">Buat Tagihan Baru</h3>
                <p class="text-sm text-muted-foreground">
                    Pilih santri dan jenis biaya untuk membuat tagihan secara manual.
                </p>
            </div>

            <form wire:submit="save" class="space-y-6">
                <!-- Student Selection with Search Dropdown -->
                <div class="relative" x-data="{ open: false }" x-on:click.outside="open = false">
                    <label for="student_search" class="block text-sm font-medium text-foreground">Santri <span class="text-red-500">*</span></label>
                    <div class="relative mt-1">
                        <input wire:model.live="student_search" type="text" id="student_search"
                            x-on:focus="open = true"
                            placeholder="Ketik nama atau NIS santri..."
                            class="block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm"
                            autocomplete="off" />
                        
                        @if ($student_id)
                            <button type="button" wire:click="clearStudentSelection" x-on:click="open = false" class="absolute inset-y-0 right-0 pr-3 flex items-center text-muted-foreground hover:text-foreground">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        @endif
                    </div>

                    <!-- Dropdown Results -->
                    <div x-show="open"
                        class="absolute z-10 mt-1 w-full bg-card border border-border rounded-md shadow-lg max-h-60 overflow-y-auto"
                        style="display: none;">
                        @if (count($this->searchResults) > 0)
                            <ul class="py-1 text-sm text-foreground">
                                @foreach ($this->searchResults as $student)
                                    <li>
                                        <button type="button" x-on:click="open = false" wire:click="selectStudent({{ $student->id }}, '{{ addslashes($student->full_name) }}', '{{ $student->nis }}')"
                                            class="w-full text-left px-4 py-2 hover:bg-muted focus:bg-muted focus:outline-none transition-colors">
                                            <span class="font-medium block">{{ $student->full_name }}</span>
                                            <span class="text-xs text-muted-foreground">NIS: {{ $student->nis }} | Kelas: {{ $student->class_name ?? '-' }}</span>
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="px-4 py-3 text-sm text-muted-foreground text-center">
                                @if (strlen($student_search) < 2)
                                    Ketik minimal 2 karakter untuk mencari...
                                @else
                                    Santri tidak ditemukan.
                                @endif
                            </div>
                        @endif
                    </div>

                    @error('student_id')
                        <span class="text-destructive text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Fee Master Selection -->
                <div>
                    <label for="fee_master_id" class="block text-sm font-medium text-foreground">Jenis Biaya <span class="text-red-500">*</span></label>
                    <select wire:model.live="fee_master_id" id="fee_master_id"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        <option value="">Pilih Jenis Biaya</option>
                        @foreach ($this->feeMasters as $fee)
                            <option value="{{ $fee->id }}">
                                {{ $fee->item_name }} - {{ $fee->category->name ?? 'Tanpa Kategori' }} (Rp {{ number_format($fee->amount, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                    @error('fee_master_id')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-foreground">Judul Tagihan <span class="text-red-500">*</span></label>
                    <input wire:model="title" type="text" id="title"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm"
                        placeholder="contoh: SPP Januari 2026">
                    @error('title')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Amounts -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4" x-data="{
                    formatIDR(val) {
                        if (!val) return '';
                        return val.toString().replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    }
                }">
                    <div>
                        <label for="original_amount" class="block text-sm font-medium text-foreground mb-1">Jumlah Asli <span class="text-red-500">*</span></label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-muted-foreground sm:text-sm font-medium">Rp</span>
                            </div>
                            <input type="text" x-bind:value="formatIDR($wire.original_amount)"
                                x-on:input.debounce.500ms="$wire.original_amount = $event.target.value.replace(/\D/g, ''); $wire.$refresh();" placeholder="500.000"
                                class="block w-full pl-10 pr-4 py-2 border border-input bg-background rounded-md focus:outline-none focus:ring-ring focus:border-ring sm:text-sm font-mono">
                        </div>
                        @error('original_amount')
                            <span class="text-destructive text-sm block mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="discount_applied" class="block text-sm font-medium text-foreground mb-1">Diskon <span class="text-muted-foreground font-normal text-[11px]">(Opsional)</span></label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-muted-foreground sm:text-sm font-medium">Rp</span>
                            </div>
                            <input type="text" x-bind:value="formatIDR($wire.discount_applied)"
                                x-on:input.debounce.500ms="$wire.discount_applied = $event.target.value.replace(/\D/g, ''); $wire.$refresh();" placeholder="50.000"
                                class="block w-full pl-10 pr-4 py-2 border border-input bg-background rounded-md focus:outline-none focus:ring-ring focus:border-ring sm:text-sm font-mono">
                        </div>
                        @error('discount_applied')
                            <span class="text-destructive text-sm block mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="final_amount" class="block text-sm font-medium text-foreground mb-1">Jumlah Akhir</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-muted-foreground sm:text-sm font-medium">Rp</span>
                            </div>
                            <input type="text" x-bind:value="formatIDR($wire.final_amount)" readonly
                                class="block w-full pl-10 pr-4 py-2 border border-input bg-muted rounded-md shadow-sm sm:text-sm text-muted-foreground font-mono">
                        </div>
                        @error('final_amount')
                            <span class="text-destructive text-sm block mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <a href="{{ route('admin.billings') }}"
                        class="px-4 py-2 border border-input rounded-md shadow-sm text-sm font-medium text-foreground bg-background hover:bg-muted focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                        Batal
                    </a>
                    <button type="submit" wire:loading.attr="disabled" wire:target="save"
                        class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="save">Buat Tagihan</span>
                        <span wire:loading wire:target="save">Memproses...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
