<div>
    <x-slot name="header">
        Guardian Management
    </x-slot>

    <div class="bg-card rounded-lg shadow-sm border border-border">
        <div class="p-6 border-b border-border flex justify-between items-center">
            <h3 class="text-lg font-semibold text-card-foreground">Guardian List</h3>
            <div class="flex space-x-4">
                <input wire:model.live="search" type="text" placeholder="Search guardians..."
                    class="px-4 py-2 border border-input bg-background rounded-md focus:outline-none focus:ring-2 focus:ring-ring text-foreground">
                <a href="{{ route('admin.guardians.create') }}"
                    class="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 text-sm font-medium">
                    + Add Guardian
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-muted-foreground uppercase bg-muted">
                    <tr>
                        <th class="px-6 py-3">Full Name</th>
                        <th class="px-6 py-3">WhatsApp</th>
                        <th class="px-6 py-3">User Account</th>
                        <th class="px-6 py-3">Students</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach ($guardians as $guardian)
                        <tr class="hover:bg-muted/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-foreground">{{ $guardian->full_name }}</td>
                            <td class="px-6 py-4 text-muted-foreground">{{ $guardian->whatsapp }}</td>
                            <td class="px-6 py-4 text-muted-foreground">{{ $guardian->user->email ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <span class="bg-secondary text-secondary-foreground px-2 py-1 rounded-full text-xs">
                                    {{ $guardian->students->count() }} Students
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.guardians.edit', $guardian) }}"
                                    class="text-primary hover:text-primary/80 font-medium">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-border">
            {{ $guardians->links() }}
        </div>
    </div>
</div>
