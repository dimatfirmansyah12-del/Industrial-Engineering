<x-dashboard-layout>
    @php
        $userRole = auth()->user()?->role;
        $steps = \App\Services\RequestWorkflow::STATUSES;
        $currentIndex = array_search($ieRequest->status, $steps, true);
        $totalMaterials = $ieRequest->materials->count();
        $purchaseRequest = $ieRequest->purchaseRequest;
        $sapApproval = $ieRequest->sapApproval;
        $workshopSchedule = $ieRequest->workshopSchedule;
        $finalCheck = $ieRequest->finalCheck;
        $handover = $ieRequest->handover;
    @endphp

    <x-page-header
        title="Detail Request"
        subtitle="Pusat informasi request Industrial Engineering"
    >
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('ie-requests.print', $ieRequest->id) }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg font-medium">
                Print Work Order
            </a>

            @if ($userRole === 'admin')
                <a href="{{ route('ie-requests.edit', $ieRequest->id) }}"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-2 rounded-lg font-medium">
                    Edit
                </a>
            @endif

            <a href="{{ route('ie-requests.index') }}"
                class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-2 rounded-lg font-medium">
                Kembali
            </a>
        </div>
    </x-page-header>

    <div class="p-8 space-y-6">
        @if ($errors->any())
            <div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg">
                <p class="font-semibold">Ada data yang perlu diperbaiki:</p>
                <ul class="list-disc list-inside text-sm mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="bg-white rounded-xl shadow p-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="text-sm text-gray-500">No Request</p>
                    <h2 class="mt-1 text-3xl font-bold text-gray-900">{{ $ieRequest->request_number }}</h2>
                    <p class="mt-2 text-gray-600">
                        {{ $ieRequest->requester_name }} - {{ $ieRequest->department }} - {{ $ieRequest->line_area ?? '-' }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <x-priority-badge :priority="$ieRequest->priority" />
                    <x-status-badge :status="$ieRequest->status" />

                    @if (!$ieRequest->target_date)
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">No Target</span>
                    @elseif ($ieRequest->is_delay)
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                            Delay {{ $ieRequest->delay_days }} hari
                        </span>
                    @elseif ($ieRequest->is_due_soon)
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                            Due Soon {{ $ieRequest->due_soon_days }} hari lagi
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">On Track</span>
                    @endif
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-lg bg-blue-50 p-4">
                    <p class="text-sm text-blue-600">Jenis Request</p>
                    <p class="mt-1 font-bold text-gray-900">{{ $ieRequest->request_type }}</p>
                    <p class="text-sm text-gray-500">Qty: {{ $ieRequest->request_qty ?? 1 }}</p>
                </div>
                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="text-sm text-gray-500">Tanggal Request</p>
                    <p class="mt-1 font-bold text-gray-900">{{ $ieRequest->request_date ?? '-' }}</p>
                </div>
                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="text-sm text-gray-500">Target Selesai</p>
                    <p class="mt-1 font-bold text-gray-900">{{ $ieRequest->target_date ?? '-' }}</p>
                </div>
                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="text-sm text-gray-500">Request Owner</p>
                    <p class="mt-1 font-bold text-gray-900">{{ $ieRequest->user?->name ?? '-' }}</p>
                </div>
            </div>
        </section>

        <section class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Progress Timeline</h3>
            <div class="overflow-x-auto">
                <div class="flex items-center min-w-max">
                    @foreach ($steps as $index => $step)
                        @php
                            $isDone = $currentIndex !== false && $index < $currentIndex;
                            $isCurrent = $currentIndex !== false && $index === $currentIndex;
                        @endphp

                        <div class="flex items-center">
                            <div class="flex flex-col items-center w-32">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold {{ $isDone ? 'bg-green-500 text-white' : ($isCurrent ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500') }}">
                                    {{ $isDone ? 'OK' : $index + 1 }}
                                </div>
                                <p class="mt-2 text-xs text-center {{ $isCurrent ? 'text-blue-700 font-bold' : ($isDone ? 'text-green-700 font-semibold' : 'text-gray-400') }}">
                                    {{ $step }}
                                </p>
                            </div>

                            @if (!$loop->last)
                                <div class="w-16 h-1 mb-6 {{ $isDone ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Memo Approval Flow</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-gray-500">
                            <th class="py-3 px-3">Step</th>
                            <th class="py-3 px-3">Jabatan</th>
                            <th class="py-3 px-3">Approver</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3">Tanggal</th>
                            <th class="py-3 px-3">Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ieRequest->memoApprovalSteps as $approvalStep)
                            @php
                                $stepStatusClass = match($approvalStep->status) {
                                    'Approved' => 'bg-green-100 text-green-700',
                                    'Rejected' => 'bg-red-100 text-red-700',
                                    'Waiting' => 'bg-yellow-100 text-yellow-700',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp

                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-3 font-semibold text-gray-800">{{ $approvalStep->sequence }}</td>
                                <td class="py-3 px-3">{{ $approvalStep->approval_label }}</td>
                                <td class="py-3 px-3">
                                    <p class="font-semibold text-gray-800">{{ $approvalStep->approver?->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-500">{{ $approvalStep->approver?->email ?? '-' }}</p>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $stepStatusClass }}">
                                        {{ $approvalStep->status }}
                                    </span>
                                </td>
                                <td class="py-3 px-3">
                                    {{ $approvalStep->approved_at?->format('Y-m-d H:i') ?? $approvalStep->rejected_at?->format('Y-m-d H:i') ?? '-' }}
                                </td>
                                <td class="py-3 px-3">
                                    {{ $approvalStep->rejected_reason ?? $approvalStep->note ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-gray-400">
                                    Belum ada memo approval flow.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="xl:col-span-2 bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Informasi Request</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <p class="text-sm text-gray-500">PIC Drafter</p>
                        <p class="font-semibold text-gray-800">{{ $ieRequest->pic_drafter ?? $ieRequest->assigned_drafter ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">PIC Workshop</p>
                        <p class="font-semibold text-gray-800">{{ $ieRequest->pic_workshop ?? $workshopSchedule?->pic_workshop ?? '-' }}</p>
                    </div>
                </div>

                <div class="mt-5">
                    <p class="text-sm text-gray-500">Deskripsi Request</p>
                    <div class="mt-2 bg-gray-50 rounded-lg p-4 text-gray-700 whitespace-pre-line">{{ $ieRequest->description }}</div>
                </div>

                <div class="mt-5">
                    <p class="text-sm text-gray-500">Catatan</p>
                    <div class="mt-2 bg-gray-50 rounded-lg p-4 text-gray-700 whitespace-pre-line">{{ $ieRequest->notes ?? '-' }}</div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">File Request</h3>
                <div class="space-y-4">
                    <div class="rounded-lg bg-gray-50 p-4">
                        <p class="text-sm text-gray-500 mb-2">Memo</p>
                        @if ($ieRequest->memo_file)
                            <a href="{{ asset('storage/' . $ieRequest->memo_file) }}" target="_blank"
                                class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                Lihat Memo
                            </a>
                        @else
                            <p class="text-gray-400 text-sm">Belum ada file memo.</p>
                        @endif
                    </div>

                    <div class="rounded-lg bg-gray-50 p-4">
                        <p class="text-sm text-gray-500 mb-2">Drawing</p>
                        @if ($ieRequest->drawing_file)
                            <a href="{{ asset('storage/' . $ieRequest->drawing_file) }}" target="_blank"
                                class="inline-block bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                Lihat Drawing
                            </a>
                        @else
                            <p class="text-gray-400 text-sm">Belum ada file drawing.</p>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Status Per Modul</h3>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-lg border p-4">
                    <p class="text-sm text-gray-500">Memo Approval</p>
                    <p class="mt-1 font-bold text-gray-900">{{ $ieRequest->memo_status ?? '-' }}</p>
                    <p class="text-xs text-gray-500">{{ $ieRequest->memo_approved_by ?? '-' }}</p>
                </div>
                <div class="rounded-lg border p-4">
                    <p class="text-sm text-gray-500">Drawing</p>
                    <p class="mt-1 font-bold text-gray-900">{{ $ieRequest->drawing_status ?? '-' }}</p>
                    <p class="text-xs text-gray-500">{{ $ieRequest->assigned_drafter ?? '-' }}</p>
                </div>
                <div class="rounded-lg border p-4">
                    <p class="text-sm text-gray-500">Material / BOM</p>
                    <p class="mt-1 font-bold text-gray-900">{{ $ieRequest->bom_status ?? \App\Models\IeRequest::BOM_NO_BOM }}</p>
                    <p class="text-xs text-gray-500">{{ $totalMaterials }} item material</p>
                </div>
                <div class="rounded-lg border p-4">
                    <p class="text-sm text-gray-500">SAP / PR Approval</p>
                    <p class="mt-1 font-bold text-gray-900">{{ $sapApproval?->sap_number ?? '-' }}</p>
                    <p class="text-xs text-gray-500">{{ ($ieRequest->bom_status ?? null) === \App\Models\IeRequest::BOM_SUBMITTED ? ($sapApproval?->approval_status ?? \App\Models\SapApproval::WAITING_SAP_INPUT) : 'Menunggu BOM Submit' }}</p>
                </div>
                <div class="rounded-lg border p-4">
                    <p class="text-sm text-gray-500">Budget / PR</p>
                    <p class="mt-1 font-bold text-gray-900">{{ $purchaseRequest?->pr_number ?? '-' }}</p>
                    <p class="text-xs text-gray-500">{{ $purchaseRequest?->pr_status ?? 'Belum ada PR' }}</p>
                </div>
                <div class="rounded-lg border p-4">
                    <p class="text-sm text-gray-500">Workshop Schedule</p>
                    <p class="mt-1 font-bold text-gray-900">{{ $workshopSchedule?->schedule_number ?? '-' }}</p>
                    <p class="text-xs text-gray-500">{{ $workshopSchedule?->schedule_status ?? 'Belum ada schedule' }}</p>
                </div>
                <div class="rounded-lg border p-4">
                    <p class="text-sm text-gray-500">Workshop Progress</p>
                    <p class="mt-1 font-bold text-gray-900">{{ $workshopSchedule?->progress_status ?? '-' }}</p>
                    <p class="text-xs text-gray-500">{{ $workshopSchedule?->progress_percentage ?? 0 }}%</p>
                </div>
                <div class="rounded-lg border p-4">
                    <p class="text-sm text-gray-500">Final Check</p>
                    <p class="mt-1 font-bold text-gray-900">{{ $finalCheck?->check_status ?? '-' }}</p>
                    <p class="text-xs text-gray-500">{{ $finalCheck?->result_status ?? 'Belum ada hasil' }}</p>
                </div>
                <div class="rounded-lg border p-4">
                    <p class="text-sm text-gray-500">Handover</p>
                    <p class="mt-1 font-bold text-gray-900">{{ $handover?->handover_status ?? '-' }}</p>
                    <p class="text-xs text-gray-500">{{ $handover?->received_by ?? 'Belum diterima' }}</p>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Komentar / Catatan Progress</h3>

                <form action="{{ route('ie-requests.comments.store', $ieRequest->id) }}" method="POST" enctype="multipart/form-data" class="mb-6">
                    @csrf

                    <textarea name="comment" rows="3"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Tulis update, kendala, atau informasi tambahan...">{{ old('comment') }}</textarea>

                    <div class="mt-3 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <input type="file" name="attachment_file"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <button type="submit"
                            class="px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium">
                            Simpan Komentar
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Format: JPG, PNG, PDF, DOC, DOCX, XLSX. Maksimal 5MB.</p>
                </form>

                <div class="space-y-4 max-h-[520px] overflow-y-auto pr-1">
                    @forelse ($ieRequest->comments as $comment)
                        <div class="border rounded-lg p-4 bg-gray-50">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-gray-800">
                                        {{ $comment->user?->name ?? '-' }}
                                        @if ($comment->user?->role)
                                            <span class="ml-2 px-2 py-1 rounded bg-gray-200 text-gray-600 text-xs font-medium">
                                                {{ $comment->user->role }}
                                            </span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $comment->created_at?->format('d M Y H:i') }}</p>
                                </div>

                                @if (auth()->user()?->role === 'admin' || auth()->id() === $comment->user_id)
                                    <form action="{{ route('ie-requests.comments.destroy', $comment->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus komentar ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:underline">Hapus</button>
                                    </form>
                                @endif
                            </div>

                            <div class="mt-3 text-gray-700 whitespace-pre-line">{{ $comment->comment }}</div>

                            @if ($comment->attachment_file)
                                <a href="{{ asset('storage/' . $comment->attachment_file) }}" target="_blank"
                                    class="inline-block mt-3 text-sm text-blue-600 hover:underline">
                                    Lihat attachment
                                </a>
                            @endif
                        </div>
                    @empty
                        <div class="py-6 text-center text-gray-400">Belum ada komentar.</div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Activity History</h3>

                <div class="overflow-x-auto max-h-[620px]">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b text-left text-gray-500">
                                <th class="py-3 px-3">Waktu</th>
                                <th class="py-3 px-3">User</th>
                                <th class="py-3 px-3">Module</th>
                                <th class="py-3 px-3">Action</th>
                                <th class="py-3 px-3">Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ieRequest->activities as $activity)
                                <tr class="border-b hover:bg-gray-50 align-top">
                                    <td class="py-3 px-3 whitespace-nowrap">{{ $activity->created_at?->format('d M Y H:i') }}</td>
                                    <td class="py-3 px-3">{{ $activity->user?->name ?? 'System' }}</td>
                                    <td class="py-3 px-3">{{ $activity->module ?? '-' }}</td>
                                    <td class="py-3 px-3 font-semibold text-gray-800">
                                        {{ $activity->action }}
                                        @if ($activity->old_value || $activity->new_value)
                                            <p class="mt-1 text-xs font-normal text-gray-500">
                                                {{ $activity->old_value ?? '-' }} -> {{ $activity->new_value ?? '-' }}
                                            </p>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3">{{ $activity->note ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-gray-400">Belum ada activity history.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</x-dashboard-layout>
