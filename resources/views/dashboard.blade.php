<x-dashboard-layout>
    <x-page-header
        title="Dashboard Industrial Engineering"
        subtitle="Monitoring request dari memo sampai handover closed"
    />

    <div class="p-8">
        @php
            $months = [
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember',
            ];

            $badgeClass = function ($status) {
                return match($status) {
                    'Approved', 'Memo Approved', 'Done', 'Complete', 'Completed', 'Closed', 'Passed', 'OK', 'Has BOM', 'BOM Submitted', 'Received' => 'bg-green-100 text-green-700',
                    'On Progress', 'Drawing On Progress', 'Checking', 'Handover Process', 'In Progress', 'Scheduled', 'Ready to Work' => 'bg-blue-100 text-blue-700',
                    'Waiting Approval', 'Waiting Check', 'Waiting Handover', 'Waiting Material', 'Waiting SAP Input', 'Waiting PR Input', 'Waiting Section Head Approval', 'Waiting Atasan IE Approval', 'Waiting Division Head Approval', 'Waiting Director Approval', 'Partial Arrived', 'Draft', 'BOM Draft' => 'bg-yellow-100 text-yellow-700',
                    'Revision', 'Need Rework', 'Rework', 'Hold', 'Rescheduled', 'Need Purchase', 'Workshop On Progress' => 'bg-orange-100 text-orange-700',
                    'Rejected', 'SAP Approval Rejected', 'PR Approval Rejected', 'Failed', 'Cancelled', 'NG', 'Delay' => 'bg-red-100 text-red-700',
                    'Sent to Purchasing' => 'bg-blue-100 text-blue-700',
                    'PO Created' => 'bg-indigo-100 text-indigo-700',
                    default => 'bg-gray-100 text-gray-700',
                };
            };

            $attentionCards = [
                [
                    'title' => 'Delay',
                    'requests' => $delayRequests,
                    'colorClass' => 'border-red-500',
                    'url' => route('ie-requests.index', ['deadline' => 'delay']),
                    'showTarget' => true,
                ],
                [
                    'title' => 'Due Soon',
                    'requests' => $dueSoonRequests,
                    'colorClass' => 'border-yellow-500',
                    'url' => route('ie-requests.index', ['deadline' => 'due_soon']),
                    'showTarget' => true,
                ],
                [
                    'title' => 'Urgent',
                    'requests' => $urgentRequests,
                    'colorClass' => 'border-red-500',
                    'url' => route('ie-requests.index', ['priority' => 'Urgent']),
                    'showTarget' => true,
                ],
                [
                    'title' => 'Waiting Memo Approval',
                    'requests' => $waitingMemoRequests,
                    'colorClass' => 'border-blue-500',
                    'url' => route('memo-approvals.index'),
                    'showTarget' => false,
                ],
                [
                    'title' => 'Waiting Material',
                    'requests' => $waitingMaterialRequests,
                    'colorClass' => 'border-purple-500',
                    'url' => route('material-arrivals.index'),
                    'showTarget' => false,
                ],
                [
                    'title' => 'Waiting Final Check',
                    'requests' => $waitingFinalCheckRequests,
                    'colorClass' => 'border-teal-500',
                    'url' => route('final-checks.index'),
                    'showTarget' => false,
                ],
                [
                    'title' => 'Waiting Handover',
                    'requests' => $waitingHandoverRequests,
                    'colorClass' => 'border-green-500',
                    'url' => route('handovers.index'),
                    'showTarget' => false,
                ],
            ];

            $attentionCards = collect($attentionCards)
                ->filter(fn ($attentionCard) => $attentionCard['requests']->isNotEmpty())
                ->values();

            $todayWorkSections = collect([
                [
                    'title' => 'Memo Approval',
                    'subtitle' => 'Memo menunggu keputusan',
                    'requests' => $todayMemoWork,
                    'colorClass' => 'border-blue-500',
                ],
                [
                    'title' => 'Drawing',
                    'subtitle' => 'On progress atau revision',
                    'requests' => $todayDrawingWork,
                    'colorClass' => 'border-indigo-500',
                ],
                [
                    'title' => 'Material',
                    'subtitle' => 'Waiting atau partial arrived',
                    'requests' => $todayMaterialWork,
                    'colorClass' => 'border-purple-500',
                ],
                [
                    'title' => 'Workshop Hari Ini',
                    'subtitle' => 'Schedule aktif atau progress tertahan',
                    'requests' => $todayWorkshopWork,
                    'colorClass' => 'border-orange-500',
                ],
                [
                    'title' => 'Final Check',
                    'subtitle' => 'Menunggu QC / rework',
                    'requests' => $todayFinalCheckWork,
                    'colorClass' => 'border-teal-500',
                ],
                [
                    'title' => 'Handover',
                    'subtitle' => 'Menunggu serah terima',
                    'requests' => $todayHandoverWork,
                    'colorClass' => 'border-green-500',
                ],
            ]);
        @endphp

        <div class="bg-white rounded-lg shadow-sm px-4 py-3 mb-5">
            <form method="GET" action="{{ route('dashboard') }}">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Month</label>
                        <select name="month"
                            class="w-full rounded-md border-gray-300 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Month</option>
                            @foreach ($months as $monthNumber => $monthName)
                                <option value="{{ $monthNumber }}" {{ request('month') == $monthNumber ? 'selected' : '' }}>
                                    {{ $monthName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Year</label>
                        <select name="year"
                            class="w-full rounded-md border-gray-300 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Year</option>
                            @foreach ($years as $year)
                                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Department</label>
                        <select name="department"
                            class="w-full rounded-md border-gray-300 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Department</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department }}" {{ request('department') == $department ? 'selected' : '' }}>
                                    {{ $department }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2 flex gap-2">
                        <button type="submit"
                            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                            Terapkan
                        </button>

                        <a href="{{ route('dashboard') }}"
                            class="rounded-md bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3 mb-5">
            <x-summary-card
                title="Total Request"
                value="{{ $totalRequest }}"
                color="blue"
                compact
            />

            <x-summary-card
                title="Waiting Memo Approval"
                value="{{ $waitingMemoApproval }}"
                color="yellow"
                compact
            />

            <x-summary-card
                title="Drawing Progress"
                value="{{ $drawingProgress }}"
                color="blue"
                compact
            />

            <x-summary-card
                title="Waiting PR Input"
                value="{{ $waitingSapInput }}"
                color="gray"
                compact
            />

            <x-summary-card
                title="Waiting Approval"
                value="{{ $waitingInternalApproval }}"
                color="yellow"
                compact
            />

            <x-summary-card
                title="Waiting Material"
                value="{{ $waitingMaterial }}"
                color="purple"
                compact
            />

            <x-summary-card
                title="Workshop Progress"
                value="{{ $workshopProgress }}"
                color="orange"
                compact
            />

            <x-summary-card
                title="Final Check"
                value="{{ $finalCheckCount }}"
                color="green"
                compact
            />

            <x-summary-card
                title="Waiting Handover"
                value="{{ $waitingHandover }}"
                color="gray"
                compact
            />

            <x-summary-card
                title="Delay"
                value="{{ $delay }}"
                color="red"
                compact
            />

            <x-summary-card
                title="Due Soon"
                value="{{ $dueSoon }}"
                color="yellow"
                compact
            />

            <x-summary-card
                title="Closed"
                value="{{ $closed }}"
                color="green"
                compact
            />
        </div>

        <div class="mb-6">
            <div class="flex flex-col gap-1 mb-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Pekerjaan Hari Ini</h2>
                    <p class="text-sm text-gray-500">Daftar pekerjaan yang perlu ditindaklanjuti dari tiap proses.</p>
                </div>
                <p class="text-sm text-gray-500">{{ now()->format('d M Y') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach ($todayWorkSections as $section)
                    <div class="bg-white rounded-lg shadow-sm border-t-4 {{ $section['colorClass'] }} p-4">
                        <div class="mb-3 flex items-start justify-between gap-3">
                            <div>
                                <h3 class="text-sm font-bold text-gray-800">{{ $section['title'] }}</h3>
                                <p class="text-xs text-gray-500">{{ $section['subtitle'] }}</p>
                            </div>
                            <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">
                                {{ $section['requests']->count() }}
                            </span>
                        </div>

                        <div class="space-y-2">
                            @forelse ($section['requests'] as $workRequest)
                                <a href="{{ route('ie-requests.show', $workRequest->id) }}"
                                    class="block rounded-md border bg-gray-50 px-3 py-2 transition hover:border-blue-200 hover:bg-blue-50">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-bold text-blue-700">{{ $workRequest->request_number }}</p>
                                            <p class="mt-0.5 truncate text-xs text-gray-600">
                                                {{ $workRequest->request_type }} | Qty: {{ $workRequest->request_qty ?? 1 }}
                                            </p>
                                            <p class="mt-0.5 truncate text-xs text-gray-500">
                                                {{ $workRequest->department }} - {{ $workRequest->line_area ?? '-' }}
                                            </p>
                                        </div>

                                        <span class="shrink-0 rounded-full px-2 py-1 text-[11px] font-semibold {{ $badgeClass($workRequest->status) }}">
                                            {{ $workRequest->status }}
                                        </span>
                                    </div>

                                    @if ($section['title'] === 'Workshop Hari Ini' && $workRequest->workshopSchedule)
                                        <p class="mt-2 text-xs text-gray-500">
                                            PIC: {{ $workRequest->workshopSchedule->pic_workshop ?? '-' }}
                                            | Finish: {{ $workRequest->workshopSchedule->planned_finish_date?->format('Y-m-d') ?? '-' }}
                                        </p>
                                    @endif
                                </a>
                            @empty
                                <div class="rounded-md bg-gray-50 px-3 py-5 text-center text-sm text-gray-400">
                                    Tidak ada pekerjaan.
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-800">Perhatian Hari Ini</h2>
                <p class="text-sm text-gray-500">Pekerjaan urgent, delay, atau tertahan</p>
            </div>

            @if ($attentionCards->isEmpty())
                <div class="bg-white rounded-lg shadow-sm p-4 text-sm text-gray-500">
                    Tidak ada data perhatian hari ini.
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach ($attentionCards as $attentionCard)
                        <div class="h-64 bg-white rounded-lg shadow-sm p-4 border-t-4 {{ $attentionCard['colorClass'] }} flex flex-col overflow-hidden">
                            <div class="flex items-start justify-between gap-3 mb-3 pb-3 border-b border-gray-100">
                                <div>
                                    <h3 class="text-sm font-bold text-gray-800">{{ $attentionCard['title'] }}</h3>
                                    <p class="text-xs text-gray-500">{{ $attentionCard['requests']->count() }} data</p>
                                </div>

                                <a href="{{ $attentionCard['url'] }}" class="text-xs text-blue-600 hover:underline">
                                    Lihat Semua
                                </a>
                            </div>

                            <div class="flex-1 space-y-2 overflow-y-auto pr-1">
                                @foreach ($attentionCard['requests'] as $attentionRequest)
                                    <div class="border rounded-md px-3 py-2 bg-gray-50 min-h-14">
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="min-w-0">
                                                <a href="{{ route('ie-requests.show', $attentionRequest->id) }}"
                                                    class="text-sm font-semibold text-blue-700 hover:underline">
                                                    {{ $attentionRequest->request_number }}
                                                </a>
                                                <p class="text-xs text-gray-500 mt-0.5 truncate">
                                                    {{ $attentionRequest->department }}
                                                </p>
                                            </div>

                                            @if ($attentionCard['showTarget'])
                                                <span class="text-xs text-gray-500 whitespace-nowrap">
                                                    {{ $attentionRequest->target_date?->format('Y-m-d') ?? '-' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <h3 class="text-base font-bold text-gray-800 mb-3">Request by Main Status</h3>
                <div class="h-56">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <h3 class="text-base font-bold text-gray-800 mb-3">Request by Department</h3>
                <div class="h-56">
                    <canvas id="departmentChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <h3 class="text-base font-bold text-gray-800 mb-3">Request by Priority</h3>
                <div class="h-56">
                    <canvas id="priorityChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <h3 class="text-base font-bold text-gray-800 mb-3">Request by Month</h3>
                <div class="h-56">
                    <canvas id="monthChart"></canvas>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6 mb-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800">Request Terbaru</h3>
                <a href="{{ route('ie-requests.index') }}" class="text-sm text-blue-600 hover:underline">
                    Lihat Semua
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-gray-500">
                            <th class="py-3 px-3">No Request</th>
                            <th class="py-3 px-3">Requester</th>
                            <th class="py-3 px-3">Department</th>
                            <th class="py-3 px-3">Line / Area</th>
                            <th class="py-3 px-3">Priority</th>
                            <th class="py-3 px-3">Current Status</th>
                            <th class="py-3 px-3">Target Date</th>
                            <th class="py-3 px-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($latestRequests as $ieRequest)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-3 font-semibold text-gray-800">
                                    {{ $ieRequest->request_number }}
                                </td>
                                <td class="py-3 px-3 text-gray-600">
                                    {{ $ieRequest->requester_name }}
                                </td>
                                <td class="py-3 px-3 text-gray-600">
                                    {{ $ieRequest->department }}
                                </td>
                                <td class="py-3 px-3 text-gray-600">
                                    {{ $ieRequest->line_area }}
                                </td>
                                <td class="py-3 px-3">
                                    <x-priority-badge :priority="$ieRequest->priority" />
                                </td>
                                <td class="py-3 px-3">
                                    <x-status-badge :status="$ieRequest->status" />
                                </td>
                                <td class="py-3 px-3 text-gray-600">
                                    {{ $ieRequest->target_date ?? '-' }}
                                </td>
                                <td class="py-3 px-3">
                                    <a href="{{ route('ie-requests.show', $ieRequest->id) }}"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-medium">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-6 px-3 text-center text-gray-400" colspan="8">
                                    Belum ada data request.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800">Pipeline Monitoring</h3>
                <p class="text-sm text-gray-500">10 request aktif terbaru</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-gray-500">
                            <th class="py-3 px-3">No Request</th>
                            <th class="py-3 px-3">Memo</th>
                            <th class="py-3 px-3">Drawing</th>
                            <th class="py-3 px-3">BOM</th>
                            <th class="py-3 px-3">PR</th>
                            <th class="py-3 px-3">Material</th>
                            <th class="py-3 px-3">Schedule</th>
                            <th class="py-3 px-3">Workshop</th>
                            <th class="py-3 px-3">Final Check</th>
                            <th class="py-3 px-3">Handover</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pipelineRequests as $ieRequest)
                            @php
                                $memoStatus = $ieRequest->memo_status ?? '-';
                                $drawingStatus = $ieRequest->drawing_status ?? '-';
                                $bomStatus = $ieRequest->bom_status ?? 'No BOM';
                                $prStatus = $ieRequest->purchaseRequest?->pr_status ?? 'No PR';
                                $materialStatus = $ieRequest->material_arrival_overall_status;
                                $scheduleStatus = $ieRequest->workshopSchedule?->schedule_status ?? 'No Schedule';
                                $workshopStatus = $ieRequest->workshopSchedule?->progress_status ?? 'No Progress';
                                $finalCheckStatus = $ieRequest->finalCheck?->check_status ?? 'No Check';
                                $handoverStatus = $ieRequest->handover?->handover_status ?? 'No Handover';
                            @endphp

                            <tr class="border-b align-top hover:bg-gray-50">
                                <td class="py-3 px-3 font-semibold text-gray-800 whitespace-nowrap">
                                    <a href="{{ route('ie-requests.show', $ieRequest->id) }}" class="text-blue-600 hover:underline">
                                        {{ $ieRequest->request_number }}
                                    </a>
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $badgeClass($memoStatus) }}">
                                        {{ $memoStatus }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $badgeClass($drawingStatus) }}">
                                        {{ $drawingStatus }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $badgeClass($bomStatus) }}">
                                        {{ $bomStatus }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $badgeClass($prStatus) }}">
                                        {{ $prStatus }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $badgeClass($materialStatus) }}">
                                        {{ $materialStatus }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $badgeClass($scheduleStatus) }}">
                                        {{ $scheduleStatus }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $badgeClass($workshopStatus) }}">
                                        {{ $workshopStatus }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $badgeClass($finalCheckStatus) }}">
                                        {{ $finalCheckStatus }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $badgeClass($handoverStatus) }}">
                                        {{ $handoverStatus }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-6 px-3 text-center text-gray-400" colspan="10">
                                    Tidak ada request aktif sesuai filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const chartColors = [
            '#2563eb',
            '#16a34a',
            '#f59e0b',
            '#dc2626',
            '#7c3aed',
            '#0891b2',
            '#ea580c',
            '#475569',
            '#db2777',
            '#65a30d',
            '#9333ea',
            '#0f766e'
        ];

        function createChart(canvasId, type, labels, totals) {
            const canvas = document.getElementById(canvasId);

            if (!canvas) {
                return;
            }

            new Chart(canvas, {
                type: type,
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Request',
                        data: totals,
                        backgroundColor: chartColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: type === 'doughnut' ? {} : {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }

        createChart('statusChart', 'bar', @json($statusLabels), @json($statusTotals));
        createChart('departmentChart', 'bar', @json($departmentLabels), @json($departmentTotals));
        createChart('priorityChart', 'doughnut', @json($priorityLabels), @json($priorityTotals));
        createChart('monthChart', 'line', @json($monthLabels), @json($monthTotals));
    </script>
</x-dashboard-layout>
