<x-dashboard-layout>
    <x-page-header
        title="Memo Approval"
        subtitle="Approval memo berjenjang sesuai approver yang dipilih user"
    />

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

        <div class="bg-white rounded-xl shadow p-6">
            <div class="mb-4 flex justify-between items-center">
                <p class="text-sm text-gray-500">
                    Total approval menunggu:
                    <span class="font-semibold text-gray-800">{{ $approvalSteps->total() }}</span>
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-gray-500">
                            <th class="py-3 px-3">No</th>
                            <th class="py-3 px-3">No Request</th>
                            <th class="py-3 px-3">Requester</th>
                            <th class="py-3 px-3">Department</th>
                            <th class="py-3 px-3">Jenis / Qty</th>
                            <th class="py-3 px-3">Step</th>
                            <th class="py-3 px-3">Approver</th>
                            <th class="py-3 px-3">Memo File</th>
                            <th class="py-3 px-3 w-52">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($approvalSteps as $approvalStep)
                            @php
                                $request = $approvalStep->ieRequest;
                            @endphp

                            <tr class="border-b align-top hover:bg-gray-50">
                                <td class="py-3 px-3">
                                    {{ $approvalSteps->firstItem() + $loop->index }}
                                </td>
                                <td class="py-3 px-3 font-semibold text-gray-800">
                                    {{ $request->request_number }}
                                </td>
                                <td class="py-3 px-3">
                                    {{ $request->requester_name }}
                                </td>
                                <td class="py-3 px-3">
                                    <p>{{ $request->department }}</p>
                                    <p class="text-xs text-gray-500">{{ $request->line_area ?? '-' }}</p>
                                </td>
                                <td class="py-3 px-3">
                                    <p class="font-semibold text-gray-800">{{ $request->request_type }}</p>
                                    <p class="text-xs text-gray-500">Qty: {{ $request->request_qty ?? 1 }}</p>
                                </td>
                                <td class="py-3 px-3">
                                    <p class="font-semibold text-gray-800">Step {{ $approvalStep->sequence }}</p>
                                    <p class="text-xs text-gray-500">{{ $approvalStep->approval_label }}</p>
                                </td>
                                <td class="py-3 px-3">
                                    <p class="font-semibold text-gray-800">{{ $approvalStep->approver?->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-500">{{ $approvalStep->approver?->position ?? '-' }}</p>
                                </td>
                                <td class="py-3 px-3">
                                    <a href="{{ asset('storage/' . $request->memo_file) }}" target="_blank"
                                        class="text-blue-600 hover:underline">
                                        Lihat Memo
                                    </a>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="w-48 space-y-2">
                                        <form action="{{ route('memo-approvals.approve', $approvalStep->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')

                                            <textarea
                                                name="approval_note"
                                                rows="2"
                                                placeholder="Catatan approval (opsional)"
                                                class="mb-2 w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('approval_note') }}</textarea>

                                            <button type="submit"
                                                class="w-full rounded-md bg-green-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-green-700">
                                                Approve
                                            </button>
                                        </form>

                                        <details class="group">
                                            <summary class="flex cursor-pointer list-none items-center justify-center rounded-md border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50">
                                                Reject
                                            </summary>

                                            <form action="{{ route('memo-approvals.reject', $approvalStep->id) }}" method="POST"
                                                class="mt-2 rounded-lg border border-red-100 bg-red-50 p-2">
                                                @csrf
                                                @method('PATCH')

                                                <textarea
                                                    name="memo_rejected_reason"
                                                    rows="2"
                                                    placeholder="Alasan reject"
                                                    class="w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-red-500 focus:ring-red-500">{{ old('memo_rejected_reason') }}</textarea>

                                                <button type="submit"
                                                    class="mt-2 w-full rounded-md bg-red-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-red-700">
                                                    Kirim Reject
                                                </button>
                                            </form>
                                        </details>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-6 text-center text-gray-400">
                                    Tidak ada memo yang sedang menunggu approval kamu.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $approvalSteps->links() }}
            </div>
        </div>
    </div>
</x-dashboard-layout>
