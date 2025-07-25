<x-layout.app title="Tambah Pengguna">
    <div class="glass-card rounded-xl p-6">
        <h2 class="mb-6 text-xl font-semibold">{{ isset($user) ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}</h2>

        <form method="POST" action="{{ isset($user) ? route('user.update', $user->id) : route('user.store') }}">
            @csrf
            @if (isset($user))
                @method('PUT')
            @endif

            <div class="grid gap-6 md:grid-cols-2">
                <!-- Name Field -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-white" for="name">Nama Lengkap</label>
                    <input
                        class="glass-card w-full rounded-lg border border-white/20 bg-white/10 px-4 py-3 text-white placeholder-white/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500"
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name', $user->name ?? '') }}"
                        required
                        placeholder="Nama lengkap pengguna"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Username Field -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-white" for="username">Username</label>
                    <input
                        class="glass-card w-full rounded-lg border border-white/20 bg-white/10 px-4 py-3 text-white placeholder-white/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500"
                        id="username"
                        name="username"
                        type="text"
                        value="{{ old('username', $user->username ?? '') }}"
                        required
                        placeholder="Username unik pengguna"
                    >
                    @error('username')
                        <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Field -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-white" for="email">Email</label>
                    <input
                        class="glass-card w-full rounded-lg border border-white/20 bg-white/10 px-4 py-3 text-white placeholder-white/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500"
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email', $user->email ?? '') }}"
                        required
                        placeholder="Alamat email"
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role Field -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-white" for="role">Role</label>
                    <select class="w-full" id="role" name="role" required>
                        <option value=" ">Pilih role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->value }}" @selected(old('role', $user->role->value ?? '') === $role->value)>
                                {{ $role->label }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                @if (!isset($user))
                    <div>
                        <label class="mb-2 block text-sm font-medium text-white" for="password">Password</label>
                        <div class="relative" x-data="{ showPassword: false }">
                            <input
                                class="glass-card w-full rounded-lg border border-white/20 bg-white/10 px-4 py-3 text-white placeholder-white/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500"
                                id="password"
                                name="password"
                                :type="showPassword ? 'text' : 'password'"
                                required
                                placeholder="••••••••"
                            >
                            <button class="absolute right-3 top-4 text-white/50 hover:text-white" type="button" x-on:click="showPassword = !showPassword">
                                <template x-if="showPassword">
                                    <x-lucide-eye-off class="h-5 w-5" />
                                </template>
                                <template x-if="!showPassword">
                                    <x-lucide-eye class="h-5 w-5" />
                                </template>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-white" for="password_confirmation">Konfirmasi Password</label>
                        <div class="relative" x-data="{ showConfirmPassword: false }">
                            <input
                                class="glass-card w-full rounded-lg border border-white/20 bg-white/10 px-4 py-3 text-white placeholder-white/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500"
                                id="password_confirmation"
                                name="password_confirmation"
                                :type="showConfirmPassword ? 'text' : 'password'"
                                required
                                placeholder="Ulangi password"
                            >
                            <button class="absolute right-3 top-4 text-white/50 hover:text-white" type="button" x-on:click="showConfirmPassword = !showConfirmPassword">
                                <template x-if="showConfirmPassword">
                                    <x-lucide-eye-off class="h-5 w-5" />
                                </template>
                                <template x-if="!showConfirmPassword">
                                    <x-lucide-eye class="h-5 w-5" />
                                </template>
                            </button>
                        </div>
                    </div>
                @endif
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

    @if (isset($user))
        <div class="glass-card mt-6 rounded-xl p-6">
            <h2 class="mb-6 text-xl font-semibold">Ubah Password</h2>

            <form method="POST" action="{{ route('user.update-password', $user->id) }}">
                @csrf
                @method('PATCH')

                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-white" for="password">Password</label>
                        <div class="relative" x-data="{ showPassword: false }">
                            <input
                                class="glass-card w-full rounded-lg border border-white/20 bg-white/10 px-4 py-3 text-white placeholder-white/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500"
                                id="password"
                                name="password"
                                :type="showPassword ? 'text' : 'password'"
                                required
                                placeholder="••••••••"
                            >
                            <button class="absolute right-3 top-4 text-white/50 hover:text-white" type="button" x-on:click="showPassword = !showPassword">
                                <template x-if="showPassword">
                                    <x-lucide-eye-off class="h-5 w-5" />
                                </template>
                                <template x-if="!showPassword">
                                    <x-lucide-eye class="h-5 w-5" />
                                </template>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-white" for="password_confirmation">Konfirmasi Password</label>
                        <div class="relative" x-data="{ showConfirmPassword: false }">
                            <input
                                class="glass-card w-full rounded-lg border border-white/20 bg-white/10 px-4 py-3 text-white placeholder-white/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500"
                                id="password_confirmation"
                                name="password_confirmation"
                                :type="showConfirmPassword ? 'text' : 'password'"
                                required
                                placeholder="Ulangi password"
                            >
                            <button class="absolute right-3 top-4 text-white/50 hover:text-white" type="button" x-on:click="showConfirmPassword = !showConfirmPassword">
                                <template x-if="showConfirmPassword">
                                    <x-lucide-eye-off class="h-5 w-5" />
                                </template>
                                <template x-if="!showConfirmPassword">
                                    <x-lucide-eye class="h-5 w-5" />
                                </template>
                            </button>
                        </div>
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
    @endif

    <x-slot:css>
        <link href="{{ asset('assets/css/tom-select.css') }}" rel="stylesheet">
    </x-slot:css>

    <x-slot:js>
        <script src="{{ asset('assets/js/tom-select.complete.min.js') }}"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                new TomSelect('#role', {
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
        </script>
    </x-slot:js>
</x-layout.app>
