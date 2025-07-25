<div class="sidebar glass-card fixed left-0 top-0 z-20 h-screen w-64 overflow-y-auto" :class="{ 'open': sidebarOpen }" x-data="{
    activeCategory: null,
    categories: {
        attendance: {{ request()->routeIs('student-attendance.*', 'subject-attendance.*', 'teacher-attendance.*') ? 'true' : 'false' }},
        administrasi: {{ request()->routeIs('user.*', 'classroom.*', 'subject.*', 'schedule.*') ? 'true' : 'false' }},
        report: {{ request()->routeIs('report.*') ? 'true' : 'false' }},
        import: {{ request()->routeIs('import-*') ? 'true' : 'false' }}
    },
    toggleCategory(category) {
        Object.keys(this.categories).forEach(key => {
            this.categories[key] = false;
        });
        this.categories[category] = !this.categories[category];
        this.activeCategory = this.categories[category] ? category : null;
    }
}">
    <div class="flex h-full flex-col">
        <!-- Logo/School Name -->
        <div class="border-b border-white/10 p-4 text-center">
            <img class="mx-auto h-12" src="{{ asset('assets/logo-school.png') }}" alt="Logo Sekolah">
            <h2 class="mt-2 text-lg font-semibold">ABSENSI SEKOLAH</h2>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4">
            <ul class="space-y-4">
                <!-- Dashboard -->
                <li>
                    <a class="{{ request()->routeIs('dashboard') ? 'bg-white/10' : '' }} flex items-center rounded-lg px-4 py-3 hover:bg-white/10" href="{{ route('dashboard') }}">
                        <x-lucide-layout-dashboard class="h-5 w-5" />
                        <span class="ml-3">Dashboard</span>
                    </a>
                </li>

                <!-- Attendance -->
                @canany(['isKetuaKelas', 'isGuru'])
                    <li>
                        <button class="flex w-full items-center justify-between rounded-lg px-4 py-3 text-left hover:bg-white/10" x-on:click="toggleCategory('attendance')">
                            <div class="flex items-center">
                                <x-lucide-calendar-check class="h-5 w-5" />
                                <span class="ml-3">Absensi</span>
                            </div>
                            <x-lucide-chevron-down class="h-4 w-4 transition-transform duration-200" x-bind:class="{ 'rotate-180': categories.attendance }" />
                        </button>

                        <ul class="ml-8 mt-2 space-y-2" x-show="categories.attendance" x-collapse>
                            @can('isKetuaKelas')
                                <li>
                                    <a class="{{ request()->routeIs('student-attendance.index') ? 'bg-white/10' : '' }} flex items-center rounded-lg px-3 py-2 text-sm hover:bg-white/10" href="{{ route('student-attendance.index') }}">
                                        <x-lucide-calendar-days class="h-4 w-4" />
                                        <span class="ml-2">Siswa</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="{{ request()->routeIs('teacher-attendance.index') ? 'bg-white/10' : '' }} flex items-center rounded-lg px-3 py-2 text-sm hover:bg-white/10" href="{{ route('teacher-attendance.index') }}">
                                        <x-lucide-user-check class="h-4 w-4" />
                                        <span class="ml-2">Guru</span>
                                    </a>
                                </li>
                            @endcan
                            @can('isGuru')
                                <li>
                                    <a class="{{ request()->routeIs('subject-attendance.index') ? 'bg-white/10' : '' }} flex items-center rounded-lg px-3 py-2 text-sm hover:bg-white/10" href="{{ route('subject-attendance.index') }}">
                                        <x-lucide-book-check class="h-4 w-4" />
                                        <span class="ml-2">Mata Pelajaran</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                <!-- Administration -->
                @can('isAdmin')
                    <li>
                        <button class="flex w-full items-center justify-between rounded-lg px-4 py-3 text-left hover:bg-white/10" x-on:click="toggleCategory('administrasi')">
                            <div class="flex items-center">
                                <x-lucide-users class="h-5 w-5" />
                                <span class="ml-3">Administrasi</span>
                            </div>
                            <x-lucide-chevron-down class="h-4 w-4 transition-transform duration-200" x-bind:class="{ 'rotate-180': categories.administrasi }" />
                        </button>

                        <ul class="ml-8 mt-2 space-y-2" x-show="categories.administrasi" x-collapse>
                            <li>
                                <a class="{{ request()->routeIs('user.*') ? 'bg-white/10' : '' }} flex items-center rounded-lg px-3 py-2 text-sm hover:bg-white/10" href="{{ route('user.index') }}">
                                    <x-lucide-user class="h-4 w-4" />
                                    <span class="ml-2">User</span>
                                </a>
                            </li>
                            <li>
                                <a class="{{ request()->routeIs('classroom.*') ? 'bg-white/10' : '' }} flex items-center rounded-lg px-3 py-2 text-sm hover:bg-white/10" href="{{ route('classroom.index') }}">
                                    <x-lucide-users class="h-4 w-4" />
                                    <span class="ml-2">Kelas</span>
                                </a>
                            </li>
                            <li>
                                <a class="{{ request()->routeIs('subject.*') ? 'bg-white/10' : '' }} flex items-center rounded-lg px-3 py-2 text-sm hover:bg-white/10" href="{{ route('subject.index') }}">
                                    <x-lucide-book-text class="h-4 w-4" />
                                    <span class="ml-2">Pelajaran</span>
                                </a>
                            </li>
                            <li>
                                <a class="{{ request()->routeIs('schedule.*') ? 'bg-white/10' : '' }} flex items-center rounded-lg px-3 py-2 text-sm hover:bg-white/10" href="{{ route('schedule.index') }}">
                                    <x-lucide-calendar class="h-4 w-4" />
                                    <span class="ml-2">Jadwal</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan

                <!-- Reports -->
                <li>
                    <button class="flex w-full items-center justify-between rounded-lg px-4 py-3 text-left hover:bg-white/10" x-on:click="toggleCategory('report')">
                        <div class="flex items-center">
                            <x-lucide-file-text class="h-5 w-5" />
                            <span class="ml-3">Laporan</span>
                        </div>
                        <x-lucide-chevron-down class="h-4 w-4 transition-transform duration-200" x-bind:class="{ 'rotate-180': categories.report }" />
                    </button>

                    <ul class="ml-8 mt-2 space-y-2" x-show="categories.report" x-collapse>
                        <li>
                            <a class="flex items-center rounded-lg px-3 py-2 text-sm hover:bg-white/10" href="#">
                                <x-lucide-calendar class="h-4 w-4" />
                                <span class="ml-2">Siswa</span>
                            </a>
                        </li>
                        <li>
                            <a class="flex items-center rounded-lg px-3 py-2 text-sm hover:bg-white/10" href="#">
                                <x-lucide-calendar-clock class="h-4 w-4" />
                                <span class="ml-2">Guru</span>
                            </a>
                        </li>
                        <li>
                            <a class="flex items-center rounded-lg px-3 py-2 text-sm hover:bg-white/10" href="#">
                                <x-lucide-calendar-range class="h-4 w-4" />
                                <span class="ml-2">Mata Pelajaran</span>
                            </a>
                        </li>
                    </ul>
                </li>

                @can('isAdmin')
                    <!-- Import Data -->
                    <li>
                        <button class="flex w-full items-center justify-between rounded-lg px-4 py-3 text-left hover:bg-white/10" x-on:click="toggleCategory('import')">
                            <div class="flex items-center">
                                <x-lucide-file-input class="h-5 w-5" />
                                <span class="ml-3">Import Data</span>
                            </div>
                            <x-lucide-chevron-down class="h-4 w-4 transition-transform duration-200" x-bind:class="{ 'rotate-180': categories.import }" />
                        </button>

                        <ul class="ml-8 mt-2 space-y-2" x-show="categories.import" x-collapse>
                            <li>
                                <a class="{{ request()->routeIs('import-student-class.*') ? 'bg-white/10' : '' }} flex items-center rounded-lg px-3 py-2 text-sm hover:bg-white/10" href="{{ route('import-student-class.index') }}">
                                    <x-lucide-users class="h-4 w-4" />
                                    <span class="ml-2">Siswa & Kelas</span>
                                </a>
                            </li>
                            <li>
                                <a class="{{ request()->routeIs('import-teacher.*') ? 'bg-white/10' : '' }} flex items-center rounded-lg px-3 py-2 text-sm hover:bg-white/10" href="{{ route('import-teacher.index') }}">
                                    <x-lucide-graduation-cap class="h-4 w-4" />
                                    <span class="ml-2">Guru</span>
                                </a>
                            </li>
                            <li>
                                <a class="{{ request()->routeIs('import-subject.*') ? 'bg-white/10' : '' }} flex items-center rounded-lg px-3 py-2 text-sm hover:bg-white/10" href="{{ route('import-subject.index') }}">
                                    <x-lucide-book-open class="h-4 w-4" />
                                    <span class="ml-2">Mata Pelajaran</span>
                                </a>
                            </li>
                            <li>
                                <a class="{{ request()->routeIs('import-schedule.*') ? 'bg-white/10' : '' }} flex items-center rounded-lg px-3 py-2 text-sm hover:bg-white/10" href="{{ route('import-schedule.index') }}">
                                    <x-lucide-calendar-days class="h-4 w-4" />
                                    <span class="ml-2">Jadwal</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan

            </ul>
        </nav>

        <!-- User Profile -->
        <div class="border-t border-white/10 p-4">
            <div class="flex items-center">
                <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=7C3AED&color=fff" alt="{{ Auth::user()->name }}">
                <div class="ml-3">
                    <p class="text-sm font-medium">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-white/60">{{ Auth::user()->role->label() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
