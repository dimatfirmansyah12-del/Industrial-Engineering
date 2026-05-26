@props([
    'status' => '',
])

@php
    $statusClass = match($status) {
        'Request Submitted' => 'bg-gray-100 text-gray-700',
        'Memo Approved' => 'bg-green-100 text-green-700',
        'Drawing On Progress' => 'bg-blue-100 text-blue-700',
        'Drawing Done' => 'bg-cyan-100 text-cyan-700',
        'BOM Draft' => 'bg-amber-100 text-amber-700',
        'Waiting SAP Input' => 'bg-gray-100 text-gray-700',
        'Waiting PR Input' => 'bg-gray-100 text-gray-700',
        'Waiting Section Head Approval' => 'bg-yellow-100 text-yellow-700',
        'Waiting Atasan IE Approval' => 'bg-yellow-100 text-yellow-700',
        'Waiting Division Head Approval' => 'bg-yellow-100 text-yellow-700',
        'Waiting Director Approval' => 'bg-yellow-100 text-yellow-700',
        'Sent to Purchasing' => 'bg-blue-100 text-blue-700',
        'SAP Approval Rejected' => 'bg-red-100 text-red-700',
        'PR Approval Rejected' => 'bg-red-100 text-red-700',
        'Waiting Material' => 'bg-purple-100 text-purple-700',
        'Material Complete' => 'bg-indigo-100 text-indigo-700',
        'Workshop Scheduled' => 'bg-orange-100 text-orange-700',
        'Workshop On Progress' => 'bg-yellow-100 text-yellow-700',
        'Final Check' => 'bg-teal-100 text-teal-700',
        'Waiting Handover' => 'bg-emerald-100 text-emerald-700',
        'Completed' => 'bg-green-100 text-green-700',
        'Closed' => 'bg-slate-200 text-slate-800',
        default => 'bg-gray-100 text-gray-700',
    };
@endphp

<span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
    {{ $status ?: '-' }}
</span>
