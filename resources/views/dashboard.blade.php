<x-admin-layout>
    <x-slot name="header">
        {{ __('Dashboard') }}
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-card overflow-hidden shadow-sm sm:rounded-lg border border-border">
            <div class="p-6">
                <div class="text-muted-foreground text-sm font-medium uppercase tracking-wider">Total Students</div>
                <div class="mt-2 text-3xl font-bold text-foreground">{{ \App\Models\Student::count() }}</div>
            </div>
        </div>

        <div class="bg-card overflow-hidden shadow-sm sm:rounded-lg border border-border">
            <div class="p-6">
                <div class="text-muted-foreground text-sm font-medium uppercase tracking-wider">Total Guardians</div>
                <div class="mt-2 text-3xl font-bold text-foreground">{{ \App\Models\Guardian::count() }}</div>
            </div>
        </div>

        <div class="bg-card overflow-hidden shadow-sm sm:rounded-lg border border-border">
            <div class="p-6">
                <div class="text-muted-foreground text-sm font-medium uppercase tracking-wider">Unpaid Invoices</div>
                <div class="mt-2 text-3xl font-bold text-destructive">
                    {{ \App\Models\Billing::where('status', 'UNPAID')->count() }}</div>
            </div>
        </div>
    </div>
</x-admin-layout>
