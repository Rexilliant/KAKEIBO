@extends('layouts.app')

@section('page_title', 'Summary Report')
@section('page_subtitle', 'Ringkasan transaksi per bulan yang lebih enak dibaca daripada ngeliatin angka mentah doang.')

@section('content')
    <section class="mb-6">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">Transaction summary</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                        Summary Report
                    </h2>
                    <p class="mt-2 text-sm leading-6 text-gray-500">
                        Ringkasan transaksi untuk periode
                        <span class="font-medium text-gray-700">
                            {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }} {{ $year }}
                        </span>.
                    </p>
                </div>

                <form method="GET" action="{{ route('transactions.summary-report') }}" class="grid gap-3 sm:grid-cols-3">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Bulan</label>
                        <select name="month"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ (string) $month === (string) $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Tahun</label>
                        <input type="number" name="year" value="{{ $year }}"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                    </div>

                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full rounded-xl bg-gray-900 px-4 py-3 text-sm font-medium text-white hover:bg-black">
                            Tampilkan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <section class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Total Pemasukan</p>
            <h3 class="mt-3 text-2xl font-semibold tracking-tight text-gray-900">
                Rp {{ number_format($totalIncome, 0, ',', '.') }}
            </h3>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Total Pengeluaran</p>
            <h3 class="mt-3 text-2xl font-semibold tracking-tight text-gray-900">
                Rp {{ number_format($totalExpense, 0, ',', '.') }}
            </h3>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Net Cashflow</p>
            <h3
                class="mt-3 text-2xl font-semibold tracking-tight {{ $netCashflow >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                Rp {{ number_format($netCashflow, 0, ',', '.') }}
            </h3>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Jumlah Transaksi</p>
            <h3 class="mt-3 text-2xl font-semibold tracking-tight text-gray-900">
                {{ $totalTransactions }}
            </h3>
        </div>
    </section>

    <section class="mb-6 grid gap-6 xl:grid-cols-2">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <h3 class="text-lg font-semibold tracking-tight text-gray-900">Top Kategori Pengeluaran</h3>
            <div class="mt-5 space-y-3">
                @forelse ($topExpenseCategories as $item)
                    <div class="flex items-center justify-between rounded-2xl bg-gray-50 px-4 py-3">
                        <span class="text-sm font-medium text-gray-900">{{ $item->category_name }}</span>
                        <span class="text-sm text-gray-700">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</span>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-gray-300 p-5 text-center text-sm text-gray-500">
                        Belum ada data pengeluaran.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <h3 class="text-lg font-semibold tracking-tight text-gray-900">Top Kategori Pemasukan</h3>
            <div class="mt-5 space-y-3">
                @forelse ($topIncomeCategories as $item)
                    <div class="flex items-center justify-between rounded-2xl bg-gray-50 px-4 py-3">
                        <span class="text-sm font-medium text-gray-900">{{ $item->category_name }}</span>
                        <span class="text-sm text-gray-700">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</span>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-gray-300 p-5 text-center text-sm text-gray-500">
                        Belum ada data pemasukan.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="mb-6 grid gap-6 xl:grid-cols-2">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <h3 class="text-lg font-semibold tracking-tight text-gray-900">Transaksi Expense Terbesar</h3>
            <div class="mt-5 rounded-2xl bg-red-50 p-4">
                @if ($largestExpense)
                    <p class="text-base font-semibold text-red-900">{{ $largestExpense->title }}</p>
                    <p class="mt-2 text-sm text-red-800">
                        Rp {{ number_format($largestExpense->amount, 0, ',', '.') }}
                    </p>
                    <p class="mt-1 text-xs text-red-700">
                        {{ $largestExpense->category->name ?? '-' }} · {{ $largestExpense->transaction_date }}
                    </p>
                @else
                    <p class="text-sm text-red-700">Belum ada expense bulan ini.</p>
                @endif
            </div>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <h3 class="text-lg font-semibold tracking-tight text-gray-900">Transaksi Income Terbesar</h3>
            <div class="mt-5 rounded-2xl bg-emerald-50 p-4">
                @if ($largestIncome)
                    <p class="text-base font-semibold text-emerald-900">{{ $largestIncome->title }}</p>
                    <p class="mt-2 text-sm text-emerald-800">
                        Rp {{ number_format($largestIncome->amount, 0, ',', '.') }}
                    </p>
                    <p class="mt-1 text-xs text-emerald-700">
                        {{ $largestIncome->category->name ?? '-' }} · {{ $largestIncome->transaction_date }}
                    </p>
                @else
                    <p class="text-sm text-emerald-700">Belum ada income bulan ini.</p>
                @endif
            </div>
        </div>
    </section>

    <section>
        <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-5">
                <h3 class="text-lg font-semibold tracking-tight text-gray-900">10 Transaksi Terbaru di Periode Ini</h3>
                <p class="mt-1 text-sm text-gray-500">Buat cek cepat tanpa muter-muter ke halaman lain.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-[0.14em] text-gray-500">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Judul</th>
                            <th class="px-6 py-4">Kategori</th>
                            <th class="px-6 py-4">Rekening</th>
                            <th class="px-6 py-4">Tipe</th>
                            <th class="px-6 py-4 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($recentTransactions as $trx)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-gray-600">{{ $trx->transaction_date }}</td>
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $trx->title }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $trx->category->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $trx->financialAccount->name ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    @if ($trx->type === 'income')
                                        <span
                                            class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">Income</span>
                                    @else
                                        <span
                                            class="rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-700">Expense</span>
                                    @endif
                                </td>
                                <td
                                    class="px-6 py-4 text-right font-semibold {{ $trx->type === 'income' ? 'text-emerald-600' : 'text-red-600' }}">
                                    Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
                                    Belum ada transaksi di periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
