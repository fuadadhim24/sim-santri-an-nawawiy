<div>
    <x-slot name="header">
        Discount Management
    </x-slot>

    <div class="bg-card rounded-lg shadow-sm border border-border">
        <div class="p-6 border-b border-border flex justify-between items-center">
            <h3 class="text-lg font-semibold text-card-foreground">Discount List</h3>
            <div class="flex space-x-4">
                <input wire:model.live="search" type="text" placeholder="Search by Fee Name..."
                    class="px-4 py-2 border border-input bg-background rounded-md focus:outline-none focus:ring-2 focus:ring-ring text-foreground">
                <a href="{{ route('admin.discounts.create') }}"
                    class="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 text-sm font-medium">
                    + Add Discount
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-muted-foreground uppercase bg-muted">
                    <tr>
                        <th class="px-6 py-3">Fee Item</th>
                        <th class="px-6 py-3">Target Status</th>
                        <th class="px-6 py-3 text-right">Discount Amount</th>
                        <th class="px-6 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($discounts as $discount)
                        <tr class="hover:bg-muted/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-foreground">
                                {{ $discount->feeMaster->item_name }}
                                <span class="text-xs text-muted-foreground block">
                                    {{ $discount->feeMaster->category }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                    {{ str_replace('_', ' ', $discount->target_status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-mono font-medium text-foreground">
                                Rp {{ number_format($discount->discount_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.discounts.edit', $discount) }}"
                                    class="text-primary hover:text-primary/80 font-medium mr-2">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-muted-foreground">
                                No discounts found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-border">
            {{ $discounts->links() }}
        </div>
    </div>
</div>
