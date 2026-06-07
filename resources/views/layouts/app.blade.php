<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SIM-SANTRI') }}</title>
    <link rel="shortcut icon" href="{{ asset('image/pondok.png') }}" type="image/png" />

    <!-- Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Source+Code+Pro:ital,wght@0,200..900;1,200..900&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Livewire styles (for pages that use Livewire) --}}
    @livewireStyles
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>
    @includeIf('sweetalert2::alert')

    {{-- Include Livewire scripts at the end of body --}}
    @livewireScripts

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.directive('swal', ({ el, directive, component, cleanup }) => {
                let content = directive.expression

                let onClick = e => {
                    if (el.__is_swal_confirmed) {
                        el.__is_swal_confirmed = false;
                        return;
                    }

                    e.preventDefault()
                    e.stopImmediatePropagation()

                    Swal.fire({
                        title: 'Konfirmasi',
                        text: content,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Lanjutkan!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            el.__is_swal_confirmed = true;
                            el.click()
                        }
                    })
                }

                el.addEventListener('click', onClick, { capture: true })

                cleanup(() => {
                    el.removeEventListener('click', onClick, { capture: true })
                })
            })
        })
    </script>
</body>

</html>
