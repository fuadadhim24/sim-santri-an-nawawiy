<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- Welcome & Total Unpaid -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800">
                    Welcome, {{ $guardian->full_name }}
                </h2>
                <p class="text-gray-600 mt-1">Here is the summary of your registered students.</p>

                @if ($totalUnpaid > 0)
                    <div class="mt-6 bg-red-50 border border-red-200 rounded-lg p-4 flex items-center justify-between">
                        <div>
                            <p class="text-red-700 font-medium">Total Unpaid Amount</p>
                            <h3 class="text-3xl font-bold text-red-800">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}
                            </h3>
                        </div>
                        <button class="px-4 py-2 bg-red-600 text-white rounded-md shadow hover:bg-red-700 transition"
                            disabled>
                            Pay Now (Processing...)
                        </button>
                    </div>
                @else
                    <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
                        <p class="text-green-700 font-medium">All bills are paid! Thank you.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Student Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach ($guardian->students as $student)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">{{ $student->full_name }}</h3>
                                <span class="text-sm text-gray-500">NIS: {{ $student->nis }}</span>
                                <span
                                    class="ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">{{ $student->unit_code == '01' ? 'SMP' : ($student->unit_code == '02' ? 'SMA' : 'PPTQ') }}</span>
                            </div>
                            <div class="text-right">
                                <span
                                    class="px-2 py-1 rounded text-xs font-semibold {{ $student->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $student->is_active ? 'ACTIVE' : 'INACTIVE' }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h4 class="font-semibold text-gray-700 mb-2">Unpaid Bills</h4>
                            @php
                                $unpaidBills = $student->billings->where('status', 'UNPAID');
                            @endphp

                            @if ($unpaidBills->isNotEmpty())
                                <ul class="divide-y divide-gray-100">
                                    @foreach ($unpaidBills as $bill)
                                        <li class="py-3 flex justify-between items-center">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $bill->title }}</p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $bill->created_at->format('d M Y') }}</p>
                                            </div>
                                            <div class="flex items-center space-x-3">
                                                <span class="text-sm font-bold text-red-600">Rp
                                                    {{ number_format($bill->final_amount, 0, ',', '.') }}</span>
                                                <button wire:click="pay({{ $bill->id }})"
                                                    wire:confirm="Simulate payment for this bill?"
                                                    class="px-3 py-1 bg-primary text-primary-foreground text-xs rounded hover:bg-primary/90 transition">
                                                    Pay
                                                </button>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-gray-500 italic">No unpaid bills.</p>
                            @endif
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-100">
                            <h4 class="font-semibold text-gray-700 mb-2">Recent History</h4>
                            @php
                                $historyBills = $student->billings->where('status', '!=', 'UNPAID')->take(3);
                            @endphp
                            @if ($historyBills->isNotEmpty())
                                <ul class="divide-y divide-gray-100">
                                    @foreach ($historyBills as $bill)
                                        <li class="py-2 flex justify-between items-center opacity-75">
                                            <div>
                                                <p class="text-sm text-gray-800">{{ $bill->title }}</p>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span
                                                    class="text-xs font-medium px-2 py-0.5 rounded {{ $bill->status == 'PAID' ? 'bg-green-100 text-green-700' : 'bg-gray-100' }}">
                                                    {{ $bill->status }}
                                                </span>
                                                @if ($bill->status == 'PAID')
                                                    <a href="{{ route('admin.receipts.show', $bill->id) }}"
                                                        target="_blank"
                                                        class="text-xs text-blue-600 hover:text-blue-800 underline">
                                                        Receipt
                                                    </a>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-xs text-gray-400 italic">No history yet.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>
