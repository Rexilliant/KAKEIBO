@extends('layouts.app')

@section('page_title', 'Tambah Anggaran')
@section('page_subtitle', 'Atur batas pengeluaran per kategori.')

@section('content')
    <section class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_380px]">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-6">
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">New budget</p>
                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                    Tambah Anggaran
                </h2>
            </div>

            <form action="{{ route('budgets.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Kategori</label>
                    <select name="category_id"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                        <option value="">Pilih kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ (string) old('category_id') === (string) $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid gap-5 md:grid-cols-3">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Bulan</label>
                        <select name="month"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}"
                                    {{ (string) old('month', now()->month) === (string) $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Tahun</label>
                        <input type="number" name="year" value="{{ old('year', now()->year) }}"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Nominal</label>
                        <input type="number" step="0.01" min="0" name="amount" value="{{ old('amount') }}"
                            placeholder="0"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Mode Budget</label>
                    <select name="enforcement_level"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                        <option value="soft" {{ old('enforcement_level', 'soft') === 'soft' ? 'selected' : '' }}>
                            Soft Warning
                        </option>
                        <option value="hard" {{ old('enforcement_level') === 'hard' ? 'selected' : '' }}>
                            Hard Limit
                        </option>
                    </select>
                    <p class="mt-2 text-xs text-gray-500">
                        Soft = cuma warning. Hard = transaksi ditolak kalau melewati budget.
                    </p>
                </div>

                <div class="flex flex-col gap-3 border-t border-gray-200 pt-5 sm:flex-row">
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-black">
                        Simpan Anggaran
                    </button>

                    <a href="{{ route('budgets.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <h3 class="text-lg font-semibold tracking-tight text-gray-900">Catatan</h3>
            <ul class="mt-4 space-y-3 text-sm text-gray-600">
                <li class="rounded-2xl bg-gray-50 px-4 py-3">Anggaran dibuat per kategori, per bulan, per tahun.</li>
                <li class="rounded-2xl bg-gray-50 px-4 py-3">Kategori yang muncul hanya kategori pengeluaran.</li>
                <li class="rounded-2xl bg-gray-50 px-4 py-3">Kalau kategori + bulan + tahun sama, sistem akan update data
                    lama.</li>
            </ul>
        </div>
    </section>
@endsection
