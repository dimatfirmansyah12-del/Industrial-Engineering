<x-dashboard-layout>
    <x-page-header
        title="Material Arrival Detail"
        subtitle="Update kedatangan material untuk request Industrial Engineering"
    >
        <a href="{{ route('material-arrivals.index') }}"
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
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
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
                    <p class="text-sm text-gray-500">PR Number</p>
                    <p class="font-semibold text-gray-800">{{ $ieRequest->purchaseRequest->pr_number }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">PR Status</p>
                    <p class="font-semibold text-gray-800">{{ $ieRequest->purchaseRequest->pr_status }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Total Budget</p>
                    <p class="font-semibold text-gray-800">
                        Rp {{ number_format($ieRequest->purchaseRequest->total_budget, 2, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <x-summary-card
                title="Total Material"
                value="{{ $totalMaterial }}"
                color="blue"
            />

            <x-summary-card
                title="Complete"
                value="{{ $completeMaterial }}"
                color="green"
            />

            <x-summary-card
                title="Partial"
                value="{{ $partialMaterial }}"
                color="orange"
            />

            <x-summary-card
                title="Waiting"
                value="{{ $waitingMaterial }}"
                color="purple"
            />
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <div class="mb-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">Daftar Kedatangan Material</h3>

                <form action="{{ route('material-arrivals.complete', $ieRequest->id) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg font-medium">
                        Check Material Complete
                    </button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-gray-500">
                            <th class="py-3 px-3">No</th>
                            <th class="py-3 px-3">Material Name</th>
                            <th class="py-3 px-3">Specification</th>
                            <th class="py-3 px-3">Qty</th>
                            <th class="py-3 px-3">Unit</th>
                            <th class="py-3 px-3">Arrived Qty</th>
                            <th class="py-3 px-3">Arrival Status</th>
                            <th class="py-3 px-3">Arrival Date</th>
                            <th class="py-3 px-3">Arrival Note</th>
                            <th class="py-3 px-3">Aksi Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ieRequest->materials as $material)
                            @php
                                $arrivalStatusClass = match($material->arrival_status) {
                                    'Complete' => 'bg-green-100 text-green-700',
                                    'Partial Arrived' => 'bg-orange-100 text-orange-700',
                                    default => 'bg-purple-100 text-purple-700',
                                };
                            @endphp

                            <tr class="border-b align-top hover:bg-gray-50">
                                <td class="py-3 px-3">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="py-3 px-3 font-semibold text-gray-800 min-w-[170px]">
                                    {{ $material->material_name }}
                                </td>
                                <td class="py-3 px-3 min-w-[220px]">
                                    {{ $material->specification ?? '-' }}
                                </td>
                                <td class="py-3 px-3">
                                    {{ number_format($material->qty, 2, ',', '.') }}
                                </td>
                                <td class="py-3 px-3">
                                    {{ $material->unit ?? '-' }}
                                </td>
                                <td class="py-3 px-3 min-w-[130px]">
                                    <input form="arrival-form-{{ $material->id }}" type="number" step="0.01" min="0" name="arrived_qty"
                                        value="{{ old('arrived_qty', number_format($material->arrived_qty, 2, '.', '')) }}"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-xs">
                                </td>
                                <td class="py-3 px-3">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $arrivalStatusClass }}">
                                        {{ $material->arrival_status }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 min-w-[150px]">
                                    <input form="arrival-form-{{ $material->id }}" type="date" name="arrival_date"
                                        value="{{ old('arrival_date', $material->arrival_date?->format('Y-m-d')) }}"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-xs">
                                </td>
                                <td class="py-3 px-3 min-w-[220px]">
                                    <textarea form="arrival-form-{{ $material->id }}" name="arrival_note" rows="2"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-xs">{{ old('arrival_note', $material->arrival_note) }}</textarea>
                                </td>
                                <td class="py-3 px-3 min-w-[120px]">
                                    <form id="arrival-form-{{ $material->id }}"
                                        action="{{ route('material-arrivals.update-material', $material->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('PATCH')
                                    </form>

                                    <button type="submit" form="arrival-form-{{ $material->id }}"
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg font-medium">
                                        Update
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-6 text-center text-gray-400">
                                    Belum ada material untuk request ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-dashboard-layout>
