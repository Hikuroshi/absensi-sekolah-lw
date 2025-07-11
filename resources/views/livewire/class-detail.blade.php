<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('class.management') }}" class="inline-flex items-center text-sm text-indigo-600 hover:underline">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Manajemen Kelas
                </a>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mt-2">Detail Kelas: {{ $class->name }}</h1>
                <div class="mt-2 text-gray-600">Tahun Ajaran: <span class="font-medium">{{ $class->academic_year }}</span></div>
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

    <!-- Class Management Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Pengaturan Kelas</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Wali Kelas Section -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Wali Kelas</label>
                <form wire:submit.prevent="updateWaliKelas" class="flex gap-3">
                    <select 
                        wire:model.defer="editWaliKelasId" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                    >
                        <option value="">Pilih Guru</option>
                        @foreach($allTeachers as $guru)
                            <option value="{{ $guru->id }}">{{ $guru->name }}</option>
                        @endforeach
                    </select>
                    <button 
                        type="submit" 
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="updateWaliKelas">Simpan</span>
                        <span wire:loading wire:target="updateWaliKelas">
                            <svg class="animate-spin h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </form>
            </div>

            <!-- Ketua Kelas Section -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ketua Kelas</label>
                <form wire:submit.prevent="updateKetuaKelas" class="flex gap-3">
                    <select 
                        wire:model.defer="editKetuaKelasId" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                    >
                        <option value="">Pilih Siswa</option>
                        @foreach($allStudents as $siswa)
                            <option value="{{ $siswa->id }}">{{ $siswa->name }}</option>
                        @endforeach
                    </select>
                    <button 
                        type="submit" 
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="updateKetuaKelas">Simpan</span>
                        <span wire:loading wire:target="updateKetuaKelas">
                            <svg class="animate-spin h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Members Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="text-lg font-semibold text-gray-900">Daftar Anggota Kelas</h2>
            <form wire:submit.prevent="addMember" class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <select 
                    wire:model.defer="newMemberId" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                >
                    <option value="">Pilih Siswa</option>
                    @foreach($allStudents as $siswa)
                        @if(!$members->contains('id', $siswa->id))
                            <option value="{{ $siswa->id }}">{{ $siswa->name }}</option>
                        @endif
                    @endforeach
                </select>
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="addMember">Tambah</span>
                    <span wire:loading wire:target="addMember">
                        <svg class="animate-spin h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </form>
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($members as $member)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $member->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($member->pivot->role === 'guru')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        Wali Kelas
                                    </span>
                                @elseif($member->pivot->role === 'ketua_kelas')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Ketua Kelas
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Siswa
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($member->pivot->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if(!in_array($member->pivot->role, ['guru', 'ketua_kelas']))
                                    <button 
                                        wire:click="removeMember({{ $member->id }})" 
                                        class="text-red-600 hover:text-red-900 transition-colors duration-150"
                                        wire:loading.attr="disabled"
                                    >
                                        <span wire:loading.remove wire:target="removeMember({{ $member->id }})">Hapus</span>
                                        <span wire:loading wire:target="removeMember({{ $member->id }})">
                                            <svg class="animate-spin h-4 w-4 text-red-600 inline" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </span>
                                    </button>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                <x-empty-state 
                                    message="Tidak ada anggota kelas" 
                                    description="Mulai dengan menambahkan anggota baru" 
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden divide-y divide-gray-200">
            @forelse($members as $member)
                <div class="px-4 py-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">{{ $member->name }}</h3>
                            <div class="mt-1 flex flex-wrap gap-1">
                                @if($member->pivot->role === 'guru')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                        Wali Kelas
                                    </span>
                                @elseif($member->pivot->role === 'ketua_kelas')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        Ketua Kelas
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                        Siswa
                                    </span>
                                @endif
                                
                                @if($member->pivot->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                        Nonaktif
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div>
                            @if(!in_array($member->pivot->role, ['guru', 'ketua_kelas']))
                                <button 
                                    wire:click="removeMember({{ $member->id }})" 
                                    class="text-red-600 hover:text-red-900 transition-colors duration-150"
                                    wire:loading.attr="disabled"
                                >
                                    <span wire:loading.remove wire:target="removeMember({{ $member->id }})">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </span>
                                    <span wire:loading wire:target="removeMember({{ $member->id }})">
                                        <svg class="animate-spin h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center text-gray-500">
                    <x-empty-state 
                        message="Tidak ada anggota kelas" 
                        description="Mulai dengan menambahkan anggota baru" 
                    />
                </div>
            @endforelse
        </div>
    </div>
</div>