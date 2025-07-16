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
    <div class="min-h-screen flex" x-data="{ sidebarOpen: window.innerWidth >= 768 }">
        @if(Auth::check())
        <!-- Sidebar Backdrop (Mobile) -->
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" 
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-50" 
             x-transition:leave="transition-opacity ease-linear duration-300" 
             x-transition:leave-start="opacity-50" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 bg-gray-900 opacity-50 md:hidden" 
             @click="sidebarOpen = false" style="display: none;"></div>

        <!-- Sidebar -->
        <div 
             @resize.window="sidebarOpen = window.innerWidth >= 768"
             :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}"
             class="fixed md:relative z-50 w-64 h-screen bg-white border-r border-gray-200 transform transition-transform duration-300 ease-in-out flex-shrink-0">
            <div class="flex flex-col h-full">
                <!-- Logo/Sistem Name -->
                <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200">
                    <h1 class="text-lg font-bold text-indigo-700 tracking-tight">Sistem Absensi</h1>
                    <button @click="sidebarOpen = false" class="md:hidden p-1 rounded-md text-gray-500 hover:text-indigo-700 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <!-- Navigation -->
                <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">
                    <a class="flex items-center px-3 py-2 text-sm font-medium rounded-md 
                        {{ request()->routeIs('dashboard') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-700' }} group" 
                        href="{{ route('dashboard') }}">
                        <svg class="h-5 w-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-indigo-700' : 'text-gray-500 group-hover:text-indigo-700' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>
                    
                    @if (Auth::user()->isKetuaKelas() || Auth::user()->isAdmin())
                    <a class="flex items-center px-3 py-2 text-sm font-medium rounded-md 
                        {{ request()->routeIs('attendance.record') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-700' }} group" 
                        href="{{ route('attendance.record') }}">
                        <svg class="h-5 w-5 mr-3 {{ request()->routeIs('attendance.record') ? 'text-indigo-700' : 'text-gray-500 group-hover:text-indigo-700' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Absensi
                    </a>
                    @endif
                    
                    <a class="flex items-center px-3 py-2 text-sm font-medium rounded-md 
                        {{ request()->routeIs('attendance.report') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-700' }} group" 
                        href="{{ route('attendance.report') }}">
                        <svg class="h-5 w-5 mr-3 {{ request()->routeIs('attendance.report') ? 'text-indigo-700' : 'text-gray-500 group-hover:text-indigo-700' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Laporan
                    </a>
                    
                    @if (Auth::user()->isAdmin())
                    <div class="px-3 pt-4 pb-2">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Administrasi</span>
                    </div>
                    
                    <a class="flex items-center px-3 py-2 text-sm font-medium rounded-md 
                        {{ request()->routeIs('user.management') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-700' }} group" 
                        href="{{ route('user.management') }}">
                        <svg class="h-5 w-5 mr-3 {{ request()->routeIs('user.management') ? 'text-indigo-700' : 'text-gray-500 group-hover:text-indigo-700' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Kelola User
                    </a>
                    
                    <a class="flex items-center px-3 py-2 text-sm font-medium rounded-md 
                        {{ request()->routeIs('class.management') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-700' }} group" 
                        href="{{ route('class.management') }}">
                        <svg class="h-5 w-5 mr-3 {{ request()->routeIs('class.management') ? 'text-indigo-700' : 'text-gray-500 group-hover:text-indigo-700' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Kelola Kelas
                    </a>
                    
                    <a class="flex items-center px-3 py-2 text-sm font-medium rounded-md 
                        {{ request()->routeIs('subject.management') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-700' }} group" 
                        href="{{ route('subject.management') }}">
                        <svg class="h-5 w-5 mr-3 {{ request()->routeIs('subject.management') ? 'text-indigo-700' : 'text-gray-500 group-hover:text-indigo-700' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Kelola Pelajaran
                    </a>
                    
                    <a class="flex items-center px-3 py-2 text-sm font-medium rounded-md 
                        {{ request()->routeIs('schedule.management') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-700' }} group" 
                        href="{{ route('schedule.management') }}">
                        <svg class="h-5 w-5 mr-3 {{ request()->routeIs('schedule.management') ? 'text-indigo-700' : 'text-gray-500 group-hover:text-indigo-700' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Kelola Jadwal
                    </a>
                    @endif
                </nav>
                
                <!-- User Profile & Logout -->
                <div class="px-4 py-4 border-t border-gray-200">
                    <div class="flex items-center">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100">
                            <span class="text-sm font-medium text-indigo-700">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-gray-500">{{ ucwords(str_replace('_', ' ', Auth::user()->role)) }}</div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="mt-3">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-indigo-50 hover:text-indigo-700 group">
                            <svg class="h-5 w-5 mr-3 text-gray-500 group-hover:text-indigo-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden h-screen">
            @if(Auth::check())
            <!-- Mobile Top Header -->
            <header class="md:hidden bg-white border-b border-gray-200 sticky top-0 z-40">
                <div class="flex items-center justify-between px-4 h-14">
                    <button @click="sidebarOpen = true" class="p-1 rounded-md text-gray-500 hover:text-indigo-700 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="text-lg font-bold text-indigo-700">Sistem Absensi</h1>
                    <div class="w-6"></div> <!-- Spacer untuk balance -->
                </div>
            </header>
            @endif
            
            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto {{ request()->routeIs('login') ? '' : 'p-4 md:p-6' }}">
                {{ $slot }}
            </main>
        </div>
    </div>
    @livewireScripts
</body>

</html>