<x-layout.app :$title>
    @php
        $typeLabels = [
            'today' => 'Hari ini',
            'week' => 'Minggu ini',
            'month' => 'Bulan ini',
            'year' => 'Tahun ini',
            'all' => 'Semua',
        ];
    @endphp

    <!-- Time Period Selector -->
    <div class="mb-6 flex flex-wrap items-center gap-2 sm:flex-nowrap">
        <span class="w-full text-sm text-white/60 sm:w-auto">Filter:</span>

        <a class="{{ $type === 'today' ? 'bg-purple-600' : 'bg-white/10' }} rounded-lg px-3 py-1 text-sm" href="?type=today">Hari Ini</a>
        <a class="{{ $type === 'week' ? 'bg-purple-600' : 'bg-white/10' }} rounded-lg px-3 py-1 text-sm" href="?type=week">Minggu Ini</a>
        <a class="{{ $type === 'month' ? 'bg-purple-600' : 'bg-white/10' }} rounded-lg px-3 py-1 text-sm" href="?type=month">Bulan Ini</a>
        <a class="{{ $type === 'year' ? 'bg-purple-600' : 'bg-white/10' }} rounded-lg px-3 py-1 text-sm" href="?type=year">Tahun Ini</a>
        <a class="{{ $type === 'all' ? 'bg-purple-600' : 'bg-white/10' }} rounded-lg px-3 py-1 text-sm" href="?type=all">Semua</a>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <!-- Total Attendance -->
        <div class="glass-card-default rounded-lg border border-purple-400/30 bg-purple-500/20 p-5 text-white">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold">Total Kehadiran</h3>
                <x-lucide-users class="h-5 w-5 text-purple-300" />
            </div>
            <div class="mt-4 text-3xl font-bold">
                {{ array_sum($totalSummary) }}
            </div>
            <p class="mt-1 text-sm text-purple-200">Periode: {{ $typeLabels[$type] ?? $type }}</p>
        </div>

        <!-- Hadir -->
        <div class="glass-card-default rounded-lg border border-green-400/30 bg-green-500/20 p-5 text-white">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold">Hadir</h3>
                <x-lucide-check-circle class="h-5 w-5 text-green-300" />
            </div>
            <div class="mt-4 text-3xl font-bold">
                {{ $totalSummary['hadir'] }}
            </div>
            <p class="mt-1 text-sm text-green-200">
                {{ $todaySummary ? $todaySummary->hadir . ' hari ini' : '0 hari ini' }}
            </p>
        </div>

        <!-- Izin -->
        <div class="glass-card-default rounded-lg border border-blue-400/30 bg-blue-500/20 p-5 text-white">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold">Izin</h3>
                <x-lucide-message-square-warning class="h-5 w-5 text-blue-300" />
            </div>
            <div class="mt-4 text-3xl font-bold">
                {{ $totalSummary['izin'] }}
            </div>
            <p class="mt-1 text-sm text-blue-200">
                {{ $todaySummary ? $todaySummary->izin . ' hari ini' : '0 hari ini' }}
            </p>
        </div>

        <!-- Sakit -->
        <div class="glass-card-default rounded-lg border border-yellow-400/30 bg-yellow-500/20 p-5 text-white">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold">Sakit</h3>
                <x-lucide-thermometer class="h-5 w-5 text-yellow-300" />
            </div>
            <div class="mt-4 text-3xl font-bold">
                {{ $totalSummary['sakit'] }}
            </div>
            <p class="mt-1 text-sm text-yellow-200">
                {{ $todaySummary ? $todaySummary->sakit . ' hari ini' : '0 hari ini' }}
            </p>
        </div>

        <!-- Alpa -->
        <div class="glass-card-default rounded-lg border border-red-400/30 bg-red-500/20 p-5 text-white">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold">Alpa</h3>
                <x-lucide-x-circle class="h-5 w-5 text-red-300" />
            </div>
            <div class="mt-4 text-3xl font-bold">
                {{ $totalSummary['alpa'] }}
            </div>
            <p class="mt-1 text-sm text-red-200">
                {{ $todaySummary ? $todaySummary->alpa . ' hari ini' : '0 hari ini' }}
            </p>
        </div>

        <!-- PKL -->
        <div class="glass-card-default rounded-lg border border-gray-400/30 bg-gray-500/20 p-5 text-white">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold">PKL</h3>
                <x-lucide-briefcase class="h-5 w-5 text-gray-300" />
            </div>
            <div class="mt-4 text-3xl font-bold">
                {{ $totalSummary['pkl'] }}
            </div>
            <p class="mt-1 text-sm text-gray-200">
                {{ $todaySummary ? $todaySummary->pkl . ' hari ini' : '0 hari ini' }}
            </p>
        </div>
    </div>

    <div class="glass-card mt-8 rounded-lg p-6">
        <h3 class="mb-4 text-lg font-semibold">Statistik Kehadiran</h3>
        <canvas id="attendanceChart"></canvas>
    </div>

    <x-slot:js>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('attendanceChart');
                if (ctx) {
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['Hadir', 'Izin', 'Sakit', 'Alpa', 'PKL'],
                            datasets: [{
                                label: 'Jumlah Kehadiran',
                                data: [
                                    {{ $totalSummary['hadir'] ?? 0 }},
                                    {{ $totalSummary['izin'] ?? 0 }},
                                    {{ $totalSummary['sakit'] ?? 0 }},
                                    {{ $totalSummary['alpa'] ?? 0 }},
                                    {{ $totalSummary['pkl'] ?? 0 }}
                                ],
                                backgroundColor: [
                                    'rgba(74, 222, 128, 0.7)',
                                    'rgba(96, 165, 250, 0.7)',
                                    'rgba(234, 179, 8, 0.7)',
                                    'rgba(248, 113, 113, 0.7)',
                                    'rgba(129, 140, 248, 0.7)'
                                ],
                                borderColor: [
                                    'rgba(74, 222, 128, 1)',
                                    'rgba(96, 165, 250, 1)',
                                    'rgba(234, 179, 8, 1)',
                                    'rgba(248, 113, 113, 1)',
                                    'rgba(129, 140, 248, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    labels: {
                                        color: '#ffffff'
                                    }
                                },
                                tooltip: {
                                    bodyColor: '#ffffff',
                                    titleColor: '#ffffff',
                                    backgroundColor: '#1f2937'
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        color: '#ffffff'
                                    },
                                    grid: {
                                        color: 'rgba(255, 255, 255, 0.1)'
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        color: '#ffffff'
                                    },
                                    grid: {
                                        color: 'rgba(255, 255, 255, 0.1)'
                                    }
                                }
                            }
                        }
                    });
                }
            });
        </script>
    </x-slot:js>
</x-layout.app>
