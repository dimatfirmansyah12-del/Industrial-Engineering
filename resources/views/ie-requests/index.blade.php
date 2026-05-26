<x-dashboard-layout>
            @php
                $userRole = auth()->user()?->role;
                $canCreateRequest = in_array($userRole, ['admin', 'customer'], true);
                $canManageRequest = $userRole === 'admin';
                $requestStatuses = \App\Services\RequestWorkflow::STATUSES;
            @endphp

            <x-page-header
                title="Request Monitoring"
                subtitle="Daftar request Industrial Engineering"
            >
                <div class="flex w-full flex-col gap-2 2xl:w-auto 2xl:flex-row 2xl:items-center 2xl:justify-end">
                    <div class="flex flex-wrap justify-end gap-2">
                        @if (in_array(auth()->user()?->role, ['admin', 'manager'], true))
                            <a href="{{ route('ie-requests.export', request()->query()) }}"
                                class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                                Export CSV
                            </a>
                        @endif

                        <a href="{{ route('ie-requests.kanban') }}"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                            Kanban Board
                        </a>

                        @if ($canCreateRequest)
                            <a href="{{ route('ie-requests.create') }}"
                                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                + Tambah Request
                            </a>
                        @endif
                    </div>

                    <form method="GET" action="{{ route('ie-requests.index') }}"
                        class="flex w-full flex-wrap items-center justify-end gap-2 2xl:w-auto">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari no request, requester, department..."
                            class="w-full rounded-lg border-gray-300 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:w-56">

                        <select name="status"
                            class="w-full rounded-lg border-gray-300 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:w-44">
                            <option value="">All Status</option>
                            @foreach ($requestStatuses as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>

                        <select name="priority"
                            class="w-full rounded-lg border-gray-300 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:w-36">
                            <option value="">All Priority</option>
                            @foreach (['Low', 'Medium', 'High', 'Urgent'] as $priority)
                                <option value="{{ $priority }}" {{ request('priority') == $priority ? 'selected' : '' }}>
                                    {{ $priority }}
                                </option>
                            @endforeach
                        </select>

                        <select name="deadline"
                            class="w-full rounded-lg border-gray-300 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:w-36">
                            <option value="">All Deadline</option>
                            <option value="delay" {{ request('deadline') == 'delay' ? 'selected' : '' }}>Delay</option>
                            <option value="due_soon" {{ request('deadline') == 'due_soon' ? 'selected' : '' }}>Due Soon</option>
                        </select>

                        <button type="submit"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                            Terapkan
                        </button>

                        <a href="{{ route('ie-requests.index') }}"
                            class="rounded-lg bg-gray-200 px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-300">
                            Reset
                        </a>
                    </form>
                </div>
            </x-page-header>

            <div class="p-8">
                @if (session('success'))
                    <div class="mb-4 bg-green-100 text-green-700 px-4 py-3 rounded-lg">
                        {{ session('success') }}
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
                                    <th class="py-3 px-3">Tanggal</th>
                                    <th class="py-3 px-3">Requester</th>
                                    <th class="py-3 px-3">Department</th>
                                    <th class="py-3 px-3">Jenis</th>
                                    <th class="py-3 px-3">Priority</th>
                                    <th class="py-3 px-3">Status</th>
                                    <th class="py-3 px-3">Target</th>
                                    <th class="py-3 px-3">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($requests as $request)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 px-3">
                                            {{ $requests->firstItem() + $loop->index }}
                                        </td>

                                        <td class="py-3 px-3 font-semibold">
                                            {{ $request->request_number }}
                                        </td>

                                        <td class="py-3 px-3">
                                            {{ $request->request_date }}
                                        </td>

                                        <td class="py-3 px-3">
                                            {{ $request->requester_name }}
                                        </td>

                                        <td class="py-3 px-3">
                                            {{ $request->department }}
                                        </td>

                                        <td class="py-3 px-3">
                                            <div class="min-w-[140px]">
                                                <p class="font-semibold text-gray-800">{{ $request->request_type }}</p>
                                                <p class="text-xs text-gray-500">Qty: {{ $request->request_qty ?? 1 }}</p>
                                            </div>
                                        </td>

                                        <td class="py-3 px-3">
                                            <x-priority-badge :priority="$request->priority" />
                                        </td>

                                        <td class="py-3 px-3">
                                            <x-status-badge :status="$request->status" />
                                        </td>

                                        <!-- Target Date + Delay -->
                                        <td class="py-3 px-3">
                                            <div class="flex flex-col gap-1">
                                                <span>{{ $request->target_date?->format('Y-m-d') ?? '-' }}</span>

                                                @if ($request->is_delay)
                                                    <span class="w-fit px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                        Delay {{ $request->delay_days }} hari
                                                    </span>
                                                @elseif ($request->is_due_soon)
                                                    <span class="w-fit px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                                        Due Soon {{ $request->due_soon_days }} hari lagi
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Action -->
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
                                                    class="absolute right-0 z-30 mt-2 w-36 overflow-hidden rounded-lg border border-gray-200 bg-white py-1 shadow-lg"
                                                    style="display: none;">
                                                    <a href="{{ route('ie-requests.show', $request->id) }}"
                                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                        Detail
                                                    </a>

                                                    @if ($canManageRequest)
                                                        <a href="{{ route('ie-requests.edit', $request->id) }}"
                                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                            Edit
                                                        </a>

                                                        <form action="{{ route('ie-requests.destroy', $request->id) }}" method="POST"
                                                            onsubmit="return confirm('Yakin ingin menghapus request ini?')">
                                                            @csrf
                                                            @method('DELETE')

                                                            <button type="submit"
                                                                class="block w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50">
                                                                Hapus
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="py-6 text-center text-gray-400">
                                            Belum ada data request.
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
