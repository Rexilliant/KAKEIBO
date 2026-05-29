@extends('layouts.app')

@section('page_title', 'Detail Rekening')
@section('page_subtitle', 'Lihat detail lengkap rekening.')

@section('content')
    <section class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_380px]">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">Account detail</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                        {{ $financialAccount->name }}
                    </h2>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ ucfirst(str_replace('_', ' ', $financialAccount->type)) }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('financial-accounts.edit', $financialAccount->id) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-blue-300 px-4 py-2.5 text-sm font-medium text-blue-700 transition hover:bg-blue-50">
                        Edit
                    </a>

                    <a href="{{ route('financial-accounts.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Kembali
                    </a>
                </div>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Provider</p>
                    <p class="mt-2 text-base font-semibold text-gray-900">{{ $financialAccount->provider ?? '-' }}</p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Nomor Rekening / Akun</p>
                    <p class="mt-2 text-base font-semibold text-gray-900">{{ $financialAccount->account_number ?? '-' }}</p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Saldo</p>
                    <p class="mt-2 text-base font-semibold text-gray-900">
                        Rp {{ number_format($financialAccount->balance, 0, ',', '.') }}
                    </p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Status</p>
                    <p class="mt-2">
                        @if ($financialAccount->is_active)
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
                    <p class="mt-1 font-medium text-gray-900">{{ $financialAccount->created_at?->format('d M Y H:i') }}</p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Terakhir diubah</p>
                    <p class="mt-1 font-medium text-gray-900">{{ $financialAccount->updated_at?->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>
    </section>
@endsection
