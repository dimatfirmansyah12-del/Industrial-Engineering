<x-dashboard-layout>
    <x-page-header
        title="Budget / PR Detail"
        subtitle="Detail purchasing setelah SAP / PR Approval"
    >
        <a href="{{ route('budget-pr.index') }}"
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

        <div class="bg-white rounded-xl shadow p-6 mb-8">
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
                    <p class="text-sm text-gray-500">Drawing Status</p>
                    <p class="font-semibold text-gray-800">{{ $ieRequest->drawing_status }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Drawing File</p>
                    @if ($ieRequest->drawing_file)
                        <a href="{{ asset('storage/' . $ieRequest->drawing_file) }}" target="_blank"
                            class="font-semibold text-blue-600 hover:underline">
                            Lihat Drawing
                        </a>
                    @else
                        <p class="font-semibold text-gray-400">-</p>
                    @endif
                </div>
            </div>
        </div>

        @php
            $sapApproval = $ieRequest->sapApproval;
        @endphp

        <div class="bg-white rounded-xl shadow p-6 mb-8">
            <h3 class="text-lg font-bold text-gray-800 mb-4">SAP / PR Approval</h3>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <p class="text-sm text-gray-500">No. PR SAP</p>
                    <p class="font-semibold text-gray-800">{{ $sapApproval?->sap_number ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Nilai Pembelian</p>
                    <p class="font-semibold text-gray-800">
                        {{ $sapApproval?->purchase_value !== null ? 'Rp ' . number_format((float) $sapApproval->purchase_value, 2, ',', '.') : '-' }}
                    </p>
                </div>

                <div class="md:col-span-4">
                    <p class="text-sm text-gray-500">Description</p>
                    <p class="font-semibold text-gray-800">{{ $sapApproval?->sap_description ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Atasan IE</p>
                    <p class="font-semibold text-gray-800">{{ $sapApproval?->section_head_status ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Division Head</p>
                    <p class="font-semibold text-gray-800">{{ $sapApproval?->division_head_status ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Director</p>
                    <p class="font-semibold text-gray-800">{{ $sapApproval?->director_status ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Sent to Purchasing</p>
                    <p class="font-semibold text-gray-800">{{ $sapApproval?->sent_to_purchasing_at?->format('Y-m-d H:i') ?? '-' }}</p>
                </div>
            </div>
        </div>

        @php
            $purchaseRequest = $ieRequest->purchaseRequest;
            $prStatus = $purchaseRequest?->pr_status;
            $prStatusClass = match($prStatus) {
                'Waiting Approval' => 'bg-yellow-100 text-yellow-700',
                'Approved' => 'bg-green-100 text-green-700',
                'Rejected' => 'bg-red-100 text-red-700',
                'PO Created' => 'bg-blue-100 text-blue-700',
                default => 'bg-gray-100 text-gray-700',
            };
        @endphp

        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Purchase Request</h3>

            @if (!$purchaseRequest)
                <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
                    PR belum tercatat di purchasing. No. PR dan approval tidak dibuat dari halaman ini;
                    request akan otomatis masuk ke sini setelah Direktur approve di SAP / PR Approval.
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <p class="text-sm text-gray-500">No. PR SAP</p>
                        <p class="font-semibold text-gray-800">{{ $purchaseRequest->pr_number }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">PR Date</p>
                        <p class="font-semibold text-gray-800">{{ $purchaseRequest->pr_date?->format('Y-m-d') ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Nilai Pembelian</p>
                        <p class="font-semibold text-gray-800">Rp {{ number_format($purchaseRequest->total_budget, 2, ',', '.') }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">PR Status</p>
                        <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-medium {{ $prStatusClass }}">
                            {{ $purchaseRequest->pr_status }}
                        </span>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Requested By</p>
                        <p class="font-semibold text-gray-800">{{ $purchaseRequest->requested_by ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Approved By</p>
                        <p class="font-semibold text-gray-800">{{ $purchaseRequest->approved_by ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Approved At</p>
                        <p class="font-semibold text-gray-800">{{ $purchaseRequest->approved_at ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Rejected Reason</p>
                        <p class="font-semibold text-gray-800">{{ $purchaseRequest->rejected_reason ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Note</p>
                        <p class="font-semibold text-gray-800">{{ $purchaseRequest->note ?? '-' }}</p>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-6">
                    @if ($purchaseRequest->pr_status === 'Approved')
                        <form action="{{ route('budget-pr.po-created', $purchaseRequest->id) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium">
                                Mark as PO Created
                            </button>
                        </form>
                    @elseif ($purchaseRequest->pr_status === 'Rejected')
                        <p class="text-sm text-red-700">
                            PR ditolak: {{ $purchaseRequest->rejected_reason }}
                        </p>
                    @elseif ($purchaseRequest->pr_status === 'PO Created')
                        <p class="text-sm text-blue-700 font-semibold">
                            Pengajuan sudah selesai dan PO sudah dibuat.
                        </p>
                    @else
                        <p class="text-sm text-gray-600">
                            Approval No. PR sekarang berjalan di SAP / PR Approval. Halaman ini hanya untuk proses purchasing setelah request dikirim.
                        </p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-dashboard-layout>
