<div>
    <x-slot name="header">
        Generate Monthly Bill (SPP)
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-card rounded-lg shadow-sm border border-border p-6">
            <div class="mb-6">
                <h3 class="text-lg font-medium text-foreground">Bulk Invoice Generation</h3>
                <p class="text-sm text-muted-foreground">
                    This will generate SPP invoices for all active students (excluding 'Ngaji Only').
                    If an invoice for the selected period already exists for a student, it will be skipped.
                </p>
            </div>

            <form wire:submit="generate" class="space-y-6">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Month -->
                    <div>
                        <label for="month" class="block text-sm font-medium text-foreground">Month</label>
                        <select wire:model="month" id="month"
                            class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                            @foreach (range(1, 12) as $m)
                                <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                            @endforeach
                        </select>
                        @error('month')
                            <span class="text-destructive text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Year -->
                    <div>
                        <label for="year" class="block text-sm font-medium text-foreground">Year</label>
                        <input wire:model="year" type="number" id="year"
                            class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        @error('year')
                            <span class="text-destructive text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <a href="{{ route('admin.billings') }}"
                        class="px-4 py-2 border border-input rounded-md shadow-sm text-sm font-medium text-foreground bg-background hover:bg-muted focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                        Generate Invoices
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
