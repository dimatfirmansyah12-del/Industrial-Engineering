<x-dashboard-layout>
    <x-page-header
        title="Final Check Detail"
        subtitle="Pemeriksaan akhir pekerjaan workshop sebelum handover"
    >
        <a href="{{ route('final-checks.index') }}"
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
            $workshopSchedule = $ieRequest->workshopSchedule;
            $finalCheck = $ieRequest->finalCheck;
            $progressPercentage = min(100, max(0, (int) $workshopSchedule->progress_percentage));
            $checkStatus = $finalCheck?->check_status ?? 'No Check';
            $resultStatus = $finalCheck?->result_status ?? '-';
            $checkStatusClass = match($checkStatus) {
                'Waiting Check' => 'bg-gray-100 text-gray-700',
                'Checking' => 'bg-blue-100 text-blue-700',
                'Need Rework' => 'bg-orange-100 text-orange-700',
                'Passed' => 'bg-green-100 text-green-700',
                'Failed' => 'bg-red-100 text-red-700',
                default => 'bg-gray-100 text-gray-700',
            };
            $resultClass = match($resultStatus) {
                'OK' => 'bg-green-100 text-green-700',
                'NG' => 'bg-red-100 text-red-700',
                default => 'bg-gray-100 text-gray-700',
            };
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Detail Request</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                        <p class="font-semibold text-gray-800">{{ $ieRequest->line_area }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Request Type</p>
                        <p class="font-semibold text-gray-800">{{ $ieRequest->request_type ?? '-' }}</p>
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
                        <p class="text-sm text-gray-500">Target Date</p>
                        <p class="font-semibold text-gray-800">{{ $ieRequest->target_date ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Workshop</h3>

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
                        <p class="text-sm text-gray-500">Planned Finish</p>
                        <p class="font-semibold text-gray-800">{{ $workshopSchedule->planned_finish_date?->format('Y-m-d') ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Actual Finish</p>
                        <p class="font-semibold text-gray-800">{{ $workshopSchedule->actual_finish_date?->format('Y-m-d') ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Progress Status</p>
                        <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                            {{ $workshopSchedule->progress_status }}
                        </span>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Progress Percentage</p>
                        <p class="font-semibold text-gray-800">{{ $progressPercentage }}%</p>
                    </div>

                    <div class="md:col-span-2">
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-green-600 h-3 rounded-full" style="width: {{ $progressPercentage }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (!$finalCheck)
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Buat Final Check</h3>

                <form action="{{ route('final-checks.store', $ieRequest->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <x-form-input
                            label="Check Date"
                            name="check_date"
                            type="date"
                        />

                        <x-form-input
                            label="Checked By"
                            name="checked_by"
                            value="{{ auth()->user()->name }}"
                            placeholder="Nama pemeriksa"
                        />

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Evidence File</label>
                            <input type="file" name="evidence_file"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="mt-4">
                        <x-form-textarea
                            label="Final Note"
                            name="final_note"
                            rows="3"
                            placeholder="Catatan final check"
                        />
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium">
                            Buat Final Check
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="bg-white rounded-xl shadow p-6 mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Data Final Check</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-sm text-gray-500">Check Status</p>
                        <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-medium {{ $checkStatusClass }}">
                            {{ $finalCheck->check_status }}
                        </span>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Result Status</p>
                        <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-medium {{ $resultClass }}">
                            {{ $resultStatus }}
                        </span>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Check Date</p>
                        <p class="font-semibold text-gray-800">{{ $finalCheck->check_date?->format('Y-m-d') ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Checked By</p>
                        <p class="font-semibold text-gray-800">{{ $finalCheck->checked_by ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Evidence File</p>
                        @if ($finalCheck->evidence_file)
                            <a href="{{ asset('storage/' . $finalCheck->evidence_file) }}" target="_blank"
                                class="font-semibold text-blue-600 hover:underline">
                                Lihat Evidence
                            </a>
                        @else
                            <p class="font-semibold text-gray-800">-</p>
                        @endif
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Final Note</p>
                        <p class="font-semibold text-gray-800">{{ $finalCheck->final_note ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Problem Note</p>
                        <p class="font-semibold text-gray-800">{{ $finalCheck->problem_note ?? '-' }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-500">Correction Note</p>
                        <p class="font-semibold text-gray-800">{{ $finalCheck->correction_note ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @if ($finalCheck->check_status === 'Waiting Check')
                    <div class="bg-white rounded-xl shadow p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Start Checking</h3>

                        <form action="{{ route('final-checks.checking', $finalCheck->id) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium">
                                Start Checking
                            </button>
                        </form>
                    </div>
                @endif

                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Passed / OK</h3>

                    <form action="{{ route('final-checks.passed', $finalCheck->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <x-form-textarea
                            label="Final Note"
                            name="final_note"
                            rows="3"
                            value="{{ $finalCheck->final_note }}"
                            placeholder="Catatan hasil OK"
                        />

                        <div class="mt-4">
                            <x-form-textarea
                                label="Correction Note"
                                name="correction_note"
                                rows="2"
                                value="{{ $finalCheck->correction_note }}"
                                placeholder="Catatan koreksi jika ada"
                            />
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Evidence File</label>
                            <input type="file" name="evidence_file"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg font-medium">
                                Passed
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Need Rework</h3>

                    <form action="{{ route('final-checks.need-rework', $finalCheck->id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <x-form-textarea
                            label="Problem Note"
                            name="problem_note"
                            rows="3"
                            value="{{ $finalCheck->problem_note }}"
                            placeholder="Wajib isi problem yang harus dirework"
                        />

                        <div class="mt-4">
                            <x-form-textarea
                                label="Correction Note"
                                name="correction_note"
                                rows="2"
                                value="{{ $finalCheck->correction_note }}"
                                placeholder="Arahan koreksi untuk workshop"
                            />
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit"
                                class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2 rounded-lg font-medium">
                                Need Rework
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Failed</h3>

                    <form action="{{ route('final-checks.failed', $finalCheck->id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <x-form-textarea
                            label="Problem Note"
                            name="problem_note"
                            rows="3"
                            value="{{ $finalCheck->problem_note }}"
                            placeholder="Wajib isi alasan failed"
                        />

                        <div class="mt-6 flex justify-end">
                            <button type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg font-medium">
                                Failed
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</x-dashboard-layout>
