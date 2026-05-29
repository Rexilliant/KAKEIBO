@extends('layouts.app')

@section('page_title', 'Edit Kategori')
@section('page_subtitle', 'Perbarui kategori yang sudah ada.')

@section('content')
    <section class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_380px]">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-6">
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">Edit category</p>
                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                    Edit Kategori
                </h2>
            </div>

            <form action="{{ route('categories.update', $category->id) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Nama Kategori</label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Jenis Transaksi</label>
                        <select name="transaction_type"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                            <option value="income"
                                {{ old('transaction_type', $category->transaction_type) === 'income' ? 'selected' : '' }}>
                                Income</option>
                            <option value="expense"
                                {{ old('transaction_type', $category->transaction_type) === 'expense' ? 'selected' : '' }}>
                                Expense</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Tipe Kakeibo</label>
                        <select name="kakeibo_type"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                            <option value="income"
                                {{ old('kakeibo_type', $category->kakeibo_type) === 'income' ? 'selected' : '' }}>Income
                            </option>
                            <option value="needs"
                                {{ old('kakeibo_type', $category->kakeibo_type) === 'needs' ? 'selected' : '' }}>Needs
                            </option>
                            <option value="wants"
                                {{ old('kakeibo_type', $category->kakeibo_type) === 'wants' ? 'selected' : '' }}>Wants
                            </option>
                            <option value="culture"
                                {{ old('kakeibo_type', $category->kakeibo_type) === 'culture' ? 'selected' : '' }}>Culture
                            </option>
                            <option value="unexpected"
                                {{ old('kakeibo_type', $category->kakeibo_type) === 'unexpected' ? 'selected' : '' }}>
                                Unexpected</option>
                        </select>
                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Icon</label>
                        <input type="text" name="icon" value="{{ old('icon', $category->icon) }}"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Color</label>
                        <input type="text" name="color" value="{{ old('color', $category->color) }}"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                    </div>
                </div>

                <label class="flex items-start gap-3 rounded-2xl border border-gray-200 bg-gray-50 p-4">
                    <input type="checkbox" name="is_active" value="1"
                        {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                        class="mt-1 h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Kategori aktif</p>
                        <p class="mt-1 text-sm leading-6 text-gray-500">Kalau nonaktif, kategori ini gak akan muncul di form
                            transaksi.</p>
                    </div>
                </label>

                <div class="flex flex-col gap-3 border-t border-gray-200 pt-5 sm:flex-row">
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-black">
                        Update Kategori
                    </button>

                    <a href="{{ route('categories.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <h3 class="text-lg font-semibold tracking-tight text-gray-900">Info</h3>
            <div class="mt-4 space-y-3 text-sm">
                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Dibuat</p>
                    <p class="mt-1 font-medium text-gray-900">{{ $category->created_at?->format('d M Y H:i') }}</p>
                </div>
                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Terakhir diubah</p>
                    <p class="mt-1 font-medium text-gray-900">{{ $category->updated_at?->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>
    </section>
@endsection
