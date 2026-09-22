@props(['href', 'active' => false, 'icon' => 'dot'])

@php
    $base = 'flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition-all duration-150 text-sm font-medium';
    $activeClass = 'bg-blue-600 text-white shadow-sm shadow-blue-500/20';
    $inactiveClass = 'text-slate-300 hover:bg-slate-800 hover:text-white';
    $iconClass = str_starts_with($icon, 'bi-') ? $icon : 'bi-' . $icon;
@endphp

<a href="{{ $href }}" class="{{ $base }} {{ $active ? $activeClass : $inactiveClass }}">
    <i class="bi {{ $iconClass }} text-lg leading-none"></i>
    <span>{{ $slot }}</span>
</a>
