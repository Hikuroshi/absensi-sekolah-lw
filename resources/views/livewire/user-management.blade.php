<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Manajemen User</h1>
            <button 
                wire:click="openModal" 
                class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span class="sm:inline">Tambah User</span>
            </button>
        </div>
    </div>

    <!-- Search & Filter Section -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:gap-4 gap-2">
        <div class="relative flex-1">
            <input 
                type="text" 
                wire:model.live.debounce.500ms="search" 
                placeholder="Cari user..." 
                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
            />
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>
        <div class="relative flex-1 sm:flex-initial">
            <select 
                wire:model.live="roleFilter" 
                class="w-full pl-3 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 appearance-none"
            >
            <option value="">Semua Role</option>
            <option value="admin">Admin</option>
            <option value="guru">Guru</option>
            <option value="ketua_kelas">Ketua Kelas</option>
            <option value="siswa">Siswa</option>
        </select>
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <x-alert type="success" :message="session('message')" />
    @endif

    <div 
        wire:loading 
        class="fixed bottom-4 right-4 z-50"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
    >
        <x-loading />
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($this->users as $user)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->role_label }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->id_number ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button 
                                wire:click="openModal({{ $user->id }})"
                                class="text-indigo-600 hover:text-indigo-900 mr-3 transition-colors duration-150"
                            >Edit</button>
                            <button 
                                wire:click="confirmDelete({{ $user->id }})"
                                class="text-red-600 hover:text-red-900 mr-3 transition-colors duration-150"
                            >Hapus</button>
                            <button 
                                wire:click="resetPassword({{ $user->id }})"
                                class="text-yellow-600 hover:text-yellow-900 transition-colors duration-150"
                            >Reset PW</button>
                </td>
            </tr>
            @empty
            <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <x-empty-state 
                                message="Tidak ada data user" 
                                description="Mulai dengan menambahkan user baru" 
                            />
                        </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>

    <!-- Mobile Cards -->
    <div class="md:hidden space-y-3">
        @forelse ($this->users as $user)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-base font-medium text-gray-900 mb-1">{{ $user->name }}</h3>
                        <div class="space-y-0.5">
                            <p class="text-xs text-gray-600"><span class="font-medium">Email:</span> {{ $user->email }}</p>
                            <p class="text-xs text-gray-600"><span class="font-medium">Role:</span> {{ $user->role_label }}</p>
                            <p class="text-xs text-gray-600"><span class="font-medium">{{ $user->id_number_label }}:</span> {{ $user->id_number ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                <div class="mt-3 flex gap-1.5">
                    <button 
                        wire:click="openModal({{ $user->id }})"
                        class="flex-1 inline-flex items-center justify-center px-2 py-1.5 border border-indigo-600 text-indigo-600 text-xs font-medium rounded hover:bg-indigo-50 transition-colors duration-150"
                    >
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </button>
                    <button 
                        wire:click="confirmDelete({{ $user->id }})"
                        class="flex-1 inline-flex items-center justify-center px-2 py-1.5 border border-red-600 text-red-600 text-xs font-medium rounded hover:bg-red-50 transition-colors duration-150"
                    >
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus
                    </button>
                    <button 
                        wire:click="resetPassword({{ $user->id }})"
                        class="flex-1 inline-flex items-center justify-center px-2 py-1.5 border border-yellow-600 text-yellow-600 text-xs font-medium rounded hover:bg-yellow-50 transition-colors duration-150"
                    >
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        Reset PW
                    </button>
                </div>
            </div>
        @empty
            <x-empty-state 
                message="Tidak ada data user" 
                description="Mulai dengan menambahkan user baru" 
            />
        @endforelse
    </div>

    <!-- Pagination -->
    @if($this->users->hasPages())
        <div class="mt-6">
            <x-pagination :model="$this->users" />
        </div>
    @endif

    <!-- Modal -->
    <div 
        x-data="{ open: @entangle('showModal') }" 
        x-show="open" 
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" 
        style="display: none;"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto"
            @click.stop
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
        >
            <form wire:submit.prevent="save">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $editingUser ? 'Edit User' : 'Tambah User' }}
                        </h3>
                        <button 
                            type="button" 
                            wire:click="closeModal"
                            class="text-gray-400 hover:text-gray-600 transition-colors duration-150"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <!-- Name Field -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama *</label>
                                <input 
                                    type="text" 
                                    id="name"
                                    wire:model.defer="name" 
                                    placeholder="Masukkan nama user" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 @error('name') border-red-500 @enderror"
                                    required 
                                />
                                @error('name') 
                                    <p class="mt-1 ms-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email Field -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                <input 
                                    type="email" 
                                    id="email"
                                    wire:model.defer="email" 
                                    placeholder="Masukkan email user" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 @error('email') border-red-500 @enderror"
                                    required 
                                />
                                @error('email') 
                                    <p class="mt-1 ms-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Role Field -->
                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                                <select 
                                    id="role"
                                    wire:model.defer="role" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 @error('role') border-red-500 @enderror"
                                    required
                                >
                <option value="">Pilih Role</option>
                <option value="admin">Admin</option>
                <option value="guru">Guru</option>
                <option value="ketua_kelas">Ketua Kelas</option>
                <option value="siswa">Siswa</option>
            </select>
                                @error('role') 
                                    <p class="mt-1 ms-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <!-- ID Number Field -->
                            <div>
                                <label for="id_number" class="block text-sm font-medium text-gray-700 mb-1">
                                    <span x-text="role === 'guru' ? 'NIP' : role === 'siswa' || role === 'ketua_kelas' ? 'NIS' : 'Nomor'"></span>
                                </label>
                                <input 
                                    type="text" 
                                    id="id_number"
                                    wire:model.defer="id_number" 
                                    x-bind:placeholder="role === 'guru' ? 'Masukkan NIP' : role === 'siswa' || role === 'ketua_kelas' ? 'Masukkan NIS' : 'Masukkan nomor'"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 @error('id_number') border-red-500 @enderror"
                                />
                                @error('id_number') 
                                    <p class="mt-1 ms-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password Fields -->
                            <div x-data="{ showPassword: false, showConfirmPassword: false }">
                                <!-- Password Field -->
                                <div class="mb-4">
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                    <div class="relative">
                                        <input 
                                            :type="showPassword ? 'text' : 'password'" 
                                            id="password"
                                            wire:model.defer="password" 
                                            placeholder="Masukkan password user" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 @error('password') border-red-500 @enderror pr-10"
                                            @if(!$editingUser) required @endif
                                        />
                                        <button 
                                            type="button" 
                                            @click="showPassword = !showPassword"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700"
                                        >
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path x-show="!showPassword" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path x-show="!showPassword" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                <path x-show="showPassword" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                            </svg>
                                        </button>
                                    </div>
                                    @if ($editingUser)
                                        <p class="mt-1 ms-1 text-xs text-gray-500">Kosongkan jika tidak ingin mengubah</p>
                                    @endif
                                    @error('password') 
                                        <p class="mt-1 ms-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row gap-3 sm:justify-end">
                    <button 
                        type="button" 
                        wire:click="closeModal"
                        class="w-full sm:w-auto px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        class="w-full sm:w-auto px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>
                            {{ $editingUser ? 'Perbarui' : 'Simpan' }}
                        </span>
                        <span wire:loading>
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Menyimpan...
                        </span>
                    </button>
            </div>
        </form>
    </div>
    </div>

    <x-delete-confirmation 
        :show="$showDeleteModal"
        confirm-action="wire:click=delete"
        cancel-action="wire:click=closeDeleteModal"
    />
</div> 