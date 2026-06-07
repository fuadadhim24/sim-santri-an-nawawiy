<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SIM-SANTRI') }}</title>
    <link rel="shortcut icon" href="{{ asset('image/pondok.png') }}" type="image/png" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Source+Code+Pro:ital,wght@0,200..900;1,200..900&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Livewire styles need to be loaded after Vite assets so that component CSS is applied correctly --}}
    @livewireStyles
</head>

<body class="font-sans antialiased bg-background text-foreground" x-data="{ openHelp: false }">
    <div class="flex min-h-screen bg-background">
        <!-- Sidebar -->
        <aside id="sidebar"
            class="w-64 bg-sidebar border-r border-sidebar-border text-sidebar-foreground hidden md:flex md:flex-col fixed h-full transition-all duration-300 z-30 flex flex-col">
            <div class="h-16 flex items-center justify-between px-6 border-b border-sidebar-border">
                <h1 class="text-xl font-bold text-sidebar-primary tracking-tight">SIM-SANTRI</h1>
                <button id="mobile-sidebar-close" class="md:hidden text-sidebar-foreground hover:text-sidebar-primary">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                <!-- Dashboard Link -->
                <a href="{{ route('dashboard') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('dashboard') ? 'bg-sidebar-primary text-sidebar-primary-foreground' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }} group transition-colors">
                    <svg class="mr-3 h-5 w-5 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-sidebar-foreground group-hover:text-sidebar-accent-foreground' }}"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                    </svg>
                    Dasbor
                </a>

                @if (Auth::user()->role === 'SUPER_ADMIN')
                    <div class="pt-4 pb-2">
                        <p class="px-3 text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                            Manajemen Akun
                        </p>
                    </div>

                    <a href="{{ route('admin.users') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.users*') ? 'bg-sidebar-primary text-sidebar-primary-foreground' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }} group transition-colors">
                        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.users*') ? 'text-white' : 'text-sidebar-foreground group-hover:text-sidebar-accent-foreground' }}"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                        Pengguna
                    </a>
                @endif

                @if (in_array(Auth::user()->role, ['SUPER_ADMIN', 'BENDAHARA']))
                    <div class="pt-4 pb-2">
                        <p class="px-3 text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                            Data Master Keuangan
                        </p>
                    </div>

                    <a href="{{ route('admin.fee-categories') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.fee-categories*') ? 'bg-sidebar-primary text-sidebar-primary-foreground' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }} group transition-colors">
                        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.fee-categories*') ? 'text-white' : 'text-sidebar-foreground group-hover:text-sidebar-accent-foreground' }}"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                        </svg>
                        Kategori Biaya
                    </a>

                    <a href="{{ route('admin.fee-masters') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.fee-masters*') ? 'bg-sidebar-primary text-sidebar-primary-foreground' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }} group transition-colors">
                        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.fee-masters*') ? 'text-white' : 'text-sidebar-foreground group-hover:text-sidebar-accent-foreground' }}"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                        </svg>
                        Master Biaya
                    </a>

                    <a href="{{ route('admin.discounts') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.discounts*') ? 'bg-sidebar-primary text-sidebar-primary-foreground' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }} group transition-colors">
                        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.discounts*') ? 'text-white' : 'text-sidebar-foreground group-hover:text-sidebar-accent-foreground' }}"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9V3m0 0l-3 3m3-3l3 3M6 21h12" />
                        </svg>
                        Diskon
                    </a>
                @endif

                <div class="pt-4 pb-2">
                    <p class="px-3 text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                        Operasional
                    </p>
                </div>

                @if (in_array(Auth::user()->role, ['SUPER_ADMIN', 'ADMINISTRASI']))
                <a href="{{ route('admin.guardians') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.guardians') ? 'bg-sidebar-primary text-sidebar-primary-foreground' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }} group transition-colors">
                    <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.guardians') ? 'text-white' : 'text-sidebar-foreground group-hover:text-sidebar-accent-foreground' }}"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                    </svg>
                    Wali Santri
                </a>

                <a href="{{ route('admin.students') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.students*') ? 'bg-sidebar-primary text-sidebar-primary-foreground' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }} group transition-colors">
                    <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.students*') ? 'text-white' : 'text-sidebar-foreground group-hover:text-sidebar-accent-foreground' }}"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.499 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
                    </svg>
                    Santri
                </a>

                <a href="{{ route('admin.rombels') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.rombels*') ? 'bg-sidebar-primary text-sidebar-primary-foreground' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }} group transition-colors">
                    <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.rombels*') ? 'text-white' : 'text-sidebar-foreground group-hover:text-sidebar-accent-foreground' }}"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-.84-1.875-1.875-1.875s-1.875.84-1.875 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a1.5 1.5 0 01-3 0v0c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-.84-1.875-1.875-1.875s-1.875.84-1.875 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a1.5 1.5 0 01-3 0v0c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-.84-1.875-1.875-1.875s-1.875.84-1.875 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a1.5 1.5 0 01-3 0" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                    </svg>
                    Manajemen Rombel
                </a>


                <a href="{{ route('admin.spmb-schedules') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.spmb-schedules*') ? 'bg-sidebar-primary text-sidebar-primary-foreground' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }} group transition-colors">
                    <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.spmb-schedules*') ? 'text-white' : 'text-sidebar-foreground group-hover:text-sidebar-accent-foreground' }}"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                    Jadwal SPMB
                </a>

                <a href="{{ route('admin.student-acceptance') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.student-acceptance') ? 'bg-sidebar-primary text-sidebar-primary-foreground' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }} group transition-colors">
                    <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.student-acceptance') ? 'text-white' : 'text-sidebar-foreground group-hover:text-sidebar-accent-foreground' }}"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Penerimaan Santri
                </a>
                @endif

                @if (in_array(Auth::user()->role, ['SUPER_ADMIN', 'BENDAHARA']))
                <a href="{{ route('admin.billings') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.billings*') ? 'bg-sidebar-primary text-sidebar-primary-foreground' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }} group transition-colors">
                    <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.billings*') ? 'text-white' : 'text-sidebar-foreground group-hover:text-sidebar-accent-foreground' }}"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    Tagihan
                </a>
                @endif

                @if (in_array(Auth::user()->role, ['SUPER_ADMIN', 'BENDAHARA']))
                    <div class="pt-4 pb-2">
                        <p class="px-3 text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                            Laporan
                        </p>
                    </div>

                    <a href="{{ route('admin.reports.financial') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.reports.financial') ? 'bg-sidebar-primary text-sidebar-primary-foreground' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }} group transition-colors">
                        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.reports.financial') ? 'text-white' : 'text-sidebar-foreground group-hover:text-sidebar-accent-foreground' }}"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />
                        </svg>
                        Laporan Keuangan
                    </a>
                @endif

                @if (in_array(Auth::user()->role, ['SUPER_ADMIN', 'ADMINISTRASI']))
                    <div class="pt-4 pb-2">
                        <p class="px-3 text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                            Sistem & Informasi
                        </p>
                    </div>
                    <a href="{{ route('admin.faqs') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.faqs*') ? 'bg-sidebar-primary text-sidebar-primary-foreground' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }} group transition-colors">
                        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.faqs*') ? 'text-white' : 'text-sidebar-foreground group-hover:text-sidebar-accent-foreground' }}"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                        </svg>
                        FAQ & Informasi
                    </a>
                @endif
            </nav>
            </nav>

            <div class="border-t border-sidebar-border p-4 space-y-1">
                <a href="{{ route('profile.edit') }}"
                    class="flex w-full items-center px-2 py-2 text-sm font-medium text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground rounded-md transition-colors {{ request()->routeIs('profile.*') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : '' }}">
                    <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Profil Saya
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex w-full items-center px-2 py-2 text-sm font-medium text-destructive hover:bg-destructive/10 hover:text-destructive rounded-md transition-colors">
                        <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

    {{-- Livewire scripts required for wire:click, polling, modals, etc. --}}
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

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col md:ml-64 transition-all duration-300 h-screen p-3 md:p-4 bg-sidebar">
            <div class="flex-1 flex flex-col bg-background rounded-lg shadow-sm border border-border overflow-hidden">
                <!-- Top Navbar -->
                <header
                    class="bg-card border-b border-border h-16 flex items-center justify-between px-6 flex-shrink-0">
                    <div class="flex items-center">
                        <!-- Mobile menu button -->
                        <button id="mobile-menu-button"
                            class="md:hidden p-2 rounded-md text-foreground hover:bg-muted focus:outline-none focus:ring-2 focus:ring-ring">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <h2 class="ml-4 text-xl font-semibold text-foreground leading-tight">
                            @if (isset($header))
                                {{ $header }}
                            @else
                                Dasbor
                            @endif
                        </h2>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- User Dropdown Placeholder -->
                        <span class="text-sm font-medium text-foreground">{{ Auth::user()->name ?? 'Admin' }}</span>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 p-6 overflow-y-auto bg-background/50">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </div>
    </div>
    <!-- Floating Help Drawer Widget -->
    <div class="relative">
        <!-- Floating Toggle Button -->
        <button @click="openHelp = !openHelp" 
                class="fixed bottom-6 right-6 z-50 flex items-center justify-center w-12 h-12 rounded-full bg-primary text-primary-foreground shadow-lg hover:bg-primary/90 transition-all duration-300 transform hover:scale-110 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                title="Pusat Bantuan">
            <svg x-show="!openHelp" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <svg x-show="openHelp" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <!-- Drawer Panel -->
        <div x-show="openHelp" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             x-cloak
             class="fixed inset-y-0 right-0 w-80 md:w-96 bg-card border-l border-border shadow-2xl z-[9999] flex flex-col">
            
            <!-- Drawer Header -->
            <div class="p-4 border-b border-border flex items-center justify-between bg-muted/40">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-bold text-foreground">Pusat Bantuan SIM</span>
                </div>
                <button @click="openHelp = false" class="p-1 rounded-md text-muted-foreground hover:bg-muted hover:text-foreground">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Drawer Body -->
            <div class="flex-1 overflow-y-auto p-5 space-y-6">
                @if(request()->routeIs('admin.fee-masters*'))
                    <!-- Help Content for Fee Masters -->
                    <div>
                        <h4 class="font-semibold text-primary mb-2">Panduan Master Biaya</h4>
                        <p class="text-xs text-muted-foreground leading-relaxed">
                            Menu ini digunakan untuk mengatur cetakan tagihan santri.
                        </p>
                        <div class="mt-4 space-y-4">
                            <div class="bg-muted/50 rounded-lg p-3 border border-border/50">
                                <span class="text-xs font-bold text-foreground block mb-1">Tipe Siklus Tagihan</span>
                                <ul class="list-disc list-inside text-xs text-muted-foreground space-y-1">
                                    <li><strong>Sekali Bayar:</strong> Tagihan satu kali saja (misal: Uang Pangkal).</li>
                                    <li><strong>Bulanan:</strong> Digenerate otomatis setiap bulan pada tanggal generate (misal: SPP).</li>
                                    <li><strong>Tahunan:</strong> Digenerate otomatis setiap tahun (misal: Daftar Ulang).</li>
                                </ul>
                            </div>
                            <div class="bg-muted/50 rounded-lg p-3 border border-border/50">
                                <span class="text-xs font-bold text-foreground block mb-1">Jatuh Tempo (Hari)</span>
                                <p class="text-xs text-muted-foreground leading-relaxed">
                                    Jumlah hari tenggang untuk melunasi tagihan setelah diterbitkan (default: 14 hari).
                                </p>
                            </div>
                            <div class="bg-muted/50 rounded-lg p-3 border border-border/50">
                                <span class="text-xs font-bold text-foreground block mb-1">Target Tagihan</span>
                                <p class="text-xs text-muted-foreground leading-relaxed">
                                    Saring sasaran tagihan berdasarkan jenjang sekolah (SMP/SMA/PPTQ) atau status domisili (Mondok/Non Mondok).
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif(request()->routeIs('admin.billings*'))
                    <!-- Help Content for Billings -->
                    <div>
                        <h4 class="font-semibold text-primary mb-2">Panduan Kelola Tagihan</h4>
                        <p class="text-xs text-muted-foreground leading-relaxed">
                            Menu ini digunakan untuk memantau, menerima pembayaran, dan memecah tagihan santri.
                        </p>
                        <div class="mt-4 space-y-4">
                            <div class="bg-muted/50 rounded-lg p-3 border border-border/50">
                                <span class="text-xs font-bold text-foreground block mb-1">Cara Entri Tunai (Manual)</span>
                                <p class="text-xs text-muted-foreground leading-relaxed">
                                    Klik tombol <strong>Entri Tunai</strong> di baris tagihan, masukkan data pembayaran tunai yang diterima, lalu simpan. Status otomatis berubah menjadi LUNAS.
                                </p>
                            </div>
                            <div class="bg-muted/50 rounded-lg p-3 border border-border/50">
                                <span class="text-xs font-bold text-foreground block mb-1">Cara Pecah Cicilan</span>
                                <p class="text-xs text-muted-foreground leading-relaxed">
                                    Klik <strong>Pecah Cicilan</strong> pada tagihan aktif, tentukan jumlah cicilan, lalu sistem akan membagi nominal tagihan tersebut menjadi beberapa sub-tagihan baru secara otomatis.
                                </p>
                            </div>
                            <div class="bg-muted/50 rounded-lg p-3 border border-border/50">
                                <span class="text-xs font-bold text-foreground block mb-1">Buat Tagihan Manual</span>
                                <p class="text-xs text-muted-foreground leading-relaxed">
                                    Gunakan tombol <strong>Buat Tagihan Manual</strong> di bagian kanan atas. Cari santri menggunakan kolom pencarian autocomplete, lalu masukkan judul dan nominal tagihan kustom.
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif(request()->routeIs('admin.fee-categories*'))
                    <div>
                        <h4 class="font-semibold text-primary mb-2">Panduan Kategori Biaya</h4>
                        <p class="text-xs text-muted-foreground leading-relaxed">Kategori biaya adalah pengelompokan jenis tagihan di pesantren.</p>
                        <div class="mt-4 space-y-4">
                            <div class="bg-muted/50 rounded-lg p-3 border border-border/50">
                                <span class="text-xs font-bold text-foreground block mb-1">Apa itu Kategori Biaya?</span>
                                <p class="text-xs text-muted-foreground">Kategori biaya adalah induk dari Master Biaya. Contoh: "SPP", "Uang Pangkal", "Kegiatan". Setiap kategori memiliki aturan apakah tagihan otomatis aktif saat santri diterima atau harus dibuat manual.</p>
                            </div>
                            <div class="bg-muted/50 rounded-lg p-3 border border-border/50">
                                <span class="text-xs font-bold text-foreground block mb-1">Mode Aktivasi</span>
                                <ul class="list-disc list-inside text-xs text-muted-foreground space-y-1">
                                    <li><strong>Otomatis:</strong> Tagihan langsung dibuat saat santri berstatus diterima.</li>
                                    <li><strong>Manual:</strong> Tagihan harus dibuat oleh admin/bendahara secara manual.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @elseif(request()->routeIs('admin.discounts*'))
                    <div>
                        <h4 class="font-semibold text-primary mb-2">Panduan Diskon / Potongan</h4>
                        <p class="text-xs text-muted-foreground leading-relaxed">Diskon digunakan untuk memberikan potongan biaya kepada santri berdasarkan status khusus mereka.</p>
                        <div class="mt-4 space-y-4">
                            <div class="bg-muted/50 rounded-lg p-3 border border-border/50">
                                <span class="text-xs font-bold text-foreground block mb-1">Cara Kerja Diskon</span>
                                <p class="text-xs text-muted-foreground">Diskon ditautkan ke Master Biaya tertentu dan berlaku otomatis untuk santri yang memiliki status khusus (Yatim, Piatu, Yatim Piatu, Dhuafa). Santri berstatus "UMUM" tidak mendapatkan diskon.</p>
                            </div>
                            <div class="bg-muted/50 rounded-lg p-3 border border-border/50">
                                <span class="text-xs font-bold text-foreground block mb-1">Efek pada Tagihan</span>
                                <p class="text-xs text-muted-foreground">Ketika diskon ditambahkan atau diubah, seluruh tagihan <strong>belum lunas</strong> dari Master Biaya terkait akan otomatis dihitung ulang nominalnya.</p>
                            </div>
                        </div>
                    </div>
                @elseif(request()->routeIs('admin.students*') || request()->routeIs('admin.student-acceptance*'))
                    <div>
                        <h4 class="font-semibold text-primary mb-2">Panduan Kelola Santri</h4>
                        <p class="text-xs text-muted-foreground leading-relaxed">Menu ini digunakan untuk mengelola data santri pesantren.</p>
                        <div class="mt-4 space-y-4">
                            <div class="bg-muted/50 rounded-lg p-3 border border-border/50">
                                <span class="text-xs font-bold text-foreground block mb-1">Alur Penerimaan Santri</span>
                                <p class="text-xs text-muted-foreground">Santri baru masuk dengan status <strong>pending</strong>. Setelah diverifikasi, admin mengubah status menjadi <strong>diterima</strong>. Saat diterima, tagihan-tagihan wajib akan otomatis digenerate sesuai konfigurasi kategori biaya.</p>
                            </div>
                            <div class="bg-muted/50 rounded-lg p-3 border border-border/50">
                                <span class="text-xs font-bold text-foreground block mb-1">Detail Santri</span>
                                <p class="text-xs text-muted-foreground">Klik nama santri untuk melihat detail profil, riwayat tagihan, dan status pembayaran santri secara lengkap.</p>
                            </div>
                            <div class="bg-muted/50 rounded-lg p-3 border border-border/50">
                                <span class="text-xs font-bold text-foreground block mb-1">Status Khusus</span>
                                <p class="text-xs text-muted-foreground">Santri dengan status khusus (Yatim, Piatu, Dhuafa) akan otomatis mendapat potongan tagihan jika sudah diatur di menu Diskon.</p>
                            </div>
                        </div>
                    </div>
                @elseif(request()->routeIs('admin.guardians*'))
                    <div>
                        <h4 class="font-semibold text-primary mb-2">Panduan Kelola Wali Santri</h4>
                        <p class="text-xs text-muted-foreground leading-relaxed">Menu ini digunakan untuk mengelola data orang tua/wali santri.</p>
                        <div class="mt-4 space-y-4">
                            <div class="bg-muted/50 rounded-lg p-3 border border-border/50">
                                <span class="text-xs font-bold text-foreground block mb-1">Akun Wali Santri</span>
                                <p class="text-xs text-muted-foreground">Setiap wali santri memiliki akun login sendiri untuk mengakses portal pembayaran. Mereka dapat melihat tagihan dan membayar secara online melalui dashboard wali.</p>
                            </div>
                            <div class="bg-muted/50 rounded-lg p-3 border border-border/50">
                                <span class="text-xs font-bold text-foreground block mb-1">Relasi Wali dan Santri</span>
                                <p class="text-xs text-muted-foreground">Satu wali bisa memiliki lebih dari satu santri. Semua tagihan anak-anaknya akan tampil di satu dashboard portal wali.</p>
                            </div>
                        </div>
                    </div>
                @elseif(request()->routeIs('admin.rombels*'))
                    <div>
                        <h4 class="font-semibold text-primary mb-2">Panduan Rombongan Belajar</h4>
                        <p class="text-xs text-muted-foreground leading-relaxed">Menu ini digunakan untuk mengelola kelas dan penempatan santri.</p>
                        <div class="mt-4 space-y-4">
                            <div class="bg-muted/50 rounded-lg p-3 border border-border/50">
                                <span class="text-xs font-bold text-foreground block mb-1">Apa itu Rombel?</span>
                                <p class="text-xs text-muted-foreground">Rombel (Rombongan Belajar) adalah pembagian kelas santri. Contoh: Kelas 7A, Kelas 8B, dsb. Anda dapat memindahkan santri antar rombel sesuai kebutuhan.</p>
                            </div>
                        </div>
                    </div>
                @elseif(request()->routeIs('admin.spmb-schedules*'))
                    <div>
                        <h4 class="font-semibold text-primary mb-2">Panduan Jadwal SPMB</h4>
                        <p class="text-xs text-muted-foreground leading-relaxed">Menu ini digunakan untuk mengelola jadwal Seleksi Penerimaan Murid Baru.</p>
                        <div class="mt-4 space-y-4">
                            <div class="bg-muted/50 rounded-lg p-3 border border-border/50">
                                <span class="text-xs font-bold text-foreground block mb-1">Cara Membuat Jadwal</span>
                                <p class="text-xs text-muted-foreground">Klik <strong>Tambah Jadwal</strong>, tentukan tanggal pelaksanaan, kuota peserta, dan jenjang yang dibuka. Wali santri dapat memilih jadwal ini saat mendaftar secara online.</p>
                            </div>
                            <div class="bg-muted/50 rounded-lg p-3 border border-border/50">
                                <span class="text-xs font-bold text-foreground block mb-1">Kuota Otomatis</span>
                                <p class="text-xs text-muted-foreground">Sistem akan secara otomatis menutup pendaftaran jika kuota peserta pada jadwal tersebut sudah penuh.</p>
                            </div>
                        </div>
                    </div>
                @elseif(request()->routeIs('admin.reports*'))
                    <div>
                        <h4 class="font-semibold text-primary mb-2">Panduan Laporan Keuangan</h4>
                        <p class="text-xs text-muted-foreground leading-relaxed">Menu ini menampilkan ringkasan dan detail keuangan pesantren.</p>
                        <div class="mt-4 space-y-4">
                            <div class="bg-muted/50 rounded-lg p-3 border border-border/50">
                                <span class="text-xs font-bold text-foreground block mb-1">Filter Laporan</span>
                                <p class="text-xs text-muted-foreground">Gunakan filter tanggal, jenjang, dan status pembayaran untuk menyaring data laporan sesuai kebutuhan Anda.</p>
                            </div>
                            <div class="bg-muted/50 rounded-lg p-3 border border-border/50">
                                <span class="text-xs font-bold text-foreground block mb-1">Ekspor Data</span>
                                <p class="text-xs text-muted-foreground">Laporan dapat diekspor untuk keperluan cetak atau pelaporan ke yayasan.</p>
                            </div>
                        </div>
                    </div>
                @elseif(request()->routeIs('admin.faqs*'))
                    <div>
                        <h4 class="font-semibold text-primary mb-2">Panduan Kelola FAQ</h4>
                        <p class="text-xs text-muted-foreground leading-relaxed">Menu ini digunakan untuk mengelola pertanyaan yang sering diajukan (FAQ) oleh wali santri.</p>
                        <div class="mt-4 space-y-4">
                            <div class="bg-muted/50 rounded-lg p-3 border border-border/50">
                                <span class="text-xs font-bold text-foreground block mb-1">Tips Efektif</span>
                                <p class="text-xs text-muted-foreground">Catat pertanyaan yang sering ditanyakan wali santri via WhatsApp, lalu masukkan ke sini. FAQ ini akan tampil otomatis di portal wali santri sehingga mereka dapat menemukan jawaban secara mandiri.</p>
                            </div>
                        </div>
                    </div>
                @elseif(request()->routeIs('admin.users*'))
                    <div>
                        <h4 class="font-semibold text-primary mb-2">Panduan Kelola Pengguna</h4>
                        <p class="text-xs text-muted-foreground leading-relaxed">Menu ini digunakan untuk mengelola akun staf pengelola sistem.</p>
                        <div class="mt-4 space-y-4">
                            <div class="bg-muted/50 rounded-lg p-3 border border-border/50">
                                <span class="text-xs font-bold text-foreground block mb-1">Peran Pengguna</span>
                                <ul class="list-disc list-inside text-xs text-muted-foreground space-y-1">
                                    <li><strong>Super Admin:</strong> Akses penuh ke seluruh fitur sistem.</li>
                                    <li><strong>Administrasi:</strong> Mengelola data santri, wali, rombel, dan SPMB.</li>
                                    <li><strong>Bendahara:</strong> Mengelola keuangan, tagihan, pembayaran, diskon, dan laporan.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @elseif(request()->routeIs('dashboard') || request()->routeIs('admin.dashboard'))
                    <div>
                        <h4 class="font-semibold text-primary mb-2">Panduan Dashboard</h4>
                        <p class="text-xs text-muted-foreground leading-relaxed">Dashboard menampilkan ringkasan statistik sistem pesantren secara real-time.</p>
                        <div class="mt-4 space-y-4">
                            <div class="bg-muted/50 rounded-lg p-3 border border-border/50">
                                <span class="text-xs font-bold text-foreground block mb-1">Kartu Ringkasan</span>
                                <p class="text-xs text-muted-foreground">Kartu di bagian atas menampilkan jumlah santri aktif, total tagihan belum lunas, total pendapatan, dan tingkat pelunasan. Data ini diperbarui secara otomatis.</p>
                            </div>
                            <div class="bg-muted/50 rounded-lg p-3 border border-border/50">
                                <span class="text-xs font-bold text-foreground block mb-1">Navigasi Cepat</span>
                                <p class="text-xs text-muted-foreground">Gunakan menu sidebar di sebelah kiri untuk berpindah antar halaman. Klik tombol <strong>(?)</strong> melayang di pojok kanan bawah kapan saja untuk membuka panduan ini.</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div>
                        <h4 class="font-semibold text-primary mb-2">Pusat Bantuan SIM Santri</h4>
                        <p class="text-xs text-muted-foreground leading-relaxed">
                            Klik tombol <strong>(?)</strong> melayang di pojok kanan bawah untuk mendapatkan panduan kontekstual di setiap halaman.
                        </p>
                        <div class="mt-4 bg-muted/50 rounded-lg p-4 text-center border border-border/50">
                            <span class="text-xs font-medium text-foreground block mb-2">Butuh Bantuan Lainnya?</span>
                            <a href="{{ route('admin.faqs') }}" class="inline-flex justify-center items-center px-3 py-1.5 bg-primary text-primary-foreground text-xs font-semibold rounded hover:bg-primary/90 transition">
                                Buka FAQ Pengelola
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Drawer Footer -->
            <div class="p-4 border-t border-border bg-muted/20 text-center">
                <span class="text-[10px] text-muted-foreground block">SIM-SANTRI AN-NAWAWIY v1.0</span>
            </div>
        </div>
    </div>
</body>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const button = document.getElementById('mobile-menu-button');
        const sidebar = document.getElementById('sidebar');
        const closeButton = document.getElementById('mobile-sidebar-close');

        if (button && sidebar) {
            button.addEventListener('click', () => {
                sidebar.classList.toggle('hidden');
            });
        }

        if (closeButton && sidebar) {
            closeButton.addEventListener('click', () => {
                sidebar.classList.add('hidden');
            });
        }
    });
</script>

</html>
