<!DOCTYPE html>
<html class="scroll-smooth" lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Absensi Sekolah</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background:
                linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
                url('/assets/bg-school.jpg') no-repeat center center fixed;
            background-size: cover;
        }
    </style>
</head>

<body class="flex min-h-screen items-center justify-center p-4">
    <div class="glass-card w-full max-w-md overflow-hidden rounded-2xl shadow-xl">
        <div class="border-b border-white/10 bg-white/20 px-8 py-6 text-center">
            <img class="mx-auto mb-3 h-16" src="/assets/logo-school.png" alt="Logo Sekolah">
            <h1 class="text-2xl font-semibold text-white">ABSENSI SEKOLAH</h1>
            <p class="mt-1 text-white/80">Masuk untuk mengakses dashboard</p>
        </div>

        <form class="p-8" method="POST" action="{{ route('authenticate') }}">
            @csrf

            <div class="mb-5">
                <label class="mb-2 block text-sm font-medium text-white" for="username">Username</label>
                <input
                    class="w-full rounded-lg border border-white/20 bg-white/10 px-4 py-3 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500"
                    id="username"
                    name="username"
                    type="text"
                    required
                    placeholder="Masukkan username"
                >
                @error('username')
                    <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="mb-2 block text-sm font-medium text-white" for="password">Password</label>
                <div class="relative" x-data="{ showPassword: false }">
                    <input
                        class="w-full rounded-lg border border-white/20 bg-white/10 px-4 py-3 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500"
                        id="password"
                        name="password"
                        :type="showPassword ? 'text' : 'password'"
                        required
                        placeholder="••••••••"
                    >
                    <button class="absolute right-3 top-4 text-white/50 hover:text-white" type="button" x-on:click="showPassword = !showPassword">
                        <template x-if="showPassword">
                            <x-lucide-eye-off class="h-5 w-5" />
                        </template>
                        <template x-if="!showPassword">
                            <x-lucide-eye class="h-5 w-5" />
                        </template>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <button class="w-full rounded-lg bg-gradient-to-br from-indigo-600 to-purple-600 px-4 py-3 font-medium text-white shadow-lg transition-all hover:shadow-purple-500/20" type="submit">
                MASUK
            </button>
        </form>
    </div>
</body>

</html>
