<div>
    <x-slot name="header">Edit Profil Saya</x-slot>

    <div class="space-y-6">
        <!-- Back Link -->
        <div class="flex items-center justify-between">
            <a href="{{ route('wali.dashboard') }}" 
                class="inline-flex items-center text-sm font-semibold text-primary hover:opacity-85 transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Dasbor
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Side: Separate Forms (Spans 2 columns on large screens) -->
            <div class="lg:col-span-2 space-y-6">
                
                {{-- CARD 1: INFORMASI PROFIL --}}
                <div class="bg-card border border-border rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-border bg-muted/20">
                        <h3 class="font-bold text-foreground">Informasi Profil</h3>
                        <p class="text-xs text-muted-foreground mt-0.5">Perbarui informasi kontak dan alamat tempat tinggal Anda.</p>
                    </div>

                    <div class="p-6">
                        @if (session('profile_message'))
                            <div class="mb-4 p-3 text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg">
                                {{ session('profile_message') }}
                            </div>
                        @endif

                        @if (session('profile_error'))
                            <div class="mb-4 p-3 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg">
                                {{ session('profile_error') }}
                            </div>
                        @endif

                        <form wire:submit.prevent="updateProfile" class="space-y-5">
                            <!-- Full Name (Read-Only) -->
                            <div>
                                <label for="full_name" class="block text-sm font-semibold text-foreground mb-1.5">Nama Lengkap</label>
                                <input wire:model="full_name" type="text" id="full_name" disabled
                                    class="w-full px-4 py-2 border border-border rounded-lg bg-muted text-muted-foreground cursor-not-allowed text-sm">
                                <p class="text-[11px] text-muted-foreground mt-1">Nama lengkap tidak dapat diubah. Hubungi admin pesantren jika ada kesalahan penulisan.</p>
                            </div>

                            <!-- WhatsApp -->
                            <div>
                                <label for="whatsapp" class="block text-sm font-semibold text-foreground mb-1.5">Nomor WhatsApp <span class="text-red-500">*</span></label>
                                <input wire:model="whatsapp" type="text" id="whatsapp" placeholder="081234567890"
                                    class="w-full px-4 py-2 border border-border bg-background text-foreground rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-ring transition-colors @error('whatsapp') border-destructive @enderror">
                                @error('whatsapp')
                                    <span class="text-destructive text-xs mt-1 block font-medium">{{ $message }}</span>
                                @enderror
                                <p class="text-[11px] text-muted-foreground mt-1">Format nomor: 08xxxxxxxxxx atau 628xxxxxxxxxx</p>
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-semibold text-foreground mb-1.5">Alamat Email <span class="text-muted-foreground font-normal">(Opsional)</span></label>
                                <input wire:model="email" type="email" id="email" placeholder="nama@domain.com"
                                    class="w-full px-4 py-2 border border-border bg-background text-foreground rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-ring transition-colors @error('email') border-destructive @enderror">
                                @error('email')
                                    <span class="text-destructive text-xs mt-1 block font-medium">{{ $message }}</span>
                                @enderror
                                <p class="text-[11px] text-muted-foreground mt-1">Digunakan untuk menerima notifikasi transaksi/bukti bayar.</p>
                            </div>

                            <!-- Address -->
                            <div>
                                <label for="address" class="block text-sm font-semibold text-foreground mb-1.5">Alamat Lengkap <span class="text-muted-foreground font-normal">(Opsional)</span></label>
                                <textarea wire:model="address" id="address" rows="4" placeholder="Tulis alamat rumah lengkap dengan RT/RW, kelurahan, kecamatan..."
                                    class="w-full px-4 py-2 border border-border bg-background text-foreground rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-ring transition-colors resize-none @error('address') border-destructive @enderror"></textarea>
                                @error('address')
                                    <span class="text-destructive text-xs mt-1 block font-medium">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Form Actions -->
                            <div class="flex justify-end pt-4 border-t border-border mt-4">
                                <button type="submit" wire:loading.attr="disabled" wire:target="updateProfile"
                                    class="inline-flex items-center justify-center px-5 py-2 bg-primary text-primary-foreground font-semibold rounded-lg hover:opacity-90 transition-all text-sm shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg wire:loading wire:target="updateProfile" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span wire:loading.remove wire:target="updateProfile">Simpan Profil</span>
                                    <span wire:loading wire:target="updateProfile">Menyimpan...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- CARD 2: UBAH KATA SANDI --}}
                <div class="bg-card border border-border rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-border bg-muted/20">
                        <h3 class="font-bold text-foreground">Ubah Kata Sandi</h3>
                        <p class="text-xs text-muted-foreground mt-0.5">Amankan akun Anda dengan menggunakan kata sandi yang kuat.</p>
                    </div>

                    <div class="p-6">
                        @if (session('password_message'))
                            <div class="mb-4 p-3 text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg">
                                {{ session('password_message') }}
                            </div>
                        @endif

                        @if (session('password_error'))
                            <div class="mb-4 p-3 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg">
                                {{ session('password_error') }}
                            </div>
                        @endif

                        <form wire:submit.prevent="updatePassword" class="space-y-4">
                            <div>
                                <label for="current_password" class="block text-sm font-semibold text-foreground mb-1.5">Kata Sandi Saat Ini</label>
                                <input wire:model="current_password" type="password" id="current_password" placeholder="••••••••"
                                    class="w-full px-4 py-2 border border-border bg-background text-foreground rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-ring transition-colors @error('current_password') border-destructive @enderror">
                                @error('current_password')
                                    <span class="text-destructive text-xs mt-1 block font-medium">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="new_password" class="block text-sm font-semibold text-foreground mb-1.5">Kata Sandi Baru</label>
                                    <input wire:model="new_password" type="password" id="new_password" placeholder="••••••••"
                                        class="w-full px-4 py-2 border border-border bg-background text-foreground rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-ring transition-colors @error('new_password') border-destructive @enderror">
                                    @error('new_password')
                                        <span class="text-destructive text-xs mt-1 block font-medium">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label for="new_password_confirmation" class="block text-sm font-semibold text-foreground mb-1.5">Konfirmasi Kata Sandi Baru</label>
                                    <input wire:model="new_password_confirmation" type="password" id="new_password_confirmation" placeholder="••••••••"
                                        class="w-full px-4 py-2 border border-border bg-background text-foreground rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-ring transition-colors">
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex justify-end pt-4 border-t border-border mt-4">
                                <button type="submit" wire:loading.attr="disabled" wire:target="updatePassword"
                                    class="inline-flex items-center justify-center px-5 py-2 bg-primary text-primary-foreground font-semibold rounded-lg hover:opacity-90 transition-all text-sm shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg wire:loading wire:target="updatePassword" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span wire:loading.remove wire:target="updatePassword">Perbarui Sandi</span>
                                    <span wire:loading wire:target="updatePassword">Memproses...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

            <!-- Right Side: Info / Help Guide -->
            <div class="lg:col-span-1 space-y-4">
                <div class="bg-card border border-border rounded-xl p-5 shadow-sm space-y-3">
                    <h4 class="font-bold text-foreground text-sm flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Verifikasi Akun
                    </h4>
                    <p class="text-xs text-muted-foreground leading-relaxed">
                        Nomor WhatsApp yang Anda cantumkan akan digunakan oleh pengurus pesantren untuk koordinasi dan mengirim notifikasi tagihan/laporan santri.
                    </p>
                    <div class="p-3 rounded-lg text-xs" style="background-color: var(--secondary); color: var(--secondary-foreground);">
                        <strong>Penting:</strong> Gunakan nomor WhatsApp yang aktif dan dapat menerima pesan/panggilan langsung.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
