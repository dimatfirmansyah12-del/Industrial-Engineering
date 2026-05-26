<x-dashboard-layout>
    <x-page-header
        title="Workshop Progress Detail"
        subtitle="Update progress pekerjaan workshop"
    >
        <a href="{{ route('workshop-progress.index') }}"
            class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-2 rounded-lg font-medium">
            Kembali
        </a>
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

        @if ($errors->any())
            <div class="mb-4 bg-red-100 text-red-700 px-4 py-3 rounded-lg">
                <p class="font-semibold">Ada data yang perlu diperbaiki:</p>
                <ul class="list-disc list-inside text-sm mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $request = $workshopSchedule->ieRequest;
            $requestQty = max(1, (int) ($request?->request_qty ?? 1));
            $usesAutoQtyProgress = $requestQty > 1;
            $completedQty = min($requestQty, max(0, (int) ($workshopSchedule->completed_qty ?? 0)));
            $progressPercentage = min(100, max(0, (int) $workshopSchedule->progress_percentage));
            $progressStatusClass = match($workshopSchedule->progress_status) {
                'On Progress' => 'bg-blue-100 text-blue-700',
                'Hold' => 'bg-yellow-100 text-yellow-700',
                'Rework' => 'bg-orange-100 text-orange-700',
                'Done' => 'bg-green-100 text-green-700',
                default => 'bg-gray-100 text-gray-700',
            };
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Detail Request</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500">No Request</p>
                        <p class="font-semibold text-gray-800">{{ $request?->request_number ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Requester</p>
                        <p class="font-semibold text-gray-800">{{ $request?->requester_name ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Department</p>
                        <p class="font-semibold text-gray-800">{{ $request?->department ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Line / Area</p>
                        <p class="font-semibold text-gray-800">{{ $request?->line_area ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Request Type</p>
                        <p class="font-semibold text-gray-800">{{ $request?->request_type ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Qty Request</p>
                        <p class="font-semibold text-gray-800">{{ $request?->request_qty ?? 1 }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Priority</p>
                        <div class="mt-1">
                            <x-priority-badge :priority="$request?->priority" />
                        </div>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Target Date</p>
                        <p class="font-semibold text-gray-800">{{ $request?->target_date ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Detail Schedule</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500">Schedule Number</p>
                        <p class="font-semibold text-gray-800">{{ $workshopSchedule->schedule_number }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">PIC Workshop</p>
                        <p class="font-semibold text-gray-800">{{ $workshopSchedule->pic_workshop ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Planned Start</p>
                        <p class="font-semibold text-gray-800">{{ $workshopSchedule->planned_start_date?->format('Y-m-d') ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Planned Finish</p>
                        <p class="font-semibold text-gray-800">{{ $workshopSchedule->planned_finish_date?->format('Y-m-d') ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Schedule Status</p>
                        <p class="font-semibold text-gray-800">{{ $workshopSchedule->schedule_status }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6 mb-8">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Current Progress</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Progress Status</p>
                    <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-medium {{ $progressStatusClass }}">
                        {{ $workshopSchedule->progress_status }}
                    </span>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Started At</p>
                    <p class="font-semibold text-gray-800">{{ $workshopSchedule->started_at ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Finished At</p>
                    <p class="font-semibold text-gray-800">{{ $workshopSchedule->finished_at ?? '-' }}</p>
                </div>

                <div class="md:col-span-3">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm text-gray-500">
                            Progress Percentage
                            @if ($usesAutoQtyProgress)
                                <span class="ml-2 text-xs text-gray-400">Qty {{ $completedQty }}/{{ $requestQty }}</span>
                            @endif
                        </p>
                        <p class="text-sm font-semibold text-gray-800">{{ $progressPercentage }}%</p>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-blue-600 h-3 rounded-full" style="width: {{ $progressPercentage }}%"></div>
                    </div>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Progress Note</p>
                    <p class="font-semibold text-gray-800">{{ $workshopSchedule->progress_note ?? '-' }}</p>
                </div>

                <div class="md:col-span-2">
                    <p class="text-sm text-gray-500">Problem Note</p>
                    <p class="font-semibold text-gray-800">{{ $workshopSchedule->problem_note ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6 mb-8">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Update Progress</h3>

            <form action="{{ route('workshop-progress.update', $workshopSchedule->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <x-form-select
                        label="Progress Status"
                        name="progress_status"
                        value="{{ $workshopSchedule->progress_status }}"
                        :options="[
                            'Not Started' => 'Not Started',
                            'On Progress' => 'On Progress',
                            'Hold' => 'Hold',
                            'Rework' => 'Rework',
                            'Done' => 'Done',
                        ]"
                    />

                    @if ($usesAutoQtyProgress)
                        <x-form-input
                            label="Qty Selesai"
                            name="completed_qty"
                            type="number"
                            min="0"
                            max="{{ $requestQty }}"
                            value="{{ old('completed_qty', $completedQty) }}"
                        />

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Progress Percentage</label>
                            <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm font-semibold text-gray-700">
                                {{ $progressPercentage }}%
                            </div>
                        </div>
                    @else
                        <x-form-input
                            label="Progress Percentage"
                            name="progress_percentage"
                            type="number"
                            min="0"
                            max="100"
                            value="{{ old('progress_percentage', $workshopSchedule->progress_percentage) }}"
                        />
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Photo/File Progress</label>
                        <input type="file" name="photo_file"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <x-form-textarea
                        label="Progress Note"
                        name="progress_note"
                        rows="3"
                        value="{{ $workshopSchedule->progress_note }}"
                        placeholder="Catatan progress"
                    />

                    <x-form-textarea
                        label="Problem Note"
                        name="problem_note"
                        rows="3"
                        value="{{ $workshopSchedule->problem_note }}"
                        placeholder="Wajib diisi jika Hold atau Rework"
                    />
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium">
                        Update Progress
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow p-6 mb-8">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Tambah Catatan Progress</h3>

            <form action="{{ route('workshop-progress.logs.store', $workshopSchedule->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <x-form-textarea
                            label="Note"
                            name="note"
                            rows="3"
                            placeholder="Catatan tambahan progress"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Photo/File</label>
                        <input type="file" name="photo_file"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div class="mt-4">
                    <x-form-textarea
                        label="Problem"
                        name="problem_note"
                        rows="2"
                        placeholder="Catatan problem jika ada"
                    />
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit"
                        class="bg-slate-700 hover:bg-slate-800 text-white px-5 py-2 rounded-lg font-medium">
                        Tambah Log
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Progress Log</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-gray-500">
                            <th class="py-3 px-3">Waktu</th>
                            <th class="py-3 px-3">User</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3">Percentage</th>
                            <th class="py-3 px-3">Qty Selesai</th>
                            <th class="py-3 px-3">Note</th>
                            <th class="py-3 px-3">Problem</th>
                            <th class="py-3 px-3">Photo/File</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($workshopSchedule->progressLogs as $log)
                            <tr class="border-b align-top hover:bg-gray-50">
                                <td class="py-3 px-3">
                                    {{ $log->created_at }}
                                </td>
                                <td class="py-3 px-3">
                                    {{ $log->user?->name ?? '-' }}
                                </td>
                                <td class="py-3 px-3">
                                    {{ $log->progress_status }}
                                </td>
                                <td class="py-3 px-3">
                                    {{ $log->progress_percentage }}%
                                </td>
                                <td class="py-3 px-3">
                                    @if ($log->completed_qty !== null)
                                        {{ min($requestQty, max(0, (int) $log->completed_qty)) }}/{{ $requestQty }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3 px-3">
                                    {{ $log->note ?? '-' }}
                                </td>
                                <td class="py-3 px-3">
                                    {{ $log->problem_note ?? '-' }}
                                </td>
                                <td class="py-3 px-3">
                                    @if ($log->photo_file)
                                        <a href="{{ asset('storage/' . $log->photo_file) }}" target="_blank"
                                            class="text-blue-600 hover:underline">
                                            Lihat File
                                        </a>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-6 text-center text-gray-400">
                                    Belum ada log progress.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-dashboard-layout>
