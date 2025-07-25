<x-layout.app :$title>
    @if (session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    <div class="glass-card rounded-xl p-6 text-center">
        <h2 class="text-xl font-semibold">Pilih tipe sesi absensi</h2>
        <p class="mb-6 text-sm text-white/60">
            Pilih <span class="font-bold">Masuk/Keluar </span>
            untuk absensi hari <span class="font-bold">{{ now()->translatedFormat('l') }} </span>
            tanggal <span class="font-bold">{{ now()->translatedFormat('j F Y') }}</span>.
        </p>

        <div class="flex flex-row items-center justify-center gap-6 text-center">
            <a class="flex w-full items-center justify-center whitespace-nowrap rounded-lg bg-purple-600 px-4 py-2 text-sm font-medium text-white hover:bg-purple-700 sm:w-auto" href="{{ route('student-attendance.create', 'masuk') }}">
                <x-lucide-log-in class="mr-2 h-4 w-4" />
                Masuk
            </a>
            @if ($has_masuk)
                <a class="flex w-full items-center justify-center whitespace-nowrap rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 sm:w-auto" href="{{ route('student-attendance.create', 'keluar') }}">
                    <x-lucide-log-out class="mr-2 h-4 w-4" />
                    Keluar
                </a>
            @else
                <span class="flex w-full cursor-not-allowed items-center justify-center whitespace-nowrap rounded-lg bg-blue-600/20 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700/20 sm:w-auto">
                    <x-lucide-log-out class="mr-2 h-4 w-4" />
                    Keluar
                </span>
            @endif
        </div>
    </div>
</x-layout.app>
