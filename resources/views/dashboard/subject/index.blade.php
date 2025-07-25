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
                    placeholder="Cari mata pelajaran..."
                >
                <button class="hidden" type="submit">Search</button>
            </form>

            <a class="flex w-full items-center justify-center rounded-lg bg-purple-600 px-4 py-2 text-sm font-medium text-white hover:bg-purple-700 sm:w-auto" href="{{ route('subject.create') }}">
                <x-lucide-plus class="mr-2 h-4 w-4" />
                Tambah Mata Pelajaran
            </a>
        </div>

        <div class="overflow-x-auto rounded-lg border border-white/10">
            <div class="space-y-3 sm:hidden">
                @foreach ($subjects as $subject)
                    <div class="glass-card flex items-center justify-between rounded-lg p-4">
                        <div class="text-sm">
                            <div class="text-xs text-white/60">Nama</div>
                            <div class="mb-2 font-medium">{{ $subject->name }}</div>

                            <div class="text-xs text-white/60">Kode</div>
                            <div class="mb-2">{{ $subject->code }}</div>

                            <div class="text-xs text-white/60">Deskripsi</div>
                            <div>{{ $subject->description }}</div>
                        </div>
                        <div class="flex flex-col space-y-2">
                            <a class="rounded-lg p-1 text-blue-400 hover:bg-white/10" href="{{ route('subject.edit', $subject->id) }}">
                                <x-lucide-edit class="h-4 w-4" />
                            </a>
                            <button class="rounded-lg p-1 text-red-400 hover:bg-white/10" x-on:click="$dispatch('open-confirm-modal', {
                                id: 'delete-subject-modal',
                                action: '{{ route('subject.destroy', $subject->id) }}',
                            })">
                                <x-lucide-trash-2 class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                @endforeach
                @if ($subjects->isEmpty())
                    <div class="glass-card rounded-lg p-4 text-center text-white/60">
                        Tidak ada mata pelajaran ditemukan
                    </div>
                @endif
            </div>

            <table class="hidden w-full sm:table">
                <thead>
                    <tr class="border-b border-white/10 text-left text-sm text-white/80">
                        <th class="whitespace-nowrap px-4 py-3">No</th>
                        <th class="whitespace-nowrap px-4 py-3">Nama</th>
                        <th class="whitespace-nowrap px-4 py-3">Kode</th>
                        <th class="whitespace-nowrap px-4 py-3">Deskripsi</th>
                        <th class="whitespace-nowrap px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @foreach ($subjects as $subject)
                        <tr class="hover:bg-white/5">
                            <td class="whitespace-nowrap px-4 py-4">{{ $loop->iteration }}</td>
                            <td class="whitespace-nowrap px-4 py-4 font-medium">{{ $subject->name }}</td>
                            <td class="whitespace-nowrap px-4 py-4">{{ $subject->code }}</td>
                            <td class="px-4 py-4">{{ $subject->description }}</td>
                            <td class="whitespace-nowrap px-4 py-4 text-right">
                                <div class="flex justify-end space-x-2">
                                    <a class="rounded-lg p-2 text-blue-400 hover:bg-white/10" href="{{ route('subject.edit', $subject->id) }}">
                                        <x-lucide-edit class="h-4 w-4" />
                                    </a>
                                    <button class="rounded-lg p-2 text-red-400 hover:bg-white/10" x-on:click="$dispatch('open-confirm-modal', {
                                            id: 'delete-subject-modal',
                                            action: '{{ route('subject.destroy', $subject->id) }}',
                                        })">
                                        <x-lucide-trash-2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @if ($subjects->isEmpty())
                        <tr>
                            <td class="py-4 text-center text-white/60" colspan="5">Tidak ada mata pelajaran ditemukan</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <x-pagination :items="$subjects" />
    </div>

    <x-confirm-modal id="delete-subject-modal" />
</x-layout.app>
