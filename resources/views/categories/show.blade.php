@extends('layouts.app')

@section('page_title', 'Detail Kategori')
@section('page_subtitle', 'Lihat detail lengkap kategori.')

@section('content')
    <section class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_380px]">
        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">Category detail</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                        {{ $category->name }}
                    </h2>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('categories.edit', $category->id) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-blue-300 px-4 py-2.5 text-sm font-medium text-blue-700 transition hover:bg-blue-50">
                        Edit
                    </a>

                    <a href="{{ route('categories.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Kembali
                    </a>
                </div>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Jenis Transaksi</p>
                    <p class="mt-2 text-base font-semibold text-gray-900">{{ ucfirst($category->transaction_type) }}</p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Tipe Kakeibo</p>
                    <p class="mt-2 text-base font-semibold text-gray-900">{{ ucfirst($category->kakeibo_type) }}</p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Icon</p>
                    <p class="mt-2 text-base font-semibold text-gray-900">{{ $category->icon ?? '-' }}</p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Color</p>
                    <p class="mt-2 text-base font-semibold text-gray-900">{{ $category->color ?? '-' }}</p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4 sm:col-span-2">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Status</p>
                    <p class="mt-2">
                        @if ($category->is_active)
                            <span
                                class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">Aktif</span>
                        @else
                            <span
                                class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">Nonaktif</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <h3 class="text-lg font-semibold tracking-tight text-gray-900">Metadata</h3>
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
