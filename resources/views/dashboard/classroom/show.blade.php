<x-layout.app :$title>
    @if (session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    <div class="glass-card rounded-xl p-6">
        <form id="leader-form" method="POST" action="{{ route('class-member.leader-update', $classroom->id) }}">
            @csrf
            @method('PUT')

            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-white" for="wali_kelas_id">Wali Kelas</label>
                    <select class="cool-select w-full" id="wali_kelas_id" name="wali_kelas_id" required>
                        <option value=" ">Pilih Wali Kelas</option>
                        @foreach ($wali_kelass as $wali_kelas)
                            <option value="{{ $wali_kelas->id }}" @selected(old('wali_kelas_id', $leader->wali_kelas->id ?? '') === $wali_kelas->id)>
                                {{ $wali_kelas->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('wali_kelas_id')
                        <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-white" for="ketua_kelas_id">Ketua Kelas</label>
                    <select class="cool-select w-full" id="ketua_kelas_id" name="ketua_kelas_id" required>
                        <option value=" ">Pilih Ketua Kelas</option>
                        @foreach ($ketua_kelass as $ketua_kelas)
                            <option value="{{ $ketua_kelas->id }}" @selected(old('ketua_kelas_id', $leader->ketua_kelas->id ?? '') === $ketua_kelas->id)>
                                {{ $ketua_kelas->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('ketua_kelas_id')
                        <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </form>

        <hr class="my-6 border-white/10">

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
                    placeholder="Cari anggota kelas..."
                >
                <button class="hidden" type="submit">Search</button>
            </form>

            <div class="flex items-center space-x-3">
                <a class="flex w-full items-center justify-center rounded-lg bg-purple-600 px-4 py-2 text-sm font-medium text-white hover:bg-purple-700 sm:w-auto" href="{{ route('class-member.create-student', $classroom->id) }}">
                    <x-lucide-plus class="mr-2 h-4 w-4" />
                    Tambah Siswa
                </a>
                <button class="flex items-center rounded-lg bg-purple-600 px-4 py-2 text-sm font-medium text-white hover:bg-purple-700" form="leader-form" type="submit">
                    <x-lucide-save class="mr-2 h-4 w-4" />
                    Simpan
                </button>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg border border-white/10">
            <div class="space-y-3 sm:hidden">
                @foreach ($members as $member)
                    <div class="glass-card flex items-center justify-between rounded-lg p-4">
                        <div class="text-sm">
                            <div class="text-xs text-white/60">Nama</div>
                            <div class="mb-2 font-medium">{{ $member->name }}</div>

                            <div class="text-xs text-white/60">Role</div>
                            <div>
                                <x-role-badge :role="$member->role" />
                            </div>
                        </div>
                        <div class="flex flex-col space-y-2">
                            {{-- <button class="rounded-lg p-1 text-red-400 hover:bg-white/10" x-on:click="$dispatch('open-confirm-modal', {
                                id: 'delete-member-modal',
                                action: '{{ route('member.destroy', $member->id) }}',
                            })">
                                <x-lucide-trash-2 class="h-4 w-4" />
                            </button> --}}
                        </div>
                    </div>
                @endforeach
                @if ($members->isEmpty())
                    <div class="glass-card rounded-lg p-4 text-center text-white/60">
                        Tidak ada anggota ditemukan</td>
                    </div>
                @endif
            </div>

            <table class="hidden w-full sm:table">
                <thead>
                    <tr class="border-b border-white/10 text-left text-sm text-white/80">
                        <th class="whitespace-nowrap px-4 py-3">No</th>
                        <th class="whitespace-nowrap px-4 py-3">Nama</th>
                        <th class="whitespace-nowrap px-4 py-3">Role</th>
                        <th class="whitespace-nowrap px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @foreach ($members as $member)
                        <tr class="hover:bg-white/5">
                            <td class="whitespace-nowrap px-4 py-4">{{ $loop->iteration }}</td>
                            <td class="whitespace-nowrap px-4 py-4 font-medium">{{ $member->name }}</td>
                            <td class="whitespace-nowrap px-4 py-4">
                                <x-role-badge :role="$member->role" />
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-right">
                                <div class="flex justify-end space-x-2">
                                    {{-- <button class="rounded-lg p-2 text-red-400 hover:bg-white/10" x-on:click="$dispatch('open-confirm-modal', {
                                            id: 'delete-member-modal',
                                            action: '{{ route('member.destroy', $member->id) }}',
                                        })">
                                        <x-lucide-trash-2 class="h-4 w-4" />
                                    </button> --}}
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @if ($members->isEmpty())
                        <tr>
                            <td class="py-4 text-center text-white/60" colspan="5">Tidak ada anggota ditemukan</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <x-pagination :items="$members" />
    </div>

    <x-confirm-modal id="delete-member-modal" />

    <x-slot:css>
        <link href="{{ asset('assets/css/tom-select.css') }}" rel="stylesheet">
    </x-slot:css>

    <x-slot:js>
        <script src="{{ asset('assets/js/tom-select.complete.min.js') }}"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.cool-select').forEach(function(selectElement) {
                    new TomSelect(selectElement, {
                        dropdownParent: 'body',
                        render: {
                            option: function(data, escape) {
                                return `<div class='text-white'>${escape(data.text)}</div>`;
                            },
                            item: function(data, escape) {
                                return `<div class='text-white'>${escape(data.text)}</div>`;
                            },
                        },
                        onFocus: function() {
                            this.control.classList.add('focus');
                        },
                        onBlur: function() {
                            this.control.classList.remove('focus');
                        },
                    });
                });
            });
        </script>
    </x-slot:js>
</x-layout.app>
