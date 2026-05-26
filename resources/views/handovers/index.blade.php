<x-dashboard-layout>
    <x-page-header
        title="Handover"
        subtitle="Serah terima hasil pekerjaan Industrial Engineering ke customer"
    >
        <form method="GET" action="{{ route('handovers.index') }}"
            class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="No request, requester, department, line / area"
                class="w-full rounded-lg border-gray-300 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:w-72">

            <select name="handover_status"
                class="w-full rounded-lg border-gray-300 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:w-48">
                <option value="">All Handover Status</option>
                @foreach (['No Handover', 'Waiting Handover', 'Handover Process', 'Received', 'Rejected'] as $status)
                    <option value="{{ $status }}" {{ request('handover_status') == $status ? 'selected' : '' }}>
                        {{ $status }}
                    </option>
                @endforeach
            </select>

            <button type="submit"
                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Terapkan
            </button>

            <a href="{{ route('handovers.index') }}"
                class="rounded-lg bg-gray-600 px-4 py-2 text-center text-sm font-medium text-white hover:bg-gray-700">
                Reset
            </a>
        </form>
    </x-page-header>

    <div class="p-8">
        @if (session('success'))
            <div class="mb-4 bg-green-100 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 bg-red-100 text-red-700 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-gray-500">
                            <th class="py-3 px-3">No</th>
                            <th class="py-3 px-3">No Request</th>
                            <th class="py-3 px-3">Requester</th>
                            <th class="py-3 px-3">Department</th>
                            <th class="py-3 px-3">Line / Area</th>
                            <th class="py-3 px-3">Jenis / Qty</th>
                            <th class="py-3 px-3">Final Check</th>
                            <th class="py-3 px-3">Handover Number</th>
                            <th class="py-3 px-3">Handover Status</th>
                            <th class="py-3 px-3">Handover Date</th>
                            <th class="py-3 px-3">Received By</th>
                            <th class="py-3 px-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ieRequests as $ieRequest)
                            @php
                                $handover = $ieRequest->handover;
                                $handoverStatus = $handover?->handover_status ?? 'No Handover';
                                $handoverStatusClass = match($handoverStatus) {
                                    'Waiting Handover' => 'bg-gray-100 text-gray-700',
                                    'Handover Process' => 'bg-blue-100 text-blue-700',
                                    'Received' => 'bg-green-100 text-green-700',
                                    'Rejected' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp

                            <tr class="border-b align-top hover:bg-gray-50">
                                <td class="py-3 px-3">
                                    {{ $ieRequests->firstItem() + $loop->index }}
                                </td>
                                <td class="py-3 px-3 font-semibold text-gray-800">
                                    {{ $ieRequest->request_number }}
                                </td>
                                <td class="py-3 px-3">
                                    {{ $ieRequest->requester_name }}
                                </td>
                                <td class="py-3 px-3">
                                    {{ $ieRequest->department }}
                                </td>
                                <td class="py-3 px-3">
                                    {{ $ieRequest->line_area }}
                                </td>
                                <td class="py-3 px-3">
                                    <p class="font-semibold text-gray-800">{{ $ieRequest->request_type }}</p>
                                    <p class="text-xs text-gray-500">Qty: {{ $ieRequest->request_qty ?? 1 }}</p>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="space-y-1">
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                            {{ $ieRequest->finalCheck?->check_status ?? '-' }}
                                        </span>
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                            {{ $ieRequest->finalCheck?->result_status ?? '-' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3 px-3">
                                    {{ $handover?->handover_number ?? '-' }}
                                </td>
                                <td class="py-3 px-3">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $handoverStatusClass }}">
                                        {{ $handoverStatus }}
                                    </span>
                                </td>
                                <td class="py-3 px-3">
                                    {{ $handover?->handover_date?->format('Y-m-d') ?? '-' }}
                                </td>
                                <td class="py-3 px-3">
                                    {{ $handover?->received_by ?? '-' }}
                                </td>
                                <td class="py-3 px-3">
                                    <a href="{{ route('handovers.show', $ieRequest->id) }}"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-medium">
                                        Detail Handover
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="py-6 text-center text-gray-400">
                                    Belum ada request yang siap handover.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $ieRequests->links() }}
            </div>
        </div>
    </div>
</x-dashboard-layout>
