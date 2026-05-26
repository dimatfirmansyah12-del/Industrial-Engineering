<x-dashboard-layout>
    <x-page-header
        title="Kanban Board Request"
        subtitle="Visual monitoring status request Industrial Engineering"
    >
        <a href="{{ route('ie-requests.index') }}"
            class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-2 rounded-lg font-medium">
            List View
        </a>
    </x-page-header>

    <div class="p-8 bg-gray-100 min-h-screen">
        <div class="bg-white rounded-xl shadow p-6 mb-6">
            <form method="GET" action="{{ route('ie-requests.kanban') }}">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                        <select name="department"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Department</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department }}" {{ request('department') == $department ? 'selected' : '' }}>
                                    {{ $department }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                        <select name="priority"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Priority</option>
                            @foreach (['Low', 'Medium', 'High', 'Urgent'] as $priority)
                                <option value="{{ $priority }}" {{ request('priority') == $priority ? 'selected' : '' }}>
                                    {{ $priority }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deadline</label>
                        <select name="deadline"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Deadline</option>
                            <option value="delay" {{ request('deadline') == 'delay' ? 'selected' : '' }}>Delay</option>
                            <option value="due_soon" {{ request('deadline') == 'due_soon' ? 'selected' : '' }}>Due Soon</option>
                        </select>
                    </div>

                    <div class="flex gap-2 md:col-span-2">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium">
                            Terapkan
                        </button>

                        <a href="{{ route('ie-requests.kanban') }}"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2 rounded-lg font-medium">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto pb-4">
            <div class="flex gap-4 min-w-max">
                @foreach ($statuses as $status)
                    @php
                        $statusRequests = $requests->get($status, collect());
                    @endphp

                    <div class="bg-white rounded-xl shadow p-4 w-[280px] min-w-[280px]">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-gray-800 text-sm">{{ $status }}</h3>
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                {{ $statusRequests->count() }}
                            </span>
                        </div>

                        <div class="space-y-3">
                            @forelse ($statusRequests as $request)
                                <div class="bg-gray-50 rounded-lg p-4 border hover:shadow transition">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="font-bold text-gray-800">{{ $request->request_number }}</p>
                                            <p class="text-xs text-gray-500 mt-1">{{ $request->requester_name }}</p>
                                        </div>
                                    </div>

                                    <div class="mt-3 space-y-1 text-xs text-gray-600">
                                        <p><span class="text-gray-400">Jenis:</span> {{ $request->request_type }}</p>
                                        <p><span class="text-gray-400">Qty:</span> {{ $request->request_qty ?? 1 }}</p>
                                        <p><span class="text-gray-400">Dept:</span> {{ $request->department }}</p>
                                        <p><span class="text-gray-400">Line:</span> {{ $request->line_area ?? '-' }}</p>
                                    </div>

                                    <div class="mt-3">
                                        <x-priority-badge :priority="$request->priority" />
                                    </div>

                                    <div class="mt-3 text-xs text-gray-600">
                                        <p class="text-gray-400">Target Date</p>
                                        <p class="font-medium text-gray-800">{{ $request->target_date ?? '-' }}</p>
                                    </div>

                                    <div class="mt-2">
                                        @if ($request->is_delay)
                                            <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                Delay {{ $request->delay_days }} hari
                                            </span>
                                        @elseif ($request->is_due_soon)
                                            <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                                Due Soon {{ $request->due_soon_days }} hari lagi
                                            </span>
                                        @endif
                                    </div>

                                    <a href="{{ route('ie-requests.show', $request->id) }}"
                                        class="inline-block mt-4 text-sm text-blue-600 hover:underline">
                                        Detail
                                    </a>
                                </div>
                            @empty
                                <div class="py-8 text-center text-sm text-gray-400">
                                    Tidak ada request.
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-dashboard-layout>
