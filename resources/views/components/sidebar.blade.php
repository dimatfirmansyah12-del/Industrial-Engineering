<aside class="hidden md:flex w-64 bg-slate-900 text-white min-h-screen shrink-0 flex-col">
    @php
        $role = auth()->user()->role ?? 'customer';
        $canSee = fn (array $roles) => $role === 'admin' || in_array($role, $roles, true);
        $badgeCount = fn (string $key) => (int) ($moduleNotificationCounts[$key] ?? 0);
        $badgeLabel = fn (int $count) => $count > 99 ? '99+' : $count;
    @endphp

    <div class="flex justify-center border-b border-slate-700 px-4 py-5">
        <img src="{{ asset('images/ie-industrial-engineering-logo-wide.jpg') }}"
            alt="Industrial Engineering"
            class="w-52 rounded-md object-contain">
    </div>

    <nav class="mt-4 px-4 space-y-2 flex-1 overflow-y-auto pb-4">
        <a href="{{ route('dashboard') }}"
            class="block px-4 py-3 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800 text-slate-300' }}">
            Dashboard
        </a>

        <a href="{{ route('ie-requests.index') }}"
            class="block px-4 py-3 rounded-lg {{ request()->routeIs('ie-requests.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800 text-slate-300' }}">
            Request Monitoring
        </a>

        <a href="{{ route('ie-requests.kanban') }}"
            class="block px-4 py-3 rounded-lg {{ request()->routeIs('ie-requests.kanban') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800 text-slate-300' }}">
            Kanban Board
        </a>

        @if ($canSee(['approver']))
            <a href="{{ route('memo-approvals.index') }}"
                class="flex items-center justify-between gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('memo-approvals.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800 text-slate-300' }}">
                <span>Memo Approval</span>

                @if ($badgeCount('memo-approvals') > 0)
                    <span class="inline-flex min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 py-0.5 text-xs font-bold text-white">
                        {{ $badgeLabel($badgeCount('memo-approvals')) }}
                    </span>
                @endif
            </a>
        @endif

        @if ($canSee(['drafter']))
            <a href="{{ route('drawing-progress.index') }}"
                class="flex items-center justify-between gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('drawing-progress.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800 text-slate-300' }}">
                <span>Drawing Progress</span>

                @if ($badgeCount('drawing-progress') > 0)
                    <span class="inline-flex min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 py-0.5 text-xs font-bold text-white">
                        {{ $badgeLabel($badgeCount('drawing-progress')) }}
                    </span>
                @endif
            </a>
        @endif

        @if ($canSee(['drafter']))
            <a href="{{ route('material-bom.index') }}"
                class="flex items-center justify-between gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('material-bom.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800 text-slate-300' }}">
                <span>Material / BOM</span>

                @if ($badgeCount('material-bom') > 0)
                    <span class="inline-flex min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 py-0.5 text-xs font-bold text-white">
                        {{ $badgeLabel($badgeCount('material-bom')) }}
                    </span>
                @endif
            </a>
        @endif

        @if ($canSee(['section_head', 'division_head', 'director']))
            <a href="{{ route('sap-approvals.index') }}"
                class="flex items-center justify-between gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('sap-approvals.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800 text-slate-300' }}">
                <span>SAP / PR Approval</span>

                @if ($badgeCount('sap-approvals') > 0)
                    <span class="inline-flex min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 py-0.5 text-xs font-bold text-white">
                        {{ $badgeLabel($badgeCount('sap-approvals')) }}
                    </span>
                @endif
            </a>
        @endif

        @if ($canSee(['purchasing']))
            <a href="{{ route('budget-pr.index') }}"
                class="flex items-center justify-between gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('budget-pr.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800 text-slate-300' }}">
                <span>Budget / PR</span>

                @if ($badgeCount('budget-pr') > 0)
                    <span class="inline-flex min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 py-0.5 text-xs font-bold text-white">
                        {{ $badgeLabel($badgeCount('budget-pr')) }}
                    </span>
                @endif
            </a>
        @endif

        @if ($canSee(['purchasing']))
            <a href="{{ route('material-arrivals.index') }}"
                class="flex items-center justify-between gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('material-arrivals.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800 text-slate-300' }}">
                <span>Material Arrival</span>

                @if ($badgeCount('material-arrivals') > 0)
                    <span class="inline-flex min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 py-0.5 text-xs font-bold text-white">
                        {{ $badgeLabel($badgeCount('material-arrivals')) }}
                    </span>
                @endif
            </a>
        @endif

        @if ($canSee(['workshop']))
            <a href="{{ route('workshop-schedules.index') }}"
                class="flex items-center justify-between gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('workshop-schedules.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800 text-slate-300' }}">
                <span>Workshop Schedule</span>

                @if ($badgeCount('workshop-schedules') > 0)
                    <span class="inline-flex min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 py-0.5 text-xs font-bold text-white">
                        {{ $badgeLabel($badgeCount('workshop-schedules')) }}
                    </span>
                @endif
            </a>
        @endif

        @if ($canSee(['workshop']))
            <a href="{{ route('workshop-progress.index') }}"
                class="flex items-center justify-between gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('workshop-progress.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800 text-slate-300' }}">
                <span>Workshop Progress</span>

                @if ($badgeCount('workshop-progress') > 0)
                    <span class="inline-flex min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 py-0.5 text-xs font-bold text-white">
                        {{ $badgeLabel($badgeCount('workshop-progress')) }}
                    </span>
                @endif
            </a>
        @endif

        @if ($canSee(['qc']))
            <a href="{{ route('final-checks.index') }}"
                class="flex items-center justify-between gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('final-checks.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800 text-slate-300' }}">
                <span>Final Check</span>

                @if ($badgeCount('final-checks') > 0)
                    <span class="inline-flex min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 py-0.5 text-xs font-bold text-white">
                        {{ $badgeLabel($badgeCount('final-checks')) }}
                    </span>
                @endif
            </a>
        @endif

        @if ($canSee(['manager']))
            <a href="{{ route('handovers.index') }}"
                class="flex items-center justify-between gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('handovers.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800 text-slate-300' }}">
                <span>Handover</span>

                @if ($badgeCount('handovers') > 0)
                    <span class="inline-flex min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 py-0.5 text-xs font-bold text-white">
                        {{ $badgeLabel($badgeCount('handovers')) }}
                    </span>
                @endif
            </a>
        @endif

        @if ($canSee(['manager']))
            <a href="{{ route('reports.index') }}"
                class="block px-4 py-3 rounded-lg {{ request()->routeIs('reports.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800 text-slate-300' }}">
                Report
            </a>
        @endif

        @if ($role === 'admin')
            <div class="pt-4 mt-4 border-t border-slate-700">
            <p class="px-4 mb-2 text-xs uppercase tracking-wide text-slate-500">
                Master Data
            </p>

            <a href="{{ route('departments.index') }}"
                class="block px-4 py-3 rounded-lg {{ request()->routeIs('departments.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800 text-slate-300' }}">
                Department
            </a>

            <a href="{{ route('line-areas.index') }}"
                class="block px-4 py-3 rounded-lg {{ request()->routeIs('line-areas.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800 text-slate-300' }}">
                Line / Area
            </a>

            <a href="{{ route('users.index') }}"
                class="block px-4 py-3 rounded-lg {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800 text-slate-300' }}">
                User Management
            </a>
            </div>
        @endif
    </nav>

</aside>
