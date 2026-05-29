@extends('layouts.app')

@section('page_title', 'Edit Anggaran')
@section('page_subtitle', 'Perbarui nominal dan periode anggaran.')

@section('content')
    <section class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_380px]">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-6">
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">Edit budget</p>
                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                    Edit Anggaran
                </h2>
            </div>

            <form action="{{ route('budgets.update', $budget->id) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Kategori</label>
                    <select name="category_id"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ (string) old('category_id', $budget->category_id) === (string) $category->id ? 'selected' : '' }}>
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
                                    {{ (string) old('month', $budget->month) === (string) $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Tahun</label>
                        <input type="number" name="year" value="{{ old('year', $budget->year) }}"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Nominal</label>
                        <input type="number" step="0.01" min="0" name="amount"
                            value="{{ old('amount', $budget->amount) }}"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Mode Budget</label>
                    <select name="enforcement_level"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                        <option value="soft"
                            {{ old('enforcement_level', $budget->enforcement_level) === 'soft' ? 'selected' : '' }}>
                            Soft Warning
                        </option>
                        <option value="hard"
                            {{ old('enforcement_level', $budget->enforcement_level) === 'hard' ? 'selected' : '' }}>
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
                        Update Anggaran
                    </button>

                    <a href="{{ route('budgets.index') }}"
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
                    <p class="mt-1 font-medium text-gray-900">{{ $budget->created_at?->format('d M Y H:i') }}</p>
                </div>
                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Terakhir diubah</p>
                    <p class="mt-1 font-medium text-gray-900">{{ $budget->updated_at?->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>
    </section>
@endsection
