<x-dashboard-layout>
    <x-page-header
        title="SAP / PR Approval"
        subtitle="Input No. PR SAP dan approval sebelum request dikirim ke purchasing"
    >
        <form method="GET" action="{{ route('sap-approvals.index') }}"
            class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari request, description, No. PR..."
                class="w-full rounded-lg border-gray-300 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:w-64">

            <select name="approval_status"
                class="w-full rounded-lg border-gray-300 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:w-56">
                <option value="">All Approval Status</option>
                @foreach ($approvalStatuses as $approvalStatus)
                    <option value="{{ $approvalStatus }}" {{ request('approval_status') == $approvalStatus ? 'selected' : '' }}>
                        {{ $approvalStatus }}
                    </option>
                @endforeach
            </select>

            <button type="submit"
                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Terapkan
            </button>

            <a href="{{ route('sap-approvals.index') }}"
                class="rounded-lg bg-gray-200 px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-300">
                Reset
            </a>
        </form>
    </x-page-header>

    <div class="p-8">
        @if (session('success'))
            <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="rounded-xl bg-white p-6 shadow">
            <div class="mb-4 flex justify-between items-center">
                <p class="text-sm text-gray-500">
                    Total data ditemukan:
                    <span class="font-semibold text-gray-800">{{ $requests->total() }}</span>
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-[1080px] w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-gray-500">
                            <th class="px-3 py-3">No</th>
                            <th class="px-3 py-3">Request Info</th>
                            <th class="px-3 py-3">Description</th>
                            <th class="px-3 py-3">No. PR</th>
                            <th class="px-3 py-3">Nilai Pembelian</th>
                            <th class="px-3 py-3">Approval Status</th>
                            <th class="px-3 py-3">Atasan IE</th>
                            <th class="px-3 py-3">Division Head</th>
                            <th class="px-3 py-3">Director</th>
                            <th class="px-3 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($requests as $request)
                            @php
                                $sapApproval = $request->sapApproval;
                                $approvalStatus = $sapApproval?->approval_status ?? \App\Models\SapApproval::WAITING_SAP_INPUT;
                                $approvalClass = match($approvalStatus) {
                                    'Waiting SAP Input' => 'bg-gray-100 text-gray-700',
                                    'Waiting PR Input' => 'bg-gray-100 text-gray-700',
                                    'Waiting Section Head Approval', 'Waiting Atasan IE Approval', 'Waiting Division Head Approval', 'Waiting Director Approval' => 'bg-yellow-100 text-yellow-700',
                                    'Sent to Purchasing' => 'bg-blue-100 text-blue-700',
                                    'Rejected' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp

                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-3 py-3">{{ $requests->firstItem() + $loop->index }}</td>
                                <td class="px-3 py-3">
                                    <p class="font-semibold text-gray-800">{{ $request->request_number }}</p>
                                    <p class="text-xs text-gray-500">{{ $request->requester_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $request->department }} - {{ $request->line_area ?? '-' }}</p>
                                    <p class="font-semibold text-gray-800">{{ $request->request_type }}</p>
                                    <p class="text-xs text-gray-500">Qty: {{ $request->request_qty ?? 1 }}</p>
                                </td>
                                <td class="px-3 py-3 max-w-xs">
                                    <p class="break-words">{{ $sapApproval?->sap_description ?? '-' }}</p>
                                </td>
                                <td class="px-3 py-3 font-semibold text-gray-800">{{ $sapApproval?->sap_number ?? '-' }}</td>
                                <td class="px-3 py-3 font-semibold text-gray-800">
                                    {{ $sapApproval?->purchase_value !== null ? 'Rp ' . number_format((float) $sapApproval->purchase_value, 2, ',', '.') : '-' }}
                                </td>
                                <td class="px-3 py-3">
                                    <span class="inline-block rounded-full px-3 py-1 text-xs font-medium {{ $approvalClass }}">
                                        {{ $approvalStatus }}
                                    </span>
                                </td>
                                <td class="px-3 py-3">{{ $sapApproval?->section_head_status ?? 'Waiting' }}</td>
                                <td class="px-3 py-3">{{ $sapApproval?->division_head_status ?? 'Waiting' }}</td>
                                <td class="px-3 py-3">{{ $sapApproval?->director_status ?? 'Waiting' }}</td>
                                <td class="px-3 py-3">
                                    <a href="{{ route('sap-approvals.show', $request->id) }}"
                                        class="text-blue-600 hover:underline">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-6 text-center text-gray-400">
                                    Belum ada request dengan BOM yang siap masuk SAP / PR Approval.
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
