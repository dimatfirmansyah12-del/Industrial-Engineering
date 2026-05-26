<div class="bg-white shadow-sm border-b border-gray-200 px-4 md:px-8 py-4 flex flex-col gap-4 lg:flex-row lg:justify-between lg:items-center">
    <div class="min-w-0">
        <h2 class="text-2xl font-bold text-gray-800">
            {{ $title }}
        </h2>

        @if (!empty($subtitle))
            <p class="text-sm text-gray-500">
                {{ $subtitle }}
            </p>
        @endif
    </div>

    <div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center sm:justify-end sm:gap-4 lg:w-auto">
        @if ($slot->isNotEmpty())
            <div class="flex w-full flex-wrap gap-3 sm:justify-end lg:w-auto">
                {{ $slot }}
            </div>
        @endif

        @auth
            @php
                $user = auth()->user();
                $initial = strtoupper(substr($user->name, 0, 1));
            @endphp

            @if (($memoApprovalNotificationCount ?? 0) > 0)
                <div x-data="{ open: false }"
                    @click.outside="open = false"
                    @keydown.escape.window="open = false"
                    class="relative">
                    <button type="button"
                        @click="open = !open"
                        class="relative flex h-12 w-12 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-600 shadow-sm transition hover:border-blue-200 hover:bg-blue-50"
                        title="Memo baru menunggu approval">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17H9m10-2V11a7 7 0 1 0-14 0v4l-2 2h18l-2-2Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 21h4" />
                        </svg>

                        <span class="absolute -right-1 -top-1 inline-flex min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 py-0.5 text-xs font-bold text-white ring-2 ring-white">
                            {{ $memoApprovalNotificationCount > 99 ? '99+' : $memoApprovalNotificationCount }}
                        </span>
                    </button>

                    <div x-show="open"
                        x-transition
                        @click.stop
                        class="absolute right-0 z-30 mt-2 w-80 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-lg"
                        style="display: none;">
                        <div class="border-b border-gray-100 px-4 py-3">
                            <p class="text-sm font-bold text-gray-800">Memo Baru</p>
                            <p class="text-xs text-gray-500">
                                {{ $memoApprovalNotificationCount }} memo menunggu approval
                            </p>
                        </div>

                        <div class="max-h-80 overflow-y-auto py-1">
                            @foreach (($memoApprovalNotifications ?? collect()) as $approvalStep)
                                <a href="{{ route('memo-approvals.index') }}"
                                    class="block border-b border-gray-50 px-4 py-3 transition hover:bg-blue-50">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-semibold text-gray-800">
                                                {{ $approvalStep->ieRequest?->request_number ?? '-' }}
                                            </p>
                                            <p class="truncate text-xs text-gray-500">
                                                {{ $approvalStep->ieRequest?->requester_name ?? '-' }}
                                                @if ($approvalStep->ieRequest?->department)
                                                    - {{ $approvalStep->ieRequest->department }}
                                                @endif
                                            </p>
                                            <p class="mt-1 text-xs text-blue-600">
                                                Step {{ $approvalStep->sequence }}: {{ $approvalStep->approval_label }}
                                            </p>
                                        </div>

                                        <span class="shrink-0 text-xs text-gray-400">
                                            {{ $approvalStep->updated_at?->format('d M H:i') }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <a href="{{ route('memo-approvals.index') }}"
                            class="block border-t border-gray-100 px-4 py-3 text-center text-sm font-semibold text-blue-600 hover:bg-blue-50">
                            Lihat semua memo
                        </a>
                    </div>
                </div>
            @endif

            <div x-data="{ open: false }"
                @click.outside="open = false"
                @keydown.escape.window="open = false"
                class="relative">
                <button type="button"
                    @click="open = !open"
                    class="flex items-center gap-2 rounded-full border border-gray-200 bg-white py-1.5 pl-1.5 pr-3 text-left shadow-sm transition hover:border-blue-200 hover:bg-blue-50">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">
                        {{ $initial }}
                    </span>

                    <span class="hidden min-w-0 sm:block">
                        <span class="block max-w-36 truncate text-sm font-semibold text-gray-800">{{ $user->name }}</span>
                        <span class="block text-xs text-gray-500">{{ ucfirst($user->role) }}</span>
                    </span>

                    <svg class="h-4 w-4 shrink-0 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15.5A3.5 3.5 0 1 0 12 8a3.5 3.5 0 0 0 0 7.5Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 0 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6V21a2 2 0 0 1-4 0v-.1a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1A2 2 0 0 1 4.2 17l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.6-1H3a2 2 0 0 1 0-4h.1a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9L4.2 7A2 2 0 0 1 7 4.2l.1.1a1.7 1.7 0 0 0 1.9.3 1.7 1.7 0 0 0 1-1.6V3a2 2 0 0 1 4 0v.1a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1A2 2 0 0 1 19.8 7l-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1h.1a2 2 0 0 1 0 4H21a1.7 1.7 0 0 0-1.6 1Z" />
                    </svg>

                    <svg class="h-4 w-4 shrink-0 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                    </svg>
                </button>

                <div x-show="open"
                    x-transition
                    @click.stop
                    class="absolute right-0 z-30 mt-2 w-48 overflow-hidden rounded-lg border border-gray-200 bg-white py-1 shadow-lg"
                    style="display: none;">
                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="h-4 w-4 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15.5A3.5 3.5 0 1 0 12 8a3.5 3.5 0 0 0 0 7.5Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 0 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6V21a2 2 0 0 1-4 0v-.1a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1A2 2 0 0 1 4.2 17l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.6-1H3a2 2 0 0 1 0-4h.1a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9L4.2 7A2 2 0 0 1 7 4.2l.1.1a1.7 1.7 0 0 0 1.9.3 1.7 1.7 0 0 0 1-1.6V3a2 2 0 0 1 4 0v.1a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1A2 2 0 0 1 19.8 7l-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1h.1a2 2 0 0 1 0 4H21a1.7 1.7 0 0 0-1.6 1Z" />
                        </svg>
                        Profile
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 17l5-5-5-5" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12H9" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 5H5v14h8" />
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        @endauth
    </div>
</div>
