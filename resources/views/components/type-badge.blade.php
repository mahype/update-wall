@props(['type'])

@php
    $classes = match($type) {
        'security' => 'bg-red-100 text-red-700',
        'plugin' => 'bg-purple-100 text-purple-700',
        'theme' => 'bg-pink-100 text-pink-700',
        'core' => 'bg-indigo-100 text-indigo-700',
        'image' => 'bg-cyan-100 text-cyan-700',
        'distro' => 'bg-amber-100 text-amber-700',
        default => 'bg-gray-100 text-gray-600',
    };
@endphp

<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $classes }}">
    {{ ucfirst($type) }}
</span>
