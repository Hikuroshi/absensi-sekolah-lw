<x-layout.app :$title>
    @if (session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    <div class="grid gap-6 md:grid-cols-2" x-data="{
        selectedStudents: [],
        toggleStudent(studentId) {
            if (this.selectedStudents.includes(studentId)) {
                this.selectedStudents = this.selectedStudents.filter(id => id !== studentId);
            } else {
                this.selectedStudents.push(studentId);
            }
            // Update the correct hidden input
            this.$refs.studentsInput.value = JSON.stringify(this.selectedStudents);
        },
        isSelected(studentId) {
            return this.selectedStudents.includes(studentId);
        }
    }">
        <div class="glass-card rounded-xl p-6">
            <h2 class="text-xl font-semibold">Pilih Siswa</h2>
            <p class="mb-6 text-sm text-white/60">Silakan pilih siswa yang ingin ditambahkan ke kelas. Hanya siswa yang belum memiliki kelas yang muncul.</p>

            <div class="mb-6 flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                <form class="relative w-full">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <x-lucide-search class="h-5 w-5 text-white/50" />
                    </div>
                    <input
                        class="glass-card w-full rounded-lg border border-white/20 bg-white/10 py-2 pl-10 pr-4 text-white placeholder-white/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500"
                        name="search-new-student"
                        type="text"
                        value="{{ request('search-new-student') }}"
                        placeholder="Cari pengguna..."
                    >
                    <button class="hidden" type="submit">Search</button>
                </form>
            </div>

            <div class="overflow-x-auto rounded-lg border border-white/10">
                <div class="space-y-4 sm:hidden">
                    @foreach ($users as $user)
                        <div class="glass-card flex items-center justify-between rounded-lg p-4">
                            <div class="text-sm">
                                <div class="text-xs text-white/60">Nama</div>
                                <div class="mb-2 font-medium">{{ $user->name }}</div>
                            </div>
                            <div class="flex flex-col space-y-2">
                                <button class="rounded-lg p-1 hover:bg-white/10" x-on:click="toggleStudent('{{ $user->id }}')" :class="{
                                    'text-green-400': !isSelected('{{ $user->id }}'),
                                    'text-purple-400': isSelected('{{ $user->id }}')
                                }">
                                    <template x-if="!isSelected('{{ $user->id }}')">
                                        <x-lucide-check class="h-4 w-4" />
                                    </template>
                                    <template x-if="isSelected('{{ $user->id }}')">
                                        <x-lucide-check-check class="h-4 w-4" />
                                    </template>
                                </button>
                            </div>
                        </div>
                    @endforeach
                    @if ($users->isEmpty())
                        <div class="glass-card rounded-lg p-4 text-center text-white/60">
                            Tidak ada pengguna ditemukan
                        </div>
                    @endif
                </div>

                <table class="hidden w-full sm:table">
                    <thead>
                        <tr class="border-b border-white/10 text-left text-sm text-white/80">
                            <th class="whitespace-nowrap px-4 py-3">No</th>
                            <th class="whitespace-nowrap px-4 py-3">Nama</th>
                            <th class="whitespace-nowrap px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach ($users as $user)
                            <tr class="hover:bg-white/5">
                                <td class="whitespace-nowrap px-4 py-4">{{ $loop->iteration }}</td>
                                <td class="whitespace-nowrap px-4 py-4 font-medium">{{ $user->name }}</td>
                                <td class="whitespace-nowrap px-4 py-4 text-right">
                                    <div class="flex justify-end space-x-2">
                                        <button class="rounded-lg p-2 hover:bg-white/10" x-on:click="toggleStudent('{{ $user->id }}')" :class="{
                                            'text-green-400': !isSelected('{{ $user->id }}'),
                                            'text-purple-400': isSelected('{{ $user->id }}')
                                        }">
                                            <template x-if="!isSelected('{{ $user->id }}')">
                                                <x-lucide-check class="h-4 w-4" />
                                            </template>
                                            <template x-if="isSelected('{{ $user->id }}')">
                                                <x-lucide-check-check class="h-4 w-4" />
                                            </template>
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
        <div class="glass-card rounded-xl p-6">
            <div class="mb-6 flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                <h2 class="text-xl font-semibold">Siswa Terpilih</h2>
                <form method="POST" action="{{ route('class-member.store-student', $classroom->id) }}">
                    @csrf
                    <input name="student_ids" type="hidden" x-ref="studentsInput" x-bind:value="JSON.stringify(selectedStudents)">
                    <button class="flex w-full items-center justify-center rounded-lg bg-purple-600 px-4 py-2 text-sm font-medium text-white hover:bg-purple-700 sm:w-auto" type="submit" x-bind:disabled="selectedStudents.length === 0" :class="{ 'opacity-50 cursor-not-allowed': selectedStudents.length === 0 }">
                        <x-lucide-save class="mr-2 h-4 w-4" />
                        Simpan
                    </button>
                </form>
            </div>

            <div class="overflow-x-auto rounded-lg border border-white/10">
                <div class="space-y-4 sm:hidden">
                    <template x-for="(studentId, index) in selectedStudents" :key="studentId">
                        <div class="glass-card flex items-center justify-between rounded-lg p-4">
                            <div class="text-sm">
                                <div class="text-xs text-white/60">Nama</div>
                                <div class="mb-2 font-medium">
                                    <template x-for="user in {{ Js::from($users->keyBy('id')->toArray()) }}" :key="user.id">
                                        <span x-show="user.id == studentId" x-text="user.name"></span>
                                    </template>
                                </div>
                            </div>
                            <div class="flex flex-col space-y-2">
                                <button class="rounded-lg p-1 text-red-400 hover:bg-white/10" x-on:click="toggleStudent(studentId)">
                                    <x-lucide-x class="h-4 w-4" />
                                </button>
                            </div>
                        </div>
                    </template>
                    <div class="glass-card rounded-lg p-4 text-center text-white/60" x-show="selectedStudents.length === 0">
                        Tidak ada pengguna ditemukan
                    </div>
                </div>

                <table class="hidden w-full sm:table">
                    <thead>
                        <tr class="border-b border-white/10 text-left text-sm text-white/80">
                            <th class="whitespace-nowrap px-4 py-3">No</th>
                            <th class="whitespace-nowrap px-4 py-3">Nama</th>
                            <th class="whitespace-nowrap px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        <template x-for="(studentId, index) in selectedStudents" :key="studentId">
                            <tr class="hover:bg-white/5">
                                <td class="whitespace-nowrap px-4 py-4" x-text="index + 1"></td>
                                <td class="whitespace-nowrap px-4 py-4 font-medium">
                                    <template x-for="user in {{ Js::from($users->keyBy('id')->toArray()) }}" :key="user.id">
                                        <span x-show="user.id == studentId" x-text="user.name"></span>
                                    </template>
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-right">
                                    <div class="flex justify-end space-x-2">
                                        <button class="rounded-lg p-2 text-red-400 hover:bg-white/10" x-on:click="toggleStudent(studentId)">
                                            <x-lucide-x class="h-4 w-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="selectedStudents.length === 0">
                            <td class="py-4 text-center text-white/60" colspan="3">Belum ada siswa terpilih</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="glass-card mt-6 rounded-xl p-6">
        <h2 class="text-xl font-semibold">Daftar Anggota Kelas</h2>
        <p class="mb-6 text-sm text-white/60">Menampilkan anggota kelas yang telah ditambahkan.</p>

        <div class="mb-6 flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <form class="relative w-full sm:max-w-md">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <x-lucide-search class="h-5 w-5 text-white/50" />
                </div>
                <input
                    class="glass-card w-full rounded-lg border border-white/20 bg-white/10 py-2 pl-10 pr-4 text-white placeholder-white/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500"
                    name="search-student"
                    type="text"
                    value="{{ request('search-student') }}"
                    placeholder="Cari mata pelajaran..."
                >
                <button class="hidden" type="submit">Search</button>
            </form>

            <a class="flex w-full items-center justify-center rounded-lg bg-purple-600 px-4 py-2 text-sm font-medium text-white hover:bg-purple-700 sm:w-auto" href="{{ route('classroom.show', $classroom->id) }}">
                <x-lucide-users class="mr-2 h-4 w-4" />
                Manajemen Kelas
            </a>
        </div>

        <div class="overflow-x-auto rounded-lg border border-white/10">
            <div class="space-y-3 sm:hidden">
                @foreach ($students as $student)
                    <div class="glass-card flex items-center justify-between rounded-lg p-4">
                        <div class="text-sm">
                            <div class="text-xs text-white/60">Nama</div>
                            <div class="mb-2 font-medium">{{ $student->name }}</div>

                            <div class="text-xs text-white/60">Role</div>
                            <div>
                                <x-role-badge :role="$student->role" />
                            </div>
                        </div>
                        <div class="flex flex-col space-y-2">
                            <button class="rounded-lg p-1 text-red-400 hover:bg-white/10" x-on:click="$dispatch('open-confirm-modal', {
                                id: 'delete-student-modal',
                                action: '{{ route('class-member.delete-student', [$classroom->id, $student->id]) }}',
                            })">
                                <x-lucide-trash-2 class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                @endforeach
                @if ($students->isEmpty())
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
                        <th class="whitespace-nowrap px-4 py-3">Role</th>
                        <th class="whitespace-nowrap px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @foreach ($students as $student)
                        <tr class="hover:bg-white/5">
                            <td class="whitespace-nowrap px-4 py-4">{{ $loop->iteration }}</td>
                            <td class="whitespace-nowrap px-4 py-4 font-medium">{{ $student->name }}</td>
                            <td class="whitespace-nowrap px-4 py-4">
                                <x-role-badge :role="$student->role" />
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-right">
                                <div class="flex justify-end space-x-2">
                                    <button class="rounded-lg p-2 text-red-400 hover:bg-white/10" x-on:click="$dispatch('open-confirm-modal', {
                                            id: 'delete-student-modal',
                                            action: '{{ route('class-member.delete-student', [$classroom->id, $student->id]) }}',
                                        })">
                                        <x-lucide-trash-2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @if ($students->isEmpty())
                        <tr>
                            <td class="py-4 text-center text-white/60" colspan="5">Tidak ada mata pelajaran ditemukan</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <x-pagination :items="$students" />
    </div>

    <x-confirm-modal id="delete-student-modal" />
</x-layout.app>
