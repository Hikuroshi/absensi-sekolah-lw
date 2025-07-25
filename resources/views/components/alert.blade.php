@props([
    'type' => 'info', // info, success, warning, error
    'message',
    'dismissible' => true,
])

@php
    $colors = [
        'info' => [
            'bg' => 'bg-blue-500/20',
            'border' => 'border-blue-400/30',
            'icon' => 'lucide-info',
        ],
        'success' => [
            'bg' => 'bg-green-500/20',
            'border' => 'border-green-400/30',
            'icon' => 'lucide-check-circle-2',
        ],
        'warning' => [
            'bg' => 'bg-yellow-500/20',
            'border' => 'border-yellow-400/30',
            'icon' => 'lucide-alert-triangle',
        ],
        'error' => [
            'bg' => 'bg-red-500/20',
            'border' => 'border-red-400/30',
            'icon' => 'lucide-alert-circle',
        ],
    ];

    $color = $colors[$type] ?? $colors['info'];
@endphp

<div
    class="glass-card-default {{ $color['bg'] }} {{ $color['border'] }} mb-4 flex items-center rounded-lg border p-4 text-white shadow-lg"
    role="alert"
    x-data="{ show: true }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform scale-95"
    x-transition:enter-end="opacity-100 transform scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform scale-100"
    x-transition:leave-end="opacity-0 transform scale-95"
>
    <x-dynamic-component class="mr-3 h-5 w-5" :component="$color['icon']" />

    <div class="flex-1">
        {{ $message ?? $slot }}
    </div>

    @if ($dismissible)
        <button class="ml-4 inline-flex rounded-md p-1 text-white/50 hover:text-white focus:outline-none focus:ring-2 focus:ring-white/50" type="button" aria-label="Close" x-on:click="show = false">
            <x-lucide-x class="h-5 w-5" />
        </button>
    @endif
</div>
