<x-layout.app :$title>
    @if (session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if ($errors->any())
        <x-alert type="error">
            @if ($errors->has('status'))
                Ada Anggota kelas yang belum diabsen.
            @else
                Terjadi kesalahan dalam pengisian form.
            @endif
        </x-alert>
    @endif

    <form action="{{ $old_attendance ? route('teacher-attendance.update', $old_attendance->id) : route('teacher-attendance.store', $schedule->id) }}" method="POST">
        @csrf

        <div class="glass-card rounded-xl p-6">
            <div class="mb-6 flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                <div class="w-full">
                    <h2 class="text-xl font-semibold">Absensi: {{ $schedule->classroom->name }} - {{ $schedule->subject->name }} - {{ $schedule->teacher->name }}</h2>
                    <p class="mb-6 text-sm text-white/60">
                        Absensi di <span class="font-bold">{{ $schedule->classroom->name }}</span>
                        saat pelajaran <span class="font-bold">{{ $schedule->subject->name }}</span>
                        yang diampu oleh <span class="font-bold">{{ $schedule->teacher->name }}</span>
                        pada <span class="font-bold">{{ $schedule->day->label() }}, {{ $schedule->start_time }} - {{ $schedule->end_time }}</span>.
                    </p>
                </div>

                <button class="flex w-full items-center justify-center whitespace-nowrap rounded-lg bg-purple-600 px-4 py-2 text-sm font-medium text-white hover:bg-purple-700 sm:w-auto" type="submit">
                    <x-lucide-save class="mr-2 h-4 w-4" />
                    Simpan Absensi
                </button>
            </div>

            <div class="overflow-x-auto rounded-lg border border-white/10">
                <div class="space-y-3 sm:hidden">
                    <div class="glass-card rounded-lg p-4">
                        <div class="text-sm">
                            <div class="text-xs text-white/60">Nama</div>
                            <div class="mb-2 font-medium">{{ $teacher->name }}</div>

                            <div class="text-xs text-white/60">Role</div>
                            <div class="mb-2">
                                <x-role-badge :role="$teacher->role" />
                            </div>

                            <div class="text-xs text-white/60">Catatan</div>
                            <div class="mb-2 w-full">
                                <input
                                    class="glass-card w-full rounded-lg border border-white/20 bg-white/10 px-4 py-3 text-white placeholder-white/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500"
                                    id="note"
                                    name="note"
                                    type="text"
                                    value="{{ old('note', $old_attendance->note ?? '') }}"
                                    placeholder="Catatan (Opsional)"
                                >
                            </div>

                            <div class="text-xs text-white/60">Status Absensi</div>
                            <div class="mb-2 w-full">
                                <select class="cool-select w-full" id="status" name="status" required>
                                    <option value=" ">Pilih Status</option>
                                    @foreach ($statuss as $status)
                                        <option value="{{ $status->value }}" @selected(old('status', $old_attendance->status->value ?? '') === $status->value)>
                                            {{ $status->label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <table class="hidden w-full sm:table">
                    <thead>
                        <tr class="border-b border-white/10 text-left text-sm text-white/80">
                            <th class="whitespace-nowrap px-4 py-3">Nama</th>
                            <th class="whitespace-nowrap px-4 py-3">Role</th>
                            <th class="whitespace-nowrap px-4 py-3">Catatan</th>
                            <th class="whitespace-nowrap px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        <tr class="hover:bg-white/5">
                            <td class="whitespace-nowrap px-4 py-4 font-medium">{{ $teacher->name }}</td>
                            <td class="whitespace-nowrap px-4 py-4">
                                <x-role-badge :role="$teacher->role" />
                            </td>
                            <td class="whitespace-nowrap px-4 py-4">
                                <input
                                    class="glass-card w-full rounded-lg border border-white/20 bg-white/10 px-4 py-3 text-white placeholder-white/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500"
                                    id="note"
                                    name="note"
                                    type="text"
                                    value="{{ old('note', $old_attendance->note ?? '') }}"
                                    placeholder="Catatan (Opsional)"
                                >
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-right">
                                <select class="cool-select w-full" id="status" name="status" required>
                                    <option value=" ">Pilih Status</option>
                                    @foreach ($statuss as $status)
                                        <option value="{{ $status->value }}" @selected(old('status', $old_attendance->status->value ?? '') === $status->value)>
                                            {{ $status->label }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </form>

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
                        controlInput: null,
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
