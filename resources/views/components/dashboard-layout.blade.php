<x-app-layout>
    <div class="min-h-screen bg-gray-100 flex flex-col md:flex-row">
        @php
            $role = auth()->user()->role ?? 'customer';
            $canSeeMobile = fn (array $roles) => $role === 'admin' || in_array($role, $roles, true);
            $badgeCount = fn (string $key) => (int) ($moduleNotificationCounts[$key] ?? 0);
            $badgeLabel = fn (int $count) => $count > 99 ? '99+' : $count;
        @endphp

        <div class="md:hidden bg-slate-900 text-white border-b border-slate-700">
            <div class="flex px-4 pt-4 pb-2">
                <img src="{{ asset('images/ie-industrial-engineering-logo-wide.jpg') }}"
                    alt="Industrial Engineering"
                    class="w-40 rounded-md object-contain">
            </div>

            <nav class="flex gap-2 overflow-x-auto px-4 pb-4">
                <a href="{{ route('dashboard') }}"
                    class="whitespace-nowrap rounded-lg px-4 py-2 text-sm {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white font-medium' : 'bg-slate-800 text-slate-300' }}">
                    Dashboard
                </a>

                <a href="{{ route('ie-requests.index') }}"
                    class="whitespace-nowrap rounded-lg px-4 py-2 text-sm {{ request()->routeIs('ie-requests.index', 'ie-requests.show', 'ie-requests.create', 'ie-requests.edit') ? 'bg-blue-600 text-white font-medium' : 'bg-slate-800 text-slate-300' }}">
                    Request
                </a>

                <a href="{{ route('ie-requests.kanban') }}"
                    class="whitespace-nowrap rounded-lg px-4 py-2 text-sm {{ request()->routeIs('ie-requests.kanban') ? 'bg-blue-600 text-white font-medium' : 'bg-slate-800 text-slate-300' }}">
                    Kanban
                </a>

                @if ($canSeeMobile(['approver']))
                    <a href="{{ route('memo-approvals.index') }}"
                        class="flex whitespace-nowrap rounded-lg px-4 py-2 text-sm {{ request()->routeIs('memo-approvals.*') ? 'bg-blue-600 text-white font-medium' : 'bg-slate-800 text-slate-300' }}">
                        Memo

                        @if ($badgeCount('memo-approvals') > 0)
                            <span class="ml-2 inline-flex min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 py-0.5 text-xs font-bold text-white">
                                {{ $badgeLabel($badgeCount('memo-approvals')) }}
                            </span>
                        @endif
                    </a>
                @endif

                @if ($canSeeMobile(['manager']))
                    <a href="{{ route('reports.index') }}"
                        class="whitespace-nowrap rounded-lg px-4 py-2 text-sm {{ request()->routeIs('reports.*') ? 'bg-blue-600 text-white font-medium' : 'bg-slate-800 text-slate-300' }}">
                        Report
                    </a>
                @endif
            </nav>
        </div>

        <x-sidebar />

        <main class="flex-1 min-w-0 overflow-x-hidden">
            {{ $slot }}
        </main>

    </div>
</x-app-layout>
