@extends('layouts.app')

@section('page_title', 'Detail Anggaran')
@section('page_subtitle', 'Lihat budget, actual spending, sisa, dan statusnya.')

@section('content')
    <section class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_380px]">
        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">Budget detail</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                        {{ $budget->category->name ?? '-' }}
                    </h2>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ \Carbon\Carbon::create()->month($budget->month)->translatedFormat('F') }} {{ $budget->year }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    @if ($budgetStatus === 'over')
                        <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-700">Over Budget</span>
                    @elseif ($budgetStatus === 'warning')
                        <span class="rounded-full bg-yellow-50 px-3 py-1 text-xs font-medium text-yellow-700">Hampir
                            Habis</span>
                    @else
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">Aman</span>
                    @endif
                </div>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Budget</p>
                    <p class="mt-2 text-base font-semibold text-gray-900">
                        Rp {{ number_format($budget->amount, 0, ',', '.') }}
                    </p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Actual Spending</p>
                    <p class="mt-2 text-base font-semibold text-gray-900">
                        Rp {{ number_format($spentAmount, 0, ',', '.') }}
                    </p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Sisa</p>
                    <p class="mt-2 text-base font-semibold {{ $remainingAmount < 0 ? 'text-red-600' : 'text-gray-900' }}">
                        Rp {{ number_format($remainingAmount, 0, ',', '.') }}
                    </p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Progress</p>
                    <p class="mt-2 text-base font-semibold text-gray-900">
                        {{ number_format($progressPercentage, 0) }}%
                    </p>
                </div>
            </div>

            <div class="mt-6">
                <div class="h-3 w-full rounded-full bg-gray-200">
                    <div class="h-3 rounded-full
                        {{ $budgetStatus === 'over' ? 'bg-red-500' : ($budgetStatus === 'warning' ? 'bg-yellow-500' : 'bg-emerald-600') }}"
                        style="width: {{ min(100, $progressPercentage) }}%">
                    </div>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-2">
                <a href="{{ route('budgets.edit', $budget->id) }}"
                    class="inline-flex items-center justify-center rounded-xl border border-blue-300 px-4 py-2.5 text-sm font-medium text-blue-700 transition hover:bg-blue-50">
                    Edit
                </a>

                <a href="{{ route('budgets.index') }}"
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
