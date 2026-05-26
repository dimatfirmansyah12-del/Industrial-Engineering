<x-dashboard-layout>
    <x-page-header
        title="Workshop Schedule"
        subtitle="Penjadwalan pekerjaan workshop setelah material lengkap"
    >
        <form method="GET" action="{{ route('workshop-schedules.index') }}"
            class="flex w-full flex-wrap items-center justify-end gap-2 lg:w-auto">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari no request, requester, department..."
                class="w-full rounded-lg border-gray-300 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:w-64">

            <select name="schedule_status"
                class="w-full rounded-lg border-gray-300 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:w-48">
                <option value="">All Schedule Status</option>
                @foreach (['No Schedule', 'Scheduled', 'Rescheduled', 'Cancelled', 'Ready to Work', 'In Progress', 'Finished'] as $scheduleStatus)
                    <option value="{{ $scheduleStatus }}" {{ request('schedule_status') == $scheduleStatus ? 'selected' : '' }}>
                        {{ $scheduleStatus }}
                    </option>
                @endforeach
            </select>

            <select name="pic_workshop"
                class="w-full rounded-lg border-gray-300 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:w-40">
                <option value="">All PIC</option>
                @foreach ($workshopPeople as $person)
                    <option value="{{ $person->name }}" {{ request('pic_workshop') == $person->name ? 'selected' : '' }}>
                        {{ $person->name }}
                    </option>
                @endforeach
            </select>

            <input type="date" name="planned_date" value="{{ request('planned_date') }}"
                class="w-full rounded-lg border-gray-300 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:w-40">

            <button type="submit"
                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Terapkan
            </button>

            <a href="{{ route('workshop-schedules.index') }}"
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

        <details class="mb-8" open>
            <summary class="mb-3 flex cursor-pointer list-none items-center justify-between rounded-xl bg-white px-6 py-4 shadow">
                <div>
                    <h3 class="font-bold text-gray-800">Workshop People Today</h3>
                    <p class="text-sm text-gray-500">Klik untuk tampilkan atau sembunyikan card orang workshop.</p>
                </div>
                <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                    Show / Hide
                </span>
            </summary>

            @include('workshop-schedules.partials.people-cards', [
                'workshopPeople' => $workshopPeople,
                'workOptions' => $workOptions,
            ])
        </details>

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
                            <th class="py-3 px-3">Request Info</th>
                            <th class="py-3 px-3">Jenis / Qty</th>
                            <th class="py-3 px-3">Schedule</th>
                            <th class="py-3 px-3">PIC / Status</th>
                            <th class="py-3 px-3">Material</th>
                            <th class="py-3 px-3 w-40">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($requests as $request)
                            @php
                                $schedule = $request->workshopSchedule;
                                $scheduleStatus = $schedule?->schedule_status ?? 'No Schedule';
                                $scheduleStatusClass = match($scheduleStatus) {
                                    'Scheduled' => 'bg-blue-100 text-blue-700',
                                    'Rescheduled' => 'bg-yellow-100 text-yellow-700',
                                    'Cancelled' => 'bg-red-100 text-red-700',
                                    'Ready to Work' => 'bg-green-100 text-green-700',
                                    'In Progress' => 'bg-orange-100 text-orange-700',
                                    'Finished' => 'bg-slate-200 text-slate-800',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp

                            <tr class="border-b align-top hover:bg-gray-50">
                                <td class="py-3 px-3">
                                    {{ $requests->firstItem() + $loop->index }}
                                </td>
                                <td class="py-3 px-3">
                                    <p class="font-bold text-gray-800">{{ $request->request_number }}</p>
                                    <p class="text-xs text-gray-500">{{ $request->requester_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $request->department }} - {{ $request->line_area ?? '-' }}</p>
                                </td>
                                <td class="py-3 px-3">
                                    <p class="font-semibold text-gray-800">{{ $request->request_type }}</p>
                                    <p class="text-xs text-gray-500">Qty: {{ $request->request_qty ?? 1 }}</p>
                                </td>
                                <td class="py-3 px-3">
                                    <p class="font-semibold text-gray-800">{{ $schedule?->schedule_number ?? 'Belum ada schedule' }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $schedule?->planned_start_date?->format('Y-m-d') ?? '-' }}
                                        s/d
                                        {{ $schedule?->planned_finish_date?->format('Y-m-d') ?? '-' }}
                                    </p>
                                    @if ($schedule?->estimated_duration)
                                        <p class="mt-1 text-xs text-gray-500">
                                            Durasi: {{ $schedule->estimated_duration }} hari
                                        </p>
                                    @endif
                                </td>
                                <td class="py-3 px-3">
                                    <p class="font-semibold text-gray-800">{{ $schedule?->pic_workshop ?? '-' }}</p>
                                    <span class="mt-1 inline-block px-3 py-1 rounded-full text-xs font-medium {{ $scheduleStatusClass }}">
                                        {{ $scheduleStatus }}
                                    </span>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        Complete
                                    </span>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $request->complete_materials_count }}/{{ $request->materials_count }} item
                                    </p>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="w-36 space-y-2">
                                        <a href="{{ route('workshop-schedules.show', $request->id) }}"
                                            class="block rounded-md border border-gray-200 px-3 py-1.5 text-center text-xs font-semibold text-gray-600 transition hover:bg-gray-50">
                                            Detail Lengkap
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-b bg-slate-50">
                                <td colspan="7" class="px-3 py-3">
                                    <details>
                                        <summary class="cursor-pointer list-none rounded-lg border border-blue-100 bg-white px-4 py-2 text-sm font-bold text-blue-700 transition hover:bg-blue-50">
                                            Quick Edit Schedule - {{ $request->request_number }}
                                        </summary>

                                        <div class="mt-3 rounded-xl border border-blue-100 bg-white p-4">
                                            @if (!$schedule)
                                                <form action="{{ route('workshop-schedules.store', $request->id) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf

                                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                                                        <div>
                                                            <label class="mb-1 block text-xs font-semibold text-gray-600">Planned Start</label>
                                                            <input type="date" name="planned_start_date"
                                                                class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                        </div>
                                                        <div>
                                                            <label class="mb-1 block text-xs font-semibold text-gray-600">Planned Finish</label>
                                                            <input type="date" name="planned_finish_date"
                                                                class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                        </div>
                                                        <div>
                                                            <label class="mb-1 block text-xs font-semibold text-gray-600">PIC Workshop</label>
                                                            <select name="pic_workshop"
                                                                class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                                <option value="">Pilih PIC</option>
                                                                @foreach ($workshopPeople as $person)
                                                                    <option value="{{ $person->name }}">{{ $person->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="flex items-end">
                                                            <button type="submit"
                                                                class="w-full rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">
                                                                Buat Schedule
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <div class="mt-3">
                                                        <label class="mb-1 block text-xs font-semibold text-gray-600">Schedule Note</label>
                                                        <textarea name="schedule_note" rows="2"
                                                            class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                            placeholder="Catatan schedule"></textarea>
                                                    </div>
                                                </form>
                                            @else
                                                <div class="grid grid-cols-1 gap-4 xl:grid-cols-[1fr_280px]">
                                                    <form action="{{ route('workshop-schedules.update', $schedule->id) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')

                                                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                                            <div>
                                                                <label class="mb-1 block text-xs font-semibold text-gray-600">Planned Start</label>
                                                                <input type="date" name="planned_start_date"
                                                                    value="{{ $schedule->planned_start_date?->format('Y-m-d') }}"
                                                                    class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                            </div>
                                                            <div>
                                                                <label class="mb-1 block text-xs font-semibold text-gray-600">Planned Finish</label>
                                                                <input type="date" name="planned_finish_date"
                                                                    value="{{ $schedule->planned_finish_date?->format('Y-m-d') }}"
                                                                    class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                            </div>
                                                            <div>
                                                                <label class="mb-1 block text-xs font-semibold text-gray-600">PIC Workshop</label>
                                                                <select name="pic_workshop"
                                                                    class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                                    <option value="">Pilih PIC</option>
                                                                    @foreach ($workshopPeople as $person)
                                                                        <option value="{{ $person->name }}" {{ $schedule->pic_workshop === $person->name ? 'selected' : '' }}>
                                                                            {{ $person->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="mt-3">
                                                            <label class="mb-1 block text-xs font-semibold text-gray-600">Schedule Note</label>
                                                            <textarea name="schedule_note" rows="2"
                                                                class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ $schedule->schedule_note }}</textarea>
                                                        </div>

                                                        <button type="submit"
                                                            class="mt-3 rounded-md bg-yellow-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-yellow-600">
                                                            Update Schedule
                                                        </button>
                                                    </form>

                                                    <div class="space-y-2">
                                                        @if (in_array($schedule->schedule_status, ['Scheduled', 'Rescheduled']))
                                                            <form action="{{ route('workshop-schedules.ready', $schedule->id) }}" method="POST">
                                                                @csrf
                                                                @method('PATCH')

                                                                <button type="submit"
                                                                    class="w-full rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-green-700">
                                                                    Ready to Work
                                                                </button>
                                                            </form>
                                                        @endif

                                                        <details class="rounded-md border border-blue-100 bg-blue-50 p-3">
                                                            <summary class="cursor-pointer list-none text-sm font-bold text-blue-700">
                                                                Reschedule
                                                            </summary>

                                                            <form action="{{ route('workshop-schedules.reschedule', $schedule->id) }}" method="POST" class="mt-3 space-y-2">
                                                                @csrf
                                                                @method('PATCH')

                                                                <input type="date" name="planned_start_date"
                                                                    value="{{ $schedule->planned_start_date?->format('Y-m-d') }}"
                                                                    class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                                <input type="date" name="planned_finish_date"
                                                                    value="{{ $schedule->planned_finish_date?->format('Y-m-d') }}"
                                                                    class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                                <textarea name="reschedule_reason" rows="2"
                                                                    class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                                    placeholder="Alasan reschedule"></textarea>

                                                                <button type="submit"
                                                                    class="w-full rounded-md bg-blue-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-700">
                                                                    Simpan Reschedule
                                                                </button>
                                                            </form>
                                                        </details>

                                                        <details class="rounded-md border border-red-100 bg-red-50 p-3">
                                                            <summary class="cursor-pointer list-none text-sm font-bold text-red-700">
                                                                Cancel
                                                            </summary>

                                                            <form action="{{ route('workshop-schedules.cancel', $schedule->id) }}" method="POST" class="mt-3 space-y-2">
                                                                @csrf
                                                                @method('PATCH')

                                                                <textarea name="reschedule_reason" rows="2"
                                                                    class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500"
                                                                    placeholder="Alasan cancel"></textarea>

                                                                <button type="submit"
                                                                    class="w-full rounded-md bg-red-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-red-700">
                                                                    Konfirmasi Cancel
                                                                </button>
                                                            </form>
                                                        </details>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </details>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-6 text-center text-gray-400">
                                    Belum ada request dengan material lengkap.
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
