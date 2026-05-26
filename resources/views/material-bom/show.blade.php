<x-dashboard-layout>
    <x-page-header
        title="Material / BOM Detail"
        subtitle="Daftar kebutuhan material untuk request Industrial Engineering"
    >
        @if ($canSubmitBom)
            <form action="{{ route('material-bom.submit', $ieRequest->id) }}" method="POST">
                @csrf
                @method('PATCH')

                <button type="submit"
                    class="rounded-lg bg-green-600 px-5 py-2 font-medium text-white hover:bg-green-700">
                    Submit BOM
                </button>
            </form>
        @endif

        @if ($canReviseBom)
            <form action="{{ route('material-bom.revise', $ieRequest->id) }}" method="POST"
                onsubmit="return confirm('Buka BOM sebagai Draft untuk revisi?')">
                @csrf
                @method('PATCH')

                <button type="submit"
                    class="rounded-lg bg-yellow-500 px-5 py-2 font-medium text-white hover:bg-yellow-600">
                    Revisi BOM
                </button>
            </form>
        @endif

        @if ($ieRequest->bom_status === \App\Models\IeRequest::BOM_SUBMITTED && $ieRequest->materials->isNotEmpty() && in_array(auth()->user()?->role, ['admin', 'section_head', 'division_head', 'director'], true))
            <a href="{{ route('sap-approvals.show', $ieRequest->id) }}"
                class="rounded-lg bg-blue-600 px-5 py-2 font-medium text-white hover:bg-blue-700">
                SAP / PR Approval
            </a>
        @endif

        <a href="{{ route('material-bom.index') }}"
            class="rounded-lg bg-gray-600 px-5 py-2 font-medium text-white hover:bg-gray-700">
            Kembali
        </a>
    </x-page-header>

    <div class="max-w-full overflow-x-hidden p-6 lg:p-8">
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

        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-red-700">
                <p class="font-semibold">Ada data yang perlu diperbaiki:</p>
                <ul class="mt-2 list-inside list-disc text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $bomStatus = $ieRequest->bom_status ?? \App\Models\IeRequest::BOM_NO_BOM;
            $bomStatusClass = match($bomStatus) {
                \App\Models\IeRequest::BOM_SUBMITTED => 'bg-green-100 text-green-700',
                \App\Models\IeRequest::BOM_DRAFT => 'bg-yellow-100 text-yellow-700',
                default => 'bg-gray-100 text-gray-700',
            };
        @endphp

        <datalist id="material-category-options">
            @foreach ($materialCategories as $categoryName => $defaultUnit)
                <option value="{{ $categoryName }}"></option>
            @endforeach
        </datalist>

        <div class="mb-6 rounded-xl bg-white p-5 shadow">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-3">
                        <h3 class="truncate text-xl font-bold text-gray-900">
                            {{ $ieRequest->request_number }}
                        </h3>

                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $bomStatusClass }}">
                            {{ $bomStatus }}
                        </span>
                    </div>

                    <p class="mt-1 text-sm text-gray-500">
                        {{ $ieRequest->requester_name }} - {{ $ieRequest->department }} - {{ $ieRequest->line_area ?? '-' }}
                    </p>

                    <div class="mt-3 flex flex-wrap gap-x-6 gap-y-2 text-sm">
                        <div>
                            <span class="text-gray-500">Jenis:</span>
                            <span class="font-semibold text-gray-800">{{ $ieRequest->request_type }}</span>
                        </div>

                        <div>
                            <span class="text-gray-500">Qty:</span>
                            <span class="font-semibold text-gray-800">{{ $ieRequest->request_qty ?? 1 }}</span>
                        </div>

                        <div>
                            <span class="text-gray-500">Drawing:</span>
                            @if ($ieRequest->drawing_file)
                                <a href="{{ asset('storage/' . $ieRequest->drawing_file) }}" target="_blank"
                                    class="font-semibold text-blue-600 hover:underline">
                                    Lihat Drawing
                                </a>
                            @else
                                <span class="font-semibold text-gray-400">-</span>
                            @endif
                        </div>
                    </div>

                    @if ($ieRequest->bom_submitted_at)
                        <p class="mt-3 text-xs text-gray-500">
                            Submitted by {{ $ieRequest->bom_submitted_by ?? '-' }}
                            at {{ $ieRequest->bom_submitted_at?->format('Y-m-d H:i') }}
                        </p>
                    @endif

                    @if ($ieRequest->bom_revision_note)
                        <p class="mt-2 text-sm text-yellow-700">
                            Revisi: {{ $ieRequest->bom_revision_note }}
                        </p>
                    @endif
                </div>

                <div class="w-full border-t border-gray-100 pt-4 text-sm xl:w-auto xl:min-w-[220px] xl:border-l xl:border-t-0 xl:pl-6 xl:pt-0">
                    <div>
                        <p class="text-xs text-gray-500">Total Material</p>
                        <p class="mt-1 text-lg font-bold text-gray-900">{{ $ieRequest->materials->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if ($canEditBom)
            <details class="mb-6 overflow-hidden rounded-xl bg-white shadow" {{ $ieRequest->materials->isEmpty() ? 'open' : '' }}>
                <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-5 py-4">
                    <div>
                        <h3 class="font-bold text-gray-800">Tambah Material</h3>
                        <p class="text-sm text-gray-500">Input kebutuhan material BOM.</p>
                    </div>

                    <span class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white">
                        + Material
                    </span>
                </summary>

                <form action="{{ route('material-bom.materials.store', $ieRequest->id) }}" method="POST"
                    x-data="{ unitMap: @js($materialCategories), category: @js(old('material_category')), unit: @js(old('unit', '')) }"
                    class="border-t border-gray-100 p-5">
                    @csrf

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Material</label>
                            <input type="text" name="material_category"
                                list="material-category-options"
                                x-model="category"
                                @input="if (unitMap[category] !== undefined) unit = unitMap[category]"
                                placeholder="Square Pipe, Plate, Baut..."
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <x-form-input
                            label="Spec Material"
                            name="specification"
                            placeholder="40 x 40 x 2"
                        />

                        <x-form-input
                            label="Qty"
                            name="qty"
                            type="number"
                            step="0.01"
                            min="0"
                            value="0"
                        />

                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Unit</label>
                            <input type="text" name="unit"
                                x-model="unit"
                                placeholder="pcs, meter, set"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="mt-5 flex justify-end">
                        <button type="submit"
                            class="rounded-lg bg-blue-600 px-5 py-2 font-medium text-white hover:bg-blue-700">
                            Tambah Material
                        </button>
                    </div>
                </form>
            </details>
        @else
            <div class="mb-6 rounded-xl border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
                BOM sedang terkunci. Material hanya bisa diubah saat status BOM Draft.
            </div>
        @endif

        <div class="max-w-full overflow-hidden rounded-xl bg-white p-5 shadow">
            <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-lg font-bold text-gray-800">Daftar Material</h3>
                <p class="text-sm text-gray-500">
                    Total item:
                    <span class="font-semibold text-gray-800">{{ $ieRequest->materials->count() }}</span>
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-[640px] w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-gray-500">
                            <th class="w-12 px-3 py-3">No</th>
                            <th class="px-3 py-3">Material</th>
                            <th class="px-3 py-3">Spec Material</th>
                            <th class="w-24 px-3 py-3">Qty</th>
                            <th class="w-24 px-3 py-3">Unit</th>
                            <th class="w-20 px-3 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody x-data="{ actionId: null, editingId: null }"
                        @keydown.escape.window="actionId = null; editingId = null">
                        @forelse ($ieRequest->materials as $material)
                            <tr class="border-b align-top hover:bg-gray-50">
                                <td class="px-3 py-3">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="px-3 py-3">
                                    <p class="font-semibold text-gray-900">{{ $material->material_category ?? $material->material_name }}</p>
                                </td>

                                <td class="px-3 py-3 text-gray-700">
                                    {{ $material->specification ?? '-' }}
                                </td>

                                <td class="px-3 py-3 font-semibold text-gray-800">
                                    {{ number_format($material->qty, 2, ',', '.') }}
                                </td>

                                <td class="px-3 py-3 text-gray-700">
                                    {{ $material->unit ?? '-' }}
                                </td>

                                <td class="px-3 py-3">
                                    @if ($canEditBom)
                                        <div class="relative inline-block text-left"
                                            @click.outside="actionId = null">
                                            <button type="button"
                                                @click="actionId = actionId === {{ $material->id }} ? null : {{ $material->id }}"
                                                title="Quick Action"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-600 shadow-sm hover:bg-gray-50">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15.5A3.5 3.5 0 1 0 12 8a3.5 3.5 0 0 0 0 7.5Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 0 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6V21a2 2 0 0 1-4 0v-.1a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1A2 2 0 0 1 4.2 17l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.6-1H3a2 2 0 0 1 0-4h.1a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9L4.2 7A2 2 0 0 1 7 4.2l.1.1a1.7 1.7 0 0 0 1.9.3 1.7 1.7 0 0 0 1-1.6V3a2 2 0 0 1 4 0v.1a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1A2 2 0 0 1 19.8 7l-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1h.1a2 2 0 0 1 0 4H21a1.7 1.7 0 0 0-1.6 1Z" />
                                                </svg>
                                            </button>

                                            <div x-show="actionId === {{ $material->id }}"
                                                x-transition
                                                class="absolute right-0 z-20 mt-2 w-36 overflow-hidden rounded-lg border border-gray-200 bg-white py-1 shadow-lg"
                                                style="display: none;">
                                                <button type="button"
                                                    @click="editingId = {{ $material->id }}; actionId = null"
                                                    class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">
                                                    Edit
                                                </button>

                                                <form action="{{ route('material-bom.materials.destroy', $material->id) }}" method="POST"
                                                    onsubmit="return confirm('Yakin ingin menghapus material ini?')">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                        class="block w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @else
                                        <span class="inline-flex rounded-lg bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-500">
                                            Locked
                                        </span>
                                    @endif
                                </td>
                            </tr>

                            @if ($canEditBom)
                                <tr x-show="editingId === {{ $material->id }}"
                                    x-transition
                                    class="border-b bg-blue-50/40"
                                    style="display: none;">
                                    <td colspan="6" class="px-3 py-4">
                                        <form action="{{ route('material-bom.materials.update', $material->id) }}" method="POST"
                                            x-data="{ unitMap: @js($materialCategories), category: @js(old('material_category', $material->material_category ?? $material->material_name)), unit: @js(old('unit', $material->unit)) }"
                                            class="rounded-lg border border-blue-100 bg-white p-4">
                                            @csrf
                                            @method('PUT')

                                            <div class="grid grid-cols-1 gap-3 lg:grid-cols-12">
                                                <div class="lg:col-span-4">
                                                    <label class="mb-1 block text-xs font-semibold text-gray-600">Material</label>
                                                    <input type="text" name="material_category"
                                                        list="material-category-options"
                                                        x-model="category"
                                                        @input="if (unitMap[category] !== undefined) unit = unitMap[category]"
                                                        placeholder="Square Pipe, Plate, Baut..."
                                                        class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                </div>

                                                <div class="lg:col-span-4">
                                                    <label class="mb-1 block text-xs font-semibold text-gray-600">Spec Material</label>
                                                    <input type="text" name="specification"
                                                        value="{{ old('specification', $material->specification) }}"
                                                        class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                </div>

                                                <div class="lg:col-span-2">
                                                    <label class="mb-1 block text-xs font-semibold text-gray-600">Qty</label>
                                                    <input type="number" step="0.01" min="0" name="qty"
                                                        value="{{ old('qty', number_format($material->qty, 2, '.', '')) }}"
                                                        class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                </div>

                                                <div class="lg:col-span-2">
                                                    <label class="mb-1 block text-xs font-semibold text-gray-600">Unit</label>
                                                    <input type="text" name="unit"
                                                        x-model="unit"
                                                        class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                </div>
                                            </div>

                                            <div class="mt-4 flex justify-end gap-2">
                                                <button type="button"
                                                    @click="editingId = null"
                                                    class="rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300">
                                                    Batal
                                                </button>

                                                <button type="submit"
                                                    class="rounded-md bg-yellow-500 px-4 py-2 text-sm font-semibold text-white hover:bg-yellow-600">
                                                    Update Material
                                                </button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-gray-400">
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
