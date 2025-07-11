<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Manajemen Kelas</h1>
            <button 
                wire:click="openModal" 
                class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span class="sm:inline">Tambah Kelas</span>
            </button>
        </div>
    </div>

    <!-- Search Section -->
    <div class="mb-6">
        <div class="relative">
            <input 
                type="text" 
                wire:model.live.debounce.500ms="search" 
                placeholder="Cari kelas..." 
                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
            />
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kelas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun Ajaran</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wali Kelas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ketua Kelas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelajaran & Guru Pengampu</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($this->classes as $class)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $class->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $class->academic_year }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $class->waliKelas()?->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $class->members()->wherePivot('role', 'ketua_kelas')->wherePivot('is_active', true)->first()?->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @forelse ($class->subjectsWithTeachers as $subject)
                                <div>
                                    <span class="font-semibold">{{ $subject->name }}</span>:
                                    <span>{{ $subject->pivot->user_id ? (\App\Models\User::find($subject->pivot->user_id)?->name ?? '-') : '-' }}</span>
                                </div>
                            @empty
                                <span class="text-gray-400">-</span>
                            @endforelse
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a 
                                href="{{ route('class.detail', ['id' => $class->id]) }}"
                                class="text-blue-600 hover:text-blue-900 mr-3 transition-colors duration-150"
                            >Lihat</a>
                            <button 
                                wire:click="openModal({{ $class->id }})"
                                class="text-indigo-600 hover:text-indigo-900 mr-3 transition-colors duration-150"
                            >Edit</button>
                            <button 
                                wire:click="confirmDelete({{ $class->id }})"
                                class="text-red-600 hover:text-red-900 transition-colors duration-150"
                            >Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <x-empty-state 
                                message="Tidak ada data kelas" 
                                description="Mulai dengan menambahkan kelas baru" 
                            />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="md:hidden space-y-3">
        @forelse ($this->classes as $class)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-base font-medium text-gray-900 mb-1">{{ $class->name }}</h3>
                        <div class="space-y-0.5">
                            <p class="text-xs text-gray-600"><span class="font-medium">Tahun Ajaran:</span> {{ $class->academic_year }}</p>
                            <p class="text-xs text-gray-600"><span class="font-medium">Wali Kelas:</span> {{ $class->waliKelas()?->name ?? '-' }}</p>
                            <p class="text-xs text-gray-600"><span class="font-medium">Ketua Kelas:</span> {{ $class->members()->wherePivot('role', 'ketua_kelas')->wherePivot('is_active', true)->first()?->name ?? '-' }}</p>
                            <div class="mt-2">
                                <span class="font-medium text-xs text-gray-700">Pelajaran & Guru Pengampu:</span>
                                @forelse ($class->subjectsWithTeachers as $subject)
                                    <div class="text-xs">
                                        <span class="font-semibold">{{ $subject->name }}</span>:
                                        <span>{{ $subject->pivot->user_id ? (\App\Models\User::find($subject->pivot->user_id)?->name ?? '-') : '-' }}</span>
                                    </div>
                                @empty
                                    <span class="text-gray-400 text-xs">-</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-3 flex gap-1.5">
                    <a 
                        href="{{ route('class.detail', ['id' => $class->id]) }}"
                        class="flex-1 inline-flex items-center justify-center px-2 py-1.5 border border-blue-600 text-blue-600 text-xs font-medium rounded hover:bg-blue-50 transition-colors duration-150"
                    >
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Lihat
                    </a>
                    <button 
                        wire:click="openModal({{ $class->id }})"
                        class="flex-1 inline-flex items-center justify-center px-2 py-1.5 border border-indigo-600 text-indigo-600 text-xs font-medium rounded hover:bg-indigo-50 transition-colors duration-150"
                    >
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </button>
                    <button 
                        wire:click="confirmDelete({{ $class->id }})"
                        class="flex-1 inline-flex items-center justify-center px-2 py-1.5 border border-red-600 text-red-600 text-xs font-medium rounded hover:bg-red-50 transition-colors duration-150"
                    >
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus
                    </button>
                </div>
            </div>
        @empty
            <x-empty-state 
                message="Tidak ada data kelas" 
                description="Mulai dengan menambahkan kelas baru" 
            />
        @endforelse
    </div>

    <!-- Pagination -->
    @if($this->classes->hasPages())
        <div class="mt-6">
            <x-pagination :model="$this->classes" />
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
            class="bg-white rounded-lg shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto"
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
                            {{ $editingClass ? 'Edit Kelas' : 'Tambah Kelas' }}
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
                <div class="px-6 py-4 space-y-4">
                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Kelas *</label>
                        <input 
                            type="text" 
                            id="name"
                            wire:model.defer="name" 
                            placeholder="Masukkan nama kelas" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 @error('name') border-red-500 @enderror"
                            required 
                        />
                        @error('name') 
                            <p class="mt-1 ms-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Academic Year Field -->
                    <div>
                        <label for="academic_year" class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran *</label>
                        <input 
                            type="text" 
                            id="academic_year"
                            wire:model.defer="academic_year" 
                            placeholder="Masukkan tahun ajaran (misal: 2024/2025)" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 @error('academic_year') border-red-500 @enderror"
                            required 
                        />
                        @error('academic_year') 
                            <p class="mt-1 ms-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- (Field wali kelas dan ketua kelas dihapus, sekarang diambil otomatis dari class_members) -->
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
                            {{ $editingClass ? 'Perbarui' : 'Simpan' }}
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
