<x-dashboard-layout>
    <x-page-header
        title="Drawing Progress"
        subtitle="Monitoring proses drawing request Industrial Engineering"
    >
        <form method="GET" action="{{ route('drawing-progress.index') }}"
            class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari request, requester, department..."
                class="w-full rounded-lg border-gray-300 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:w-72">

            <select name="drawing_status"
                class="w-full rounded-lg border-gray-300 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:w-52">
                <option value="">All Drawing Status</option>
                @foreach (['Not Started', 'On Progress', 'Revision', 'Done'] as $drawingStatus)
                    <option value="{{ $drawingStatus }}" {{ request('drawing_status') == $drawingStatus ? 'selected' : '' }}>
                        {{ $drawingStatus }}
                    </option>
                @endforeach
            </select>

            <button type="submit"
                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Terapkan
            </button>

            <a href="{{ route('drawing-progress.index') }}"
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
                            <th class="py-3 px-3">Drawing Status</th>
                            <th class="py-3 px-3">Assigned Drafter</th>
                            <th class="py-3 px-3">Drawing File</th>
                            <th class="py-3 px-3">Tanggal Mulai</th>
                            <th class="py-3 px-3">Tanggal Selesai</th>
                            <th class="py-3 px-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($requests as $request)
                            @php
                                $drawingStatusClass = match($request->drawing_status) {
                                    'On Progress' => 'bg-blue-100 text-blue-700',
                                    'Revision' => 'bg-orange-100 text-orange-700',
                                    'Done' => 'bg-green-100 text-green-700',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp

                            <tr class="border-b align-top hover:bg-gray-50">
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
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $drawingStatusClass }}">
                                        {{ $request->drawing_status }}
                                    </span>

                                    @if ($request->drawing_revision_note)
                                        <p class="text-xs text-orange-700 mt-2">
                                            {{ $request->drawing_revision_note }}
                                        </p>
                                    @endif

                                    @if ($request->drawing_note)
                                        <p class="text-xs text-gray-500 mt-2">
                                            {{ $request->drawing_note }}
                                        </p>
                                    @endif
                                </td>
                                <td class="py-3 px-3">
                                    {{ $request->assigned_drafter ?? '-' }}
                                </td>
                                <td class="py-3 px-3">
                                    @if ($request->drawing_file)
                                        <a href="{{ asset('storage/' . $request->drawing_file) }}" target="_blank"
                                            class="text-blue-600 hover:underline">
                                            Lihat Drawing
                                        </a>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3">
                                    {{ $request->drawing_started_at ?? '-' }}
                                </td>
                                <td class="py-3 px-3">
                                    {{ $request->drawing_finished_at ?? '-' }}
                                </td>
                                <td class="py-3 px-3">
                                    <div x-data="{ open: false }"
                                        @click.outside="open = false"
                                        @keydown.escape.window="open = false"
                                        class="relative inline-block text-left">
                                        <button type="button"
                                            @click="open = !open"
                                            title="Quick Action"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-600 shadow-sm hover:bg-gray-50">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15.5A3.5 3.5 0 1 0 12 8a3.5 3.5 0 0 0 0 7.5Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 0 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6V21a2 2 0 0 1-4 0v-.1a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1A2 2 0 0 1 4.2 17l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.6-1H3a2 2 0 0 1 0-4h.1a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9L4.2 7A2 2 0 0 1 7 4.2l.1.1a1.7 1.7 0 0 0 1.9.3 1.7 1.7 0 0 0 1-1.6V3a2 2 0 0 1 4 0v.1a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1A2 2 0 0 1 19.8 7l-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1h.1a2 2 0 0 1 0 4H21a1.7 1.7 0 0 0-1.6 1Z" />
                                            </svg>
                                        </button>

                                        <div x-show="open"
                                            x-transition
                                            @click.stop
                                            class="fixed right-6 top-20 z-50 w-80 max-w-[calc(100vw-2rem)] overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl"
                                            style="display: none;">
                                            <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                                                <div class="min-w-0">
                                                    <p class="truncate text-sm font-bold text-gray-800">{{ $request->request_number }}</p>
                                                    <p class="text-xs text-gray-500">Drawing quick action</p>
                                                </div>

                                                <button type="button"
                                                    @click="open = false"
                                                    class="rounded-md p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M4.22 4.22a.75.75 0 0 1 1.06 0L10 8.94l4.72-4.72a.75.75 0 1 1 1.06 1.06L11.06 10l4.72 4.72a.75.75 0 1 1-1.06 1.06L10 11.06l-4.72 4.72a.75.75 0 0 1-1.06-1.06L8.94 10 4.22 5.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <div class="max-h-[calc(100vh-7rem)] overflow-y-auto py-1">
                                                <a href="{{ route('ie-requests.show', $request->id) }}"
                                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                    Detail
                                                </a>

                                                @if (in_array($request->drawing_status, ['Not Started', 'Revision']))
                                                    <form action="{{ route('drawing-progress.start', $request->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')

                                                        <button type="submit"
                                                            class="block w-full px-4 py-2 text-left text-sm text-blue-600 hover:bg-blue-50">
                                                            Start
                                                        </button>
                                                    </form>
                                                @endif

                                                <div class="border-t border-gray-100 p-3">
                                                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Assign</p>
                                                    <form action="{{ route('drawing-progress.assign', $request->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')

                                                        <input type="text" name="assigned_drafter"
                                                            value="{{ old('assigned_drafter', $request->assigned_drafter) }}"
                                                            placeholder="Nama drafter"
                                                            class="w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-slate-500 focus:ring-slate-500">

                                                        <button type="submit"
                                                            class="mt-2 w-full rounded-md bg-slate-700 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-slate-800">
                                                            Simpan Assign
                                                        </button>
                                                    </form>
                                                </div>

                                                <div class="border-t border-gray-100 p-3">
                                                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Revision</p>
                                                    <form action="{{ route('drawing-progress.revision', $request->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')

                                                        <textarea
                                                            name="drawing_revision_note"
                                                            rows="2"
                                                            placeholder="Catatan revisi"
                                                            class="w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-orange-500 focus:ring-orange-500">{{ old('drawing_revision_note') }}</textarea>

                                                        <button type="submit"
                                                            class="mt-2 w-full rounded-md bg-orange-500 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-orange-600">
                                                            Kirim Revisi
                                                        </button>
                                                    </form>
                                                </div>

                                                <div class="border-t border-gray-100 p-3">
                                                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Done</p>
                                                    <form action="{{ route('drawing-progress.done', $request->id) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PATCH')

                                                        <input type="file" name="drawing_file"
                                                            class="w-full rounded-md border-gray-300 bg-white text-xs shadow-sm focus:border-green-500 focus:ring-green-500">

                                                        <textarea
                                                            name="drawing_note"
                                                            rows="2"
                                                            placeholder="Catatan drawing"
                                                            class="mt-2 w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('drawing_note') }}</textarea>

                                                        <button type="submit"
                                                            class="mt-2 w-full rounded-md bg-green-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-green-700">
                                                            Simpan Done
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="py-6 text-center text-gray-400">
                                    Belum ada request memo Approved untuk proses drawing.
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
