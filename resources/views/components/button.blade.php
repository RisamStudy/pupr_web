@props([
    'type' => 'button',
    'color' => 'indigo',
    'size' => 'md'
])

@php
    $baseClasses = "inline-flex justify-center border border-transparent shadow-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2";

    $colorClasses = [
        'indigo' => "text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500",
        'red' => "text-white bg-red-600 hover:bg-red-700 focus:ring-red-500",
        'green' => "text-white bg-green-600 hover:bg-green-700 focus:ring-green-500",
        'blue' => "text-white bg-blue-600 hover:bg-blue-700 focus:ring-blue-500",
    ][$color] ?? $colorClasses['indigo'];

    $sizeClasses = [
        'sm' => "px-2 py-1 text-xs",
        'md' => "px-4 py-2 text-sm",
        'lg' => "px-6 py-3 text-base",
    ][$size] ?? $sizeClasses['md'];

    $classes = $baseClasses . ' ' . $colorClasses . ' ' . $sizeClasses;
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
