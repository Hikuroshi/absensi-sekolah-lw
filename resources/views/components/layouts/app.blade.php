<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Sistem Absensi') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-gray-50 font-sans antialiased">
    <div class="min-h-screen">
        @if(Auth::check())
        <!-- Navigation -->
        <nav class="border-b border-gray-100 bg-white/90 shadow-sm sticky top-0 z-40" x-data="{ open: false }">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-14 sm:h-16 justify-between items-center">
                    <div class="flex items-center gap-2">
                        <div class="flex-shrink-0">
                            <h1 class="text-lg sm:text-xl font-bold text-indigo-700 tracking-tight">Sistem Absensi</h1>
                        </div>
                        <div class="hidden md:ml-6 md:flex md:space-x-2 lg:space-x-4">
                            <a class="rounded-md px-3 py-2 text-sm font-medium text-gray-600 hover:text-indigo-700 transition-colors duration-150" href="{{ route('dashboard') }}">Dashboard</a>
                            @if (Auth::user()->isKetuaKelas() || Auth::user()->isAdmin())
                                <a class="rounded-md px-3 py-2 text-sm font-medium text-gray-600 hover:text-indigo-700 transition-colors duration-150" href="{{ route('attendance.record') }}">Absensi</a>
                            @endif
                            <a class="rounded-md px-3 py-2 text-sm font-medium text-gray-600 hover:text-indigo-700 transition-colors duration-150" href="{{ route('attendance.report') }}">Laporan</a>
                            @if (Auth::user()->isAdmin())
                                <a class="rounded-md px-3 py-2 text-sm font-medium text-gray-600 hover:text-indigo-700 transition-colors duration-150" href="{{ route('user.management') }}">Kelola User</a>
                                <a class="rounded-md px-3 py-2 text-sm font-medium text-gray-600 hover:text-indigo-700 transition-colors duration-150" href="{{ route('class.management') }}">Kelola Kelas</a>
                                <a class="rounded-md px-3 py-2 text-sm font-medium text-gray-600 hover:text-indigo-700 transition-colors duration-150" href="{{ route('subject.management') }}">Kelola Pelajaran</a>
                                <a class="rounded-md px-3 py-2 text-sm font-medium text-gray-600 hover:text-indigo-700 transition-colors duration-150" href="{{ route('schedule.management') }}">Kelola Jadwal</a>
                            @endif
                        </div>
                    </div>
                    <!-- Profile & Hamburger -->
                    <div class="flex items-center">
                        <!-- Hamburger for mobile -->
                        <button class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-indigo-700 focus:outline-none md:hidden transition-colors duration-150" @click="open = !open">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <!-- Profile (hidden on mobile, shown on md+) -->
                        <div class="relative hidden md:block" x-data="{ open: false }">
                            <button class="flex items-center rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-shadow duration-150" @click="open = !open">
                                <span class="sr-only">Open user menu</span>
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100">
                                    <span class="text-sm font-medium text-indigo-700">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                </div>
                            </button>
                            <div class="absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-lg bg-white shadow-lg border border-gray-200 focus:outline-none transition-all duration-200" x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                                <div class="py-2 px-4 pb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100">
                                            <span class="text-base font-semibold text-indigo-700">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ Auth::user()->name }}</div>
                                            <div class="text-gray-500 text-xs">{{ ucwords(str_replace('_', ' ', Auth::user()->role)) }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="border-t border-gray-200"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="block w-full px-4 py-3 text-left text-sm font-medium text-gray-600 hover:text-indigo-700 transition-colors duration-150 rounded-b-lg" type="submit">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Mobile menu, show/hide based on hamburger -->
            <div class="md:hidden" x-show="open" @click.away="open = false">
                <div class="space-y-1 px-2 pb-3 pt-2 sm:px-3 flex flex-col">
                    <a class="block rounded-md px-3 py-2 text-base font-medium text-gray-600 hover:text-indigo-700 transition-colors duration-150" href="{{ route('dashboard') }}">Dashboard</a>
                    @if (Auth::user()->isKetuaKelas() || Auth::user()->isAdmin())
                        <a class="block rounded-md px-3 py-2 text-base font-medium text-gray-600 hover:text-indigo-700 transition-colors duration-150" href="{{ route('attendance.record') }}">Absensi</a>
                    @endif
                    <a class="block rounded-md px-3 py-2 text-base font-medium text-gray-600 hover:text-indigo-700 transition-colors duration-150" href="{{ route('attendance.report') }}">Laporan</a>
                    @if (Auth::user()->isAdmin())
                        <a class="block rounded-md px-3 py-2 text-base font-medium text-gray-600 hover:text-indigo-700 transition-colors duration-150" href="{{ route('user.management') }}">Kelola User</a>
                        <a class="block rounded-md px-3 py-2 text-base font-medium text-gray-600 hover:text-indigo-700 transition-colors duration-150" href="{{ route('class.management') }}">Kelola Kelas</a>
                        <a class="block rounded-md px-3 py-2 text-base font-medium text-gray-600 hover:text-indigo-700 transition-colors duration-150" href="{{ route('subject.management') }}">Kelola Pelajaran</a>
                        <a class="block rounded-md px-3 py-2 text-base font-medium text-gray-600 hover:text-indigo-700 transition-colors duration-150" href="{{ route('schedule.management') }}">Kelola Jadwal</a>
                    @endif
                    <!-- Profile in mobile menu -->
                    <div class="border-t border-gray-200 mt-2 pt-2">
                        <div class="flex items-center space-x-3 px-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100">
                                <span class="text-sm font-medium text-indigo-700">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ Auth::user()->name }}</div>
                                <div class="text-gray-500 text-sm">{{ ucwords(str_replace('_', ' ', Auth::user()->role)) }}</div>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="mt-2">
                            @csrf
                            <button class="block rounded-md px-3 py-2 text-base font-medium text-gray-600 hover:text-indigo-700 transition-colors duration-150" type="submit">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
        @endif
        <!-- Page Content -->
        <main class="{{ request()->routeIs('login') ? '' : 'py-6' }}">
            {{ $slot }}
        </main>
    </div>
    @livewireScripts
</body>

</html>
