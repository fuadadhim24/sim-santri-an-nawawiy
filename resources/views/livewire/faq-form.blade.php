<div>
    <x-slot name="header">
        {{ $isEdit ? 'Edit FAQ' : 'Tambah FAQ' }}
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-card rounded-lg shadow-sm border border-border p-6">
            <form wire:submit="save" class="space-y-6">

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-foreground">Judul *</label>
                    <input wire:model="title" type="text" id="title" placeholder="Contoh: Jadwal Pendaftaran Santri Baru"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                    @error('title')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category" class="block text-sm font-medium text-foreground">Kategori *</label>
                    <select wire:model.live="category" id="category"
                        class="mt-1 block w-full px-3 py-2 pr-8 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        @foreach($this->categoryOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('category')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Content -->
                <div>
                    <label for="content" class="block text-sm font-medium text-foreground">Isi / Konten *</label>
                    <textarea wire:model="content" id="content" rows="8" placeholder="Tulis informasi detail di sini..."
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm"></textarea>
                    <p class="mt-1 text-xs text-muted-foreground">Anda bisa menulis teks panjang, termasuk daftar biaya, persyaratan, dll.</p>
                    @error('content')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Image Upload -->
                <div>
                    <label class="block text-sm font-medium text-foreground">Gambar (opsional)</label>
                    @if($existingImage)
                        <div class="mt-2 flex items-center space-x-3">
                            <img src="{{ asset('storage/' . $existingImage) }}" alt="Preview" class="h-20 w-auto rounded border border-border">
                            <button type="button" wire:click="removeImage" class="text-sm text-destructive hover:text-destructive/80">Hapus gambar</button>
                        </div>
                    @endif
                    <input wire:model="image" type="file" accept="image/*"
                        class="mt-2 block w-full text-sm text-foreground file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                    <p class="mt-1 text-xs text-muted-foreground">Format: JPG, PNG, GIF. Maks 2MB.</p>
                    @error('image')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- PDF Upload -->
                <div>
                    <label class="block text-sm font-medium text-foreground">Dokumen PDF (opsional)</label>
                    @if($existingPdf)
                        <div class="mt-2 flex items-center space-x-3">
                            <span class="inline-flex items-center px-3 py-1.5 rounded bg-red-50 text-red-700 text-sm">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                PDF terlampir
                            </span>
                            <button type="button" wire:click="removePdf" class="text-sm text-destructive hover:text-destructive/80">Hapus PDF</button>
                        </div>
                    @endif
                    <input wire:model="pdf" type="file" accept=".pdf"
                        class="mt-2 block w-full text-sm text-foreground file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                    <p class="mt-1 text-xs text-muted-foreground">Format: PDF. Maks 5MB.</p>
                    @error('pdf')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Sort Order -->
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-foreground">Urutan Tampil</label>
                        <input wire:model="sort_order" type="number" id="sort_order" min="0"
                            class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        <p class="mt-1 text-xs text-muted-foreground">
                            Angka kecil = tampil lebih atas.<br>
                            <span class="text-primary font-medium">{{ $this->latestOrderInfo }}</span>
                        </p>
                        @error('sort_order')
                            <span class="text-destructive text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Active Toggle -->
                    <div>
                        <label class="block text-sm font-medium text-foreground">Status</label>
                        <label class="mt-3 inline-flex items-center cursor-pointer">
                            <input wire:model="is_active" type="checkbox" class="rounded border-input text-primary shadow-sm focus:ring-ring">
                            <span class="ml-2 text-sm text-foreground">Aktif (tampil di dashboard wali)</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <a href="{{ route('admin.faqs') }}"
                        class="px-4 py-2 border border-input rounded-md shadow-sm text-sm font-medium text-foreground bg-background hover:bg-muted focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                        Batal
                    </a>
                    <button type="submit" wire:loading.attr="disabled" wire:target="save"
                        class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="save">{{ $isEdit ? 'Simpan Perubahan' : 'Tambah FAQ' }}</span>
                        <span wire:loading wire:target="save">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
