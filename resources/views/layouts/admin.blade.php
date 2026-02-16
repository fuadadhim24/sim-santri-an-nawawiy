<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SIM-SANTRI') }}</title>

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
        <aside
            class="w-64 bg-sidebar border-r border-sidebar-border text-sidebar-foreground hidden md:flex md:flex-col fixed h-full transition-all duration-300 z-30">
            <div class="h-16 flex items-center justify-center border-b border-sidebar-border px-6">
                <h1 class="text-xl font-bold text-sidebar-primary tracking-tight">SIM-SANTRI</h1>
            </div>

            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                <!-- Dashboard Link -->
                <a href="#"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-sidebar-primary-foreground bg-sidebar-primary hover:bg-sidebar-primary/90 group">
                    <svg class="mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                    </svg>
                    Dashboard
                </a>

                <!-- Placeholder for other links -->
                <a href="#"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground group transition-colors">
                    <svg class="mr-3 h-5 w-5 text-sidebar-foreground group-hover:text-sidebar-accent-foreground"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                    Data Master
                </a>
            </nav>

            <div class="border-t border-sidebar-border p-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex w-full items-center px-2 py-2 text-sm font-medium text-destructive hover:bg-destructive/10 hover:text-destructive rounded-md transition-colors">
                        <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col md:ml-64 transition-all duration-300">
            <!-- Top Navbar -->
            <header
                class="bg-card shadow-sm border-b border-border h-16 flex items-center justify-between px-6 sticky top-0 z-20">
                <div class="flex items-center">
                    <!-- Mobile menu button -->
                    <button
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
                            Dashboard
                        @endif
                    </h2>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Notification Icon Placeholder -->
                    <button class="p-2 rounded-full text-foreground hover:bg-muted relative">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                        <span
                            class="absolute top-2 right-2 block h-2 w-2 rounded-full bg-destructive ring-2 ring-card"></span>
                    </button>

                    <!-- User Dropdown Placeholder -->
                    <span class="text-sm font-medium text-foreground">{{ Auth::user()->name ?? 'Admin' }}</span>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-6 overflow-y-auto bg-muted/30">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>

</html>
