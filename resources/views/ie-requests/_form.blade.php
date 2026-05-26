@php
    $requestData = $ieRequest ?? null;
    $isEdit = $isEdit ?? false;
    $submitLabel = $submitLabel ?? 'Simpan';

    $departmentOptions = $departments->mapWithKeys(function ($department) {
        $label = $department->name;

        if ($department->code) {
            $label .= ' - ' . $department->code;
        }

        return [$department->name => $label];
    });

    $lineAreaOptions = $lineAreas->mapWithKeys(function ($lineArea) {
        $label = $lineArea->name;

        if ($lineArea->code) {
            $label .= ' - ' . $lineArea->code;
        }

        if ($lineArea->department) {
            $label .= ' | ' . $lineArea->department;
        }

        return [$lineArea->name => $label];
    });

    $priorityOptions = [
        'Low' => 'Low',
        'Medium' => 'Medium',
        'High' => 'High',
        'Urgent' => 'Urgent',
    ];

    $approverPayload = ($approvers ?? collect())->map(function ($approver) {
        return [
            'id' => $approver->id,
            'name' => $approver->name,
            'email' => $approver->email,
            'role' => $approver->role,
            'position' => $approver->position ?: ($approver->role === 'admin' ? 'Admin IE' : 'Approver'),
        ];
    })->values();

    $oldApproverIds = collect(old('approver_user_ids', []))
        ->map(fn ($approverId) => (int) $approverId)
        ->values();
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <x-form-input
            label="No Request"
            name="request_number"
            value="{{ $requestData?->request_number ?? $requestNumber }}"
            readonly
        />
        <p class="text-xs text-gray-400 mt-1">
            {{ $isEdit ? 'Nomor request tidak bisa diubah.' : 'Nomor request dibuat otomatis oleh sistem.' }}
        </p>
    </div>

    <x-form-input
        label="Tanggal Request"
        name="request_date"
        type="date"
        value="{{ $requestData?->request_date }}"
    />

    <x-form-input
        label="Nama Requester"
        name="requester_name"
        value="{{ $requestData?->requester_name }}"
        placeholder="Nama pemohon"
    />

    <x-form-select
        label="Department"
        name="department"
        :options="$departmentOptions"
        value="{{ $requestData?->department }}"
        placeholder="Pilih Department"
    />

    <x-form-select
        label="Line / Area"
        name="line_area"
        :options="$lineAreaOptions"
        value="{{ $requestData?->line_area }}"
        placeholder="Pilih Line / Area"
    />

    <x-form-input
        label="Jenis Request"
        name="request_type"
        value="{{ $requestData?->request_type }}"
        placeholder="Contoh: Trolley material, meja kerja, rak tooling, jig, repair equipment"
    />

    <x-form-input
        label="Qty Request"
        name="request_qty"
        type="number"
        min="1"
        value="{{ $requestData?->request_qty ?? 1 }}"
        placeholder="Jumlah request"
    />

    <x-form-select
        label="Priority"
        name="priority"
        value="{{ $requestData?->priority ?? 'Medium' }}"
        :options="$priorityOptions"
        placeholder="Pilih Priority"
    />

    <x-form-input
        label="Target Selesai"
        name="target_date"
        type="date"
        value="{{ $requestData?->target_date }}"
    />
</div>

<div class="mt-6">
    <x-form-textarea
        label="Deskripsi Request"
        name="description"
        rows="4"
        value="{{ $requestData?->description }}"
        placeholder="Jelaskan kebutuhan customer / line produksi"
    />
</div>

<div class="mt-6">
    <x-form-textarea
        label="Catatan"
        name="notes"
        rows="3"
        value="{{ $requestData?->notes }}"
        placeholder="Catatan tambahan jika ada"
    />
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            {{ $isEdit ? 'Upload Memo Baru' : 'Upload Memo' }}
        </label>
        <input type="file" name="memo_file"
            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">

        @if ($isEdit && $requestData?->memo_file)
            <a href="{{ asset('storage/' . $requestData->memo_file) }}" target="_blank"
                class="inline-block mt-2 text-sm text-blue-600 hover:underline">
                Lihat memo saat ini
            </a>
        @elseif ($isEdit)
            <p class="text-xs text-gray-400 mt-2">Belum ada memo.</p>
        @else
            <p class="text-xs text-gray-400 mt-1">Format: PDF, JPG, PNG, DOC, DOCX. Maksimal 5MB.</p>
        @endif
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            {{ $isEdit ? 'Upload Drawing Baru' : 'Upload Drawing' }}
        </label>
        <input type="file" name="drawing_file"
            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">

        @if ($isEdit && $requestData?->drawing_file)
            <a href="{{ asset('storage/' . $requestData->drawing_file) }}" target="_blank"
                class="inline-block mt-2 text-sm text-blue-600 hover:underline">
                Lihat drawing saat ini
            </a>
        @elseif ($isEdit)
            <p class="text-xs text-gray-400 mt-2">Belum ada drawing.</p>
        @else
            <p class="text-xs text-gray-400 mt-1">Format: PDF, JPG, PNG, DWG, DXF. Maksimal 10MB.</p>
        @endif
    </div>
</div>

@if (!$isEdit)
    <div class="mt-6 rounded-xl border border-blue-100 bg-blue-50/40 p-4"
        x-data="memoApproverPicker(@js($approverPayload), @js($oldApproverIds))">
        <div class="mb-4 flex flex-col gap-1">
            <h3 class="text-base font-bold text-gray-800">Memo Approval Flow</h3>
            <p class="text-sm text-gray-500">Pilih approver sesuai urutan approval memo. Minimal 1 approver wajib dipilih.</p>
        </div>

        <div class="relative"
            @click.outside="open = false"
            @keydown.escape.window="open = false">
            <div class="mb-2 flex items-center justify-between gap-3">
                <label class="block text-sm font-medium text-gray-700">Cari Approver</label>
                <button type="button"
                    @click="open = true; $nextTick(() => $refs.approverSearch.focus())"
                    class="rounded-md bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700">
                    + Tambah Approver
                </button>
            </div>
            <input type="text"
                x-ref="approverSearch"
                x-model="search"
                @focus="open = true"
                @click="open = true"
                placeholder="Ketik nama, email, section head, department head, atau division head"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">

            <div x-show="open"
                x-transition
                @click.stop
                class="absolute z-40 mt-2 max-h-72 w-full overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg"
                style="display: none;">
                <template x-if="filteredApprovers().length === 0">
                    <div class="px-4 py-3 text-sm text-gray-400">Approver tidak ditemukan.</div>
                </template>

                <template x-for="approver in filteredApprovers()" :key="approver.id">
                    <button type="button"
                        @click="addApprover(approver)"
                        class="flex w-full items-start justify-between gap-3 px-4 py-3 text-left transition hover:bg-blue-50">
                        <span>
                            <span class="block text-sm font-semibold text-gray-800" x-text="approver.name"></span>
                            <span class="block text-xs text-gray-500" x-text="approver.email"></span>
                        </span>
                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700" x-text="approver.position"></span>
                    </button>
                </template>
            </div>
        </div>

        <div class="mt-4 overflow-hidden rounded-lg border border-gray-200 bg-white">
            <div class="grid grid-cols-[70px_1fr_160px_80px] bg-gray-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-gray-500">
                <span>Urutan</span>
                <span>Approver</span>
                <span>Jabatan</span>
                <span>Aksi</span>
            </div>

            <template x-if="selected.length === 0">
                <div class="px-4 py-5 text-center text-sm text-gray-400">
                    Belum ada approver dipilih.
                </div>
            </template>

            <template x-for="(approver, index) in selected" :key="approver.id">
                <div class="grid grid-cols-[70px_1fr_160px_80px] items-center border-t border-gray-100 px-4 py-3 text-sm">
                    <span class="font-semibold text-gray-700" x-text="index + 1"></span>
                    <span>
                        <span class="block font-semibold text-gray-800" x-text="approver.name"></span>
                        <span class="block text-xs text-gray-500" x-text="approver.email"></span>
                        <input type="hidden" name="approver_user_ids[]" :value="approver.id">
                    </span>
                    <span class="text-gray-600" x-text="approver.position"></span>
                    <button type="button"
                        @click="removeApprover(approver.id)"
                        class="rounded-md border border-red-200 px-3 py-1 text-xs font-semibold text-red-600 hover:bg-red-50">
                        Hapus
                    </button>
                </div>
            </template>
        </div>

        @error('approver_user_ids')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror

        @error('approver_user_ids.*')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <script>
        function memoApproverPicker(approvers, initialIds) {
            return {
                approvers,
                selected: initialIds
                    .map((id) => approvers.find((approver) => approver.id === Number(id)))
                    .filter(Boolean),
                search: '',
                open: false,
                filteredApprovers() {
                    const keyword = this.search.toLowerCase().trim();

                    return this.approvers
                        .filter((approver) => !this.selected.some((selectedApprover) => selectedApprover.id === approver.id))
                        .filter((approver) => {
                            if (!keyword) {
                                return true;
                            }

                            return [
                                approver.name,
                                approver.email,
                                approver.position,
                                approver.role,
                            ].some((value) => String(value || '').toLowerCase().includes(keyword));
                        });
                },
                addApprover(approver) {
                    this.selected.push(approver);
                    this.search = '';
                    this.open = false;
                },
                removeApprover(approverId) {
                    this.selected = this.selected.filter((approver) => approver.id !== approverId);
                    this.search = '';
                },
            };
        }
    </script>
@else
    <div class="mt-6 rounded-xl border border-gray-200 bg-gray-50 p-4">
        <h3 class="text-base font-bold text-gray-800">Memo Approval Flow</h3>
        <div class="mt-4 overflow-hidden rounded-lg border border-gray-200 bg-white">
            <div class="grid grid-cols-[70px_1fr_160px_140px] bg-gray-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-gray-500">
                <span>Urutan</span>
                <span>Approver</span>
                <span>Jabatan</span>
                <span>Status</span>
            </div>

            @forelse ($requestData?->memoApprovalSteps ?? [] as $approvalStep)
                <div class="grid grid-cols-[70px_1fr_160px_140px] items-center border-t border-gray-100 px-4 py-3 text-sm">
                    <span class="font-semibold text-gray-700">{{ $approvalStep->sequence }}</span>
                    <span>
                        <span class="block font-semibold text-gray-800">{{ $approvalStep->approver?->name ?? '-' }}</span>
                        <span class="block text-xs text-gray-500">{{ $approvalStep->approver?->email ?? '-' }}</span>
                    </span>
                    <span class="text-gray-600">{{ $approvalStep->approval_label }}</span>
                    <span class="font-semibold text-gray-700">{{ $approvalStep->status }}</span>
                </div>
            @empty
                <div class="px-4 py-5 text-center text-sm text-gray-400">
                    Belum ada approval flow.
                </div>
            @endforelse
        </div>
    </div>
@endif

<div class="mt-8 flex justify-end gap-3">
    <a href="{{ route('ie-requests.index') }}"
        class="px-6 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium">
        Batal
    </a>

    <button type="submit"
        class="px-6 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium">
        {{ $submitLabel }}
    </button>
</div>
