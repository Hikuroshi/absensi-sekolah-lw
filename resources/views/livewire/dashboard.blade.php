<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-xs sm:text-sm text-gray-600">Selamat datang, {{ Auth::user()->name }}!</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="mb-8 grid grid-cols-1 gap-5 md:grid-cols-3 relative">
        @if (Auth::user()->isAdmin())
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex h-8 w-8 items-center justify-center rounded-md bg-blue-500">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="truncate text-sm font-medium text-gray-500">Total Kelas</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $attendanceStats['total_classes'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex h-8 w-8 items-center justify-center rounded-md bg-green-500">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="truncate text-sm font-medium text-gray-500">Total Absensi Hari Ini</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $attendanceStats['today_attendance_count'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex h-8 w-8 items-center justify-center rounded-md bg-purple-500">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="truncate text-sm font-medium text-gray-500">Total Pengguna</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $attendanceStats['active_users'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex h-8 w-8 items-center justify-center rounded-md bg-blue-500">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="truncate text-sm font-medium text-gray-500">
                                    @if (Auth::user()->isKetuaKelas())
                                        Jumlah Siswa di Kelas
                                    @elseif (Auth::user()->isSiswa())
                                        Total Absen Saya
                                    @else
                                        Kelas Saya
                                    @endif
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    @if (Auth::user()->isKetuaKelas())
                                        {{ $attendanceStats['jumlah_siswa_kelas'] ?? 0 }}
                                    @elseif (Auth::user()->isSiswa())
                                        {{ $attendanceStats['total_absen_saya'] ?? 0 }}
                                    @else
                                        {{ $attendanceStats['my_classes'] ?? 0 }}
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex h-8 w-8 items-center justify-center rounded-md bg-green-500">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="truncate text-sm font-medium text-gray-500">
                                    @if (Auth::user()->isAdmin())
                                        Total Absensi Hari Ini
                                    @elseif (Auth::user()->isGuru())
                                        Mata Pelajaran Diajar
                                    @else
                                        Mata Pelajaran di Kelas
                                    @endif
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $attendanceStats['today_attendance_count'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex h-8 w-8 items-center justify-center rounded-md bg-yellow-500">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="truncate text-sm font-medium text-gray-500">Total Absensi Hari Ini</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ $attendanceStats['total_absensi_lengkap'] ?? 0 }} Sesi
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Jadwal Hari Ini + Status Absensi (Guru & Ketua Kelas) -->
    @if (count($todaySchedules) > 0)
        <div class="mt-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                <h3 class="text-lg font-bold text-gray-900">Jadwal Hari Ini & Status Absensi</h3>
                <p 
                x-data="{ currentTime: '{{ now()->translatedFormat('l, d F Y — H:i:s') }} WIB' }" 
                x-init="setInterval(() => {
                    const now = new Date();
                    const options = { 
                    weekday: 'long', 
                    day: 'numeric', 
                    month: 'long', 
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false
                    };
                    
                    const formatted = new Intl.DateTimeFormat('id-ID', options).format(now);
                    // Ganti ' pukul ' dengan ' — ' dan '.' dengan ':'
                    currentTime = formatted.replace(' pukul ', ' — ')
                                        .replace(/\./g, ':') + ' WIB';
                }, 1000)"
                x-text="currentTime"
                class="text-xs text-gray-600"
                ></p>
            </div>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 mb-3 mt-5 sm:mb-4"></h3>
        
        <!-- Mobile View (Cards) -->
        <div class="sm:hidden space-y-4">
            @foreach ($todaySchedules as $jadwal)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="mb-2">
                        <span class="text-xs text-gray-500">Kelas</span>
                        <span class="block text-sm font-medium">{{ $jadwal['kelas'] }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-xs text-gray-500">Guru</span>
                        <span class="block text-sm">{{ $jadwal['guru'] }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-xs text-gray-500">Pelajaran</span>
                        <span class="block text-sm">{{ $jadwal['mapel'] }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-xs text-gray-500">Waktu</span>
                        <span class="block text-sm">
                            @if ($jadwal['start_time'] && $jadwal['end_time'])
                                {{ \Carbon\Carbon::parse($jadwal['start_time'])->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal['end_time'])->format('H:i') }}
                            @else
                                -
                            @endif
                        </span>
                    </div>
                    <div class="mb-2">
                        <span class="text-xs text-gray-500">Absen Masuk</span>
                        <span class="block text-sm">{{ $jadwal['absen_masuk'] }}/{{ $jadwal['total_siswa'] }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-xs text-gray-500">Absen Keluar</span>
                        <span class="block text-sm">{{ $jadwal['absen_keluar'] }}/{{ $jadwal['total_siswa'] }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500">Status</span>
                        <span class="block text-sm">
                            @if ($jadwal['status'] === 'lengkap')
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-green-100 text-green-700 text-xs font-semibold">✅ Lengkap</span>
                            @elseif ($jadwal['status'] === 'belum_lengkap')
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-yellow-100 text-yellow-700 text-xs font-semibold">⚠️ Belum Lengkap</span>
                            @elseif ($jadwal['status'] === 'belum_absen_masuk')
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-100 text-blue-700 text-xs font-semibold">⬅️ Belum Absen Masuk</span>
                            @elseif ($jadwal['status'] === 'belum_absen_keluar')
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-purple-100 text-purple-700 text-xs font-semibold">➡️ Belum Absen Keluar</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-red-100 text-red-700 text-xs font-semibold">❌ Belum Absen</span>
                            @endif
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Desktop View (Table) -->
        <div class="hidden sm:block bg-white rounded-lg shadow-sm border border-gray-200 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guru</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelajaran</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Absen Masuk</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Absen Keluar</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($todaySchedules as $jadwal)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $jadwal['kelas'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $jadwal['guru'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $jadwal['mapel'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                @if ($jadwal['start_time'] && $jadwal['end_time'])
                                    {{ \Carbon\Carbon::parse($jadwal['start_time'])->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal['end_time'])->format('H:i') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">{{ $jadwal['absen_masuk'] }}/{{ $jadwal['total_siswa'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">{{ $jadwal['absen_keluar'] }}/{{ $jadwal['total_siswa'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                @if ($jadwal['status'] === 'lengkap')
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-green-100 text-green-700 text-xs font-semibold">✅ Lengkap</span>
                                @elseif ($jadwal['status'] === 'belum_lengkap')
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-yellow-100 text-yellow-700 text-xs font-semibold">⚠️ Belum Lengkap</span>
                                @elseif ($jadwal['status'] === 'belum_absen_masuk')
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-blue-100 text-blue-700 text-xs font-semibold">⬅️ Belum Absen Masuk</span>
                                @elseif ($jadwal['status'] === 'belum_absen_keluar')
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-purple-100 text-purple-700 text-xs font-semibold">➡️ Belum Absen Keluar</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-red-100 text-red-700 text-xs font-semibold">❌ Belum Absen</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>
