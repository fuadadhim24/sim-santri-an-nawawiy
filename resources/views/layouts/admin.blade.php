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
                <a href="{{ route('dashboard') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('dashboard') ? 'bg-sidebar-primary text-sidebar-primary-foreground' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }} group transition-colors">
                    <svg class="mr-3 h-5 w-5 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-sidebar-foreground group-hover:text-sidebar-accent-foreground' }}"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                    </svg>
                    Dashboard
                </a>

                <div class="pt-4 pb-2">
                    <p class="px-3 text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                        Master Data
                    </p>
                </div>

                <a href="{{ route('admin.users') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.users') ? 'bg-sidebar-primary text-sidebar-primary-foreground' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }} group transition-colors">
                    <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.users') ? 'text-white' : 'text-sidebar-foreground group-hover:text-sidebar-accent-foreground' }}"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                    Users
                </a>

                <a href="{{ route('admin.guardians') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.guardians') ? 'bg-sidebar-primary text-sidebar-primary-foreground' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }} group transition-colors">
                    <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.guardians') ? 'text-white' : 'text-sidebar-foreground group-hover:text-sidebar-accent-foreground' }}"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                    </svg>
                    Guardians
                </a>

                <a href="{{ route('admin.students') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.students*') ? 'bg-sidebar-primary text-sidebar-primary-foreground' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }} group transition-colors">
                    <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.students*') ? 'text-white' : 'text-sidebar-foreground group-hover:text-sidebar-accent-foreground' }}"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.499 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
                    </svg>
                    Students
                </a>

                <a href="{{ route('admin.fee-masters') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.fee-masters*') ? 'bg-sidebar-primary text-sidebar-primary-foreground' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }} group transition-colors">
                    <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.fee-masters*') ? 'text-white' : 'text-sidebar-foreground group-hover:text-sidebar-accent-foreground' }}"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                    </svg>
                    Fee Masters
                </a>

                <a href="{{ route('admin.discounts') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.discounts*') ? 'bg-sidebar-primary text-sidebar-primary-foreground' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }} group transition-colors">
                    <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.discounts*') ? 'text-white' : 'text-sidebar-foreground group-hover:text-sidebar-accent-foreground' }}"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                    </svg>
                    Discounts
                </a>

                <a href="{{ route('admin.billings') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.billings*') ? 'bg-sidebar-primary text-sidebar-primary-foreground' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }} group transition-colors">
                    <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.billings*') ? 'text-white' : 'text-sidebar-foreground group-hover:text-sidebar-accent-foreground' }}"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    Invoices
                </a>

                <a href="{{ route('admin.payments.create') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.payments.create') ? 'bg-sidebar-primary text-sidebar-primary-foreground' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }} group transition-colors">
                    <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.payments.create') ? 'text-white' : 'text-sidebar-foreground group-hover:text-sidebar-accent-foreground' }}"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                    </svg>
                    Cash Payment
                </a>
            </nav>

            <div class="border-t border-sidebar-border p-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex w-full items-center px-2 py-2 text-sm font-medium text-destructive hover:bg-destructive/10 hover:text-destructive rounded-md transition-colors">
                        <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
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
