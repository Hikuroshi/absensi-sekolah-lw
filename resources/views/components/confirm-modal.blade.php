@props([
    'id' => 'confirm-modal',
    'action' => '',
    'title' => 'Konfirmasi Hapus',
    'message' => 'Apakah Anda yakin ingin menghapus data ini?',
    'confirmText' => 'Hapus',
    'cancelText' => 'Batal',
])

<div
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="display: none;"
    x-data="{
        open: false,
        formAction: '{{ $action }}',
    }"
    x-show="open"
    x-on:open-confirm-modal.window="
        if ($event.detail.id === '{{ $id }}') {
            formAction = $event.detail.action || '{{ $action }}';
            open = true;
        }
    "
    x-on:keydown.escape.window="open = false"
    {{-- x-trap.noscroll="open" --}}
    x-transition.opacity
>
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" x-show="open" x-on:click="open = false"></div>

    <!-- Modal Content -->
    <div class="glass-card relative z-10 w-full max-w-md rounded-xl border border-white/20 p-6 shadow-xl" x-show="open">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-medium text-white">{{ $title }}</h3>
            <button class="rounded-md p-1 text-white/50 hover:text-white focus:outline-none focus:ring-2 focus:ring-purple-500" type="button" x-on:click="open = false">
                <x-lucide-x class="h-5 w-5" />
            </button>
        </div>

        <p class="mb-6 text-white/80">{{ $message }}</p>

        <div class="flex justify-end space-x-3">
            <button class="rounded-lg border border-white/20 bg-transparent px-4 py-2 text-sm font-medium text-white hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-purple-500" type="button" x-ref="cancelButton" x-on:click="open = false">
                {{ $cancelText }}
            </button>

            <form x-bind:action="formAction" method="POST">
                @csrf
                @method('DELETE')
                <button class="rounded-lg bg-gradient-to-br from-red-600 to-red-700 px-4 py-2 text-sm font-medium text-white shadow-lg hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-500" type="submit">
                    {{ $confirmText }}
                </button>
            </form>
        </div>
    </div>
</div>
