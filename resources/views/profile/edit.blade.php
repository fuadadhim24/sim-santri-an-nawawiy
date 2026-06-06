@php 
    $user = auth()->user();
    $isGuardian = $user && $user->role === 'WALI_SANTRI';
    $isAdmin = $user && in_array($user->role, ['SUPER_ADMIN', 'ADMINISTRASI', 'BENDAHARA']);
@endphp

@if ($isGuardian)
    @component('layouts.guardian')
        @slot('header')
            Profil
        @endslot

        <div class="space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-6">
                    <div class="space-y-6">
                        @include('profile.partials.update-profile-information-form')
                    </div>

                    <div class="space-y-6">
                        @include('profile.partials.update-password-form')
                    </div>

                    <div class="space-y-6">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    @endcomponent
@elseif ($isAdmin)
    @component('layouts.admin')
        @slot('header')
            Profil Saya
        @endslot

        <div class="space-y-6">
            <div class="bg-card overflow-hidden shadow-sm sm:rounded-lg border border-border">
                <div class="p-6 space-y-6">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>

                    <div class="border-t border-border pt-6 max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>

                    @if ($user->role !== 'SUPER_ADMIN')
                        <div class="border-t border-border pt-6 max-w-xl">
                            @include('profile.partials.delete-user-form')
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endcomponent
@else
    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Profile') }}
            </h2>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>
@endif

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('form').forEach((form) => {
            const submitButton = form.querySelector('button[type="submit"]');
            if (!submitButton) {
                return;
            }

            form.addEventListener('submit', () => {
                submitButton.disabled = true;
                submitButton.classList.add('opacity-50', 'cursor-not-allowed');
                submitButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    Memuat...
                `;
            }, { once: true });
        });
    });
</script>
