<div>
    <x-slot name="header">
        {{ $isEdit ? 'Edit Fee Master' : 'Create Fee Master' }}
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-card rounded-lg shadow-sm border border-border p-6">
            <form wire:submit="save" class="space-y-6">

                <!-- Category -->
                <div>
                    <label for="category" class="block text-sm font-medium text-foreground">Category</label>
                    <select wire:model="category" id="category"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        <option value="PENDAFTARAN">Pendaftaran</option>
                        <option value="DAFTAR_ULANG">Daftar Ulang</option>
                        <option value="BULANAN">Bulanan (SPP)</option>
                        <option value="SEMESTERAN">Semesteran</option>
                        <option value="AKHIR_SEKOLAH">Akhir Sekolah</option>
                    </select>
                    @error('category')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Item Name -->
                <div>
                    <label for="item_name" class="block text-sm font-medium text-foreground">Item Name</label>
                    <input wire:model="item_name" type="text" id="item_name" placeholder="e.g. SPP Januari"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                    @error('item_name')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Amount -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-foreground">Amount (Rp)</label>
                    <input wire:model="amount" type="number" id="amount" placeholder="e.g. 500000"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                    @error('amount')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Unit Target -->
                    <div>
                        <label for="unit_target" class="block text-sm font-medium text-foreground">Unit Target
                            (Optional)</label>
                        <select wire:model="unit_target" id="unit_target"
                            class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                            <option value="">All Units</option>
                            <option value="01">SMP</option>
                            <option value="02">SMA</option>
                            <option value="03">PPTQ</option>
                        </select>
                        <p class="mt-1 text-xs text-muted-foreground">Leave empty to apply to all units.</p>
                        @error('unit_target')
                            <span class="text-destructive text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Residence Target -->
                    <div>
                        <label for="residence_target" class="block text-sm font-medium text-foreground">Residence Target
                            (Optional)</label>
                        <select wire:model="residence_target" id="residence_target"
                            class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                            <option value="">All Residence Types</option>
                            <option value="MONDOK">Mondok</option>
                            <option value="NON_MONDOK">Non Mondok</option>
                        </select>
                        <p class="mt-1 text-xs text-muted-foreground">Leave empty to apply to all types.</p>
                        @error('residence_target')
                            <span class="text-destructive text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <a href="{{ route('admin.fee-masters') }}"
                        class="px-4 py-2 border border-input rounded-md shadow-sm text-sm font-medium text-foreground bg-background hover:bg-muted focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                        {{ $isEdit ? 'Update Fee' : 'Create Fee' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
