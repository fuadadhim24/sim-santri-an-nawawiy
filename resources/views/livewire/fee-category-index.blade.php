<div>
    <x-slot name="header">
        Manajemen Kategori Biaya
    </x-slot>

    <div class="bg-card rounded-lg shadow-sm border border-border">
        <div class="p-6 border-b border-border flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h3 class="text-lg font-semibold text-card-foreground">Daftar Kategori</h3>
            <div class="flex items-center space-x-2">
                <input wire:model.live="search" type="text" placeholder="Cari kategori..."
                    class="w-full md:w-64 py-2 px-4 border border-input bg-background rounded-md focus:outline-none focus:ring-2 focus:ring-ring text-foreground">
                <a href="{{ route('admin.fee-categories.create') }}"
                    class="inline-flex items-center justify-center py-2 px-4 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 font-semibold whitespace-nowrap">
                    + Tambah Kategori
                </a>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="p-4 mx-6 mt-4 bg-green-100 text-green-700 rounded-md">
                {{ session('message') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-muted-foreground uppercase bg-muted">
                    <tr>
                        <th class="px-6 py-3">Kode</th>
                        <th class="px-6 py-3">Nama Kategori</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($categories as $category)
                        <tr class="hover:bg-muted/50 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-primary">{{ $category->code }}</td>
                            <td class="px-6 py-4 font-medium text-foreground">{{ $category->name }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-3">
                                    <a href="{{ route('admin.fee-categories.edit', $category->id) }}"
                                        class="text-primary hover:text-primary/80 font-medium">Edit</a>
                                    <button wire:click="delete({{ $category->id }})"
                                        wire:confirm="Hapus kategori ini? Biaya yang terkait mungkin akan kehilangan kategorinya."
                                        class="text-destructive hover:text-destructive/80 font-medium">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-muted-foreground">
                                Tidak ada kategori ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-border">
            {{ $categories->links() }}
        </div>
    </div>
</div>
