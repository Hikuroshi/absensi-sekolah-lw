<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Pencatatan Absensi</h1>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('error'))
        <x-alert type="danger" :message="session('error')" />
    @endif
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
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Kelas</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" wire:model.live="selectedClass" wire:change="selectClass($event.target.value)">
                        <option value="">Pilih Kelas</option>
                        @foreach ($availableClasses as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Pelajaran</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" wire:model.live="selectedSubject" wire:change="selectSubject($event.target.value)">
                        <option value="">Pilih Pelajaran</option>
                        @foreach ($availableSubjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Sesi</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" wire:model.live="sessionType">
                        <option value="masuk">Absensi Masuk</option>
                        <option value="keluar">Absensi Keluar</option>
                    </select>
                </div>
                <div class="flex items-end">
                    @if ($isAbsensiStarted && !$isAbsensiFinished)
                        <button
                            class="w-full rounded-md bg-green-600 px-4 py-2 font-medium text-white hover:bg-green-700 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                            wire:click="finishAbsensi"
                            wire:loading.attr="disabled"
                            wire:target="finishAbsensi"
                        >
                            <span wire:loading.remove wire:target="finishAbsensi">Selesaikan Absensi</span>
                            <span wire:loading wire:target="finishAbsensi">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Menyelesaikan...
                            </span>
                        </button>
                    @else
                        <button
                            class="w-full rounded-md bg-indigo-600 px-4 py-2 font-medium text-white hover:bg-indigo-700 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                            @if (!$selectedClass || !$selectedSubject || !$sessionType || $isAbsensiStarted || $isAbsensiFinished) disabled @endif
                            wire:click="startAbsensi"
                            wire:loading.attr="disabled"
                            wire:target="startAbsensi"
                        >
                            <span wire:loading.remove wire:target="startAbsensi">Mulai Absensi</span>
                            <span wire:loading wire:target="startAbsensi">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memulai...
                            </span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if ($isAbsensiStarted && $selectedClass && $selectedSubject)
        <!-- Attendance Recording -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-4 py-5 sm:p-6">
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        Absensi {{ $availableSubjects->firstWhere('id', $selectedSubject)?->name ?? '-' }} - {{ $availableClasses->firstWhere('id', $selectedClass)?->name ?? '-' }}
                    </h3>
                    <p class="text-sm text-gray-600">
                        {{ \Carbon\Carbon::now()->format('d M Y') }}
                    </p>
                </div>

                @if (count($classMembers) > 0)
                    <!-- Mobile First Cards -->
                    <div class="space-y-4 md:hidden">
                        @foreach ($classMembers as $member)
                            @php 
                                $memberObj = is_array($member) ? (object) $member : $member;
                                $role = ($memberObj->pivot->role ?? $memberObj->role ?? '');
                            @endphp
                            <div class="rounded-lg border p-4" wire:key="member-mobile-{{ $memberObj->id }}">
                                <div class="mb-3 flex items-center justify-between">
                                    <div>
                                        <h4 class="font-medium text-gray-900 flex items-center">
                                            {{ $memberObj->name }}
                                            @if ($role === 'guru')
                                                <span class="ml-2 inline-block rounded bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-800">Guru</span>
                                            @elseif ($role === 'siswa')
                                                <span class="ml-2 inline-block rounded bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-800">Siswa</span>
                                            @elseif ($role === 'ketua_kelas')
                                                <span class="ml-2 inline-block rounded bg-yellow-100 px-2 py-0.5 text-xs font-semibold text-yellow-800">Ketua Kelas</span>
                                            @endif
                                        </h4>
                                        <p class="text-sm text-gray-600">{{ ucfirst($role ?: '-') }}</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <button 
                                        class="@if ($this->isButtonActive($memberObj->id, 'hadir')) bg-green-500 text-white ring-2 ring-green-600 font-bold @else bg-gray-100 text-gray-800 hover:bg-green-100 @endif rounded-md px-3 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-400" 
                                        wire:click="updateAttendance({{ $memberObj->id }}, 'hadir')"
                                        wire:loading.attr="disabled"
                                        wire:target="updateAttendance({{ $memberObj->id }}, 'hadir')"
                                    >
                                        <span wire:loading.remove wire:target="updateAttendance({{ $memberObj->id }}, 'hadir')">Hadir</span>
                                        <span wire:loading wire:target="updateAttendance({{ $memberObj->id }}, 'hadir')">
                                            <svg class="animate-spin h-4 w-4 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </span>
                                    </button>
                                    <button 
                                        class="@if ($this->isButtonActive($memberObj->id, 'izin')) bg-yellow-400 text-white ring-2 ring-yellow-600 font-bold @else bg-gray-100 text-gray-800 hover:bg-yellow-100 @endif rounded-md px-3 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-yellow-300" 
                                        wire:click="updateAttendance({{ $memberObj->id }}, 'izin')"
                                        wire:loading.attr="disabled"
                                        wire:target="updateAttendance({{ $memberObj->id }}, 'izin')"
                                    >
                                        <span wire:loading.remove wire:target="updateAttendance({{ $memberObj->id }}, 'izin')">Izin</span>
                                        <span wire:loading wire:target="updateAttendance({{ $memberObj->id }}, 'izin')">
                                            <svg class="animate-spin h-4 w-4 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </span>
                                    </button>
                                    <button 
                                        class="@if ($this->isButtonActive($memberObj->id, 'sakit')) bg-blue-500 text-white ring-2 ring-blue-600 font-bold @else bg-gray-100 text-gray-800 hover:bg-blue-100 @endif rounded-md px-3 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-300" 
                                        wire:click="updateAttendance({{ $memberObj->id }}, 'sakit')"
                                        wire:loading.attr="disabled"
                                        wire:target="updateAttendance({{ $memberObj->id }}, 'sakit')"
                                    >
                                        <span wire:loading.remove wire:target="updateAttendance({{ $memberObj->id }}, 'sakit')">Sakit</span>
                                        <span wire:loading wire:target="updateAttendance({{ $memberObj->id }}, 'sakit')">
                                            <svg class="animate-spin h-4 w-4 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </span>
                                    </button>
                                    <button 
                                        class="@if ($this->isButtonActive($memberObj->id, 'alpa')) bg-red-500 text-white ring-2 ring-red-600 font-bold @else bg-gray-100 text-gray-800 hover:bg-red-100 @endif rounded-md px-3 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-300" 
                                        wire:click="updateAttendance({{ $memberObj->id }}, 'alpa')"
                                        wire:loading.attr="disabled"
                                        wire:target="updateAttendance({{ $memberObj->id }}, 'alpa')"
                                    >
                                        <span wire:loading.remove wire:target="updateAttendance({{ $memberObj->id }}, 'alpa')">Alpa</span>
                                        <span wire:loading wire:target="updateAttendance({{ $memberObj->id }}, 'alpa')">
                                            <svg class="animate-spin h-4 w-4 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Desktop Table -->
                    <div class="hidden md:block">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach ($classMembers as $member)
                                    @php 
                                        $role = ($member->pivot->role ?? $member->role ?? ''); 
                                    @endphp
                                    <tr wire:key="member-{{ $member->id }}">
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 font-medium">
                                            {{ $member->name ?? '-' }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                            @if ($role === 'guru')
                                                <span class="inline-block rounded bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-800">Guru</span>
                                            @elseif ($role === 'siswa')
                                                <span class="inline-block rounded bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-800">Siswa</span>
                                            @elseif ($role === 'ketua_kelas')
                                                <span class="inline-block rounded bg-yellow-100 px-2 py-0.5 text-xs font-semibold text-yellow-800">Ketua Kelas</span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                            <div class="flex gap-2">
                                                <button 
                                                    class="@if ($this->isButtonActive($member->id, 'hadir')) bg-green-500 text-white ring-2 ring-green-600 font-bold @else bg-gray-100 text-gray-800 hover:bg-green-100 @endif rounded-md px-3 py-2 text-xs font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-400" 
                                                    wire:click="updateAttendance({{ $member->id }}, 'hadir')"
                                                    wire:loading.attr="disabled"
                                                    wire:target="updateAttendance({{ $member->id }}, 'hadir')"
                                                >
                                                    <span wire:loading.remove wire:target="updateAttendance({{ $member->id }}, 'hadir')">Hadir</span>
                                                    <span wire:loading wire:target="updateAttendance({{ $member->id }}, 'hadir')">...</span>
                                                </button>
                                                <button 
                                                    class="@if ($this->isButtonActive($member->id, 'izin')) bg-yellow-400 text-white ring-2 ring-yellow-600 font-bold @else bg-gray-100 text-gray-800 hover:bg-yellow-100 @endif rounded-md px-3 py-2 text-xs font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-yellow-300" 
                                                    wire:click="updateAttendance({{ $member->id }}, 'izin')"
                                                    wire:loading.attr="disabled"
                                                    wire:target="updateAttendance({{ $member->id }}, 'izin')"
                                                >
                                                    <span wire:loading.remove wire:target="updateAttendance({{ $member->id }}, 'izin')">Izin</span>
                                                    <span wire:loading wire:target="updateAttendance({{ $member->id }}, 'izin')">...</span>
                                                </button>
                                                <button 
                                                    class="@if ($this->isButtonActive($member->id, 'sakit')) bg-blue-500 text-white ring-2 ring-blue-600 font-bold @else bg-gray-100 text-gray-800 hover:bg-blue-100 @endif rounded-md px-3 py-2 text-xs font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-300" 
                                                    wire:click="updateAttendance({{ $member->id }}, 'sakit')"
                                                    wire:loading.attr="disabled"
                                                    wire:target="updateAttendance({{ $member->id }}, 'sakit')"
                                                >
                                                    <span wire:loading.remove wire:target="updateAttendance({{ $member->id }}, 'sakit')">Sakit</span>
                                                    <span wire:loading wire:target="updateAttendance({{ $member->id }}, 'sakit')">...</span>
                                                </button>
                                                <button 
                                                    class="@if ($this->isButtonActive($member->id, 'alpa')) bg-red-500 text-white ring-2 ring-red-600 font-bold @else bg-gray-100 text-gray-800 hover:bg-red-100 @endif rounded-md px-3 py-2 text-xs font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-300" 
                                                    wire:click="updateAttendance({{ $member->id }}, 'alpa')"
                                                    wire:loading.attr="disabled"
                                                    wire:target="updateAttendance({{ $member->id }}, 'alpa')"
                                                >
                                                    <span wire:loading.remove wire:target="updateAttendance({{ $member->id }}, 'alpa')">Alpa</span>
                                                    <span wire:loading wire:target="updateAttendance({{ $member->id }}, 'alpa')">...</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <p>Tidak ada anggota kelas yang tersedia untuk diabsen.</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>