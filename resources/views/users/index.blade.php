<x-dashboard-layout>
    <x-page-header
        title="User Management"
        subtitle="Kelola role user untuk akses sistem Industrial Engineering"
    >
        <form method="GET" action="{{ route('users.index') }}"
            class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari name, email, atau role"
                class="w-full rounded-lg border-gray-300 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:w-72"
            >

            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Terapkan
            </button>

            <a href="{{ route('users.index') }}"
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

        <div class="bg-white rounded-xl shadow p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-gray-500">
                            <th class="py-3 px-3">No</th>
                            <th class="py-3 px-3">Name</th>
                            <th class="py-3 px-3">Email</th>
                            <th class="py-3 px-3">Role</th>
                            <th class="py-3 px-3">Position</th>
                            <th class="py-3 px-3">Registered At</th>
                            <th class="py-3 px-3">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($users as $user)
                            @php
                                $roleClass = match($user->role) {
                                    'admin' => 'bg-red-100 text-red-700',
                                    'manager' => 'bg-purple-100 text-purple-700',
                                    'customer' => 'bg-gray-100 text-gray-700',
                                    'approver' => 'bg-cyan-100 text-cyan-700',
                                    'drafter' => 'bg-blue-100 text-blue-700',
                                    'section_head' => 'bg-cyan-100 text-cyan-700',
                                    'division_head' => 'bg-indigo-100 text-indigo-700',
                                    'director' => 'bg-slate-900 text-white',
                                    'purchasing' => 'bg-yellow-100 text-yellow-700',
                                    'workshop' => 'bg-orange-100 text-orange-700',
                                    'qc' => 'bg-green-100 text-green-700',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp

                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-3">
                                    {{ $users->firstItem() + $loop->index }}
                                </td>
                                <td class="py-3 px-3 font-semibold">
                                    {{ $user->name }}
                                </td>
                                <td class="py-3 px-3">
                                    {{ $user->email }}
                                </td>
                                <td class="py-3 px-3">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $roleClass }}">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="py-3 px-3">
                                    {{ $user->position ?? '-' }}
                                </td>
                                <td class="py-3 px-3">
                                    {{ $user->created_at?->format('d M Y H:i') ?? '-' }}
                                </td>
                                <td class="py-3 px-3">
                                    <a href="{{ route('users.edit', $user->id) }}" class="text-yellow-600 hover:underline">
                                        Edit Role
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-6 text-center text-gray-400">
                                    Belum ada data user.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-dashboard-layout>
