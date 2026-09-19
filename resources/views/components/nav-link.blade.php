@props(['href', 'active' => false, 'icon' => '•'])

@php
    $base = 'flex items-center gap-3 rounded-lg px-3 py-2 transition-colors duration-150';
    $activeClass = 'bg-blue-600 text-white';
    $inactiveClass = 'text-slate-300 hover:bg-slate-700 hover:text-white';
@endphp

<a href="{{ $href }}" class="{{ $base }} {{ $active ? $activeClass : $inactiveClass }}">
    <span class="text-base">{{ $icon }}</span>
    <span>{{ $slot }}</span>
</a>
