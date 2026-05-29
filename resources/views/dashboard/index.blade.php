@extends('layouts.app')

@section('page_title', 'Dashboard')
@section('page_subtitle', 'Ringkasan finansial yang lebih tajam, cepat dibaca, dan siap dipakai ambil keputusan.')

@section('content')
    @php
        $formatDelta = function ($value) {
            $prefix = $value > 0 ? '+' : '';
            return $prefix . 'Rp ' . number_format($value, 0, ',', '.');
        };

        $formatPercent = function ($value) {
            if ($value === null) {
                return '-';
            }
            $prefix = $value > 0 ? '+' : '';
            return $prefix . number_format($value, 0) . '%';
        };
    @endphp

    <div id="skeleton" class="space-y-4">
        <div class="h-28 animate-pulse rounded-3xl bg-gray-200"></div>
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="h-28 animate-pulse rounded-3xl bg-gray-200"></div>
            <div class="h-28 animate-pulse rounded-3xl bg-gray-200"></div>
            <div class="h-28 animate-pulse rounded-3xl bg-gray-200"></div>
            <div class="h-28 animate-pulse rounded-3xl bg-gray-200"></div>
        </div>
        <div class="grid gap-4 xl:grid-cols-2">
            <div class="h-72 animate-pulse rounded-3xl bg-gray-200"></div>
            <div class="h-72 animate-pulse rounded-3xl bg-gray-200"></div>
        </div>
    </div>

    <div id="dashboardContent" class="hidden">
        <section class="mb-6 sm:hidden">
            <div class="grid gap-3">
                <div class="rounded-2xl bg-gray-900 p-4 text-white shadow-sm">
                    <p class="text-xs text-gray-300">Net Cashflow</p>
                    <h3 class="mt-1 text-lg font-semibold">Rp {{ number_format($netCashflow, 0, ',', '.') }}</h3>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-2xl bg-white p-4 shadow-sm">
                        <p class="text-xs text-gray-500">Income</p>
                        <p class="mt-1 text-sm font-semibold text-emerald-600">
                            Rp {{ number_format($income, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-white p-4 shadow-sm">
                        <p class="text-xs text-gray-500">Expense</p>
                        <p class="mt-1 text-sm font-semibold text-red-600">
                            Rp {{ number_format($expense, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <div id="print-area">
            <section class="mb-6">
                <div
                    class="overflow-hidden rounded-3xl bg-gradient-to-r from-gray-950 via-gray-900 to-emerald-700 p-5 text-white shadow-sm sm:p-6 lg:p-8">
                    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.5fr)_420px] xl:items-center">
                        <div class="min-w-0">
                            <p class="text-xs font-medium uppercase tracking-[0.22em] text-emerald-200">
                                Professional Finance Overview
                            </p>

                            <h2 class="mt-3 text-2xl font-semibold tracking-tight sm:text-3xl lg:text-4xl">
                                Halo, {{ Auth::user()->name }}.
                            </h2>

                            <p class="mt-3 max-w-2xl text-sm leading-7 text-gray-200 sm:text-base">
                                Dashboard ini fokus bantu lu ngerti kondisi uang dengan cepat:
                                cashflow, alokasi, budget pressure, dan pola pengeluaran dominan.
                            </p>
                        </div>

                        <div class="rounded-3xl border border-white/10 bg-white/10 p-4 backdrop-blur sm:p-5">
                            <form method="GET" action="{{ route('dashboard') }}" class="space-y-4">
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <label
                                            class="mb-2 block text-xs font-medium uppercase tracking-[0.14em] text-gray-200">
                                            Bulan
                                        </label>
                                        <select name="month"
                                            class="block w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm text-white focus:border-white/40 focus:outline-none">
                                            @for ($m = 1; $m <= 12; $m++)
                                                <option value="{{ $m }}"
                                                    {{ (string) $selectedMonth === (string) $m ? 'selected' : '' }}
                                                    class="text-gray-900">
                                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div>
                                        <label
                                            class="mb-2 block text-xs font-medium uppercase tracking-[0.14em] text-gray-200">
                                            Tahun
                                        </label>
                                        <input type="number" name="year" value="{{ $selectedYear }}"
                                            class="block w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm text-white placeholder:text-gray-300 focus:border-white/40 focus:outline-none">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                    <button type="submit"
                                        class="inline-flex w-full items-center justify-center rounded-xl bg-white px-4 py-3 text-sm font-medium text-gray-900 transition hover:bg-gray-100">
                                        Terapkan
                                    </button>

                                    <a href="{{ route('dashboard.export.csv', ['month' => $selectedMonth, 'year' => $selectedYear]) }}"
                                        class="inline-flex w-full items-center justify-center rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-center text-sm font-medium text-white transition hover:bg-white/20">
                                        Export
                                    </a>

                                    <button type="button" onclick="window.print()"
                                        class="inline-flex w-full items-center justify-center rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm font-medium text-white transition hover:bg-white/20">
                                        Print
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

            @if ($balanceWarning === 'danger')
                <section class="mb-6">
                    <div class="rounded-3xl border border-red-200 bg-red-50 p-5 shadow-sm">
                        <h3 class="text-lg font-semibold text-red-800">Warning: Alokasi Melebihi Saldo</h3>
                        <p class="mt-2 text-sm leading-6 text-red-700">
                            Dana yang sudah dialokasikan ke target lebih besar daripada total saldo rekening aktif.
                        </p>
                    </div>
                </section>
            @elseif ($balanceWarning === 'empty')
                <section class="mb-6">
                    <div class="rounded-3xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                        <h3 class="text-lg font-semibold text-amber-800">Warning: Uang Bebas Habis</h3>
                        <p class="mt-2 text-sm leading-6 text-amber-700">
                            Semua saldo rekening aktif sudah punya tujuan. Lu gak benar-benar punya ruang bebas.
                        </p>
                    </div>
                </section>
            @elseif ($balanceWarning === 'low')
                <section class="mb-6">
                    <div class="rounded-3xl border border-yellow-200 bg-yellow-50 p-5 shadow-sm">
                        <h3 class="text-lg font-semibold text-yellow-800">Warning: Uang Bebas Menipis</h3>
                        <p class="mt-2 text-sm leading-6 text-yellow-700">
                            Available balance tinggal sedikit dibanding total saldo.
                        </p>
                    </div>
                </section>
            @endif

            @if (!empty($budgetWarnings) && count($budgetWarnings) > 0)
                <section class="mb-6">
                    <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold tracking-tight text-gray-900">Budget Alerts</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Kategori yang hampir habis atau sudah lewat batas anggaran di periode ini.
                            </p>
                        </div>

                        <div class="space-y-3">
                            @foreach ($budgetWarnings as $warning)
                                @if ($warning['status'] === 'over')
                                    <div class="rounded-2xl border border-red-200 bg-red-50 p-4">
                                        <p class="font-semibold text-red-800">{{ $warning['category'] }} melewati budget
                                        </p>
                                        <p class="mt-1 text-sm text-red-700">
                                            Budget: Rp {{ number_format($warning['budget'], 0, ',', '.') }} ·
                                            Terpakai: Rp {{ number_format($warning['spent'], 0, ',', '.') }} ·
                                            Lebih: Rp {{ number_format($warning['over'], 0, ',', '.') }}
                                        </p>
                                    </div>
                                @else
                                    <div class="rounded-2xl border border-yellow-200 bg-yellow-50 p-4">
                                        <p class="font-semibold text-yellow-800">{{ $warning['category'] }} hampir habis
                                        </p>
                                        <p class="mt-1 text-sm text-yellow-700">
                                            Budget: Rp {{ number_format($warning['budget'], 0, ',', '.') }} ·
                                            Terpakai: Rp {{ number_format($warning['spent'], 0, ',', '.') }}
                                        </p>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif

            <section class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Total Pemasukan</p>
                    <h3 class="mt-4 break-words text-2xl font-semibold tracking-tight text-gray-900">
                        Rp {{ number_format($income, 0, ',', '.') }}
                    </h3>
                    <p class="mt-2 text-sm {{ $incomeDelta >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                        {{ $formatDelta($incomeDelta) }} · {{ $formatPercent($incomeDeltaPercent) }}
                    </p>
                    <p class="mt-1 text-xs text-gray-500">vs bulan lalu</p>
                </div>

                <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Total Pengeluaran</p>
                    <h3 class="mt-4 break-words text-2xl font-semibold tracking-tight text-gray-900">
                        Rp {{ number_format($expense, 0, ',', '.') }}
                    </h3>
                    <p class="mt-2 text-sm {{ $expenseDelta <= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                        {{ $formatDelta($expenseDelta) }} · {{ $formatPercent($expenseDeltaPercent) }}
                    </p>
                    <p class="mt-1 text-xs text-gray-500">vs bulan lalu</p>
                </div>

                <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Net Cashflow</p>
                    <h3
                        class="mt-4 break-words text-2xl font-semibold tracking-tight {{ $netCashflow >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                        Rp {{ number_format($netCashflow, 0, ',', '.') }}
                    </h3>
                    <p class="mt-2 text-sm {{ $netCashflowDelta >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                        {{ $formatDelta($netCashflowDelta) }} · {{ $formatPercent($netCashflowDeltaPercent) }}
                    </p>
                    <p class="mt-1 text-xs text-gray-500">vs bulan lalu</p>
                </div>

                <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Jumlah Transaksi</p>
                    <h3 class="mt-4 text-2xl font-semibold tracking-tight text-gray-900">
                        {{ $transactionCount }}
                    </h3>
                    <p class="mt-2 text-sm text-gray-500">Total transaksi pada periode terpilih.</p>
                </div>
            </section>

            <section class="mb-6 grid gap-4 lg:grid-cols-2 xl:grid-cols-3">
                <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <h3 class="text-lg font-semibold tracking-tight text-gray-900">Financial Health Score</h3>
                    <p class="mt-1 text-sm text-gray-500">Skor ringkas kondisi finansial di periode ini.</p>

                    <div class="mt-5">
                        <div class="flex items-end gap-3">
                            <div
                                class="text-4xl font-semibold {{ $financialHealthScore >= 80 ? 'text-emerald-600' : ($financialHealthScore >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $financialHealthScore }}
                            </div>
                            <div class="pb-1 text-sm font-medium text-gray-500">{{ $financialHealthLabel }}</div>
                        </div>

                        <div class="mt-4 h-3 w-full rounded-full bg-gray-200">
                            <div class="h-3 rounded-full {{ $financialHealthScore >= 80 ? 'bg-emerald-600' : ($financialHealthScore >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                style="width: {{ $financialHealthScore }}%">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <h3 class="text-lg font-semibold tracking-tight text-gray-900">Forecast Akhir Bulan</h3>
                    <p class="mt-1 text-sm text-gray-500">Proyeksi sederhana kalau pola sekarang lanjut terus.</p>

                    <div class="mt-5 grid gap-3">
                        <div class="rounded-2xl bg-gray-50 p-4">
                            <div class="text-sm text-gray-500">Forecast Income</div>
                            <div class="mt-1 font-semibold text-gray-900">
                                Rp {{ number_format($forecastIncome, 0, ',', '.') }}
                            </div>
                        </div>

                        <div class="rounded-2xl bg-gray-50 p-4">
                            <div class="text-sm text-gray-500">Forecast Expense</div>
                            <div class="mt-1 font-semibold text-gray-900">
                                Rp {{ number_format($forecastExpense, 0, ',', '.') }}
                            </div>
                        </div>

                        <div class="rounded-2xl bg-gray-50 p-4">
                            <div class="text-sm text-gray-500">Forecast Net</div>
                            <div
                                class="mt-1 font-semibold {{ $forecastNetCashflow >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                Rp {{ number_format($forecastNetCashflow, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6 lg:col-span-2 xl:col-span-1">
                    <h3 class="text-lg font-semibold tracking-tight text-gray-900">Quick Health Notes</h3>
                    <p class="mt-1 text-sm text-gray-500">Ringkasan cepat buat bantu baca dashboard.</p>

                    <div class="mt-5 grid gap-3">
                        <div class="rounded-2xl bg-gray-50 p-4 text-sm text-gray-700">
                            Burn rate: <span class="font-semibold">{{ number_format($burnRate, 0) }}%</span>
                        </div>

                        <div class="rounded-2xl bg-gray-50 p-4 text-sm text-gray-700">
                            Saving rate: <span class="font-semibold">{{ number_format($savingRate, 0) }}%</span>
                        </div>

                        <div class="rounded-2xl bg-gray-50 p-4 text-sm text-gray-700">
                            Available balance:
                            <span class="font-semibold">Rp {{ number_format($availableBalance, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Saldo Rekening Aktif</p>
                    <h3 class="mt-3 break-words text-2xl font-semibold tracking-tight text-gray-900">
                        Rp {{ number_format($totalBalance, 0, ',', '.') }}
                    </h3>
                </div>

                <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Dana Teralokasi</p>
                    <h3 class="mt-3 break-words text-2xl font-semibold tracking-tight text-gray-900">
                        Rp {{ number_format($allocatedBalance, 0, ',', '.') }}
                    </h3>
                </div>

                <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Available Balance</p>
                    <h3
                        class="mt-3 break-words text-2xl font-semibold tracking-tight {{ $availableBalance < 0 ? 'text-red-600' : 'text-gray-900' }}">
                        Rp {{ number_format($availableBalance, 0, ',', '.') }}
                    </h3>
                </div>

                <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Allocation Health</p>
                    <h3 class="mt-3 text-2xl font-semibold tracking-tight text-gray-900">
                        {{ number_format($allocationPercentage, 0) }}%
                    </h3>
                </div>
            </section>

            <section class="mb-6 grid gap-6 xl:grid-cols-2">
                <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <h3 class="text-lg font-semibold tracking-tight text-gray-900">Income vs Expense</h3>
                    <p class="mt-1 text-sm text-gray-500">Perbandingan cashflow periode terpilih.</p>
                    <div class="mt-5 h-[260px]">
                        <canvas id="cashflowChart"></canvas>
                    </div>
                </div>

                <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <h3 class="text-lg font-semibold tracking-tight text-gray-900">Top Spending Categories</h3>
                    <p class="mt-1 text-sm text-gray-500">Kategori pengeluaran terbesar.</p>
                    <div class="mt-5 h-[260px]">
                        <canvas id="expenseCategoryChart"></canvas>
                    </div>
                </div>
            </section>

            <section class="mb-6">
                <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <h3 class="text-lg font-semibold tracking-tight text-gray-900">6 Month Trend</h3>
                    <p class="mt-1 text-sm text-gray-500">Tren pemasukan, pengeluaran, dan net cashflow selama 6 bulan
                        terakhir.</p>
                    <div class="mt-5 h-[320px]">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </section>

            <section class="mb-6">
                <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold tracking-tight text-gray-900">Budget Utilization</h3>
                            <p class="mt-1 text-sm text-gray-500">Kategori budget dengan tekanan tertinggi di periode ini.
                            </p>
                        </div>
                        <a href="{{ route('budgets.index') }}"
                            class="text-sm font-medium text-emerald-700 hover:text-emerald-800">
                            Lihat budgets
                        </a>
                    </div>

                    <div class="mt-5 grid gap-4 lg:grid-cols-2 xl:grid-cols-3">
                        @forelse ($budgetUtilizations as $budgetItem)
                            <div class="rounded-2xl border border-gray-200 p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <h4 class="font-medium text-gray-900">{{ $budgetItem['category'] }}</h4>

                                    @if ($budgetItem['status'] === 'over')
                                        <span
                                            class="rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-700">Over</span>
                                    @elseif ($budgetItem['status'] === 'warning')
                                        <span
                                            class="rounded-full bg-yellow-50 px-3 py-1 text-xs font-medium text-yellow-700">Warning</span>
                                    @else
                                        <span
                                            class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">Safe</span>
                                    @endif
                                </div>

                                <div class="mt-4 space-y-2 text-sm text-gray-600">
                                    <div>
                                        Budget:
                                        <span class="font-medium text-gray-900">
                                            Rp {{ number_format($budgetItem['budget'], 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div>
                                        Terpakai:
                                        <span class="font-medium text-gray-900">
                                            Rp {{ number_format($budgetItem['spent'], 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div>
                                        Sisa:
                                        <span
                                            class="font-medium {{ $budgetItem['remaining'] < 0 ? 'text-red-600' : 'text-gray-900' }}">
                                            Rp {{ number_format($budgetItem['remaining'], 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-4 h-2.5 w-full rounded-full bg-gray-200">
                                    <div class="h-2.5 rounded-full {{ $budgetItem['status'] === 'over' ? 'bg-red-500' : ($budgetItem['status'] === 'warning' ? 'bg-yellow-500' : 'bg-emerald-600') }}"
                                        style="width: {{ min(100, $budgetItem['progress']) }}%">
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div
                                class="rounded-2xl border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500 lg:col-span-2 xl:col-span-3">
                                Belum ada budget utilization untuk periode ini.
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="mb-6 grid gap-6 xl:grid-cols-3">
                <div class="xl:col-span-2 rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <h3 class="text-lg font-semibold tracking-tight text-gray-900">Smart Insights</h3>
                    <p class="mt-1 text-sm text-gray-500">Ringkasan otomatis buat bantu lu baca kondisi periode ini.</p>

                    <div class="mt-5 space-y-3">
                        @forelse ($smartInsights as $insight)
                            <div class="rounded-2xl bg-gray-50 p-4 text-sm leading-6 text-gray-700">
                                {{ $insight }}
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-gray-300 p-4 text-sm text-gray-500">
                                Belum ada insight untuk periode ini.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <h3 class="text-lg font-semibold tracking-tight text-gray-900">Top Accounts</h3>
                    <p class="mt-1 text-sm text-gray-500">Rekening paling sering dipakai di periode ini.</p>

                    <div class="mt-5 space-y-3">
                        @forelse ($topAccounts as $account)
                            <div class="rounded-2xl bg-gray-50 p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-sm font-medium text-gray-900">{{ $account->account_name }}</span>
                                    <span class="text-xs text-gray-500">{{ $account->total_transactions }} trx</span>
                                </div>
                                <div class="mt-2 text-sm text-gray-600">
                                    Total nominal: Rp {{ number_format($account->total_amount, 0, ',', '.') }}
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-gray-300 p-4 text-sm text-gray-500">
                                Belum ada data rekening.
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="mb-6">
                <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold tracking-tight text-gray-900">Goal Completion Forecast</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Estimasi kasar kapan target saving bisa selesai kalau kapasitas saving tetap.
                            </p>
                        </div>
                        <a href="{{ route('saving-targets.index') }}"
                            class="text-sm font-medium text-emerald-700 hover:text-emerald-800">
                            Lihat targets
                        </a>
                    </div>

                    <div class="mt-5 grid gap-4 lg:grid-cols-2 xl:grid-cols-3">
                        @forelse ($goalForecasts as $goal)
                            <div class="rounded-2xl border border-gray-200 p-4">
                                <h4 class="font-medium text-gray-900">{{ $goal['name'] }}</h4>

                                <div class="mt-4 space-y-2 text-sm text-gray-600">
                                    <div>
                                        Terkumpul:
                                        <span class="font-medium text-gray-900">
                                            Rp {{ number_format($goal['current_amount'], 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div>
                                        Target:
                                        <span class="font-medium text-gray-900">
                                            Rp {{ number_format($goal['target_amount'], 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div>
                                        Sisa:
                                        <span class="font-medium text-gray-900">
                                            Rp {{ number_format($goal['remaining'], 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div>
                                        Kapasitas saving/bln:
                                        <span class="font-medium text-gray-900">
                                            Rp {{ number_format($goal['estimated_monthly_capacity'], 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-4 rounded-2xl bg-gray-50 p-3 text-sm">
                                    @if ($goal['months_to_goal'])
                                        Estimasi selesai:
                                        <span class="font-semibold text-gray-900">{{ $goal['months_to_goal'] }}
                                            bulan</span>
                                    @else
                                        <span class="text-gray-500">Belum bisa dihitung. Kapasitas saving masih nol.</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div
                                class="rounded-2xl border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500 lg:col-span-2 xl:col-span-3">
                                Belum ada target saving aktif.
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="mb-6 grid gap-6 xl:grid-cols-3">
                {{-- TRANSAKSI TERBARU --}}
                <div class="xl:col-span-2 rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold tracking-tight text-gray-900">Transaksi Terbaru</h3>
                            <p class="mt-1 text-sm text-gray-500">10 transaksi terbaru pada periode terpilih.</p>
                        </div>
                        <a href="{{ route('transactions.index', ['month' => $selectedMonth, 'year' => $selectedYear]) }}"
                            class="text-sm font-medium text-emerald-700 hover:text-emerald-800">
                            Lihat semua
                        </a>
                    </div>

                    {{-- Desktop table --}}
                    <div class="mt-5 hidden overflow-hidden rounded-2xl border border-gray-200 md:block">
                        <div class="overflow-x-auto">
                            <table class="min-w-[760px] divide-y divide-gray-200 text-sm">
                                <thead
                                    class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-[0.14em] text-gray-500">
                                    <tr>
                                        <th class="px-5 py-4">Tanggal</th>
                                        <th class="px-5 py-4">Judul</th>
                                        <th class="px-5 py-4">Kategori</th>
                                        <th class="px-5 py-4">Rekening</th>
                                        <th class="px-5 py-4 text-right">Nominal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @forelse ($latestTransactions as $transaction)
                                        <tr class="hover:bg-gray-50">
                                            <td class="whitespace-nowrap px-5 py-4 text-gray-600">
                                                {{ optional($transaction->transaction_date)->format('d M Y') }}
                                            </td>
                                            <td class="px-5 py-4">
                                                <div class="max-w-[220px] truncate font-medium text-gray-900">
                                                    {{ $transaction->title }}
                                                </div>
                                                @if ($transaction->note)
                                                    <div class="mt-1 max-w-[220px] truncate text-xs text-gray-500">
                                                        {{ $transaction->note }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-5 py-4 text-gray-600">{{ $transaction->category->name ?? '-' }}
                                            </td>
                                            <td class="px-5 py-4 text-gray-600">
                                                {{ $transaction->financialAccount->name ?? '-' }}</td>
                                            <td
                                                class="whitespace-nowrap px-5 py-4 text-right font-semibold {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-red-600' }}">
                                                {{ $transaction->type === 'income' ? '+' : '-' }}
                                                Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-5 py-10 text-center text-sm text-gray-500">
                                                Belum ada transaksi di periode ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Mobile cards --}}
                    <div class="mt-5 space-y-3 md:hidden">
                        @forelse ($latestTransactions as $transaction)
                            <div class="rounded-2xl border border-gray-200 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="truncate font-medium text-gray-900">
                                            {{ $transaction->title }}
                                        </div>
                                        <div class="mt-1 text-xs text-gray-500">
                                            {{ optional($transaction->transaction_date)->format('d M Y') }}
                                        </div>
                                    </div>

                                    <div
                                        class="shrink-0 text-right font-semibold {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-red-600' }}">
                                        {{ $transaction->type === 'income' ? '+' : '-' }}
                                        Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                    </div>
                                </div>

                                <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
                                    <div class="rounded-xl bg-gray-50 p-3">
                                        <div class="text-xs uppercase tracking-[0.12em] text-gray-500">Kategori</div>
                                        <div class="mt-1 truncate text-gray-800">{{ $transaction->category->name ?? '-' }}
                                        </div>
                                    </div>

                                    <div class="rounded-xl bg-gray-50 p-3">
                                        <div class="text-xs uppercase tracking-[0.12em] text-gray-500">Rekening</div>
                                        <div class="mt-1 truncate text-gray-800">
                                            {{ $transaction->financialAccount->name ?? '-' }}</div>
                                    </div>
                                </div>

                                @if ($transaction->note)
                                    <div class="mt-3 rounded-xl bg-gray-50 p-3 text-sm text-gray-600">
                                        {{ $transaction->note }}
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div
                                class="rounded-2xl border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500">
                                Belum ada transaksi di periode ini.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- SAVING TARGETS --}}
                <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-semibold tracking-tight text-gray-900">Saving Targets</h3>
                            <p class="mt-1 text-sm text-gray-500">Target aktif terbaru.</p>
                        </div>
                        <a href="{{ route('saving-targets.index') }}"
                            class="text-sm font-medium text-emerald-700 hover:text-emerald-800">
                            Lihat
                        </a>
                    </div>

                    <div class="mt-5 space-y-4">
                        @forelse ($savingTargets as $target)
                            @php
                                $progress =
                                    $target->target_amount > 0
                                        ? min(100, ($target->current_amount / $target->target_amount) * 100)
                                        : 0;
                            @endphp

                            <div class="rounded-2xl border border-gray-200 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h4 class="truncate font-medium text-gray-900">{{ $target->name }}</h4>
                                        <p class="mt-1 truncate text-xs text-gray-500">
                                            {{ $target->financialAccount->name ?? 'Belum pilih rekening' }}
                                        </p>
                                    </div>

                                    <span
                                        class="shrink-0 rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
                                        {{ ucfirst($target->status) }}
                                    </span>
                                </div>

                                <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                                    <div class="rounded-xl bg-gray-50 p-3">
                                        <div class="text-xs uppercase tracking-[0.12em] text-gray-500">Terkumpul</div>
                                        <div class="mt-1 font-medium text-gray-800">
                                            Rp {{ number_format($target->current_amount, 0, ',', '.') }}
                                        </div>
                                    </div>

                                    <div class="rounded-xl bg-gray-50 p-3">
                                        <div class="text-xs uppercase tracking-[0.12em] text-gray-500">Target</div>
                                        <div class="mt-1 font-medium text-gray-800">
                                            Rp {{ number_format($target->target_amount, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 h-2.5 w-full rounded-full bg-gray-200">
                                    <div class="h-2.5 rounded-full bg-emerald-600" style="width: {{ $progress }}%">
                                    </div>
                                </div>

                                <div class="mt-2 text-right text-xs text-gray-500">
                                    {{ number_format($progress, 0) }}%
                                </div>
                            </div>
                        @empty
                            <div
                                class="rounded-2xl border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500">
                                Belum ada target tabungan aktif.
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script>
        window.addEventListener('load', () => {
            const skeleton = document.getElementById('skeleton');
            const content = document.getElementById('dashboardContent');

            if (skeleton) skeleton.style.display = 'none';
            if (content) content.classList.remove('hidden');
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const palette = {
                income: '#10B981',
                expense: '#EF4444',
                net: '#3B82F6',
                grid: '#E5E7EB',
                text: '#6B7280'
            };

            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: palette.text
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                const v = ctx.raw ?? 0;
                                return `${ctx.dataset.label}: Rp ${Number(v).toLocaleString('id-ID')}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: palette.grid
                        },
                        ticks: {
                            color: palette.text
                        }
                    },
                    y: {
                        grid: {
                            color: palette.grid
                        },
                        ticks: {
                            color: palette.text
                        },
                        beginAtZero: true
                    }
                }
            };

            const cashflowCtx = document.getElementById('cashflowChart');
            if (cashflowCtx) {
                new Chart(cashflowCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($cashflowChart['labels']),
                        datasets: [{
                            label: 'Nominal',
                            data: @json($cashflowChart['values']),
                            backgroundColor: [palette.income, palette.expense],
                            borderRadius: 8
                        }]
                    },
                    options: {
                        ...commonOptions,
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }

            const expenseCategoryCtx = document.getElementById('expenseCategoryChart');
            if (expenseCategoryCtx) {
                new Chart(expenseCategoryCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($expenseCategoryChart['labels']),
                        datasets: [{
                            label: 'Pengeluaran',
                            data: @json($expenseCategoryChart['values']),
                            backgroundColor: palette.expense,
                            borderRadius: 8
                        }]
                    },
                    options: {
                        ...commonOptions,
                        indexAxis: 'y',
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }

            const trendCtx = document.getElementById('trendChart');
            if (trendCtx) {
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: @json($trendChart['labels']),
                        datasets: [{
                                label: 'Income',
                                data: @json($trendChart['income']),
                                borderColor: palette.income,
                                tension: 0.35,
                                pointRadius: 3
                            },
                            {
                                label: 'Expense',
                                data: @json($trendChart['expense']),
                                borderColor: palette.expense,
                                tension: 0.35,
                                pointRadius: 3
                            },
                            {
                                label: 'Net',
                                data: @json($trendChart['net']),
                                borderColor: palette.net,
                                tension: 0.35,
                                pointRadius: 3
                            }
                        ]
                    },
                    options: commonOptions
                });
            }
        });
    </script>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #print-area,
            #print-area * {
                visibility: visible;
            }

            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>
@endsection
