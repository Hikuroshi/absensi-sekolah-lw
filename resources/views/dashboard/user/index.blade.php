<x-layout.app :$title>
    @if (session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    <div class="glass-card rounded-xl p-6">
        <!-- Search and Add Button - Stacked on mobile -->
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
                    placeholder="Cari pengguna..."
                >
                <button class="hidden" type="submit">Search</button>
            </form>

            <a class="flex w-full items-center justify-center rounded-lg bg-purple-600 px-4 py-2 text-sm font-medium text-white hover:bg-purple-700 sm:w-auto" href="{{ route('user.create') }}">
                <x-lucide-plus class="mr-2 h-4 w-4" />
                Tambah Pengguna
            </a>
        </div>

        <!-- Responsive Table Container -->
        <div class="overflow-x-auto rounded-lg border border-white/10">
            <!-- Mobile Cards View (shown on small screens) -->
            <div class="space-y-4 sm:hidden">
                @foreach ($users as $user)
                    <div class="glass-card rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ $user->name }}&background=7C3AED&color=fff" alt="{{ $user->name }}">
                                <div>
                                    <div class="font-medium">{{ $user->name }}</div>
                                    <div class="text-sm text-white/60">{{ $user->username }}</div>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <a class="rounded-lg p-1 text-blue-400 hover:bg-white/10" href="{{ route('user.edit', $user->id) }}">
                                    <x-lucide-edit class="h-4 w-4" />
                                </a>
                                <button class="rounded-lg p-1 text-red-400 hover:bg-white/10" x-on:click="$dispatch('open-confirm-modal', {
                                        id: 'delete-user-modal',
                                        action: '{{ route('user.destroy', $user->id) }}',
                                    })">
                                    <x-lucide-trash-2 class="h-4 w-4" />
                                </button>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <div class="text-sm text-white/60">{{ $user->email }}</div>
                            <x-role-badge :role="$user->role" />
                        </div>
                    </div>
                @endforeach
                @if ($users->isEmpty())
                    <div class="glass-card rounded-lg p-4 text-center text-white/60">
                        Tidak ada pengguna ditemukan
                    </div>
                @endif
            </div>

            <!-- Desktop Table View (shown on larger screens) -->
            <table class="hidden w-full sm:table">
                <thead>
                    <tr class="border-b border-white/10 text-left text-sm text-white/80">
                        <th class="whitespace-nowrap px-4 py-3">No</th>
                        <th class="whitespace-nowrap px-4 py-3">Nama</th>
                        <th class="whitespace-nowrap px-4 py-3">Email</th>
                        <th class="whitespace-nowrap px-4 py-3">Role</th>
                        <th class="whitespace-nowrap px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @foreach ($users as $user)
                        <tr class="hover:bg-white/5">
                            <td class="whitespace-nowrap px-4 py-4">{{ $loop->iteration }}</td>
                            <td class="whitespace-nowrap px-4 py-4">
                                <div class="flex items-center">
                                    <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ $user->name }}&background=7C3AED&color=fff" alt="{{ $user->name }}">
                                    <div class="ml-3 flex flex-col">
                                        <span>{{ $user->name }}</span>
                                        <span class="text-xs text-white/60">{{ $user->username }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4">{{ $user->email }}</td>
                            <td class="whitespace-nowrap px-4 py-4">
                                <x-role-badge :role="$user->role" />
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-right">
                                <div class="flex justify-end space-x-2">
                                    <a class="rounded-lg p-2 text-blue-400 hover:bg-white/10" href="{{ route('user.edit', $user->id) }}">
                                        <x-lucide-edit class="h-4 w-4" />
                                    </a>
                                    <button class="rounded-lg p-2 text-red-400 hover:bg-white/10" x-on:click="$dispatch('open-confirm-modal', {
                                            id: 'delete-user-modal',
                                            action: '{{ route('user.destroy', $user->id) }}',
                                        })">
                                        <x-lucide-trash-2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @if ($users->isEmpty())
                        <tr>
                            <td class="py-4 text-center text-white/60" colspan="5">Tidak ada pengguna ditemukan</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <x-pagination :items="$users" />
    </div>

    <x-confirm-modal id="delete-user-modal" />
</x-layout.app>
