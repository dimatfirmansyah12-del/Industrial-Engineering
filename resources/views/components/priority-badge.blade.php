@props([
    'priority' => '',
])

@php
    $priorityClass = match($priority) {
        'Low' => 'bg-green-100 text-green-700',
        'Medium' => 'bg-yellow-100 text-yellow-700',
        'High' => 'bg-orange-100 text-orange-700',
        'Urgent' => 'bg-red-100 text-red-700',
        default => 'bg-gray-100 text-gray-700',
    };
@endphp

<span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $priorityClass }}">
    {{ $priority ?: '-' }}
</span>
