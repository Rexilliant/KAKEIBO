@extends('layouts.app')

@section('page_title', 'Tambah Transaksi')
@section('page_subtitle', 'Catat pemasukan atau pengeluaran baru dengan warning budget otomatis.')

@section('content')
    <section class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_380px]">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-6">
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">New transaction</p>
                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                    Tambah Transaksi
                </h2>
                <p class="mt-2 text-sm leading-6 text-gray-500">
                    Sekarang form ini bisa kasih heads-up kalau transaksi expense lu berpotensi bikin budget jebol.
                </p>
            </div>

            <form action="{{ route('transactions.store') }}" method="POST" class="space-y-5">
                @csrf

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Tanggal</label>
                        <input type="date" id="transaction_date" name="transaction_date"
                            value="{{ old('transaction_date', now()->format('Y-m-d')) }}"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Jenis</label>
                        <select id="type" name="type"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900">
                            <option value="income" {{ old('type') === 'income' ? 'selected' : '' }}>Pemasukan</option>
                            <option value="expense" {{ old('type', 'expense') === 'expense' ? 'selected' : '' }}>Pengeluaran
                            </option>
                        </select>
                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Judul Transaksi</label>
                        <input type="text" name="title" value="{{ old('title') }}"
                            placeholder="Contoh: Makan Siang / Bayar Internet / Gaji Bulanan"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Nominal</label>
                        <input type="number" step="0.01" min="0.01" id="amount" name="amount"
                            value="{{ old('amount') }}" placeholder="0"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900">
                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Kategori</label>
                        <select id="category_id" name="category_id"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900">
                            <option value="">Pilih kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    data-transaction-type="{{ $category->transaction_type }}"
                                    {{ (string) old('category_id') === (string) $category->id ? 'selected' : '' }}>
                                    {{ $category->name }} ({{ $category->transaction_type }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Rekening</label>
                        <select name="financial_account_id"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900">
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}"
                                    {{ (string) old('financial_account_id') === (string) $account->id ? 'selected' : '' }}>
                                    {{ $account->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div id="budget-warning-box" class="hidden rounded-2xl border p-4"></div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Catatan</label>
                    <textarea name="note" rows="5" placeholder="Tambahkan catatan kalau perlu..."
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900">{{ old('note') }}</textarea>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <label class="flex items-start gap-3 rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <input type="checkbox" name="is_unexpected" value="1"
                            {{ old('is_unexpected') ? 'checked' : '' }}
                            class="mt-1 h-4 w-4 rounded border-gray-300 text-emerald-600">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Tandai sebagai tak terduga</p>
                            <p class="mt-1 text-sm leading-6 text-gray-500">Cocok untuk biaya mendadak.</p>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <input type="checkbox" name="affects_budget" value="1"
                            {{ old('affects_budget', '1') ? 'checked' : '' }}
                            class="mt-1 h-4 w-4 rounded border-gray-300 text-emerald-600">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Masuk ke anggaran</p>
                            <p class="mt-1 text-sm leading-6 text-gray-500">Aktifkan kalau transaksi ini harus dihitung ke
                                budget.</p>
                        </div>
                    </label>
                </div>

                <div class="flex flex-col gap-3 border-t border-gray-200 pt-5 sm:flex-row">
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-black">
                        Simpan Transaksi
                    </button>

                    <a href="{{ route('transactions.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        <div class="space-y-6">
            <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                <h3 class="text-lg font-semibold tracking-tight text-gray-900">Panduan Cepat</h3>
                <div class="mt-4 space-y-4">
                    <div class="rounded-2xl bg-gray-50 p-4">
                        <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Budget Warning</p>
                        <p class="mt-2 text-sm leading-6 text-gray-700">
                            Warning hanya muncul untuk transaksi expense dan kategori yang punya budget bulan ini.
                        </p>
                    </div>

                    <div class="rounded-2xl bg-yellow-50 p-4">
                        <p class="text-xs uppercase tracking-[0.14em] text-yellow-700">Hampir Habis</p>
                        <p class="mt-2 text-sm leading-6 text-yellow-900">
                            Muncul kalau transaksi ini bikin pemakaian mendekati atau melewati 80% budget.
                        </p>
                    </div>

                    <div class="rounded-2xl bg-red-50 p-4">
                        <p class="text-xs uppercase tracking-[0.14em] text-red-700">Over Budget</p>
                        <p class="mt-2 text-sm leading-6 text-red-900">
                            Muncul kalau transaksi ini bikin pengeluaran melebihi batas anggaran.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        window.transactionBudgetData = @json($budgetData);
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeEl = document.getElementById('type');
            const categoryEl = document.getElementById('category_id');
            const amountEl = document.getElementById('amount');
            const warningBox = document.getElementById('budget-warning-box');
            const budgetData = window.transactionBudgetData || {};

            function renderBudgetWarning() {
                const type = typeEl.value;
                const categoryId = categoryEl.value;
                const amount = parseFloat(amountEl.value || 0);

                warningBox.className = 'hidden rounded-2xl border p-4';
                warningBox.innerHTML = '';

                if (type !== 'expense' || !categoryId || !budgetData[categoryId]) {
                    return;
                }

                const budget = budgetData[categoryId];
                const projectedSpent = parseFloat(budget.spent) + amount;
                const projectedRemaining = parseFloat(budget.budget) - projectedSpent;

                let status = 'safe';
                if (projectedSpent > parseFloat(budget.budget)) {
                    status = 'over';
                } else if (parseFloat(budget.budget) > 0 && projectedSpent >= (parseFloat(budget.budget) * 0.8)) {
                    status = 'warning';
                }

                if (status === 'safe') {
                    warningBox.className = 'rounded-2xl border border-emerald-200 bg-emerald-50 p-4';
                    warningBox.innerHTML = `
                        <p class="font-semibold text-emerald-800">Budget masih aman</p>
                        <p class="mt-1 text-sm text-emerald-700">
                            Kategori <strong>${budget.category_name}</strong><br>
                            Budget: Rp ${Number(budget.budget).toLocaleString('id-ID')} ·
                            Sudah terpakai: Rp ${Number(budget.spent).toLocaleString('id-ID')} ·
                            Setelah transaksi ini: Rp ${Number(projectedSpent).toLocaleString('id-ID')}
                        </p>
                    `;
                    return;
                }

                if (status === 'warning') {
                    warningBox.className = 'rounded-2xl border border-yellow-200 bg-yellow-50 p-4';
                    warningBox.innerHTML = `
                        <p class="font-semibold text-yellow-800">Warning: Budget hampir habis</p>
                        <p class="mt-1 text-sm text-yellow-700">
                            Kategori <strong>${budget.category_name}</strong> · Mode: <strong>${modeText}</strong><br>
                            Budget: Rp ${Number(budget.budget).toLocaleString('id-ID')} ·
                            Sudah terpakai: Rp ${Number(budget.spent).toLocaleString('id-ID')} ·
                            Setelah transaksi ini: Rp ${Number(projectedSpent).toLocaleString('id-ID')} ·
                            Sisa: Rp ${Number(projectedRemaining).toLocaleString('id-ID')}
                        </p>
                    `;
                    return;
                }

                warningBox.className = 'rounded-2xl border border-red-200 bg-red-50 p-4';
                warningBox.innerHTML = `
                    <p class="font-semibold text-red-800">Warning: Budget akan terlampaui</p>
                    <p class="mt-1 text-sm text-red-700">
                        Kategori <strong>${budget.category_name}</strong><br>
                        Budget: Rp ${Number(budget.budget).toLocaleString('id-ID')} ·
                        Sudah terpakai: Rp ${Number(budget.spent).toLocaleString('id-ID')} ·
                        Setelah transaksi ini: Rp ${Number(projectedSpent).toLocaleString('id-ID')} ·
                        Lebih: Rp ${Number(Math.abs(projectedRemaining)).toLocaleString('id-ID')}
                    </p>
                `;
            }

            typeEl.addEventListener('change', renderBudgetWarning);
            categoryEl.addEventListener('change', renderBudgetWarning);
            amountEl.addEventListener('input', renderBudgetWarning);

            renderBudgetWarning();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeEl = document.getElementById('type');
            const categoryEl = document.getElementById('category_id');

            function filterCategoriesByType() {
                const selectedType = typeEl.value;
                const currentValue = categoryEl.value;

                let hasMatchingSelectedValue = false;

                Array.from(categoryEl.options).forEach((option, index) => {
                    if (index === 0) {
                        option.hidden = false;
                        return;
                    }

                    const optionType = option.dataset.transactionType || '';
                    const shouldShow = optionType === selectedType;

                    option.hidden = !shouldShow;

                    if (shouldShow && option.value === currentValue) {
                        hasMatchingSelectedValue = true;
                    }
                });

                if (!hasMatchingSelectedValue) {
                    categoryEl.value = '';
                }
            }

            typeEl.addEventListener('change', function() {
                filterCategoriesByType();

                const event = new Event('change');
                categoryEl.dispatchEvent(event);
            });

            filterCategoriesByType();
        });
    </script>
@endsection
