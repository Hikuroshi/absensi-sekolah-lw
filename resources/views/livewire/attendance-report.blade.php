<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Laporan Absensi</h1>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <x-alert type="success" :message="session('message')" />
    @endif

    <div 
        wire:loading 
        class="fixed bottom-4 right-4 z-50"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
    >
        <x-loading />
    </div>

    <!-- Filter Section -->
    <div class="mb-6 rounded-lg bg-white shadow-sm border border-gray-200">
        <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                @if (Auth::user()->isAdmin() || Auth::user()->isGuru())
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" wire:model.live="selectedClass">
                            <option value="">Semua Kelas</option>
                            @foreach ($availableClasses as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                    <input class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" type="date" wire:model.live="dateFrom">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                    <input class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" type="date" wire:model.live="dateTo">
                </div>
                <div class="flex items-end">
                    <button
                        class="w-full rounded-md bg-green-600 px-4 py-2 font-medium text-white hover:bg-green-700 transition-colors duration-200"
                        onclick="window.location.href='{{ route('attendance.report.export', [
                            'dateFrom' => $dateFrom,
                            'dateTo' => $dateTo,
                            'selectedClass' => $selectedClass,
                        ]) }}'"
                        type="button"
                    >
                        Ekspor Excel
                    </button>
                </div>
            </div>
            @if ((Auth::user()->isAdmin() || Auth::user()->isGuru()))
                <div class="mt-2">
                    <input 
                        type="text" 
                        placeholder="Cari nama siswa..." 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                        wire:model.live.debounce.500ms="searchStudent"
                        x-data
                        x-on:reset-student-search.window="$wire.searchStudent = ''"
                    />
                </div>
            @endif
        </div>
    </div>

    <!-- Attendance Data -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Data Absensi</h3>
            <p class="mb-4 text-sm text-gray-600">Jumlah absen adalah <b>(masuk - keluar)</b></p>

            @if ($attendanceData->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Kelas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Pelajaran</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Hadir</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Izin</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Sakit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Alpa</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @if ($attendanceData->count() > 0)
                                @foreach ($attendanceData as $record)
                                    @php
                                        $pivotRole = null;
                                        if ($record->user && $record->class && $record->class->relationLoaded('members')) {
                                            $pivot = $record->class->members->firstWhere('id', $record->user->id);
                                            $pivotRole = $pivot?->pivot?->role;
                                        }
                                    @endphp
                                    <tr wire:key="attendance-row-{{ $record->user_id }}-{{ $record->class_id }}-{{ $record->subject_id }}">
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                            {{ $record->checked_at->format('d M Y') }} <br>
                                            {{ $record->checked_at->format('H:m:s') }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $record->user->name }}</div>
                                            <div class="text-sm text-gray-500">
                                                {{ $pivotRole ? ucfirst(str_replace('_', ' ', $pivotRole)) : $record->user->role_label }}
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                            {{ $record->class->name ?? '-' }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                            {{ $record->subject->name ?? '-' }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                            <span class="bg-green-100 text-green-800 inline-flex rounded-full px-2 text-xs font-semibold leading-5">
                                                {{ $record->hadir_masuk ?? 0 }} - {{ $record->hadir_keluar ?? 0 }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                            <span class="bg-yellow-100 text-yellow-800 inline-flex rounded-full px-2 text-xs font-semibold leading-5">
                                                {{ $record->izin_masuk ?? 0 }} - {{ $record->izin_keluar ?? 0 }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                            <span class="bg-blue-100 text-blue-800 inline-flex rounded-full px-2 text-xs font-semibold leading-5">
                                                {{ $record->sakit_masuk ?? 0 }} - {{ $record->sakit_keluar ?? 0 }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                            <span class="bg-red-100 text-red-800 inline-flex rounded-full px-2 text-xs font-semibold leading-5">
                                                {{ $record->alpa_masuk ?? 0 }} - {{ $record->sakit_keluar ?? 0 }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            @elseif ($attendanceData->count() === 0 && $attendanceData->onFirstPage())
                                <tr>
                                    <td colspan="8" class="text-center py-8 text-gray-500">Tidak ada data absensi</td>
                                </tr>
                            @endif
                            <!-- Skeleton loader saat loading -->
                            <template x-if="$wire.__instance.loading">
                                <tr>
                                    <td colspan="8" class="py-4">
                                        <div class="flex space-x-2 animate-pulse">
                                            <div class="h-4 bg-gray-200 rounded w-1/12"></div>
                                            <div class="h-4 bg-gray-200 rounded w-2/12"></div>
                                            <div class="h-4 bg-gray-200 rounded w-1/12"></div>
                                            <div class="h-4 bg-gray-200 rounded w-2/12"></div>
                                            <div class="h-4 bg-gray-200 rounded w-1/12"></div>
                                            <div class="h-4 bg-gray-200 rounded w-1/12"></div>
                                            <div class="h-4 bg-gray-200 rounded w-1/12"></div>
                                            <div class="h-4 bg-gray-200 rounded w-1/12"></div>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    <x-pagination :model="$attendanceData" />
                </div>
            @else
                <div class="py-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data absensi</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if($selectedClass && !empty($searchStudent))
                            Tidak ditemukan data absensi untuk kelas "{{ $availableClasses->firstWhere('id', $selectedClass)->name ?? 'Tidak diketahui' }}" dengan pencarian "{{ $searchStudent }}" dalam periode yang dipilih.
                        @elseif($selectedClass)
                            Tidak ditemukan data absensi untuk kelas "{{ $availableClasses->firstWhere('id', $selectedClass)->name ?? 'Tidak diketahui' }}" dalam periode yang dipilih.
                        @elseif(!empty($searchStudent))
                            Tidak ditemukan data absensi dengan pencarian "{{ $searchStudent }}" dalam periode yang dipilih.
                        @else
                            Tidak ditemukan data absensi untuk periode yang dipilih.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
