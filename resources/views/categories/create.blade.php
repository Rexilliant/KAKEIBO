@extends('layouts.app')

@section('page_title', 'Tambah Kategori')
@section('page_subtitle', 'Buat kategori baru untuk transaksi.')

@section('content')
    <section class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_380px]">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-6">
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">New category</p>
                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                    Tambah Kategori
                </h2>
            </div>

            <form action="{{ route('categories.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Nama Kategori</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        placeholder="Contoh: Makan, Transport, Gaji, Bonus"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Jenis Transaksi</label>
                        <select name="transaction_type"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                            <option value="income" {{ old('transaction_type') === 'income' ? 'selected' : '' }}>Income
                            </option>
                            <option value="expense"
                                {{ old('transaction_type', 'expense') === 'expense' ? 'selected' : '' }}>Expense</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Tipe Kakeibo</label>
                        <select name="kakeibo_type"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                            <option value="income" {{ old('kakeibo_type') === 'income' ? 'selected' : '' }}>Income</option>
                            <option value="needs" {{ old('kakeibo_type') === 'needs' ? 'selected' : '' }}>Needs</option>
                            <option value="wants" {{ old('kakeibo_type') === 'wants' ? 'selected' : '' }}>Wants</option>
                            <option value="culture" {{ old('kakeibo_type') === 'culture' ? 'selected' : '' }}>Culture
                            </option>
                            <option value="unexpected" {{ old('kakeibo_type') === 'unexpected' ? 'selected' : '' }}>
                                Unexpected</option>
                        </select>
                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Icon</label>
                        <input type="text" name="icon" value="{{ old('icon') }}"
                            placeholder="Contoh: wallet, coffee, car"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Color</label>
                        <input type="text" name="color" value="{{ old('color') }}"
                            placeholder="Contoh: green, red, blue"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                    </div>
                </div>

                <label class="flex items-start gap-3 rounded-2xl border border-gray-200 bg-gray-50 p-4">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}
                        class="mt-1 h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Kategori aktif</p>
                        <p class="mt-1 text-sm leading-6 text-gray-500">
                            Kalau aktif, kategori ini akan muncul di form transaksi.
                        </p>
                    </div>
                </label>

                <div class="flex flex-col gap-3 border-t border-gray-200 pt-5 sm:flex-row">
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-black">
                        Simpan Kategori
                    </button>

                    <a href="{{ route('categories.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <h3 class="text-lg font-semibold tracking-tight text-gray-900">Contoh</h3>
            <ul class="mt-4 space-y-3 text-sm text-gray-600">
                <li class="rounded-2xl bg-gray-50 px-4 py-3">Makan → expense → needs</li>
                <li class="rounded-2xl bg-gray-50 px-4 py-3">Nongkrong → expense → wants</li>
                <li class="rounded-2xl bg-gray-50 px-4 py-3">Buku → expense → culture</li>
                <li class="rounded-2xl bg-gray-50 px-4 py-3">Gaji → income → income</li>
            </ul>
        </div>
    </section>
@endsection
