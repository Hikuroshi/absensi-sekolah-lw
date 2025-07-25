@props(['items'])

@if ($items->hasPages())
    <div class="mt-6 flex flex-col items-center justify-between space-y-4 sm:flex-row sm:space-y-0">
        <div class="text-sm text-white/60">
            Menampilkan
            {{ $items->firstItem() ?? 0 }}
            sampai
            {{ $items->lastItem() ?? $items->count() }}
            @if (method_exists($items, 'total'))
                dari {{ $items->total() }}
            @endif
            entri
        </div>

        <div class="flex space-x-1">
            {{-- Previous Page --}}
            @if ($items->onFirstPage())
                <span class="glass-card flex h-8 w-8 items-center justify-center rounded-lg opacity-30">
                    <x-lucide-chevron-left class="h-4 w-4" />
                </span>
            @else
                <a class="glass-card flex h-8 w-8 items-center justify-center rounded-lg hover:bg-white/10" href="{{ $items->previousPageUrl() }}">
                    <x-lucide-chevron-left class="h-4 w-4" />
                </a>
            @endif

            {{-- Page Links --}}
            @if (method_exists($items, 'lastPage'))
                {{-- Show limited pages for LengthAwarePaginator --}}
                @php
                    $current = $items->currentPage();
                    $last = $items->lastPage();
                    $start = max($current - 1, 1);
                    $end = min($current + 1, $last);

                    // Adjust if we're near the start
if ($current <= 2) {
    $end = min(3, $last);
}

// Adjust if we're near the end
                    if ($current >= $last - 1) {
                        $start = max($last - 2, 1);
                    }
                @endphp

                {{-- Show first page if not in range --}}
                @if ($start > 1)
                    <a class="glass-card flex h-8 w-8 items-center justify-center rounded-lg hover:bg-white/10" href="{{ $items->url(1) }}">1</a>
                    @if ($start > 2)
                        <span class="glass-card flex h-8 w-8 items-center justify-center rounded-lg text-white/50">...</span>
                    @endif
                @endif

                @for ($page = $start; $page <= $end; $page++)
                    @if ($page == $items->currentPage())
                        <span class="glass-card flex h-8 w-8 items-center justify-center rounded-lg bg-purple-600 text-white">{{ $page }}</span>
                    @else
                        <a class="glass-card flex h-8 w-8 items-center justify-center rounded-lg hover:bg-white/10" href="{{ $items->url($page) }}">{{ $page }}</a>
                    @endif
                @endfor

                {{-- Show last page if not in range --}}
                @if ($end < $last)
                    @if ($end < $last - 1)
                        <span class="glass-card flex h-8 w-8 items-center justify-center rounded-lg text-white/50">...</span>
                    @endif
                    <a class="glass-card flex h-8 w-8 items-center justify-center rounded-lg hover:bg-white/10" href="{{ $items->url($last) }}">{{ $last }}</a>
                @endif
            @else
                {{-- Only current page shown in simplePaginate --}}
                <span class="glass-card flex h-8 w-8 items-center justify-center rounded-lg bg-purple-600 text-white">{{ $items->currentPage() }}</span>
            @endif

            {{-- Next Page --}}
            @if ($items->hasMorePages())
                <a class="glass-card flex h-8 w-8 items-center justify-center rounded-lg hover:bg-white/10" href="{{ $items->nextPageUrl() }}">
                    <x-lucide-chevron-right class="h-4 w-4" />
                </a>
            @else
                <span class="glass-card flex h-8 w-8 items-center justify-center rounded-lg opacity-30">
                    <x-lucide-chevron-right class="h-4 w-4" />
                </span>
            @endif
        </div>
    </div>
@endif
