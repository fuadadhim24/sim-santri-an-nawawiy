<x-guest-layout>
    <div class="flex justify-center mb-8">
        <div class="w-20 h-20 rounded-xl bg-primary/10 border-2 border-dashed border-primary/30 flex items-center justify-center">
            <span class="text-primary/50 text-xs font-medium">Logo</span>
        </div>
    </div>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- WhatsApp Number (Primary) -->
        <div class="mt-4">
            <x-input-label for="whatsapp" :value="__('Nomor WhatsApp *')" />
            <x-text-input id="whatsapp" class="block mt-1 w-full" type="tel" name="whatsapp" :value="old('whatsapp')"
                required autocomplete="tel" placeholder="08xxxxxxxxxx" />
            <p class="mt-1 text-sm text-muted-foreground">
                {{ __('Nomor WhatsApp aktif, digunakan untuk login dan komunikasi') }}
            </p>
            <x-input-error :messages="$errors->get('whatsapp')" class="mt-2" />
        </div>

        <!-- Email Address (Optional) -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email (opsional)')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                autocomplete="username" placeholder="contoh@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-muted-foreground hover:text-foreground rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring"
                href="{{ route('login') }}">
                {{ __('Sudah punya akun?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Daftar') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
