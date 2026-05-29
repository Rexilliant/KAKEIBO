@extends('layouts.app')

@section('page_title', 'Kontribusi Tabungan')
@section('page_subtitle', 'Kelola detail kontribusi tabungan berdasarkan transfer yang sudah ada.')

@section('content')
    <section class="mb-6">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">Saving contribution</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                        Daftar Kontribusi Tabungan
                    </h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-500">
                        Kontribusi tabungan dihubungkan ke transfer dana yang sudah ada, jadi catatannya rapi dan gak bikin
                        saldo dobel kayak sistem yang lagi mabuk.
                    </p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('saving-contributions.create') }}"
                        class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700">
                        + Tambah Kontribusi
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="hidden lg:block">
        <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-5">
                <h3 class="text-lg font-semibold tracking-tight text-gray-900">Daftar Kontribusi</h3>
                <p class="mt-1 text-sm text-gray-500">Versi desktop dengan informasi transfer lengkap.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-[0.14em] text-gray-500">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Target</th>
                            <th class="px-6 py-4">Transfer</th>
                            <th class="px-6 py-4">Rekening Sumber</th>
                            <th class="px-6 py-4">Rekening Tujuan</th>
                            <th class="px-6 py-4">Tipe</th>
                            <th class="px-6 py-4 text-right">Sisa Transfer</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($contributions as $contribution)
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-gray-600">
                                    {{ optional($contribution->contribution_date)->format('d M Y') }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="max-w-[220px] truncate font-medium text-gray-900">
                                        {{ $contribution->savingTarget->name ?? '-' }}
                                    </div>
                                    @if ($contribution->note)
                                        <div class="mt-1 max-w-[220px] truncate text-xs text-gray-500">
                                            {{ $contribution->note }}
                                        </div>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-gray-600">
                                    @if ($contribution->accountTransfer)
                                        <div class="font-medium text-gray-900">
                                            {{ optional($contribution->accountTransfer->transfer_date)->format('d M Y') }}
                                        </div>
                                        <div class="mt-1 text-xs text-gray-500">
                                            Rp {{ number_format($contribution->accountTransfer->amount ?? 0, 0, ',', '.') }}
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-gray-600">
                                    {{ $contribution->accountTransfer?->fromAccount?->name ?? '-' }}
                                </td>

                                <td class="px-6 py-4 text-gray-600">
                                    {{ $contribution->accountTransfer?->toAccount?->name ?? '-' }}
                                </td>

                                <td class="px-6 py-4">
                                    @if ($contribution->type === 'in')
                                        <div class="flex flex-col gap-1">
                                            <span
                                                class="w-fit rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">
                                                Setor
                                            </span>
                                            <span class="text-sm font-semibold text-emerald-600">
                                                + Rp {{ number_format($contribution->amount, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    @else
                                        <div class="flex flex-col gap-1">
                                            <span
                                                class="w-fit rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-700">
                                                Tarik
                                            </span>
                                            <span class="text-sm font-semibold text-red-600">
                                                - Rp {{ number_format($contribution->amount, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-right font-medium text-gray-900">
                                    Rp {{ number_format($contribution->remaining_amount, 0, ',', '.') }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('saving-contributions.show', $contribution->id) }}"
                                            class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50">
                                            Detail
                                        </a>

                                        <a href="{{ route('saving-contributions.edit', $contribution->id) }}"
                                            class="rounded-lg border border-blue-300 px-3 py-2 text-xs font-medium text-blue-700 transition hover:bg-blue-50">
                                            Edit
                                        </a>

                                        <form action="{{ route('saving-contributions.destroy', $contribution->id) }}"
                                            method="POST" onsubmit="return confirm('Yakin mau hapus kontribusi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rounded-lg border border-red-300 px-3 py-2 text-xs font-medium text-red-700 transition hover:bg-red-50">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-500">
                                    Belum ada kontribusi tabungan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="space-y-4 lg:hidden">
        @forelse ($contributions as $contribution)
            <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h3 class="truncate text-base font-semibold text-gray-900">
                            {{ $contribution->savingTarget->name ?? '-' }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ optional($contribution->contribution_date)->format('d M Y') }}
                        </p>
                    </div>

                    @if ($contribution->type === 'in')
                        <span class="shrink-0 rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">
                            Setor
                        </span>
                    @else
                        <span class="shrink-0 rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-700">
                            Tarik
                        </span>
                    @endif
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-2xl bg-gray-50 p-3">
                        <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Transfer</p>
                        <p class="mt-1 truncate font-medium text-gray-900">
                            {{ optional($contribution->accountTransfer?->transfer_date)->format('d M Y') ?? '-' }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500">
                            Rp {{ number_format($contribution->accountTransfer?->amount ?? 0, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-gray-50 p-3">
                        <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Nominal Kontribusi</p>
                        <p
                            class="mt-1 font-medium {{ $contribution->type === 'in' ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $contribution->type === 'in' ? '+' : '-' }}
                            Rp {{ number_format($contribution->amount, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-gray-50 p-3 col-span-2">
                        <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Rekening Sumber → Tujuan</p>
                        <p class="mt-1 font-medium text-gray-900">
                            {{ $contribution->accountTransfer?->fromAccount?->name ?? '-' }}
                            →
                            {{ $contribution->accountTransfer?->toAccount?->name ?? '-' }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-gray-50 p-3 col-span-2">
                        <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Sisa Transfer</p>
                        <p class="mt-1 font-medium text-gray-900">
                            Rp {{ number_format($contribution->remaining_amount, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                @if ($contribution->note)
                    <div class="mt-4 rounded-2xl bg-gray-50 p-3 text-sm text-gray-600">
                        {{ $contribution->note }}
                    </div>
                @endif

                <div class="mt-4 grid grid-cols-3 gap-2">
                    <a href="{{ route('saving-contributions.show', $contribution->id) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-3 py-2.5 text-xs font-medium text-gray-700 transition hover:bg-gray-50">
                        Detail
                    </a>

                    <a href="{{ route('saving-contributions.edit', $contribution->id) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-blue-300 px-3 py-2.5 text-xs font-medium text-blue-700 transition hover:bg-blue-50">
                        Edit
                    </a>

                    <form action="{{ route('saving-contributions.destroy', $contribution->id) }}" method="POST"
                        onsubmit="return confirm('Yakin mau hapus kontribusi ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex w-full items-center justify-center rounded-xl border border-red-300 px-3 py-2.5 text-xs font-medium text-red-700 transition hover:bg-red-50">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div
                class="rounded-3xl border border-dashed border-gray-300 bg-white p-10 text-center text-sm text-gray-500 shadow-sm">
                Belum ada kontribusi tabungan.
            </div>
        @endforelse
    </section>

    <section class="mt-6">
        <div class="rounded-3xl border border-gray-200 bg-white px-5 py-4 shadow-sm sm:px-6">
            {{ $contributions->links() }}
        </div>
    </section>
@endsection
