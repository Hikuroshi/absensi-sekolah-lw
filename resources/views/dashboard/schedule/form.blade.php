<x-layout.app title="Tambah Pengguna">
    <div class="glass-card rounded-xl p-6">
        <h2 class="mb-6 text-xl font-semibold">{{ isset($schedule) ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}</h2>

        <form method="POST" action="{{ isset($schedule) ? route('schedule.update', $schedule->id) : route('schedule.store') }}">
            @csrf
            @if (isset($schedule))
                @method('PUT')
            @endif

            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-white" for="classroom_id">Kelas</label>
                    <select class="cool-select w-full" id="classroom_id" name="classroom_id" required>
                        <option value=" ">Pilih kelas</option>
                        @foreach ($classrooms as $classroom)
                            <option value="{{ $classroom->id }}" @selected(old('classroom_id', $schedule->classroom->id ?? '') === $classroom->id)>
                                {{ $classroom->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('classroom_id')
                        <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-white" for="subject_id">Mata Pelajaran</label>
                    <select class="cool-select w-full" id="subject_id" name="subject_id" required>
                        <option value=" ">Pilih mata pelajaran</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}" @selected(old('subject_id', $schedule->subject->id ?? '') === $subject->id)>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('subject_id')
                        <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-white" for="teacher_id">Guru</label>
                    <select class="cool-select w-full" id="teacher_id" name="teacher_id" required>
                        <option value=" ">Pilih guru</option>
                        @foreach ($teachers as $teacher)
                            <option value="{{ $teacher->id }}" @selected(old('teacher_id', $schedule->teacher->id ?? '') === $teacher->id)>
                                {{ $teacher->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('teacher_id')
                        <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-white" for="day">Hari</label>
                    <select class="cool-select w-full" id="day" name="day" required>
                        <option value=" ">Pilih hari</option>
                        @foreach ($days as $day)
                            <option value="{{ $day->value }}" @selected(old('day', $schedule->day->value ?? '') === $day->value)>
                                {{ $day->label }}
                            </option>
                        @endforeach
                    </select>
                    @error('day')
                        <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-white" for="start_time">Jam Mulai</label>
                    <input
                        class="glass-card w-full rounded-lg border border-white/20 bg-white/10 px-4 py-3 text-white placeholder-white/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500"
                        id="start_time"
                        name="start_time"
                        type="time"
                        value="{{ old('start_time', $schedule->start_time ?? '') }}"
                        required
                        placeholder="Jam Mulai"
                    >
                    @error('start_time')
                        <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-white" for="end_time">Jam Berakhir</label>
                    <input
                        class="glass-card w-full rounded-lg border border-white/20 bg-white/10 px-4 py-3 text-white placeholder-white/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500"
                        id="end_time"
                        name="end_time"
                        type="time"
                        value="{{ old('end_time', $schedule->end_time ?? '') }}"
                        required
                        placeholder="Jam Berakhir"
                    >
                    @error('end_time')
                        <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex justify-end space-x-3">
                <button class="glass-card flex items-center rounded-lg px-4 py-2 text-sm font-medium hover:bg-white/10" onclick="history.back()">
                    Batal
                </button>
                <button class="flex items-center rounded-lg bg-purple-600 px-4 py-2 text-sm font-medium text-white hover:bg-purple-700" type="submit">
                    <x-lucide-save class="mr-2 h-4 w-4" />
                    Simpan
                </button>
            </div>
        </form>
    </div>

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
