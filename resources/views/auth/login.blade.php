<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Demo Accounts Alert -->
    <div class="mb-6 bg-primary/10 border border-primary/20 rounded-lg p-4 text-sm text-foreground shadow-sm">
        <div class="font-bold text-primary mb-2 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Akses Akun Evaluasi (Demo)
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-2">
            <div class="bg-background/50 p-2 rounded border border-border">
                <span class="font-semibold block text-xs uppercase tracking-wider text-primary">Admin</span>
                <div class="font-medium">admin@annawawiy.ac.id</div>
                <div class="text-xs text-muted-foreground">Password: password</div>
            </div>
            <div class="bg-background/50 p-2 rounded border border-border">
                <span class="font-semibold block text-xs uppercase tracking-wider text-primary">Wali Santri</span>
                <div class="font-medium">wali@test.com</div>
                <div class="text-xs text-muted-foreground">Password: password</div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email / NIS / Phone -->
        <div>
            <x-input-label for="email" :value="__('Email / NIS / No. HP')" />
            <x-text-input id="email" class="block mt-1 w-full" type="text" name="email" :value="old('email')"
                required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded border-input text-primary shadow-sm focus:ring-ring" name="remember">
                <span class="ms-2 text-sm text-muted-foreground">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-muted-foreground hover:text-foreground rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring"
                    href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
