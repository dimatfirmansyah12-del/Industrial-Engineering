<x-dashboard-layout>
            <x-page-header
                title="Report Industrial Engineering"
                subtitle="Laporan request berdasarkan tanggal, status, priority, dan department"
            >
                <a href="{{ route('ie-requests.export', request()->query()) }}"
                    class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg font-medium">
                    Export CSV
                </a>
            </x-page-header>

            <div class="p-8">

                <!-- Summary Card -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <x-summary-card
                        title="Total Request"
                        value="{{ $totalRequest }}"
                        color="blue"
                    />

                    <x-summary-card
                        title="Completed / Closed"
                        value="{{ $completed }}"
                        color="green"
                    />

                    <x-summary-card
                        title="Waiting Material"
                        value="{{ $waitingMaterial }}"
                        color="purple"
                    />

                    <x-summary-card
                        title="Urgent"
                        value="{{ $urgent }}"
                        color="red"
                    />
                </div>

                <!-- Filter -->
                <div class="bg-white rounded-xl shadow p-6 mb-8">
                    <form method="GET" action="{{ route('reports.index') }}">
                        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Awal</label>
                                <input type="date" name="start_date" value="{{ request('start_date') }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                                <input type="date" name="end_date" value="{{ request('end_date') }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select name="status"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">All Status</option>
                                    @foreach (\App\Services\RequestWorkflow::STATUSES as $status)
                                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                            {{ $status }}
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
                                <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                                <input type="text" name="department" value="{{ request('department') }}"
                                    placeholder="Production, QA..."
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
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

                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <a href="{{ route('reports.index') }}"
                                class="px-5 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium">
                                Reset
                            </a>

                            <button type="submit"
                                class="px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium">
                                Terapkan Filter
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-xl shadow p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b text-left text-gray-500">
                                    <th class="py-3 px-3">No</th>
                                    <th class="py-3 px-3">No Request</th>
                                    <th class="py-3 px-3">Tanggal</th>
                                    <th class="py-3 px-3">Requester</th>
                                    <th class="py-3 px-3">Department</th>
                                    <th class="py-3 px-3">Jenis / Qty</th>
                                    <th class="py-3 px-3">Priority</th>
                                    <th class="py-3 px-3">Status</th>
                                    <th class="py-3 px-3">Target</th>
                                    <th class="py-3 px-3">Deadline Status</th>
                                    <th class="py-3 px-3">Delay Days</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($requests as $request)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 px-3">
                                            {{ $requests->firstItem() + $loop->index }}
                                        </td>

                                        <td class="py-3 px-3 font-semibold">
                                            {{ $request->request_number }}
                                        </td>

                                        <td class="py-3 px-3">
                                            {{ $request->request_date }}
                                        </td>

                                        <td class="py-3 px-3">
                                            {{ $request->requester_name }}
                                        </td>

                                        <td class="py-3 px-3">
                                            {{ $request->department }}
                                        </td>

                                        <td class="py-3 px-3">
                                            <p class="font-semibold text-gray-800">{{ $request->request_type }}</p>
                                            <p class="text-xs text-gray-500">Qty: {{ $request->request_qty ?? 1 }}</p>
                                        </td>

                                        <td class="py-3 px-3">
                                            <x-priority-badge :priority="$request->priority" />
                                        </td>

                                        <td class="py-3 px-3">
                                            <x-status-badge :status="$request->status" />
                                        </td>

                                        <td class="py-3 px-3">
                                            {{ $request->target_date ?? '-' }}
                                        </td>

                                        <td class="py-3 px-3">
                                            @if (!$request->target_date)
                                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">No Target</span>
                                            @elseif ($request->is_delay)
                                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                    Delay {{ $request->delay_days }} hari
                                                </span>
                                            @elseif ($request->is_due_soon)
                                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                                    Due Soon {{ $request->due_soon_days }} hari lagi
                                                </span>
                                            @else
                                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">On Track</span>
                                            @endif
                                        </td>

                                        <td class="py-3 px-3">
                                            {{ $request->is_delay ? $request->delay_days : 0 }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="py-6 text-center text-gray-400">
                                            Tidak ada data report.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $requests->links() }}
                    </div>
                </div>

            </div>
</x-dashboard-layout>
