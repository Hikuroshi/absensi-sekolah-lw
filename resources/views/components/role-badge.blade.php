@props(['role'])

<span class="{{ $role->badgeClasses() }} rounded-full px-3 py-1 text-xs">
    {{ $role->label() }}
</span>
