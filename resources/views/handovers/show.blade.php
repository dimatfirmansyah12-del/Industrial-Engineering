<x-dashboard-layout>
    <x-page-header
        title="Handover Detail"
        subtitle="Serah terima hasil pekerjaan Industrial Engineering ke customer"
    >
        <a href="{{ route('handovers.index') }}"
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
            $finalCheck = $ieRequest->finalCheck;
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
                        <p class="text-sm text-gray-500">Current Request Status</p>
                        <div class="mt-1">
                            <x-status-badge :status="$ieRequest->status" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Final Check</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500">Check Status</p>
                        <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                            {{ $finalCheck->check_status }}
                        </span>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Result Status</p>
                        <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                            {{ $finalCheck->result_status }}
                        </span>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Checked By</p>
                        <p class="font-semibold text-gray-800">{{ $finalCheck->checked_by ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Check Date</p>
                        <p class="font-semibold text-gray-800">{{ $finalCheck->check_date?->format('Y-m-d') ?? '-' }}</p>
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
                </div>
            </div>
        </div>

        @if (!$handover)
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Buat Handover</h3>

                <form action="{{ route('handovers.store', $ieRequest->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <x-form-input
                            label="Handover Date"
                            name="handover_date"
                            type="date"
                        />

                        <x-form-input
                            label="Handed Over By"
                            name="handed_over_by"
                            value="{{ auth()->user()->name }}"
                            placeholder="Nama admin IE"
                        />

                        <x-form-input
                            label="Received By"
                            name="received_by"
                            placeholder="Nama penerima"
                        />

                        <x-form-input
                            label="Receiver Department"
                            name="receiver_department"
                            placeholder="Department penerima"
                        />

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Evidence File</label>
                            <input type="file" name="evidence_file"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="mt-4">
                        <x-form-textarea
                            label="Handover Note"
                            name="handover_note"
                            rows="3"
                            placeholder="Catatan serah terima"
                        />
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium">
                            Buat Handover
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="bg-white rounded-xl shadow p-6 mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Data Handover</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-sm text-gray-500">Handover Number</p>
                        <p class="font-semibold text-gray-800">{{ $handover->handover_number }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Handover Date</p>
                        <p class="font-semibold text-gray-800">{{ $handover->handover_date?->format('Y-m-d') ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Handover Status</p>
                        <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-medium {{ $handoverStatusClass }}">
                            {{ $handover->handover_status }}
                        </span>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Handed Over By</p>
                        <p class="font-semibold text-gray-800">{{ $handover->handed_over_by ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Received By</p>
                        <p class="font-semibold text-gray-800">{{ $handover->received_by ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Receiver Department</p>
                        <p class="font-semibold text-gray-800">{{ $handover->receiver_department ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Handover Note</p>
                        <p class="font-semibold text-gray-800">{{ $handover->handover_note ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Receiver Note</p>
                        <p class="font-semibold text-gray-800">{{ $handover->receiver_note ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Evidence File</p>
                        @if ($handover->evidence_file)
                            <a href="{{ asset('storage/' . $handover->evidence_file) }}" target="_blank"
                                class="font-semibold text-blue-600 hover:underline">
                                Lihat Evidence
                            </a>
                        @else
                            <p class="font-semibold text-gray-800">-</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @if ($handover->handover_status === 'Waiting Handover')
                    <div class="bg-white rounded-xl shadow p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Start Handover</h3>

                        <form action="{{ route('handovers.process', $handover->id) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium">
                                Start Handover
                            </button>
                        </form>
                    </div>
                @endif

                @if (in_array($handover->handover_status, ['Waiting Handover', 'Handover Process', 'Rejected']))
                    <div class="bg-white rounded-xl shadow p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Mark as Received</h3>

                        <form action="{{ route('handovers.received', $handover->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-form-input
                                    label="Received By"
                                    name="received_by"
                                    value="{{ $handover->received_by }}"
                                    placeholder="Nama penerima"
                                />

                                <x-form-input
                                    label="Receiver Department"
                                    name="receiver_department"
                                    value="{{ $handover->receiver_department }}"
                                    placeholder="Department penerima"
                                />
                            </div>

                            <div class="mt-4">
                                <x-form-textarea
                                    label="Receiver Note"
                                    name="receiver_note"
                                    rows="3"
                                    value="{{ $handover->receiver_note }}"
                                    placeholder="Catatan penerima"
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
                                    Received
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                @if (in_array($handover->handover_status, ['Waiting Handover', 'Handover Process']))
                    <div class="bg-white rounded-xl shadow p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Reject Handover</h3>

                        <form action="{{ route('handovers.reject', $handover->id) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <x-form-textarea
                                label="Receiver Note"
                                name="receiver_note"
                                rows="3"
                                value="{{ $handover->receiver_note }}"
                                placeholder="Wajib isi alasan reject"
                            />

                            <div class="mt-6 flex justify-end">
                                <button type="submit"
                                    class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg font-medium">
                                    Reject
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-dashboard-layout>
