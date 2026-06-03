<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('wali.dashboard') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Dashboard
            </a>
            <h1 class="text-4xl font-bold text-gray-900">Edit Profil</h1>
            <p class="text-gray-600 mt-2">Perbarui informasi profil Anda</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-lg shadow-md p-8">
            <form wire:submit.prevent="save" class="space-y-6">
                <!-- Full Name (Read-Only) -->
                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                    <input wire:model="full_name" type="text" id="full_name" disabled
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed">
                    <p class="text-xs text-gray-500 mt-1">Hubungi admin untuk mengubah nama</p>
                </div>

                <!-- WhatsApp -->
                <div>
                    <label for="whatsapp" class="block text-sm font-medium text-gray-700 mb-2">Nomor WhatsApp <span class="text-red-500">*</span></label>
                    <input wire:model="whatsapp" type="text" id="whatsapp" placeholder="081234567890"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors @error('whatsapp') border-red-500 @else border-gray-300 @enderror">
                    @error('whatsapp')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Format: 62812345678 atau 081234567890</p>
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon <span class="text-red-500">*</span></label>
                    <input wire:model="phone" type="text" id="phone" placeholder="081234567890"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors @error('phone') border-red-500 @else border-gray-300 @enderror">
                    @error('phone')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Minimal 10 digit</p>
                </div>

                <!-- Address -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Alamat <span class="text-red-500">*</span></label>
                    <textarea wire:model="address" id="address" rows="4" placeholder="Jl. Contoh No. 123, Kota..."
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors resize-none @error('address') border-red-500 @else border-gray-300 @enderror"></textarea>
                    @error('address')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Minimal 10 karakter</p>
                </div>

                <!-- Form Actions -->
                <div class="flex gap-4 pt-6 border-t border-gray-200">
                    <button type="button" wire:click="cancel"
                        class="flex-1 px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- Info Box -->
        <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-900">
                <strong>Catatan:</strong> Nama lengkap tidak dapat diubah melalui profil Anda. Jika perlu mengubah nama, silakan hubungi administrator.
            </p>
        </div>
    </div>
</div>
