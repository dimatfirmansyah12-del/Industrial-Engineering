@props([
    'title',
    'value',
    'subtitle' => '',
    'color' => 'blue',
    'compact' => false,
])

@php
    $borderClass = match($color) {
        'blue' => 'border-blue-500',
        'yellow' => 'border-yellow-500',
        'purple' => 'border-purple-500',
        'red' => 'border-red-500',
        'green' => 'border-green-500',
        'orange' => 'border-orange-500',
        'gray' => 'border-gray-500',
        default => 'border-blue-500',
    };

    $cardClass = $compact
        ? 'rounded-lg shadow-sm px-4 py-3'
        : 'rounded-xl shadow p-6';

    $titleClass = $compact
        ? 'text-xs font-medium leading-tight'
        : 'text-sm';

    $valueClass = $compact
        ? 'text-2xl leading-none mt-2'
        : 'text-3xl mt-2';

    $subtitleClass = $compact
        ? 'text-xs mt-1'
        : 'text-xs mt-2';
@endphp

<div class="bg-white border-l-4 min-w-0 {{ $borderClass }} {{ $cardClass }}">
    <p class="{{ $titleClass }} text-gray-500">{{ $title }}</p>

    <h3 class="{{ $valueClass }} font-bold text-gray-800 break-words">
        {{ $value }}
    </h3>

    @if ($subtitle)
        <p class="{{ $subtitleClass }} text-gray-400">
            {{ $subtitle }}
        </p>
    @endif
</div>
