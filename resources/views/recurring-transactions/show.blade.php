@extends('layouts.app')

@section('page_title', 'Detail Recurring Transaction')
@section('page_subtitle', 'Lihat detail lengkap transaksi rutin.')

@section('content')
    <section class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_380px]">
        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">Recurring detail</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                        {{ $recurringTransaction->title }}
                    </h2>
                    <p class="mt-2 text-sm text-gray-500">
                        Rp {{ number_format($recurringTransaction->amount, 0, ',', '.') }}
                    </p>
                </div>

                <div>
                    @if ($recurringTransaction->is_active)
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">Aktif</span>
                    @else
                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">Nonaktif</span>
                    @endif
                </div>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Jenis</p>
                    <p class="mt-2 text-base font-semibold text-gray-900">{{ ucfirst($recurringTransaction->type) }}</p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Frekuensi</p>
                    <p class="mt-2 text-base font-semibold text-gray-900">{{ ucfirst($recurringTransaction->frequency) }}
                    </p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Rekening</p>
                    <p class="mt-2 text-base font-semibold text-gray-900">
                        {{ $recurringTransaction->financialAccount->name ?? '-' }}</p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Kategori</p>
                    <p class="mt-2 text-base font-semibold text-gray-900">{{ $recurringTransaction->category->name ?? '-' }}
                    </p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Start Date</p>
                    <p class="mt-2 text-base font-semibold text-gray-900">
                        {{ optional($recurringTransaction->start_date)->format('d M Y') }}</p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Next Run Date</p>
                    <p class="mt-2 text-base font-semibold text-gray-900">
                        {{ optional($recurringTransaction->next_run_date)->format('d M Y') }}</p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4 sm:col-span-2">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Catatan</p>
                    <p class="mt-2 text-sm leading-6 text-gray-700">{{ $recurringTransaction->note ?: '-' }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <h3 class="text-lg font-semibold tracking-tight text-gray-900">Metadata</h3>
            <div class="mt-4 space-y-3 text-sm">
                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Last Run</p>
                    <p class="mt-1 font-medium text-gray-900">
                        {{ $recurringTransaction->last_run_date ? $recurringTransaction->last_run_date->format('d M Y') : '-' }}
                    </p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Dibuat</p>
                    <p class="mt-1 font-medium text-gray-900">{{ $recurringTransaction->created_at?->format('d M Y H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection
