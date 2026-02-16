<div>
    <x-slot name="header">
        Cash Payment Entry
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Search Section -->
        <div class="lg:col-span-1">
            <div class="bg-card rounded-lg shadow-sm border border-border p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Find Student</h3>

                <div class="relative">
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by Name or NIS..."
                        class="w-full px-4 py-2 border border-input bg-background rounded-md focus:outline-none focus:ring-2 focus:ring-ring text-foreground">

                    @if (strlen($search) >= 3)
                        @if (isset($students) && count($students) > 0)
                            <div class="absolute z-10 w-full mt-1 bg-popover border border-border rounded-md shadow-lg">
                                <ul class="py-1">
                                    @foreach ($students as $student)
                                        <li>
                                            <button wire:click="selectStudent({{ $student->id }})"
                                                class="w-full text-left px-4 py-2 text-sm text-popover-foreground hover:bg-muted focus:outline-none">
                                                <span class="font-medium">{{ $student->full_name }}</span>
                                                <span
                                                    class="block text-xs text-muted-foreground">{{ $student->nis }}</span>
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <div
                                class="absolute z-10 w-full mt-1 bg-popover border border-border rounded-md shadow-lg p-4 text-center text-sm text-muted-foreground">
                                No students found.
                            </div>
                        @endif
                    @endif
                </div>

                @if ($selectedStudent)
                    <div class="mt-6 p-4 bg-muted rounded-md">
                        <h4 class="font-medium text-foreground">{{ $selectedStudent->full_name }}</h4>
                        <p class="text-sm text-muted-foreground">NIS: {{ $selectedStudent->nis }}</p>
                        <p class="text-sm text-muted-foreground mt-1">
                            {{ $selectedStudent->unit_code == '01' ? 'SMP' : ($selectedStudent->unit_code == '02' ? 'SMA' : 'PPTQ') }}
                            - {{ $selectedStudent->residence_status }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Unpaid Bills Section -->
        <div class="lg:col-span-2">
            <div class="bg-card rounded-lg shadow-sm border border-border p-6 h-full">
                <h3 class="text-lg font-semibold text-foreground mb-4">Unpaid Invoices</h3>

                @if ($selectedStudent)
                    @if (count($unpaidBills) > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs text-muted-foreground uppercase bg-muted">
                                    <tr>
                                        <th class="px-4 py-3">Invoice</th>
                                        <th class="px-4 py-3">Amount</th>
                                        <th class="px-4 py-3 text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border">
                                    @foreach ($unpaidBills as $bill)
                                        <tr class="hover:bg-muted/50">
                                            <td class="px-4 py-3">
                                                <span
                                                    class="font-medium block text-foreground">{{ $bill->title }}</span>
                                                <span
                                                    class="text-xs text-muted-foreground">{{ $bill->created_at->format('d M Y') }}</span>
                                            </td>
                                            <td class="px-4 py-3 font-mono">
                                                Rp {{ number_format($bill->final_amount, 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <button wire:click="processPayment({{ $bill->id }})"
                                                    wire:confirm="Confirm cash payment for {{ $bill->title }}?"
                                                    class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-xs font-medium">
                                                    Record Payment
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center h-40 text-muted-foreground">
                            <svg class="h-10 w-10 mb-2 opacity-50" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p>No unpaid invoices for this student.</p>
                        </div>
                    @endif
                @else
                    <div class="flex flex-col items-center justify-center h-40 text-muted-foreground">
                        <svg class="h-10 w-10 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <p>Search and select a student to view invoices.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
