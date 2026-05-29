<aside id="logo-sidebar"
    class="fixed top-0 left-0 z-40 h-screen w-72 -translate-x-full border-r border-gray-200 bg-white transition-transform lg:translate-x-0"
    aria-label="Sidebar">

    @php
        $itemBase = 'group flex items-center rounded-xl px-4 py-3 text-sm font-medium transition';
        $itemInactive = 'text-gray-700 hover:bg-gray-100';
        $itemActive = 'bg-gray-900 text-white shadow-sm';
        $iconBase = 'h-5 w-5 shrink-0';
    @endphp

    <div class="flex h-full flex-col overflow-y-auto px-4 py-5 sm:px-5 sm:py-6">
        {{-- Brand --}}
        <a href="{{ route('dashboard') }}" class="mb-6 flex items-center gap-3 sm:mb-8">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-600 text-white shadow-sm">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 8c-2.21 0-4 1.79-4 4m8 0a4 4 0 10-8 0m8 0c0 3.314-1.79 6-4 6s-4-2.686-4-6m8 0H8" />
                </svg>
            </div>

            <div class="min-w-0">
                <div class="truncate text-lg font-semibold tracking-tight text-gray-900">Kakeibo</div>
                <div class="truncate text-xs text-gray-500">Personal Finance Journal</div>
            </div>
        </a>

        {{-- Banner --}}
        <div class="mb-6 rounded-2xl border border-emerald-100 bg-emerald-50 p-4">
            <p class="text-xs font-medium uppercase tracking-[0.16em] text-emerald-700">Bulan ini</p>
            <p class="mt-2 text-sm leading-6 text-gray-700">
                Catat uangmu. Pahami kebiasaanmu. Jangan biarkan saldo hilang dengan penuh percaya diri.
            </p>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto">
            <div class="space-y-6">
                {{-- OVERVIEW --}}
                <div>
                    <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-400">
                        Overview
                    </p>

                    <div class="space-y-1.5">
                        <a href="{{ route('dashboard') }}"
                            class="{{ $itemBase }} {{ request()->routeIs('dashboard') ? $itemActive : $itemInactive }}">
                            <svg class="{{ $iconBase }} {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-500 group-hover:text-gray-700' }}"
                                fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l8-8 8 8M5 10v10h14V10" />
                            </svg>
                            <span class="ml-3 truncate">Dashboard</span>
                        </a>
                    </div>
                </div>

                {{-- CASHFLOW --}}
                <div>
                    <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-400">
                        Cashflow
                    </p>

                    <div class="space-y-1.5">
                        <a href="{{ route('transactions.index') }}"
                            class="{{ $itemBase }} {{ request()->routeIs('transactions.*') ? $itemActive : $itemInactive }}">
                            <svg class="{{ $iconBase }} {{ request()->routeIs('transactions.*') ? 'text-white' : 'text-gray-500 group-hover:text-gray-700' }}"
                                fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 9V7a5 5 0 00-10 0v2m-2 0h14v10H5V9z" />
                            </svg>
                            <span class="ml-3 truncate">Transaksi</span>
                        </a>

                        <a href="{{ route('financial-accounts.index') }}"
                            class="{{ $itemBase }} {{ request()->routeIs('financial-accounts.*') ? $itemActive : $itemInactive }}">
                            <svg class="{{ $iconBase }} {{ request()->routeIs('financial-accounts.*') ? 'text-white' : 'text-gray-500 group-hover:text-gray-700' }}"
                                fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 7h18M5 7l1-2h12l1 2m-1 0v10a2 2 0 01-2 2H8a2 2 0 01-2-2V7" />
                            </svg>
                            <span class="ml-3 truncate">Rekening</span>
                        </a>

                        <a href="{{ route('account-transfers.index') }}"
                            class="{{ $itemBase }} {{ request()->routeIs('account-transfers.*') ? $itemActive : $itemInactive }}">
                            <svg class="{{ $iconBase }} {{ request()->routeIs('account-transfers.*') ? 'text-white' : 'text-gray-500 group-hover:text-gray-700' }}"
                                fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3M7 16l-4-4m0 0l4-4m-4 4h18" />
                            </svg>
                            <span class="ml-3 truncate">Pindah Dana</span>
                        </a>

                        <a href="{{ route('recurring-transactions.index') }}"
                            class="{{ $itemBase }} {{ request()->routeIs('recurring-transactions.*') ? $itemActive : $itemInactive }}">
                            <svg class="{{ $iconBase }} {{ request()->routeIs('recurring-transactions.*') ? 'text-white' : 'text-gray-500 group-hover:text-gray-700' }}"
                                fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 4v6h6M20 20v-6h-6M20 8a8 8 0 00-14.9-3M4 16a8 8 0 0014.9 3" />
                            </svg>
                            <span class="ml-3 truncate">Recurring</span>
                        </a>
                    </div>
                </div>

                {{-- PLANNING --}}
                <div>
                    <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-400">
                        Planning
                    </p>

                    <div class="space-y-1.5">
                        <a href="{{ route('categories.index') }}"
                            class="{{ $itemBase }} {{ request()->routeIs('categories.*') ? $itemActive : $itemInactive }}">
                            <svg class="{{ $iconBase }} {{ request()->routeIs('categories.*') ? 'text-white' : 'text-gray-500 group-hover:text-gray-700' }}"
                                fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10M7 12h10M7 17h6" />
                            </svg>
                            <span class="ml-3 truncate">Kategori</span>
                        </a>

                        <a href="{{ route('budgets.index') }}"
                            class="{{ $itemBase }} {{ request()->routeIs('budgets.*') ? $itemActive : $itemInactive }}">
                            <svg class="{{ $iconBase }} {{ request()->routeIs('budgets.*') ? 'text-white' : 'text-gray-500 group-hover:text-gray-700' }}"
                                fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h10" />
                            </svg>
                            <span class="ml-3 truncate">Anggaran</span>
                        </a>
                    </div>
                </div>

                {{-- GOALS --}}
                <div>
                    <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-400">
                        Goals
                    </p>

                    <div class="space-y-1.5">
                        <a href="{{ route('saving-targets.index') }}"
                            class="{{ $itemBase }} {{ request()->routeIs('saving-targets.*') ? $itemActive : $itemInactive }}">
                            <svg class="{{ $iconBase }} {{ request()->routeIs('saving-targets.*') ? 'text-white' : 'text-gray-500 group-hover:text-gray-700' }}"
                                fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                            </svg>
                            <span class="ml-3 truncate">Target Tabungan</span>
                        </a>

                        <a href="{{ route('saving-contributions.index') }}"
                            class="{{ $itemBase }} {{ request()->routeIs('saving-contributions.*') ? $itemActive : $itemInactive }}">
                            <svg class="{{ $iconBase }} {{ request()->routeIs('saving-contributions.*') ? 'text-white' : 'text-gray-500 group-hover:text-gray-700' }}"
                                fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            <span class="ml-3 truncate">Kontribusi Saving</span>
                        </a>
                    </div>
                </div>

                {{-- REVIEW --}}
                <div>
                    <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-400">
                        Review
                    </p>

                    <div class="space-y-1.5">
                        <a href="{{ route('monthly-reflections.index') }}"
                            class="{{ $itemBase }} {{ request()->routeIs('monthly-reflections.*') ? $itemActive : $itemInactive }}">
                            <svg class="{{ $iconBase }} {{ request()->routeIs('monthly-reflections.*') ? 'text-white' : 'text-gray-500 group-hover:text-gray-700' }}"
                                fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 7h8m-8 5h8m-8 5h5M6 3h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2z" />
                            </svg>
                            <span class="ml-3 truncate">Refleksi Bulanan</span>
                        </a>

                        <a href="{{ route('monthly-closings.index') }}"
                            class="{{ $itemBase }} {{ request()->routeIs('monthly-closings.*') ? $itemActive : $itemInactive }}">
                            <svg class="{{ $iconBase }} {{ request()->routeIs('monthly-closings.*') ? 'text-white' : 'text-gray-500 group-hover:text-gray-700' }}"
                                fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 7V3m8 4V3M4 11h16M5 19h14a1 1 0 001-1v-7H4v7a1 1 0 001 1z" />
                            </svg>
                            <span class="ml-3 truncate">Monthly Closing</span>
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        {{-- Footer --}}
        {{-- <div class="mt-6 border-t border-gray-200 pt-5">
            <div class="rounded-2xl bg-gray-900 p-4 text-white">
                <p class="text-xs uppercase tracking-[0.16em] text-gray-300">Prinsip Kakeibo</p>
                <p class="mt-2 text-sm leading-6 text-gray-100">
                    Bukan cuma soal berapa uangmu. Tapi kenapa uangmu pergi dengan tenang seolah tidak bersalah.
                </p>
            </div>
        </div> --}}
    </div>
</aside>
