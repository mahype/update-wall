@props(['priority'])

@php
    $classes = match($priority) {
        'critical' => 'bg-red-600 text-white',
        'high' => 'bg-orange-500 text-white',
        'normal' => 'bg-blue-100 text-blue-800',
        'low' => 'bg-gray-100 text-gray-600',
        default => 'bg-gray-100 text-gray-600',
    };
@endphp

<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $classes }}">
    {{ ucfirst($priority) }}
</span>
