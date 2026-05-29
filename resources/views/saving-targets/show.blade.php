@extends('layouts.app')

@section('page_title', 'Detail Target Tabungan')
@section('page_subtitle', 'Lihat detail lengkap target tabungan.')

@section('content')
    @php
        $progress =
            $savingTarget->target_amount > 0
                ? min(100, ($savingTarget->current_amount / $savingTarget->target_amount) * 100)
                : 0;
        $remaining = max(0, $savingTarget->target_amount - $savingTarget->current_amount);
    @endphp

    <section class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_380px]">
        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">Saving target detail</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                        {{ $savingTarget->name }}
                    </h2>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ $savingTarget->financialAccount->name ?? 'Belum pilih rekening' }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('saving-contributions.create', ['target' => $savingTarget->id, 'type' => 'in']) }}"
                        class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-emerald-700">
                        + Setor Dana
                    </a>

                    <a href="{{ route('saving-contributions.create', ['target' => $savingTarget->id, 'type' => 'out']) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-red-300 px-4 py-2.5 text-sm font-medium text-red-700 transition hover:bg-red-50">
                        - Tarik Dana
                    </a>

                    <a href="{{ route('saving-targets.edit', $savingTarget->id) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-blue-300 px-4 py-2.5 text-sm font-medium text-blue-700 transition hover:bg-blue-50">
                        Edit
                    </a>

                    <a href="{{ route('saving-targets.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Kembali
                    </a>
                </div>
            </div>

            <div class="mt-6 rounded-3xl bg-gray-50 p-5">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Terkumpul</span>
                    <span class="font-medium text-gray-900">Target</span>
                </div>
                <div class="mt-2 flex items-center justify-between">
                    <span class="text-lg font-semibold text-gray-900">Rp
                        {{ number_format($savingTarget->current_amount, 0, ',', '.') }}</span>
                    <span class="text-lg font-semibold text-gray-900">Rp
                        {{ number_format($savingTarget->target_amount, 0, ',', '.') }}</span>
                </div>
                <div class="mt-4 h-3 w-full rounded-full bg-gray-200">
                    <div class="h-3 rounded-full bg-emerald-600" style="width: {{ $progress }}%"></div>
                </div>
                <div class="mt-3 text-sm text-gray-500">
                    Progress {{ number_format($progress, 0) }}% · Sisa Rp {{ number_format($remaining, 0, ',', '.') }}
                </div>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Deadline</p>
                    <p class="mt-2 text-base font-semibold text-gray-900">
                        {{ $savingTarget->deadline?->format('d M Y') ?? '-' }}
                    </p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Status</p>
                    <p class="mt-2">
                        @if ($savingTarget->status === 'active')
                            <span
                                class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">Aktif</span>
                        @elseif ($savingTarget->status === 'completed')
                            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700">Selesai</span>
                        @else
                            <span
                                class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">Dibatalkan</span>
                        @endif
                    </p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4 sm:col-span-2">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Catatan</p>
                    <p class="mt-2 text-sm leading-6 text-gray-700">
                        {{ $savingTarget->note ?? '-' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                <h3 class="text-lg font-semibold tracking-tight text-gray-900">Metadata</h3>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="rounded-2xl bg-gray-50 p-4">
                        <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Dibuat</p>
                        <p class="mt-1 font-medium text-gray-900">{{ $savingTarget->created_at?->format('d M Y H:i') }}</p>
                    </div>

                    <div class="rounded-2xl bg-gray-50 p-4">
                        <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Terakhir diubah</p>
                        <p class="mt-1 font-medium text-gray-900">{{ $savingTarget->updated_at?->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold tracking-tight text-gray-900">Kontribusi Terbaru</h3>
                    <a href="{{ route('saving-contributions.index') }}"
                        class="text-sm font-medium text-emerald-700 hover:text-emerald-800">
                        Lihat
                    </a>
                </div>

                <div class="mt-4 space-y-3">
                    @forelse ($savingTarget->contributions->take(5) as $contribution)
                        <div class="rounded-2xl bg-gray-50 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div class="text-sm text-gray-700">
                                    {{ $contribution->contribution_date?->format('d M Y') ?? '-' }}
                                </div>
                                <div class="text-sm font-semibold text-emerald-700">
                                    Rp {{ number_format($contribution->amount, 0, ',', '.') }}
                                </div>
                            </div>
                            @if ($contribution->note)
                                <div class="mt-2 text-xs text-gray-500">{{ $contribution->note }}</div>
                            @endif
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-gray-300 p-5 text-center text-sm text-gray-500">
                            Belum ada kontribusi untuk target ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection
