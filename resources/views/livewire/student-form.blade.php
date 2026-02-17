<div>
    <x-slot name="header">
        {{ $isEdit ? 'Ubah Data Santri' : 'Tambah Santri Baru' }}
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-card rounded-lg shadow-sm border border-border p-6">
            <form wire:submit="save" class="space-y-6">

                @if ($isEdit)
                    <!-- NIS Display (Read Only) -->
                    <div>
                        <label class="block text-sm font-medium text-foreground">NIS</label>
                        <div class="mt-1 p-2 bg-muted rounded-md text-muted-foreground font-mono">
                            {{ $generatedNis }}
                        </div>
                    </div>
                @endif

                <!-- Full Name -->
                <div>
                    <label for="full_name" class="block text-sm font-medium text-foreground">Nama Lengkap</label>
                    <input wire:model="full_name" type="text" id="full_name"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                    @error('full_name')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Guardian -->
                <div>
                    <label for="guardian_id" class="block text-sm font-medium text-foreground">Wali Santri</label>
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
                        <label for="unit_code" class="block text-sm font-medium text-foreground">Unit Sekolah</label>
                        <select wire:model="unit_code" id="unit_code"
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
                        <label for="residence_status" class="block text-sm font-medium text-foreground">Status Tempat
                            Tinggal</label>
                        <select wire:model="residence_status" id="residence_status"
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
                            Khusus</label>
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
                        <label for="class_name" class="block text-sm font-medium text-foreground">Nama Kelas</label>
                        <input wire:model="class_name" type="text" id="class_name" placeholder="contoh: 7A"
                            class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        @error('class_name')
                            <span class="text-destructive text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Address -->
                <div>
                    <label for="address" class="block text-sm font-medium text-foreground">Alamat</label>
                    <textarea wire:model="address" id="address" rows="3"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm"></textarea>
                    @error('address')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Active Status -->
                <div class="flex items-center">
                    <input wire:model="is_active" type="checkbox" id="is_active"
                        class="h-4 w-4 text-primary focus:ring-primary border-input rounded">
                    <label for="is_active" class="ml-2 block text-sm text-foreground">
                        Santri Aktif
                    </label>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <a href="{{ route('admin.students') }}"
                        class="px-4 py-2 border border-input rounded-md shadow-sm text-sm font-medium text-foreground bg-background hover:bg-muted focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                        {{ $isEdit ? 'Simpan Perubahan' : 'Buat Santri' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
