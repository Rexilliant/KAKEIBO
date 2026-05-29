@extends('layouts.app')

@section('page_title', 'Detail Transfer')
@section('page_subtitle', 'Lihat detail lengkap transfer antar rekening.')

@section('content')
    <section class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_380px]">
        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">Transfer detail</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                        Rp {{ number_format($accountTransfer->amount, 0, ',', '.') }}
                    </h2>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ optional($accountTransfer->transfer_date)->format('d M Y') }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('saving-contributions.create', ['account_transfer_id' => $accountTransfer->id, 'type' => 'in']) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-emerald-300 px-4 py-2.5 text-sm font-medium text-emerald-700 transition hover:bg-emerald-50">
                        Jadikan Kontribusi Saving
                    </a>

                    <a href="{{ route('account-transfers.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Kembali
                    </a>
                </div>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Dari Rekening</p>
                    <p class="mt-2 text-base font-semibold text-gray-900">
                        {{ $accountTransfer->fromAccount->name ?? '-' }}
                    </p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Ke Rekening</p>
                    <p class="mt-2 text-base font-semibold text-gray-900">
                        {{ $accountTransfer->toAccount->name ?? '-' }}
                    </p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4 sm:col-span-2">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Catatan</p>
                    <p class="mt-2 text-sm leading-6 text-gray-700">
                        {{ $accountTransfer->note ?: '-' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <h3 class="text-lg font-semibold tracking-tight text-gray-900">Metadata</h3>
            <div class="mt-4 space-y-3 text-sm">
                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Dibuat</p>
                    <p class="mt-1 font-medium text-gray-900">{{ $accountTransfer->created_at?->format('d M Y H:i') }}</p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Terakhir diubah</p>
                    <p class="mt-1 font-medium text-gray-900">{{ $accountTransfer->updated_at?->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>
    </section>
@endsection
