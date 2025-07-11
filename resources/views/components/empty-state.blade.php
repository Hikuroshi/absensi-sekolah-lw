@props([
    'message' => 'Tidak ada data',
    'description' => null,
    'icon' => null,
])
<div class="flex flex-col items-center justify-center py-8">
    @if($icon)
        {!! $icon !!}
    @else
        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
    @endif
    <p class="text-lg font-medium text-gray-900 mb-2">{{ $message }}</p>
    @if($description)
        <p class="text-sm text-gray-500">{{ $description }}</p>
    @endif
</div> 