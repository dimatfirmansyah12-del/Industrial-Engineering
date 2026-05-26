<x-dashboard-layout>
    <x-page-header
        title="Workshop Schedule Detail"
        subtitle="Detail penjadwalan pekerjaan workshop"
    >
        <a href="{{ route('workshop-schedules.index') }}"
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
            $schedule = $ieRequest->workshopSchedule;
            $scheduleStatusClass = match($schedule?->schedule_status) {
                'Scheduled' => 'bg-blue-100 text-blue-700',
                'Rescheduled' => 'bg-yellow-100 text-yellow-700',
                'Cancelled' => 'bg-red-100 text-red-700',
                'Ready to Work' => 'bg-green-100 text-green-700',
                'In Progress' => 'bg-orange-100 text-orange-700',
                'Finished' => 'bg-slate-200 text-slate-800',
                default => 'bg-gray-100 text-gray-700',
            };

        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
            <div class="lg:col-span-3 bg-white rounded-xl shadow p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-sm text-gray-500">No Request</p>
                        <p class="font-semibold text-gray-800">{{ $ieRequest->request_number }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Requester</p>
                        <p class="font-semibold text-gray-800">{{ $ieRequest->requester_name }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Department</p>
                        <p class="font-semibold text-gray-800">{{ $ieRequest->department }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Line / Area</p>
                        <p class="font-semibold text-gray-800">{{ $ieRequest->line_area ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Request Type</p>
                        <p class="font-semibold text-gray-800">{{ $ieRequest->request_type }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Qty Request</p>
                        <p class="font-semibold text-gray-800">{{ $ieRequest->request_qty ?? 1 }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Priority</p>
                        <div class="mt-1">
                            <x-priority-badge :priority="$ieRequest->priority" />
                        </div>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Status Request</p>
                        <div class="mt-1">
                            <x-status-badge :status="$ieRequest->status" />
                        </div>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Drawing Status</p>
                        <p class="font-semibold text-gray-800">{{ $ieRequest->drawing_status }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">PR Number</p>
                        <p class="font-semibold text-gray-800">{{ $ieRequest->purchaseRequest?->pr_number ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6 border-l-4 border-green-500">
                <p class="text-sm text-gray-500">Material Summary</p>
                <h3 class="text-3xl font-bold text-gray-800 mt-2">
                    {{ $completeMaterial }}/{{ $totalMaterial }}
                </h3>
                <p class="text-xs text-gray-400 mt-2">Material complete</p>
            </div>
        </div>

        @if (!$schedule)
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Buat Schedule</h3>

                <form action="{{ route('workshop-schedules.store', $ieRequest->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <h4 class="mb-4 font-bold text-gray-800">Data Schedule</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-form-input
                            label="Planned Start Date"
                            name="planned_start_date"
                            type="date"
                        />

                        <x-form-input
                            label="Planned Finish Date"
                            name="planned_finish_date"
                            type="date"
                        />

                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">PIC Workshop</label>
                            <select name="pic_workshop"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Pilih PIC workshop</option>
                                @foreach ($workshopPeople as $person)
                                    <option value="{{ $person->name }}" {{ old('pic_workshop', $ieRequest->pic_workshop) === $person->name ? 'selected' : '' }}>
                                        {{ $person->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-gray-500">
                        Durasi otomatis dihitung dari Planned Start Date sampai Planned Finish Date.
                    </p>

                    <div class="mt-4">
                        <x-form-textarea
                            label="Schedule Note"
                            name="schedule_note"
                            rows="3"
                            placeholder="Catatan schedule"
                        />
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium">
                            Buat Schedule
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="bg-white rounded-xl shadow p-6 mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Schedule</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-sm text-gray-500">Schedule Number</p>
                        <p class="font-semibold text-gray-800">{{ $schedule->schedule_number }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Planned Start Date</p>
                        <p class="font-semibold text-gray-800">{{ $schedule->planned_start_date?->format('Y-m-d') ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Planned Finish Date</p>
                        <p class="font-semibold text-gray-800">{{ $schedule->planned_finish_date?->format('Y-m-d') ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Actual Start Date</p>
                        <p class="font-semibold text-gray-800">{{ $schedule->actual_start_date?->format('Y-m-d') ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Actual Finish Date</p>
                        <p class="font-semibold text-gray-800">{{ $schedule->actual_finish_date?->format('Y-m-d') ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">PIC Workshop</p>
                        <p class="font-semibold text-gray-800">{{ $schedule->pic_workshop ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Estimated Duration</p>
                        <p class="font-semibold text-gray-800">
                            {{ $schedule->estimated_duration ? $schedule->estimated_duration . ' hari' : '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Schedule Status</p>
                        <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-medium {{ $scheduleStatusClass }}">
                            {{ $schedule->schedule_status }}
                        </span>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Schedule Note</p>
                        <p class="font-semibold text-gray-800">{{ $schedule->schedule_note ?? '-' }}</p>
                    </div>

                    <div class="md:col-span-3">
                        <p class="text-sm text-gray-500">Reschedule Reason</p>
                        <p class="font-semibold text-gray-800">{{ $schedule->reschedule_reason ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-[1.35fr_.65fr] gap-6">
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Update Schedule</h3>

                    <form action="{{ route('workshop-schedules.update', $schedule->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <h4 class="mb-4 font-bold text-gray-800">Data Schedule</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-form-input
                                label="Planned Start Date"
                                name="planned_start_date"
                                type="date"
                                value="{{ $schedule->planned_start_date?->format('Y-m-d') }}"
                            />

                            <x-form-input
                                label="Planned Finish Date"
                                name="planned_finish_date"
                                type="date"
                                value="{{ $schedule->planned_finish_date?->format('Y-m-d') }}"
                            />

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">PIC Workshop</label>
                                <select name="pic_workshop"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Pilih PIC workshop</option>
                                    @foreach ($workshopPeople as $person)
                                        <option value="{{ $person->name }}" {{ old('pic_workshop', $schedule->pic_workshop) === $person->name ? 'selected' : '' }}>
                                            {{ $person->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <p class="mt-3 text-xs text-gray-500">
                            Durasi otomatis dihitung ulang saat Planned Start Date atau Planned Finish Date diubah.
                        </p>

                        <div class="mt-4">
                            <x-form-textarea
                                label="Schedule Note"
                                name="schedule_note"
                                rows="3"
                                value="{{ $schedule->schedule_note }}"
                            />
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit"
                                class="bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-2 rounded-lg font-medium">
                                Update Schedule
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Aksi Schedule</h3>

                    <div class="space-y-3">
                        @if (in_array($schedule->schedule_status, ['Scheduled', 'Rescheduled']))
                            <form action="{{ route('workshop-schedules.ready', $schedule->id) }}" method="POST">
                                @csrf
                                @method('PATCH')

                                <button type="submit"
                                    class="w-full rounded-lg bg-green-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-green-700">
                                    Ready to Work
                                </button>
                            </form>
                        @endif

                        <details class="rounded-lg border border-blue-100 bg-blue-50/50 p-3">
                            <summary class="cursor-pointer list-none text-sm font-bold text-blue-700">
                                Reschedule
                            </summary>

                            <form action="{{ route('workshop-schedules.reschedule', $schedule->id) }}" method="POST" class="mt-3">
                                @csrf
                                @method('PATCH')

                                <div class="grid grid-cols-1 gap-3">
                                    <x-form-input
                                        label="New Planned Start"
                                        name="planned_start_date"
                                        type="date"
                                        value="{{ $schedule->planned_start_date?->format('Y-m-d') }}"
                                    />

                                    <x-form-input
                                        label="New Planned Finish"
                                        name="planned_finish_date"
                                        type="date"
                                        value="{{ $schedule->planned_finish_date?->format('Y-m-d') }}"
                                    />
                                </div>

                                <div class="mt-3">
                                    <x-form-textarea
                                        label="Reschedule Reason"
                                        name="reschedule_reason"
                                        rows="2"
                                        placeholder="Alasan reschedule"
                                    />
                                </div>

                                <button type="submit"
                                    class="mt-3 w-full rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">
                                    Simpan Reschedule
                                </button>
                            </form>
                        </details>

                        <details class="rounded-lg border border-red-100 bg-red-50/60 p-3">
                            <summary class="cursor-pointer list-none text-sm font-bold text-red-700">
                                Cancel Schedule
                            </summary>

                            <form action="{{ route('workshop-schedules.cancel', $schedule->id) }}" method="POST" class="mt-3">
                                @csrf
                                @method('PATCH')

                                <x-form-textarea
                                    label="Alasan Cancel"
                                    name="reschedule_reason"
                                    rows="2"
                                    placeholder="Alasan cancel schedule"
                                />

                                <button type="submit"
                                    class="mt-3 w-full rounded-lg bg-red-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-red-700">
                                    Konfirmasi Cancel
                                </button>
                            </form>
                        </details>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-dashboard-layout>
