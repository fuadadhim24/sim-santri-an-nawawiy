<div>
    <x-slot name="header">
        {{ $isEdit ? 'Edit Discount' : 'Create Discount' }}
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-card rounded-lg shadow-sm border border-border p-6">
            <form wire:submit="save" class="space-y-6">

                <!-- Fee Master -->
                <div>
                    <label for="fee_master_id" class="block text-sm font-medium text-foreground">Fee Item</label>
                    <select wire:model="fee_master_id" id="fee_master_id"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        <option value="">Select Fee Item...</option>
                        @foreach ($this->feeMasters as $fee)
                            <option value="{{ $fee->id }}">
                                {{ $fee->item_name }} - {{ $fee->category }} (Rp
                                {{ number_format($fee->amount, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                    @error('fee_master_id')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Target Status -->
                <div>
                    <label for="target_status" class="block text-sm font-medium text-foreground">Target Status
                        (Beneficiary)</label>
                    <select wire:model="target_status" id="target_status"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                        <option value="ANAK_GURU">Anak Guru</option>
                        <option value="YATIM">Yatim</option>
                    </select>
                    @error('target_status')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Discount Amount -->
                <div>
                    <label for="discount_amount" class="block text-sm font-medium text-foreground">Discount Amount
                        (Rp)</label>
                    <input wire:model="discount_amount" type="number" id="discount_amount" placeholder="e.g. 50000"
                        class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                    <p class="mt-1 text-xs text-muted-foreground">The amount to be deducted from the original fee.</p>
                    @error('discount_amount')
                        <span class="text-destructive text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <a href="{{ route('admin.discounts') }}"
                        class="px-4 py-2 border border-input rounded-md shadow-sm text-sm font-medium text-foreground bg-background hover:bg-muted focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                        {{ $isEdit ? 'Update Discount' : 'Create Discount' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
