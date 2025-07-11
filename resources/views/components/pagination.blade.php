@props(['model'])

@if ($model->hasPages())
    <div class="border-t border-gray-200 bg-gray-50 px-4 py-3">
        <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
            <!-- Info Jumlah Data -->
            <div class="text-sm text-gray-500">
                @if (method_exists($model, 'total') && !is_null($model->total()))
                    Menampilkan
                    <span class="font-medium">{{ $model->firstItem() }}</span>
                    sampai
                    <span class="font-medium">{{ $model->lastItem() }}</span>
                    dari
                    <span class="font-medium">{{ $model->total() }}</span>
                    data
                @else
                    Halaman
                    <span class="font-medium">{{ $model->currentPage() }}</span>
                @endif
            </div>

            <!-- Navigasi Halaman -->
            <div class="flex items-center space-x-1">
                <!-- Tombol Previous -->
                @if ($model->onFirstPage())
                    <span class="cursor-not-allowed rounded-md border border-gray-300 bg-gray-100 px-3 py-1 text-sm font-medium text-gray-400">
                        Sebelumnya
                    </span>
                @else
                    <button class="rounded-md border border-gray-300 bg-white px-3 py-1 text-sm font-medium text-gray-700 transition-colors duration-200 hover:border-gray-400 hover:bg-gray-100" wire:click="previousPage">
                        Sebelumnya
                    </button>
                @endif

                <!-- Daftar Nomor Halaman (hanya jika bukan simplePaginate) -->
                @if (method_exists($model, 'lastPage') && !is_null($model->total()))
                    <div class="hidden space-x-1 sm:flex">
                        @php
                            $current = $model->currentPage();
                            $last = $model->lastPage();
                            $start = max($current - 2, 1);
                            $end = min($current + 2, $last);

                            if ($current <= 3) {
                                $end = min(5, $last);
                            }
                            if ($current >= $last - 2) {
                                $start = max($last - 4, 1);
                            }
                        @endphp

                        @if ($start > 1)
                            <button class="rounded-md border border-gray-300 bg-white px-3 py-1 text-sm font-medium text-gray-700 transition-colors duration-200 hover:border-gray-400 hover:bg-gray-100" wire:click="gotoPage(1)">
                                1
                            </button>
                            @if ($start > 2)
                                <span class="px-2 py-1 text-gray-500">...</span>
                            @endif
                        @endif

                        @for ($page = $start; $page <= $end; $page++)
                            @if ($page == $model->currentPage())
                                <span class="rounded-md border border-blue-500 bg-blue-50 px-3 py-1 text-sm font-medium text-blue-600 transition-colors duration-200 hover:bg-blue-100">
                                    {{ $page }}
                                </span>
                            @else
                                <button class="rounded-md border border-gray-300 bg-white px-3 py-1 text-sm font-medium text-gray-700 transition-colors duration-200 hover:border-gray-400 hover:bg-gray-100" wire:click="gotoPage({{ $page }})">
                                    {{ $page }}
                                </button>
                            @endif
                        @endfor

                        @if ($end < $last)
                            @if ($end < $last - 1)
                                <span class="px-2 py-1 text-gray-500">...</span>
                            @endif
                            <button class="rounded-md border border-gray-300 bg-white px-3 py-1 text-sm font-medium text-gray-700 transition-colors duration-200 hover:border-gray-400 hover:bg-gray-100" wire:click="gotoPage({{ $last }})">
                                {{ $last }}
                            </button>
                        @endif
                    </div>
                @endif

                <!-- Tombol Next -->
                @if ($model->hasMorePages())
                    <button class="rounded-md border border-gray-300 bg-white px-3 py-1 text-sm font-medium text-gray-700 transition-colors duration-200 hover:border-gray-400 hover:bg-gray-100" wire:click="nextPage">
                        Selanjutnya
                    </button>
                @else
                    <span class="cursor-not-allowed rounded-md border border-gray-300 bg-gray-100 px-3 py-1 text-sm font-medium text-gray-400">
                        Selanjutnya
                    </span>
                @endif
            </div>
        </div>
    </div>
@endif