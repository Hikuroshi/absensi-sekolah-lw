<!DOCTYPE html>
<html class="scroll-smooth" lang="id" x-data="{ sidebarOpen: false }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - Sistem Absensi Sekolah</title>
    <link type="image/x-icon" href="{{ asset('assets/logo-school.png') }}" rel="icon">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background:
                linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                url('{{ asset('assets/bg-school.jpg') }}') no-repeat center center fixed;
            background-size: cover;
        }
    </style>

    {{ $css ?? '' }}
</head>

<body class="min-h-screen text-gray-100">
    <!-- Sidebar -->
    <x-layout.sidebar />

    <div class="ml-0 min-h-screen transition-all duration-300 md:ml-64">
        <!-- Header -->
        <header class="glass-card sticky top-0 z-10 border-b border-white/10 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold">{{ $title }}</h1>
                <div class="flex items-center space-x-4">
                    <button class="glass-card rounded-full p-2 md:hidden" x-on:click="sidebarOpen = !sidebarOpen">
                        <x-lucide-menu class="h-5 w-5" />
                    </button>
                    <button class="glass-card rounded-full p-2">
                        <x-lucide-bell class="h-5 w-5" />
                    </button>
                    <div class="relative" x-data="{ settingsOpen: false }">
                        <button class="glass-card rounded-full p-2" x-on:click="settingsOpen = !settingsOpen" x-on:click.outside="settingsOpen = false">
                            <x-lucide-settings class="h-5 w-5" />
                        </button>

                        <!-- Dropdown Menu -->
                        <div class="glass-card absolute right-0 mt-2 w-48 origin-top-right rounded-md shadow-lg focus:outline-none" x-show="settingsOpen" x-transition.opacity>
                            <div class="rounded-md bg-black/70 py-1">
                                <a class="block px-4 py-2 text-sm hover:bg-white/10" href="#">
                                    <div class="flex items-center">
                                        <x-lucide-user class="mr-2 h-4 w-4" />
                                        <span>Profile</span>
                                    </div>
                                </a>

                                <div class="my-1 border-t border-white/10"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="block w-full px-4 py-2 text-left text-sm hover:bg-white/10" type="submit">
                                        <div class="flex items-center">
                                            <x-lucide-log-out class="mr-2 h-4 w-4" />
                                            <span>Logout</span>
                                        </div>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="p-4">
            {{ $slot }}
        </main>
    </div>

    <!-- Overlay for mobile sidebar -->
    <div class="fixed inset-0 z-10 bg-black/50 md:hidden" x-show="sidebarOpen" x-on:click="sidebarOpen = false" x-transition.opacity></div>

    {{ $js ?? '' }}
</body>

</html>
