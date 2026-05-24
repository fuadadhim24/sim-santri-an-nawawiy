<div>
    <x-slot name="header">
        Kelola FAQ & Informasi
    </x-slot>

    <div class="bg-card rounded-lg shadow-sm border border-border">
        <div class="p-6 border-b border-border flex items-center justify-between gap-4">
            <h3 class="text-lg font-semibold text-card-foreground whitespace-nowrap">Daftar FAQ</h3>
            <div class="flex items-center space-x-2">
                <input wire:model.live="search" type="text" placeholder="Cari FAQ..."
                    class="w-full md:w-64 py-2 px-4 border border-input bg-background rounded-md focus:outline-none focus:ring-2 focus:ring-ring text-foreground text-sm">
                <a href="{{ route('admin.faqs.create') }}"
                    class="inline-flex items-center justify-center py-2 px-4 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 font-semibold whitespace-nowrap flex-none shrink-0 text-sm">
                    + Tambah FAQ
                </a>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="p-4 bg-green-100 text-green-700 border-b border-border">
                {{ session('message') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-muted-foreground uppercase bg-muted">
                    <tr>
                        <th class="px-6 py-3 w-12">Urutan</th>
                        <th class="px-6 py-3">Judul</th>
                        <th class="px-6 py-3">Kategori</th>
                        <th class="px-6 py-3">Lampiran</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($faqs as $faq)
                        <tr class="hover:bg-muted/50 transition-colors">
                            <td class="px-6 py-4 text-center font-mono text-muted-foreground">{{ $faq->sort_order }}</td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-foreground">{{ $faq->title }}</p>
                                <p class="text-xs text-muted-foreground mt-0.5">{{ Str::limit(strip_tags($faq->content), 80) }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                    {{ $faq->category_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    @if($faq->image_path)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-blue-100 text-blue-700">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            Gambar
                                        </span>
                                    @endif
                                    @if($faq->pdf_path)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-red-100 text-red-700">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                            PDF
                                        </span>
                                    @endif
                                    @if(!$faq->image_path && !$faq->pdf_path)
                                        <span class="text-xs text-muted-foreground">—</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <button wire:click="toggleActive({{ $faq->id }})"
                                    class="px-2 py-1 rounded-full text-xs font-medium cursor-pointer transition-colors {{ $faq->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                    {{ $faq->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('admin.faqs.edit', $faq->id) }}"
                                        class="text-primary hover:text-primary/80 font-medium text-sm">Edit</a>
                                    <button wire:click="delete({{ $faq->id }})"
                                        wire:swal="Yakin ingin menghapus FAQ ini?"
                                        class="text-destructive hover:text-destructive/80 font-medium text-sm">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-muted-foreground">
                                Belum ada FAQ. Klik "Tambah FAQ" untuk mulai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-border">
            {{ $faqs->links() }}
        </div>
    </div>
</div>
