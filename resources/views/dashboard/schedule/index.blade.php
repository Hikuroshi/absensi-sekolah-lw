<x-layout.app :$title>
    @if (session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    <div class="glass-card rounded-xl p-6">
        <div class="mb-6 flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <form class="relative w-full sm:max-w-md">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <x-lucide-search class="h-5 w-5 text-white/50" />
                </div>
                <input
                    class="glass-card w-full rounded-lg border border-white/20 bg-white/10 py-2 pl-10 pr-4 text-white placeholder-white/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500"
                    name="search"
                    type="text"
                    value="{{ request('search') }}"
                    placeholder="Cari jadwal pelajaran..."
                >
                <button class="hidden" type="submit">Search</button>
            </form>

            <a class="flex w-full items-center justify-center rounded-lg bg-purple-600 px-4 py-2 text-sm font-medium text-white hover:bg-purple-700 sm:w-auto" href="{{ route('schedule.create') }}">
                <x-lucide-plus class="mr-2 h-4 w-4" />
                Tambah Jadwal Pelajaran
            </a>
        </div>

        <div class="overflow-x-auto rounded-lg border border-white/10">
            <div class="space-y-3 sm:hidden">
                @foreach ($schedules as $schedule)
                    <div class="glass-card flex items-center justify-between rounded-lg p-4">
                        <div class="text-sm">
                            <div class="text-xs text-white/60">Mata Pelajaran</div>
                            <div class="mb-2 font-medium">{{ $schedule->subject->name }}</div>

                            <div class="text-xs text-white/60">Kelas</div>
                            <div class="mb-2">{{ $schedule->classroom->name }}</div>

                            <div class="text-xs text-white/60">Guru</div>
                            <div class="mb-2">{{ $schedule->teacher->name }}</div>

                            <div class="text-xs text-white/60">Waktu</div>
                            <div class="mb-2">{{ $schedule->day->label() }}, {{ $schedule->start_time }} - {{ $schedule->end_time }}</div>
                        </div>
                        <div class="flex flex-col space-y-2">
                            <a class="rounded-lg p-1 text-blue-400 hover:bg-white/10" href="{{ route('schedule.edit', $schedule->id) }}">
                                <x-lucide-edit class="h-4 w-4" />
                            </a>
                            <button class="rounded-lg p-1 text-red-400 hover:bg-white/10" x-on:click="$dispatch('open-confirm-modal', {
                                id: 'delete-schedule-modal',
                                action: '{{ route('schedule.destroy', $schedule->id) }}',
                            })">
                                <x-lucide-trash-2 class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                @endforeach
                @if ($schedules->isEmpty())
                    <div class="glass-card rounded-lg p-4 text-center text-white/60">
                        Tidak ada jadwal pelajaran ditemukan
                    </div>
                @endif
            </div>

            <table class="hidden w-full sm:table">
                <thead>
                    <tr class="border-b border-white/10 text-left text-sm text-white/80">
                        <th class="whitespace-nowrap px-4 py-3">No</th>
                        <th class="whitespace-nowrap px-4 py-3">Pelajaran</th>
                        <th class="whitespace-nowrap px-4 py-3">Kelas</th>
                        <th class="whitespace-nowrap px-4 py-3">Guru</th>
                        <th class="whitespace-nowrap px-4 py-3">Waktu</th>
                        <th class="whitespace-nowrap px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @foreach ($schedules as $schedule)
                        <tr class="hover:bg-white/5">
                            <td class="whitespace-nowrap px-4 py-4">{{ $loop->iteration }}</td>
                            <td class="whitespace-nowrap px-4 py-4 font-medium">{{ $schedule->subject->name }}</td>
                            <td class="whitespace-nowrap px-4 py-4">{{ $schedule->classroom->name }}</td>
                            <td class="whitespace-nowrap px-4 py-4">{{ $schedule->teacher->name }}</td>
                            <td class="whitespace-nowrap px-4 py-4">{{ $schedule->day->label() }}, {{ $schedule->start_time }} - {{ $schedule->end_time }}</td>
                            <td class="whitespace-nowrap px-4 py-4 text-right">
                                <div class="flex justify-end space-x-2">
                                    <a class="rounded-lg p-2 text-blue-400 hover:bg-white/10" href="{{ route('schedule.edit', $schedule->id) }}">
                                        <x-lucide-edit class="h-4 w-4" />
                                    </a>
                                    <button class="rounded-lg p-2 text-red-400 hover:bg-white/10" x-on:click="$dispatch('open-confirm-modal', {
                                            id: 'delete-schedule-modal',
                                            action: '{{ route('schedule.destroy', $schedule->id) }}',
                                        })">
                                        <x-lucide-trash-2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @if ($schedules->isEmpty())
                        <tr>
                            <td class="py-4 text-center text-white/60" colspan="6">Tidak ada jadwal pelajaran ditemukan</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <x-pagination :items="$schedules" />
    </div>

    <x-confirm-modal id="delete-schedule-modal" />
</x-layout.app>
