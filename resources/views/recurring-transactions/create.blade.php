@extends('layouts.app')

@section('page_title', 'Tambah Recurring Transaction')
@section('page_subtitle', 'Buat transaksi rutin bulanan agar sistem kerja, bukan lu doang.')

@section('content')
    <section class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_380px]">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-6">
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">New recurring transaction</p>
                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                    Tambah Transaksi Rutin
                </h2>
            </div>

            <form action="{{ route('recurring-transactions.store') }}" method="POST" class="space-y-5">
                @csrf

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Judul</label>
                        <input type="text" name="title" value="{{ old('title') }}"
                            placeholder="Contoh: Gaji Bulanan / Bayar Internet"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Jenis</label>
                        <select name="type"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                            <option value="income" {{ old('type') === 'income' ? 'selected' : '' }}>Income</option>
                            <option value="expense" {{ old('type', 'expense') === 'expense' ? 'selected' : '' }}>Expense
                            </option>
                        </select>
                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Kategori</label>
                        <select name="category_id"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                            <option value="">Pilih kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ (string) old('category_id') === (string) $category->id ? 'selected' : '' }}>
                                    {{ $category->name }} ({{ $category->transaction_type }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Rekening</label>
                        <select name="financial_account_id"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                            <option value="">Pilih rekening</option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}"
                                    {{ (string) old('financial_account_id') === (string) $account->id ? 'selected' : '' }}>
                                    {{ $account->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-3">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Nominal</label>
                        <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Frekuensi</label>
                        <select name="frequency"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                            <option value="monthly" selected>Monthly</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ old('start_date', now()->format('Y-m-d')) }}"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Next Run Date</label>
                    <input type="date" name="next_run_date" value="{{ old('next_run_date', now()->format('Y-m-d')) }}"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Catatan</label>
                    <textarea name="note" rows="4"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">{{ old('note') }}</textarea>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    <label class="flex items-start gap-3 rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}
                            class="mt-1 h-4 w-4 rounded border-gray-300 text-emerald-600">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Aktif</p>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <input type="checkbox" name="affects_budget" value="1"
                            {{ old('affects_budget', '1') ? 'checked' : '' }}
                            class="mt-1 h-4 w-4 rounded border-gray-300 text-emerald-600">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Affects Budget</p>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <input type="checkbox" name="is_unexpected" value="1"
                            {{ old('is_unexpected') ? 'checked' : '' }}
                            class="mt-1 h-4 w-4 rounded border-gray-300 text-emerald-600">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Unexpected</p>
                        </div>
                    </label>
                </div>

                <div class="flex flex-col gap-3 border-t border-gray-200 pt-5 sm:flex-row">
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-black">
                        Simpan Recurring
                    </button>

                    <a href="{{ route('recurring-transactions.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <h3 class="text-lg font-semibold tracking-tight text-gray-900">Catatan</h3>
            <ul class="mt-4 space-y-3 text-sm text-gray-600">
                <li class="rounded-2xl bg-gray-50 px-4 py-3">Versi awal ini fokus ke recurring bulanan.</li>
                <li class="rounded-2xl bg-gray-50 px-4 py-3">Recurring yang jatuh tempo dibuat lewat tombol Generate Due.
                </li>
                <li class="rounded-2xl bg-gray-50 px-4 py-3">Expense recurring tetap cek saldo rekening sebelum dibuat.</li>
            </ul>
        </div>
    </section>
@endsection
