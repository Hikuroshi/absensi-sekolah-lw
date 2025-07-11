@props(['role'])

@php
    $colors = [
        'guru' => 'blue',
        'siswa' => 'green',
        'ketua_kelas' => 'yellow'
    ];
    $texts = [
        'guru' => 'Guru',
        'siswa' => 'Siswa',
        'ketua_kelas' => 'Ketua Kelas'
    ];
@endphp

@if(isset($colors[$role]))
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $colors[$role] }}-100 text-{{ $colors[$role] }}-800">
        {{ $texts[$role] ?? ucfirst($role) }}
    </span>
@endif