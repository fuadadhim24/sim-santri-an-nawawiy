<div>
    <x-slot name="header">
        Fee Master Management
    </x-slot>

    <div class="bg-card rounded-lg shadow-sm border border-border">
        <div class="p-6 border-b border-border flex flex-col md:flex-row justify-between items-center gap-4">
            <h3 class="text-lg font-semibold text-card-foreground">Fee List</h3>
            <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4 w-full md:w-auto">
                <select wire:model.live="categoryFilter"
                    class="px-4 py-2 border border-input bg-background rounded-md focus:outline-none focus:ring-2 focus:ring-ring text-foreground">
                    <option value="">All Categories</option>
                    <option value="PENDAFTARAN">Pendaftaran</option>
                    <option value="DAFTAR_ULANG">Daftar Ulang</option>
                    <option value="BULANAN">Bulanan (SPP)</option>
                    <option value="SEMESTERAN">Semesteran</option>
                    <option value="AKHIR_SEKOLAH">Akhir Sekolah</option>
                </select>
                <input wire:model.live="search" type="text" placeholder="Search fees..."
                    class="px-4 py-2 border border-input bg-background rounded-md focus:outline-none focus:ring-2 focus:ring-ring text-foreground">
                <a href="{{ route('admin.fee-masters.create') }}"
                    class="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 text-sm font-medium text-center">
                    + Add Fee
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-muted-foreground uppercase bg-muted">
                    <tr>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Item Name</th>
                        <th class="px-6 py-3">Unit Target</th>
                        <th class="px-6 py-3">Residence Target</th>
                        <th class="px-6 py-3 text-right">Amount</th>
                        <th class="px-6 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($fees as $fee)
                        <tr class="hover:bg-muted/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                    {{ str_replace('_', ' ', $fee->category) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-foreground">{{ $fee->item_name }}</td>
                            <td class="px-6 py-4 text-muted-foreground">
                                {{ $fee->unit_target ? ($fee->unit_target == '01' ? 'SMP' : ($fee->unit_target == '02' ? 'SMA' : 'PPTQ')) : 'All Units' }}
                            </td>
                            <td class="px-6 py-4 text-muted-foreground">
                                {{ $fee->residence_target ? str_replace('_', ' ', $fee->residence_target) : 'All Residence' }}
                            </td>
                            <td class="px-6 py-4 text-right font-mono font-medium text-foreground">
                                Rp {{ number_format($fee->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.fee-masters.edit', $fee) }}"
                                    class="text-primary hover:text-primary/80 font-medium mr-2">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-muted-foreground">
                                No fees found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-border">
            {{ $fees->links() }}
        </div>
    </div>
</div>
