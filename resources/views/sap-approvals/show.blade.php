<x-dashboard-layout>
    @php
        $sapApproval = $ieRequest->sapApproval;
        $userRole = auth()->user()?->role;
        $approvalStatus = $sapApproval?->approval_status ?? \App\Models\SapApproval::WAITING_SAP_INPUT;
        $approvalClass = match($approvalStatus) {
            \App\Models\SapApproval::WAITING_SAP_INPUT => 'bg-gray-100 text-gray-700',
            \App\Models\SapApproval::WAITING_SECTION_HEAD, \App\Models\SapApproval::WAITING_DIVISION_HEAD, \App\Models\SapApproval::WAITING_DIRECTOR => 'bg-yellow-100 text-yellow-700',
            \App\Models\SapApproval::SENT_TO_PURCHASING => 'bg-blue-100 text-blue-700',
            \App\Models\SapApproval::REJECTED => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
        $canInputSap = $userRole === 'admin'
            && ! $ieRequest->purchaseRequest
            && (! $sapApproval || in_array($approvalStatus, [\App\Models\SapApproval::WAITING_SAP_INPUT, \App\Models\SapApproval::REJECTED], true));
        $canSectionApprove = $sapApproval
            && in_array($userRole, ['admin', 'section_head'], true)
            && $approvalStatus === \App\Models\SapApproval::WAITING_SECTION_HEAD;
        $canDivisionApprove = $sapApproval
            && in_array($userRole, ['admin', 'division_head'], true)
            && $approvalStatus === \App\Models\SapApproval::WAITING_DIVISION_HEAD;
        $canDirectorApprove = $sapApproval
            && in_array($userRole, ['admin', 'director'], true)
            && $approvalStatus === \App\Models\SapApproval::WAITING_DIRECTOR;
    @endphp

    <x-page-header
        title="SAP / PR Approval Detail"
        subtitle="Input Description, No. PR, dan Nilai Pembelian"
    >
        <a href="{{ route('sap-approvals.index') }}"
            class="rounded-lg bg-gray-600 px-5 py-2 font-medium text-white hover:bg-gray-700">
            Kembali
        </a>
    </x-page-header>

    <div class="space-y-6 p-8">
        @if (session('success'))
            <div class="rounded-lg bg-green-100 px-4 py-3 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-lg bg-red-100 px-4 py-3 text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-lg bg-red-100 px-4 py-3 text-red-700">
                <p class="font-semibold">Ada data yang perlu diperbaiki:</p>
                <ul class="mt-2 list-inside list-disc text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="grid grid-cols-1 gap-6 lg:grid-cols-4">
            <div class="rounded-xl bg-white p-6 shadow lg:col-span-3">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
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
                        <p class="text-sm text-gray-500">Approval Status</p>
                        <span class="mt-1 inline-block rounded-full px-3 py-1 text-xs font-medium {{ $approvalClass }}">
                            {{ $approvalStatus }}
                        </span>
                    </div>
                </div>
            </div>

            <x-summary-card
                title="Nilai Pembelian"
                value="{{ $sapApproval?->purchase_value !== null ? 'Rp ' . number_format((float) $sapApproval->purchase_value, 2, ',', '.') : '-' }}"
                subtitle="Dari input SAP"
                color="green"
            />
        </section>

        <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="rounded-xl bg-white p-6 shadow xl:col-span-2">
                <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">PR SAP Input</h3>
                        <p class="text-sm text-gray-500">Admin IE mengisi data PR dari SAP sebelum approval atasan.</p>
                    </div>

                    @if ($sapApproval?->sap_file)
                        <a href="{{ asset('storage/' . $sapApproval->sap_file) }}" target="_blank"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                            Lihat File SAP
                        </a>
                    @endif
                </div>

                @if ($canInputSap)
                    <form action="{{ route('sap-approvals.sap-input', $ieRequest->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <x-form-input
                                label="No. PR"
                                name="sap_number"
                                value="{{ old('sap_number', $sapApproval?->sap_number) }}"
                                placeholder="Nomor PR"
                            />

                            <x-form-input
                                label="Nilai Pembelian"
                                name="purchase_value"
                                type="number"
                                value="{{ old('purchase_value', $sapApproval?->purchase_value !== null ? number_format((float) $sapApproval->purchase_value, 2, '.', '') : '') }}"
                                placeholder="0"
                                step="0.01"
                                min="0"
                            />

                            <x-form-textarea
                                label="Description"
                                name="sap_description"
                                rows="3"
                                value="{{ old('sap_description', $sapApproval?->sap_description) }}"
                                placeholder="Description"
                                class="md:col-span-2"
                            />
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit"
                                class="rounded-lg bg-blue-600 px-5 py-2 font-medium text-white hover:bg-blue-700">
                                Submit ke Atasan IE
                            </button>
                        </div>
                    </form>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-[820px] w-full text-sm">
                            <thead>
                                <tr class="border-b text-left text-gray-500">
                                    <th class="px-3 py-3">No</th>
                                    <th class="px-3 py-3">Description</th>
                                    <th class="px-3 py-3">No. PR</th>
                                    <th class="px-3 py-3">Nilai Pembelian</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b">
                                    <td class="px-3 py-3">1</td>
                                    <td class="px-3 py-3">{{ $sapApproval?->sap_description ?? '-' }}</td>
                                    <td class="px-3 py-3 font-semibold text-gray-800">{{ $sapApproval?->sap_number ?? '-' }}</td>
                                    <td class="px-3 py-3 font-semibold text-gray-800">
                                        {{ $sapApproval?->purchase_value !== null ? 'Rp ' . number_format((float) $sapApproval->purchase_value, 2, ',', '.') : '-' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-4 border-t border-gray-100 pt-4 md:grid-cols-2">
                        <div>
                            <p class="text-sm text-gray-500">Input By</p>
                            <p class="font-semibold text-gray-800">{{ $sapApproval?->sap_input_by ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Input At</p>
                            <p class="font-semibold text-gray-800">{{ $sapApproval?->sap_input_at?->format('Y-m-d H:i') ?? '-' }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="rounded-xl bg-white p-6 shadow">
                <h3 class="text-lg font-bold text-gray-800">Approval Step</h3>
                <div class="mt-5 space-y-4">
                    @foreach ([
                        ['label' => 'No. PR SAP', 'done' => (bool) $sapApproval?->sap_input_at],
                        ['label' => 'Atasan IE', 'done' => $sapApproval?->section_head_status === 'Approved'],
                        ['label' => 'Division Head', 'done' => $sapApproval?->division_head_status === 'Approved'],
                        ['label' => 'Director', 'done' => $sapApproval?->director_status === 'Approved'],
                        ['label' => 'Auto Sent Purchasing', 'done' => (bool) $sapApproval?->sent_to_purchasing_at],
                    ] as $step)
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold {{ $step['done'] ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500' }}">
                                {{ $step['done'] ? 'OK' : $loop->iteration }}
                            </span>
                            <span class="{{ $step['done'] ? 'font-semibold text-gray-800' : 'text-gray-500' }}">
                                {{ $step['label'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="rounded-xl bg-white p-6 shadow">
                <h3 class="text-lg font-bold text-gray-800">Atasan IE Approval</h3>

                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        <p class="font-semibold text-gray-800">{{ $sapApproval?->section_head_status ?? 'Waiting' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Approved / Rejected By</p>
                        <p class="font-semibold text-gray-800">{{ $sapApproval?->section_head_by ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Date</p>
                        <p class="font-semibold text-gray-800">{{ $sapApproval?->section_head_at?->format('Y-m-d H:i') ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Note / Reject Reason</p>
                        <p class="font-semibold text-gray-800">{{ $sapApproval?->section_head_rejected_reason ?? $sapApproval?->section_head_note ?? '-' }}</p>
                    </div>
                </div>

                @if ($canSectionApprove)
                    <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <form action="{{ route('sap-approvals.section-approve', $sapApproval->id) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <textarea name="section_head_note" rows="3"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                placeholder="Catatan approval">{{ old('section_head_note') }}</textarea>

                            <button type="submit"
                                class="mt-2 w-full rounded-lg bg-green-600 px-5 py-2 font-medium text-white hover:bg-green-700">
                                Approve Atasan IE
                            </button>
                        </form>

                        <form action="{{ route('sap-approvals.section-reject', $sapApproval->id) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <textarea name="section_head_rejected_reason" rows="3"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                placeholder="Alasan reject">{{ old('section_head_rejected_reason') }}</textarea>

                            <button type="submit"
                                class="mt-2 w-full rounded-lg bg-red-600 px-5 py-2 font-medium text-white hover:bg-red-700">
                                Reject Atasan IE
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <div class="rounded-xl bg-white p-6 shadow">
                <h3 class="text-lg font-bold text-gray-800">Division Head Approval</h3>

                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        <p class="font-semibold text-gray-800">{{ $sapApproval?->division_head_status ?? 'Waiting' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Approved / Rejected By</p>
                        <p class="font-semibold text-gray-800">{{ $sapApproval?->division_head_by ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Date</p>
                        <p class="font-semibold text-gray-800">{{ $sapApproval?->division_head_at?->format('Y-m-d H:i') ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Note / Reject Reason</p>
                        <p class="font-semibold text-gray-800">{{ $sapApproval?->division_head_rejected_reason ?? $sapApproval?->division_head_note ?? '-' }}</p>
                    </div>
                </div>

                @if ($canDivisionApprove)
                    <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <form action="{{ route('sap-approvals.division-approve', $sapApproval->id) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <textarea name="division_head_note" rows="3"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                placeholder="Catatan approval">{{ old('division_head_note') }}</textarea>

                            <button type="submit"
                                class="mt-2 w-full rounded-lg bg-green-600 px-5 py-2 font-medium text-white hover:bg-green-700">
                                Approve Division Head
                            </button>
                        </form>

                        <form action="{{ route('sap-approvals.division-reject', $sapApproval->id) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <textarea name="division_head_rejected_reason" rows="3"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                placeholder="Alasan reject">{{ old('division_head_rejected_reason') }}</textarea>

                            <button type="submit"
                                class="mt-2 w-full rounded-lg bg-red-600 px-5 py-2 font-medium text-white hover:bg-red-700">
                                Reject Division Head
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <div class="rounded-xl bg-white p-6 shadow">
                <h3 class="text-lg font-bold text-gray-800">Director Approval</h3>

                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        <p class="font-semibold text-gray-800">{{ $sapApproval?->director_status ?? 'Waiting' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Approved / Rejected By</p>
                        <p class="font-semibold text-gray-800">{{ $sapApproval?->director_by ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Date</p>
                        <p class="font-semibold text-gray-800">{{ $sapApproval?->director_at?->format('Y-m-d H:i') ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Note / Reject Reason</p>
                        <p class="font-semibold text-gray-800">{{ $sapApproval?->director_rejected_reason ?? $sapApproval?->director_note ?? '-' }}</p>
                    </div>
                </div>

                @if ($canDirectorApprove)
                    <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <form action="{{ route('sap-approvals.director-approve', $sapApproval->id) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <textarea name="director_note" rows="3"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                placeholder="Catatan approval">{{ old('director_note') }}</textarea>

                            <button type="submit"
                                class="mt-2 w-full rounded-lg bg-green-600 px-5 py-2 font-medium text-white hover:bg-green-700">
                                Approve Director
                            </button>
                        </form>

                        <form action="{{ route('sap-approvals.director-reject', $sapApproval->id) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <textarea name="director_rejected_reason" rows="3"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                placeholder="Alasan reject">{{ old('director_rejected_reason') }}</textarea>

                            <button type="submit"
                                class="mt-2 w-full rounded-lg bg-red-600 px-5 py-2 font-medium text-white hover:bg-red-700">
                                Reject Director
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </section>

        @if ($approvalStatus === \App\Models\SapApproval::SENT_TO_PURCHASING)
            <section class="rounded-xl bg-white p-6 shadow">
                <h3 class="text-lg font-bold text-gray-800">Purchasing</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Request otomatis masuk ke Budget / PR setelah Direktur approve.
                </p>
                <p class="mt-2 text-sm font-semibold text-blue-700">
                    Dikirim oleh {{ $sapApproval?->sent_to_purchasing_by ?? '-' }}
                    pada {{ $sapApproval?->sent_to_purchasing_at?->format('Y-m-d H:i') ?? '-' }}.
                </p>
            </section>
        @endif

    </div>
</x-dashboard-layout>
