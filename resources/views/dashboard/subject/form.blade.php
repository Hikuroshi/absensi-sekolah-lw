<x-layout.app title="Tambah Mata Pelajaran">
    <div class="glass-card rounded-xl p-6">
        <h2 class="mb-6 text-xl font-semibold">{{ isset($subject) ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran Baru' }}</h2>

        <form method="POST" action="{{ isset($subject) ? route('subject.update', $subject->id) : route('subject.store') }}">
            @csrf
            @if (isset($subject))
                @method('PUT')
            @endif

            <div class="grid gap-6 md:grid-cols-2">
                <!-- Name Field -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-white" for="name">Nama</label>
                    <input
                        class="glass-card w-full rounded-lg border border-white/20 bg-white/10 px-4 py-3 text-white placeholder-white/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500"
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name', $subject->name ?? '') }}"
                        required
                        placeholder="Nama Mata Pelajaran"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kode Mata Pelajaran Field -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-white" for="code">Kode Mata Pelajaran</label>
                    <input
                        class="glass-card w-full rounded-lg border border-white/20 bg-white/10 px-4 py-3 text-white placeholder-white/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500"
                        id="code"
                        name="code"
                        type="text"
                        value="{{ old('code', $subject->code ?? '') }}"
                        required
                        placeholder="Kode Mata Pelajaran"
                    >
                    @error('code')
                        <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description Field -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-white" for="description">Deskripsi</label>
                    <textarea
                        class="glass-card w-full rounded-lg border border-white/20 bg-white/10 px-4 py-3 text-white placeholder-white/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500"
                        id="description"
                        name="description"
                        rows="4"
                        placeholder="Deskripsi Mata Pelajaran"
                    >{{ old('description', $subject->description ?? '') }}</textarea>
                    @error('description')
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
</x-layout.app>
