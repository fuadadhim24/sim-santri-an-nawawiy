<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SIM-SANTRI') }} - Wali Santri</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Source+Code+Pro:ital,wght@0,200..900;1,200..900&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-background text-foreground">
    <div class="flex min-h-screen bg-background">
        <!-- Sidebar -->
        <aside id="sidebar"
            class="w-64 bg-sidebar border-r border-sidebar-border text-sidebar-foreground hidden md:flex md:flex-col fixed h-full transition-all duration-300 z-30 flex flex-col">
            <div class="h-16 flex items-center justify-between px-6 border-b border-sidebar-border">
                <h1 class="text-xl font-bold text-sidebar-primary tracking-tight">WALI SANTRI</h1>
                <button id="mobile-sidebar-close" class="md:hidden text-sidebar-foreground hover:text-sidebar-primary">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                <!-- Dashboard Link -->
                <a href="{{ route('wali.dashboard') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('wali.dashboard') ? 'bg-sidebar-primary text-sidebar-primary-foreground' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }} group transition-colors">
                    <svg class="mr-3 h-5 w-5 {{ request()->routeIs('wali.dashboard') ? 'text-white' : 'text-sidebar-foreground group-hover:text-sidebar-accent-foreground' }}"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                    </svg>
                    Dasbor
                </a>

                <a href="{{ route('wali.spmb-schedules') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('wali.spmb-schedules*') ? 'bg-sidebar-primary text-sidebar-primary-foreground' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }} group transition-colors">
                    <svg class="mr-3 h-5 w-5 {{ request()->routeIs('wali.spmb-schedules*') ? 'text-white' : 'text-sidebar-foreground group-hover:text-sidebar-accent-foreground' }}"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                    Pendaftaran SPMB
                </a>

                <!-- FAQ Link -->
                <a href="{{ route('wali.faq') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('wali.faq') ? 'bg-sidebar-primary text-sidebar-primary-foreground' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }} group transition-colors">
                    <svg class="mr-3 h-5 w-5 {{ request()->routeIs('wali.faq') ? 'text-white' : 'text-sidebar-foreground group-hover:text-sidebar-accent-foreground' }}"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                    </svg>
                    FAQ & Informasi
                </a>
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
                        <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

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
                                Dasbor Saya
                            @endif
                        </h2>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- User Dropdown Placeholder -->
                        <span class="text-sm font-medium text-foreground">{{ Auth::user()->name }}</span>
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
