@props(['label', 'value', 'color' => 'gray', 'active' => false])

@php
    $borderColor = match($color) {
        'green' => 'border-green-500',
        'yellow' => 'border-yellow-500',
        'red' => 'border-red-500',
        'gray' => 'border-gray-400',
        'indigo' => 'border-indigo-500',
        default => 'border-gray-300',
    };
    $textColor = match($color) {
        'green' => 'text-green-600',
        'yellow' => 'text-yellow-600',
        'red' => 'text-red-600',
        'gray' => 'text-gray-500',
        'indigo' => 'text-indigo-600',
        default => 'text-gray-500',
    };
@endphp

<div {{ $attributes->merge(['class' => "bg-white rounded-xl border-l-4 $borderColor p-5 shadow-sm cursor-pointer transition hover:shadow-md"]) }}>
    <p class="text-sm font-medium text-gray-500">{{ $label }}</p>
    <p class="mt-1 text-3xl font-bold {{ $textColor }}">{{ $value }}</p>
</div>
