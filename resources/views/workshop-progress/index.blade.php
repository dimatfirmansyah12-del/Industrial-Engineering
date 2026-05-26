<x-dashboard-layout>
    <x-page-header
        title="Workshop Progress"
        subtitle="Monitoring progress pekerjaan workshop"
    >
        <form method="GET" action="{{ route('workshop-progress.index') }}"
            class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari schedule, request, requester..."
                class="w-full rounded-lg border-gray-300 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:w-64">

            <select name="progress_status"
                class="w-full rounded-lg border-gray-300 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:w-44">
                <option value="">All Progress Status</option>
                @foreach (['Not Started', 'On Progress', 'Hold', 'Rework', 'Done'] as $progressStatus)
                    <option value="{{ $progressStatus }}" {{ request('progress_status') == $progressStatus ? 'selected' : '' }}>
                        {{ $progressStatus }}
                    </option>
                @endforeach
            </select>

            <button type="submit"
                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Terapkan
            </button>

            <a href="{{ route('workshop-progress.index') }}"
                class="rounded-lg bg-gray-200 px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-300">
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
            <div class="mb-4 flex justify-between items-center">
                <p class="text-sm text-gray-500">
                    Total data ditemukan:
                    <span class="font-semibold text-gray-800">{{ $schedules->total() }}</span>
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-gray-500">
                            <th class="py-3 px-3">No</th>
                            <th class="py-3 px-3">Schedule Number</th>
                            <th class="py-3 px-3">No Request</th>
                            <th class="py-3 px-3">Requester</th>
                            <th class="py-3 px-3">Department</th>
                            <th class="py-3 px-3">Line / Area</th>
                            <th class="py-3 px-3">Jenis / Qty</th>
                            <th class="py-3 px-3">PIC Workshop</th>
                            <th class="py-3 px-3">Schedule Status</th>
                            <th class="py-3 px-3">Progress Status</th>
                            <th class="py-3 px-3">Progress %</th>
                            <th class="py-3 px-3">Planned Finish</th>
                            <th class="py-3 px-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($schedules as $schedule)
                            @php
                                $request = $schedule->ieRequest;
                                $requestQty = max(1, (int) ($request?->request_qty ?? 1));
                                $usesAutoQtyProgress = $requestQty > 1;
                                $completedQty = min($requestQty, max(0, (int) ($schedule->completed_qty ?? 0)));
                                $progressStatusClass = match($schedule->progress_status) {
                                    'On Progress' => 'bg-blue-100 text-blue-700',
                                    'Hold' => 'bg-yellow-100 text-yellow-700',
                                    'Rework' => 'bg-orange-100 text-orange-700',
                                    'Done' => 'bg-green-100 text-green-700',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                                $progressPercentage = min(100, max(0, (int) $schedule->progress_percentage));
                            @endphp

                            <tr class="border-b align-top hover:bg-gray-50">
                                <td class="py-3 px-3">
                                    {{ $schedules->firstItem() + $loop->index }}
                                </td>
                                <td class="py-3 px-3 font-semibold text-gray-800">
                                    {{ $schedule->schedule_number }}
                                </td>
                                <td class="py-3 px-3">
                                    {{ $request?->request_number ?? '-' }}
                                </td>
                                <td class="py-3 px-3">
                                    {{ $request?->requester_name ?? '-' }}
                                </td>
                                <td class="py-3 px-3">
                                    {{ $request?->department ?? '-' }}
                                </td>
                                <td class="py-3 px-3">
                                    {{ $request?->line_area ?? '-' }}
                                </td>
                                <td class="py-3 px-3">
                                    <p class="font-semibold text-gray-800">{{ $request?->request_type ?? '-' }}</p>
                                    <p class="text-xs text-gray-500">Qty: {{ $request?->request_qty ?? 1 }}</p>
                                </td>
                                <td class="py-3 px-3">
                                    {{ $schedule->pic_workshop ?? '-' }}
                                </td>
                                <td class="py-3 px-3">
                                    {{ $schedule->schedule_status }}
                                </td>
                                <td class="py-3 px-3">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $progressStatusClass }}">
                                        {{ $schedule->progress_status }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 min-w-[160px]">
                                    <div class="flex items-center gap-2">
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $progressPercentage }}%"></div>
                                        </div>
                                        <span class="text-xs font-semibold text-gray-700">{{ $progressPercentage }}%</span>
                                    </div>
                                    @if ($usesAutoQtyProgress)
                                        <p class="mt-1 text-xs text-gray-500">Qty selesai: {{ $completedQty }}/{{ $requestQty }}</p>
                                    @endif
                                </td>
                                <td class="py-3 px-3">
                                    {{ $schedule->planned_finish_date?->format('Y-m-d') ?? '-' }}
                                </td>
                                <td class="py-3 px-3">
                                    <a href="{{ route('workshop-progress.show', $schedule->id) }}"
                                        class="text-blue-600 hover:underline">
                                        Detail Progress
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="py-6 text-center text-gray-400">
                                    Belum ada workshop schedule yang siap dikerjakan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $schedules->links() }}
            </div>
        </div>
    </div>
</x-dashboard-layout>
