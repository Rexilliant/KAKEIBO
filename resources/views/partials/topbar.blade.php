<header class="sticky top-0 z-30 border-b border-gray-200 bg-white/90 backdrop-blur">
    <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-4 sm:px-6 lg:px-8">
        <div class="flex min-w-0 items-center gap-3">
            <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar"
                type="button"
                class="inline-flex items-center rounded-xl border border-gray-200 bg-white p-2 text-gray-600 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 lg:hidden">
                <span class="sr-only">Open sidebar</span>
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path clip-rule="evenodd" fill-rule="evenodd"
                        d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 5A.75.75 0 012.75 9h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 9.75zm0 5a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75a.75.75 0 01-.75-.75z" />
                </svg>
            </button>

            <div class="min-w-0">
                <h1 class="truncate text-base font-semibold tracking-tight text-gray-900 sm:text-lg">
                    @yield('page_title', 'Dashboard')
                </h1>
                <p class="hidden truncate text-sm text-gray-500 sm:block">
                    @yield('page_subtitle', 'Kelola uang dengan tenang dan waras.')
                </p>
            </div>
        </div>

        <div class="flex w-full items-center justify-between gap-3 sm:w-auto sm:justify-end">
            <a href="{{ route('transactions.create') }}"
                class="inline-flex rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700">
                + Tambah
            </a>

            <div
                class="flex min-w-0 items-center gap-3 rounded-2xl border border-gray-200 bg-white px-3 py-2 shadow-sm">
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-900 text-sm font-semibold text-white">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <div class="truncate text-sm font-medium text-gray-900">{{ Auth::user()->name }}</div>
                    <div class="hidden truncate text-xs text-gray-500 sm:block">{{ Auth::user()->email }}</div>
                </div>
            </div>
        </div>
    </div>
</header>
