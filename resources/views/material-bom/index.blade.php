<x-dashboard-layout>
    <x-page-header
        title="Material / BOM"
        subtitle="Monitoring kebutuhan material berdasarkan drawing request"
    >
        <form method="GET" action="{{ route('material-bom.index') }}"
            class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari no request, requester, department..."
                class="w-full rounded-lg border-gray-300 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:w-64">

            <select name="bom_status"
                class="w-full rounded-lg border-gray-300 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:w-44">
                <option value="">All BOM Status</option>
                @foreach ($bomStatuses as $bomStatus)
                    <option value="{{ $bomStatus }}" {{ request('bom_status') == $bomStatus ? 'selected' : '' }}>
                        {{ $bomStatus }}
                    </option>
                @endforeach
            </select>

            <button type="submit"
                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Terapkan
            </button>

            <a href="{{ route('material-bom.index') }}"
                class="rounded-lg bg-gray-200 px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-300">
                Reset
            </a>
        </form>
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

        <div class="bg-white rounded-xl shadow p-6">
            <div class="mb-4 flex justify-between items-center">
                <p class="text-sm text-gray-500">
                    Total data ditemukan:
                    <span class="font-semibold text-gray-800">{{ $requests->total() }}</span>
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
                            <th class="py-3 px-3">Line / Area</th>
                            <th class="py-3 px-3">Jenis / Qty</th>
                            <th class="py-3 px-3">BOM Status</th>
                            <th class="py-3 px-3">Material</th>
                            <th class="py-3 px-3">SAP / PR Approval</th>
                            <th class="py-3 px-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($requests as $request)
                            @php
                                $bomStatus = $request->bom_status ?? \App\Models\IeRequest::BOM_NO_BOM;
                                $bomStatusClass = match($bomStatus) {
                                    \App\Models\IeRequest::BOM_SUBMITTED => 'bg-green-100 text-green-700',
                                    \App\Models\IeRequest::BOM_DRAFT => 'bg-yellow-100 text-yellow-700',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp

                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-3">
                                    {{ $requests->firstItem() + $loop->index }}
                                </td>
                                <td class="py-3 px-3 font-semibold text-gray-800">
                                    {{ $request->request_number }}
                                </td>
                                <td class="py-3 px-3">
                                    {{ $request->requester_name }}
                                </td>
                                <td class="py-3 px-3">
                                    {{ $request->department }}
                                </td>
                                <td class="py-3 px-3">
                                    {{ $request->line_area ?? '-' }}
                                </td>
                                <td class="py-3 px-3">
                                    <p class="font-semibold text-gray-800">{{ $request->request_type }}</p>
                                    <p class="text-xs text-gray-500">Qty: {{ $request->request_qty ?? 1 }}</p>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $bomStatusClass }}">
                                        {{ $bomStatus }}
                                    </span>
                                </td>
                                <td class="py-3 px-3">
                                    <p>{{ $request->materials_count }} item</p>
                                </td>
                                <td class="py-3 px-3">
                                    {{ $bomStatus === \App\Models\IeRequest::BOM_SUBMITTED ? ($request->sapApproval?->approval_status ?? \App\Models\SapApproval::WAITING_SAP_INPUT) : '-' }}
                                </td>
                                <td class="py-3 px-3">
                                    <a href="{{ route('material-bom.show', $request->id) }}"
                                        class="text-blue-600 hover:underline">
                                        Detail BOM
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-6 text-center text-gray-400">
                                    Belum ada request dengan drawing status Done.
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
