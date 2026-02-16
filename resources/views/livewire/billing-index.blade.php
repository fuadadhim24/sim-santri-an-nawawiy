<div>
    <x-slot name="header">
        Billing & Invoices
    </x-slot>

    <div class="bg-card rounded-lg shadow-sm border border-border">
        <div class="p-6 border-b border-border flex flex-col md:flex-row justify-between items-center gap-4">
            <h3 class="text-lg font-semibold text-card-foreground">Invoice List</h3>
            <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4 w-full md:w-auto">
                <select wire:model.live="statusFilter"
                    class="px-4 py-2 border border-input bg-background rounded-md focus:outline-none focus:ring-2 focus:ring-ring text-foreground">
                    <option value="">All Statuses</option>
                    <option value="UNPAID">Unpaid</option>
                    <option value="PAID">Paid</option>
                    <option value="EXPIRED">Expired</option>
                    <option value="VOID">Void</option>
                </select>
                <input wire:model.live="search" type="text" placeholder="Search invoices..."
                    class="px-4 py-2 border border-input bg-background rounded-md focus:outline-none focus:ring-2 focus:ring-ring text-foreground">
                <a href="{{ route('admin.billings.create') }}"
                    class="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 text-sm font-medium text-center">
                    + Generate Bill
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-muted-foreground uppercase bg-muted">
                    <tr>
                        <th class="px-6 py-3">Invoice Date</th>
                        <th class="px-6 py-3">Student</th>
                        <th class="px-6 py-3">Description</th>
                        <th class="px-6 py-3 text-right">Amount</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($billings as $billing)
                        <tr class="hover:bg-muted/50 transition-colors">
                            <td class="px-6 py-4 text-muted-foreground">{{ $billing->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 font-medium text-foreground">
                                {{ $billing->student->full_name }}
                                <span class="text-xs text-muted-foreground block">{{ $billing->student->nis }}</span>
                            </td>
                            <td class="px-6 py-4 text-foreground">{{ $billing->title }}</td>
                            <td class="px-6 py-4 text-right font-mono font-medium text-foreground">
                                Rp {{ number_format($billing->final_amount, 0, ',', '.') }}
                                @if ($billing->discount_applied > 0)
                                    <span class="block text-xs text-green-600 line-through">
                                        Rp {{ number_format($billing->original_amount, 0, ',', '.') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 rounded-full text-xs font-medium
                                    {{ $billing->status == 'PAID' ? 'bg-green-100 text-green-700' : ($billing->status == 'PENDING' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                    {{ $billing->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <button
                                        class="text-primary hover:text-primary/80 font-medium text-sm">Detail</button>
                                    @if ($billing->status == 'PAID')
                                        <a href="{{ route('admin.receipts.show', $billing->id) }}" target="_blank"
                                            class="px-2 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-xs font-medium flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                                </path>
                                            </svg>
                                            Receipt
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-muted-foreground">
                                No invoices found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-border">
            {{ $billings->links() }}
        </div>
    </div>
</div>
