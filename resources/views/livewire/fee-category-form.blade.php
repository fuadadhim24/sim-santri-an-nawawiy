<div>
    <x-slot name="header">
        {{ $isEdit ? 'Edit Kategori Biaya' : 'Tambah Kategori Biaya' }}
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-card rounded-lg shadow-sm border border-border p-6">
            <form wire:submit="save" class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-foreground">Nama Kategori</label>
                    <input wire:model="name" type="text" id="name" placeholder="contoh: SPP"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                    @error('name')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-foreground">Kode Kategori (Slug)</label>
                    <input wire:model="code" type="text" id="code" placeholder="contoh: SPP_BULANAN"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm uppercase">
                    <p class="mt-1 text-xs text-muted-foreground">Gunakan huruf besar, angka, dan underscore saja.</p>
                    @error('code')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Interval Selection -->
                <div class="space-y-4">
                    <label class="block text-sm font-medium text-foreground">Interval Tagihan</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label
                            class="relative flex items-start p-4 border rounded-lg cursor-pointer transition-colors {{ $billing_interval === 'ONCE' ? 'border-primary bg-primary/5 ring-1 ring-primary' : 'border-input bg-background hover:bg-muted/50' }}">
                            <div class="flex items-center h-5">
                                <input wire:model.live="billing_interval" type="radio" value="ONCE"
                                    class="h-4 w-4 text-primary border-input focus:ring-ring">
                            </div>
                            <div class="ml-3 text-sm">
                                <span class="font-medium text-foreground block">Sekali Saja</span>
                            </div>
                        </label>

                        <label
                            class="relative flex items-start p-4 border rounded-lg cursor-pointer transition-colors {{ $billing_interval === 'MONTHLY' ? 'border-primary bg-primary/5 ring-1 ring-primary' : 'border-input bg-background hover:bg-muted/50' }}">
                            <div class="flex items-center h-5">
                                <input wire:model.live="billing_interval" type="radio" value="MONTHLY"
                                    class="h-4 w-4 text-primary border-input focus:ring-ring">
                            </div>
                            <div class="ml-3 text-sm">
                                <span class="font-medium text-foreground block">Bulanan</span>
                            </div>
                        </label>

                        <label
                            class="relative flex items-start p-4 border rounded-lg cursor-pointer transition-colors {{ $billing_interval === 'YEARLY' ? 'border-primary bg-primary/5 ring-1 ring-primary' : 'border-input bg-background hover:bg-muted/50' }}">
                            <div class="flex items-center h-5">
                                <input wire:model.live="billing_interval" type="radio" value="YEARLY"
                                    class="h-4 w-4 text-primary border-input focus:ring-ring">
                            </div>
                            <div class="ml-3 text-sm">
                                <span class="font-medium text-foreground block">Tahunan</span>
                            </div>
                        </label>
                    </div>
                    @error('billing_interval')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <a href="{{ route('admin.fee-categories') }}"
                        class="px-4 py-2 border border-input rounded-md shadow-sm text-sm font-medium text-foreground bg-background hover:bg-muted focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                        {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Kategori' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
