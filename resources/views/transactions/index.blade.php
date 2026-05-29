@extends('layouts.app')

@section('page_title', 'Transaksi')
@section('page_subtitle', 'Kelola, cari, dan filter transaksi tanpa drama.')

@section('content')
    <section class="mb-6">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">Transaction management</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                        Daftar Transaksi
                    </h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-500">
                        Cari transaksi berdasarkan judul, catatan, rekening, kategori, tanggal, sampai nominal. Biar lu gak
                        nyari data kayak orang kehilangan waras.
                    </p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('transactions.create') }}"
                        class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700">
                        + Tambah Transaksi
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Income Terfilter</p>
            <h3 class="mt-3 break-words text-2xl font-semibold tracking-tight text-emerald-600">
                Rp {{ number_format($filteredIncome, 0, ',', '.') }}
            </h3>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Expense Terfilter</p>
            <h3 class="mt-3 break-words text-2xl font-semibold tracking-tight text-red-600">
                Rp {{ number_format($filteredExpense, 0, ',', '.') }}
            </h3>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:col-span-2 xl:col-span-1">
            <p class="text-sm font-medium text-gray-500">Jumlah Data</p>
            <h3 class="mt-3 text-2xl font-semibold tracking-tight text-gray-900">
                {{ $filteredCount }}
            </h3>
        </div>
    </section>

    <section class="mb-6">
        <form method="GET" action="{{ route('transactions.index') }}"
            class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="xl:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari judul atau catatan..."
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Jenis</label>
                    <select name="type"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                        <option value="">Semua jenis</option>
                        <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Income</option>
                        <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Expense</option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Kategori</label>
                    <select name="category_id"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                        <option value="">Semua kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ (string) request('category_id') === (string) $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Rekening</label>
                    <select name="financial_account_id"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                        <option value="">Semua rekening</option>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}"
                                {{ (string) request('financial_account_id') === (string) $account->id ? 'selected' : '' }}>
                                {{ $account->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Bulan</label>
                    <select name="month"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                        <option value="">Semua bulan</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}"
                                {{ (string) request('month') === (string) $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Tahun</label>
                    <input type="number" name="year" value="{{ request('year') }}" placeholder="Contoh: 2026"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Tanggal Dari</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Tanggal Sampai</label>
                    <input type="date" name="date_until" value="{{ request('date_until') }}"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Nominal Minimum</label>
                    <input type="number" step="0.01" min="0" name="amount_min"
                        value="{{ request('amount_min') }}" placeholder="0"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Nominal Maksimum</label>
                    <input type="number" step="0.01" min="0" name="amount_max"
                        value="{{ request('amount_max') }}" placeholder="0"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                </div>
            </div>

            <div
                class="mt-5 flex flex-col gap-3 border-t border-gray-200 pt-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-col gap-3 sm:flex-row">
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-black">
                        Terapkan Filter
                    </button>

                    <a href="{{ route('transactions.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Reset Filter
                    </a>
                </div>

                <div class="relative inline-block text-left" x-data="{ open: false }">
                    <button type="button" @click="open = !open"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-black">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 16V4m0 12l-4-4m4 4l4-4M4 20h16" />
                        </svg>

                        <span>Export</span>

                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false" x-transition
                        class="absolute right-0 z-20 mt-2 w-52 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-lg">

                        <a href="{{ route('transactions.export.csv', request()->query()) }}" @click="open = false"
                            class="block px-4 py-3 text-sm text-gray-700 transition hover:bg-gray-50">
                            Export CSV
                        </a>

                        <a href="{{ route('transactions.summary-report', request()->query()) }}" @click="open = false"
                            class="block px-4 py-3 text-sm text-gray-700 transition hover:bg-gray-50">
                            Summary Report
                        </a>

                        <button type="button"
                            class="block w-full cursor-not-allowed bg-gray-50 px-4 py-3 text-left text-sm text-gray-400">
                            Export PDF
                            <span class="ml-1 text-xs">(soon)</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </section>

    <section class="hidden lg:block">
        <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-5">
                <h3 class="text-lg font-semibold tracking-tight text-gray-900">Daftar Transaksi</h3>
                <p class="mt-1 text-sm text-gray-500">Versi desktop dengan filter global penuh.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-[0.14em] text-gray-500">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Judul</th>
                            <th class="px-6 py-4">Kategori</th>
                            <th class="px-6 py-4">Rekening</th>
                            <th class="px-6 py-4">Jenis</th>
                            <th class="px-6 py-4 text-right">Nominal</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($transactions as $transaction)
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-gray-600">
                                    {{ optional($transaction->transaction_date)->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="max-w-[240px] truncate font-medium text-gray-900">
                                        {{ $transaction->title }}</div>
                                    @if ($transaction->note)
                                        <div class="mt-1 max-w-[240px] truncate text-xs text-gray-500">
                                            {{ $transaction->note }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $transaction->category->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $transaction->financialAccount->name ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    @if ($transaction->type === 'income')
                                        <span
                                            class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">Income</span>
                                    @else
                                        <span
                                            class="rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-700">Expense</span>
                                    @endif
                                </td>
                                <td
                                    class="whitespace-nowrap px-6 py-4 text-right font-semibold {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-red-600' }}">
                                    Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('transactions.show', $transaction->id) }}"
                                            class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50">
                                            Detail
                                        </a>

                                        <a href="{{ route('transactions.edit', $transaction->id) }}"
                                            class="rounded-lg border border-blue-300 px-3 py-2 text-xs font-medium text-blue-700 transition hover:bg-blue-50">
                                            Edit
                                        </a>

                                        <form action="{{ route('transactions.destroy', $transaction->id) }}"
                                            method="POST" onsubmit="return confirm('Yakin mau hapus transaksi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rounded-lg border border-red-300 px-3 py-2 text-xs font-medium text-red-700 transition hover:bg-red-50">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
                                    Tidak ada transaksi yang cocok dengan filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="space-y-4 lg:hidden">
        @forelse ($transactions as $transaction)
            <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h3 class="truncate text-base font-semibold text-gray-900">{{ $transaction->title }}</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ optional($transaction->transaction_date)->format('d M Y') }}
                        </p>
                    </div>

                    @if ($transaction->type === 'income')
                        <span
                            class="shrink-0 rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">Income</span>
                    @else
                        <span
                            class="shrink-0 rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-700">Expense</span>
                    @endif
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-2xl bg-gray-50 p-3">
                        <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Kategori</p>
                        <p class="mt-1 truncate font-medium text-gray-900">{{ $transaction->category->name ?? '-' }}</p>
                    </div>

                    <div class="rounded-2xl bg-gray-50 p-3">
                        <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Rekening</p>
                        <p class="mt-1 truncate font-medium text-gray-900">
                            {{ $transaction->financialAccount->name ?? '-' }}</p>
                    </div>

                    <div class="rounded-2xl bg-gray-50 p-3 col-span-2">
                        <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Nominal</p>
                        <p
                            class="mt-1 font-medium {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-red-600' }}">
                            Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                @if ($transaction->note)
                    <div class="mt-4 rounded-2xl bg-gray-50 p-3 text-sm text-gray-600">
                        {{ $transaction->note }}
                    </div>
                @endif

                <div class="mt-4 grid grid-cols-3 gap-2">
                    <a href="{{ route('transactions.show', $transaction->id) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-3 py-2.5 text-xs font-medium text-gray-700 transition hover:bg-gray-50">
                        Detail
                    </a>

                    <a href="{{ route('transactions.edit', $transaction->id) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-blue-300 px-3 py-2.5 text-xs font-medium text-blue-700 transition hover:bg-blue-50">
                        Edit
                    </a>

                    <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST"
                        onsubmit="return confirm('Yakin mau hapus transaksi ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex w-full items-center justify-center rounded-xl border border-red-300 px-3 py-2.5 text-xs font-medium text-red-700 transition hover:bg-red-50">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div
                class="rounded-3xl border border-dashed border-gray-300 bg-white p-10 text-center text-sm text-gray-500 shadow-sm">
                Tidak ada transaksi yang cocok dengan filter.
            </div>
        @endforelse
    </section>

    <section class="mt-6">
        <div class="rounded-3xl border border-gray-200 bg-white px-5 py-4 shadow-sm sm:px-6">
            {{ $transactions->links() }}
        </div>
    </section>
@endsection
