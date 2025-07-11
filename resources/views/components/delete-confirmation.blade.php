@props([
    'show' => false,
    'title' => 'Konfirmasi Penghapusan',
    'message' => 'Anda yakin ingin menghapus item ini?',
    'description' => 'Data yang sudah dihapus tidak dapat dikembalikan. Pastikan item ini tidak terkait dengan data lain sebelum menghapus.',
    'confirmText' => 'Ya, Hapus',
    'cancelText' => 'Batal',
    'confirmAction' => '',
    'cancelAction' => '',
    'confirmButtonClass' => 'bg-red-600 hover:bg-red-700 text-white',
    'cancelButtonClass' => 'border-gray-300 text-gray-700 hover:bg-gray-50',
    'size' => 'md' // sm, md, lg
])

@php
    $sizeClasses = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg'
    ];
@endphp

<div 
    x-data="{ open: @entangle('showDeleteModal') }" 
    x-show="open" 
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" 
    style="display: none;"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    {{ $attributes->whereStartsWith('x-') }}
>
    <div 
        class="bg-white rounded-lg shadow-xl w-full {{ $sizeClasses[$size] }}"
        @click.stop
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
    >
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">
                    @isset($title)
                        {{ $title }}
                    @else
                        {{ $title }}
                    @endisset
                </h3>
                <button 
                    type="button" 
                    @if($cancelAction) 
                        {{ $cancelAction }}
                    @else
                        @click="open = false"
                    @endif
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
            <div class="flex items-start">
                <div class="flex-shrink-0 pt-0.5">
                    @isset($icon)
                        {{ $icon }}
                    @else
                        <svg class="h-10 w-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    @endisset
                </div>
                <div class="ml-4">
                    <h4 class="text-lg font-medium text-gray-900">
                        @isset($message)
                            {{ $message }}
                        @else
                            {{ $message }}
                        @endisset
                    </h4>
                    <p class="mt-1 text-sm text-gray-500">
                        @isset($description)
                            {{ $description }}
                        @else
                            {{ $description }}
                        @endisset
                    </p>
                    
                    @isset($content)
                        <div class="mt-3">
                            {{ $content }}
                        </div>
                    @endisset
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row gap-2 sm:justify-end">
            @isset($footer)
                {{ $footer }}
            @else
                <button 
                    type="button" 
                    @if($cancelAction) 
                        {{ $cancelAction }}
                    @else
                        @click="open = false"
                    @endif
                    class="w-full sm:w-auto px-3 py-1.5 text-sm border {{ $cancelButtonClass }} font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                >
                    {{ $cancelText }}
                </button>
                <button 
                    type="button" 
                    @if($confirmAction)
                        {{ $confirmAction }}
                    @endif
                    class="w-full sm:w-auto px-3 py-1.5 text-sm {{ $confirmButtonClass }} font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>
                        {{ $confirmText }}
                    </span>
                    <span wire:loading>
                        <svg class="animate-spin -ml-1 mr-1.5 h-3.5 w-3.5 text-white inline" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memproses...
                    </span>
                </button>
            @endisset
        </div>
    </div>
</div>