@extends('layouts.app')

@section('page_title', 'Detail Kontribusi Tabungan')
@section('page_subtitle', 'Lihat detail kontribusi tabungan.')

@section('content')
    <section class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_380px]">
        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">Contribution detail</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                        {{ $savingContribution->savingTarget->name ?? '-' }}
                    </h2>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ $savingContribution->contribution_date?->format('d M Y') ?? '-' }}
                    </p>
                </div>

                @if ($savingContribution->type === 'in')
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">
                        Setor Dana
                    </span>
                @else
                    <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-700">
                        Tarik Dana
                    </span>
                @endif
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Amount</p>
                    <p
                        class="mt-2 text-lg font-semibold {{ $savingContribution->type === 'in' ? 'text-emerald-600' : 'text-red-600' }}">
                        {{ $savingContribution->type === 'in' ? '+' : '-' }}
                        Rp {{ number_format($savingContribution->amount, 0, ',', '.') }}
                    </p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Transfer Terkait</p>
                    <p class="mt-2 text-base font-semibold text-gray-900">
                        {{ optional($savingContribution->accountTransfer?->transfer_date)->format('d M Y') ?? '-' }}
                    </p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Rekening Sumber</p>
                    <p class="mt-2 text-base font-semibold text-gray-900">
                        {{ $savingContribution->accountTransfer?->fromAccount?->name ?? '-' }}
                    </p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Rekening Tujuan</p>
                    <p class="mt-2 text-base font-semibold text-gray-900">
                        {{ $savingContribution->accountTransfer?->toAccount?->name ?? '-' }}
                    </p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Nominal Transfer</p>
                    <p class="mt-2 text-base font-semibold text-gray-900">
                        Rp {{ number_format($savingContribution->accountTransfer?->amount ?? 0, 0, ',', '.') }}
                    </p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Rekening Detail</p>
                    <p class="mt-2 text-base font-semibold text-gray-900">
                        {{ $savingContribution->financialAccount->name ?? '-' }}
                    </p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4 sm:col-span-2">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Catatan</p>
                    <p class="mt-2 text-sm leading-6 text-gray-700">
                        {{ $savingContribution->note ?? '-' }}
                    </p>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('saving-contributions.edit', $savingContribution->id) }}"
                    class="inline-flex items-center justify-center rounded-xl border border-blue-300 px-4 py-2.5 text-sm font-medium text-blue-700 transition hover:bg-blue-50">
                    Edit
                </a>

                <a href="{{ route('saving-contributions.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    Kembali
                </a>
            </div>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <h3 class="text-lg font-semibold tracking-tight text-gray-900">Metadata</h3>
            <div class="mt-4 space-y-3 text-sm">
                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Dibuat</p>
                    <p class="mt-1 font-medium text-gray-900">{{ $savingContribution->created_at?->format('d M Y H:i') }}
                    </p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Terakhir diubah</p>
                    <p class="mt-1 font-medium text-gray-900">{{ $savingContribution->updated_at?->format('d M Y H:i') }}
                    </p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Sudah Dipakai</p>
                    <p class="mt-2 text-base font-semibold text-gray-900">
                        Rp {{ number_format($usedAmount, 0, ',', '.') }}
                    </p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Sisa Transfer</p>
                    <p class="mt-2 text-base font-semibold text-gray-900">
                        Rp {{ number_format($remainingAmount, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection
