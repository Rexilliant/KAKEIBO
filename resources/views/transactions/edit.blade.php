@extends('layouts.app')

@section('page_title', 'Edit Transaksi')
@section('page_subtitle', 'Perbarui transaksi dengan warning budget otomatis.')

@section('content')
    <section class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_380px]">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-6">
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">Edit transaction</p>
                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                    Edit Transaksi
                </h2>
                <p class="mt-2 text-sm leading-6 text-gray-500">
                    Warning budget akan menghitung ulang seolah transaksi lama belum ada, jadi hasilnya lebih akurat.
                </p>
            </div>

            <form action="{{ route('transactions.update', $transaction->id) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Tanggal</label>
                        <input type="date" id="transaction_date" name="transaction_date"
                            value="{{ old('transaction_date', optional($transaction->transaction_date)->format('Y-m-d')) }}"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Jenis</label>
                        <select id="type" name="type"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                            <option value="income" {{ old('type', $transaction->type) === 'income' ? 'selected' : '' }}>
                                Pemasukan</option>
                            <option value="expense" {{ old('type', $transaction->type) === 'expense' ? 'selected' : '' }}>
                                Pengeluaran</option>
                        </select>
                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Judul</label>
                        <input type="text" name="title" value="{{ old('title', $transaction->title) }}"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Nominal</label>
                        <input type="number" step="0.01" min="0.01" id="amount" name="amount"
                            value="{{ old('amount', $transaction->amount) }}"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Kategori</label>
                        <select id="category_id" name="category_id"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                            <option value="">Pilih kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    data-transaction-type="{{ $category->transaction_type }}"
                                    {{ (string) old('category_id', $transaction->category_id) === (string) $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Rekening</label>
                        <select name="financial_account_id"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}"
                                    {{ (string) old('financial_account_id', $transaction->financial_account_id) === (string) $account->id ? 'selected' : '' }}>
                                    {{ $account->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div id="budget-warning-box" class="hidden rounded-2xl border p-4"></div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Catatan</label>
                    <textarea name="note" rows="4"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">{{ old('note', $transaction->note) }}</textarea>
                </div>

                <div class="flex gap-3 pt-4 border-t">
                    <button type="submit" class="rounded-xl bg-gray-900 px-5 py-3 text-sm text-white hover:bg-black">
                        Update
                    </button>

                    <a href="{{ route('transactions.index') }}" class="rounded-xl border px-5 py-3 text-sm">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        <div class="rounded-3xl border bg-white p-5 shadow-sm">
            <h3 class="text-lg font-semibold">Info</h3>
            <p class="mt-3 text-sm text-gray-600">
                Warning budget di form edit menghitung ulang tanpa transaksi ini, jadi hasilnya gak dobel.
            </p>
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
                const budgetAmount = parseFloat(budget.budget || 0);
                const spentAmount = parseFloat(budget.spent || 0);
                const projectedSpent = spentAmount + amount;
                const projectedRemaining = budgetAmount - projectedSpent;
                const modeText = budget.enforcement_level === 'hard' ? 'Hard Limit' : 'Soft Warning';

                let status = 'safe';

                if (projectedSpent > budgetAmount) {
                    status = 'over';
                } else if (budgetAmount > 0 && projectedSpent >= (budgetAmount * 0.8)) {
                    status = 'warning';
                }

                if (status === 'safe') {
                    warningBox.className = 'rounded-2xl border border-emerald-200 bg-emerald-50 p-4';
                    warningBox.innerHTML = `
                    <p class="font-semibold text-emerald-800">Budget masih aman</p>
                    <p class="mt-1 text-sm text-emerald-700">
                        Kategori <strong>${budget.category_name}</strong> · Mode: <strong>${modeText}</strong><br>
                        Budget: Rp ${Number(budgetAmount).toLocaleString('id-ID')} ·
                        Sudah terpakai: Rp ${Number(spentAmount).toLocaleString('id-ID')} ·
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
                        Budget: Rp ${Number(budgetAmount).toLocaleString('id-ID')} ·
                        Sudah terpakai: Rp ${Number(spentAmount).toLocaleString('id-ID')} ·
                        Setelah transaksi ini: Rp ${Number(projectedSpent).toLocaleString('id-ID')} ·
                        Sisa: Rp ${Number(projectedRemaining).toLocaleString('id-ID')}
                    </p>
                `;
                    return;
                }

                const overTitle = budget.enforcement_level === 'hard' ?
                    'Hard Limit: transaksi ini akan ditolak' :
                    'Warning: Budget akan terlampaui';

                warningBox.className = 'rounded-2xl border border-red-200 bg-red-50 p-4';
                warningBox.innerHTML = `
                <p class="font-semibold text-red-800">${overTitle}</p>
                <p class="mt-1 text-sm text-red-700">
                    Kategori <strong>${budget.category_name}</strong> · Mode: <strong>${modeText}</strong><br>
                    Budget: Rp ${Number(budgetAmount).toLocaleString('id-ID')} ·
                    Sudah terpakai: Rp ${Number(spentAmount).toLocaleString('id-ID')} ·
                    Setelah transaksi ini: Rp ${Number(projectedSpent).toLocaleString('id-ID')} ·
                    Lebih: Rp ${Number(Math.abs(projectedRemaining)).toLocaleString('id-ID')}
                </p>
            `;
            }

            typeEl.addEventListener('change', function() {
                filterCategoriesByType();
                renderBudgetWarning();
            });

            categoryEl.addEventListener('change', renderBudgetWarning);
            amountEl.addEventListener('input', renderBudgetWarning);

            filterCategoriesByType();
            renderBudgetWarning();
        });
    </script>

@endsection
