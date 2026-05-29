@extends('layouts.app')

@section('page_title', 'Ledger Rekening')
@section('page_subtitle', 'Riwayat perubahan saldo rekening secara detail.')

@section('content')
    <section class="mb-6">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">Account ledger</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                        {{ $financialAccount->name }}
                    </h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-500">
                        Histori lengkap perubahan saldo rekening. Di sinilah uang lu gak bisa bohong.
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Saldo Sekarang</p>
                    <p class="mt-2 text-xl font-semibold text-gray-900">
                        Rp {{ number_format($financialAccount->balance, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-6">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5">
                <h3 class="text-lg font-semibold tracking-tight text-gray-900">Filter Ledger</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Saring histori berdasarkan tipe dan rentang tanggal.
                </p>
            </div>

            <form method="GET" action="{{ route('financial-accounts.ledger', $financialAccount->id) }}"
                class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Tipe</label>
                    <select name="type"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                        <option value="">Semua</option>
                        <option value="in" {{ request('type') === 'in' ? 'selected' : '' }}>Masuk</option>
                        <option value="out" {{ request('type') === 'out' ? 'selected' : '' }}>Keluar</option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                </div>

                <div class="flex items-end gap-3">
                    <button type="submit"
                        class="inline-flex w-full items-center justify-center rounded-xl bg-gray-900 px-4 py-3 text-sm font-medium text-white hover:bg-black">
                        Terapkan
                    </button>

                    <a href="{{ route('financial-accounts.ledger', $financialAccount->id) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </section>

    <section class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Total Histori</p>
            <h3 class="mt-3 text-2xl font-semibold tracking-tight text-gray-900">
                {{ $movements->total() }}
            </h3>
            <p class="mt-2 text-sm text-gray-500">Jumlah movement sesuai filter.</p>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Provider</p>
            <h3 class="mt-3 text-2xl font-semibold tracking-tight text-gray-900">
                {{ $financialAccount->provider ?? '-' }}
            </h3>
            <p class="mt-2 text-sm text-gray-500">Sumber rekening yang sedang dilihat.</p>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Tipe Rekening</p>
            <h3 class="mt-3 text-2xl font-semibold tracking-tight text-gray-900">
                {{ ucfirst(str_replace('_', ' ', $financialAccount->type)) }}
            </h3>
            <p class="mt-2 text-sm text-gray-500">Jenis akun penyimpanan uang.</p>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Status</p>
            <h3 class="mt-3 text-2xl font-semibold tracking-tight text-gray-900">
                {{ $financialAccount->is_active ? 'Aktif' : 'Nonaktif' }}
            </h3>
            <p class="mt-2 text-sm text-gray-500">Status rekening saat ini.</p>
        </div>
    </section>

    <section class="hidden lg:block">
        <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-5">
                <h3 class="text-lg font-semibold tracking-tight text-gray-900">Riwayat Perubahan Saldo</h3>
                <p class="mt-1 text-sm text-gray-500">Versi desktop dengan tabel penuh.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-[0.14em] text-gray-500">
                        <tr>
                            <th class="px-6 py-4">Waktu</th>
                            <th class="px-6 py-4">Tipe</th>
                            <th class="px-6 py-4">Amount</th>
                            <th class="px-6 py-4">Saldo Sebelum</th>
                            <th class="px-6 py-4">Saldo Sesudah</th>
                            <th class="px-6 py-4">Referensi</th>
                            <th class="px-6 py-4">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($movements as $movement)
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-gray-600">
                                    {{ $movement->created_at?->format('d M Y H:i') }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    @if ($movement->type === 'in')
                                        <span
                                            class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">
                                            Masuk
                                        </span>
                                    @else
                                        <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-700">
                                            Keluar
                                        </span>
                                    @endif
                                </td>
                                <td
                                    class="whitespace-nowrap px-6 py-4 font-semibold {{ $movement->type === 'in' ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $movement->type === 'in' ? '+' : '-' }}
                                    Rp {{ number_format($movement->amount, 0, ',', '.') }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-gray-700">
                                    Rp {{ number_format($movement->balance_before, 0, ',', '.') }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-gray-900 font-medium">
                                    Rp {{ number_format($movement->balance_after, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ $movement->ref_type ?? '-' }}
                                    @if ($movement->ref_id)
                                        #{{ $movement->ref_id }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ $movement->note ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
                                    Belum ada histori movement untuk rekening ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="space-y-4 lg:hidden">
        @forelse ($movements as $movement)
            <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm text-gray-500">
                            {{ $movement->created_at?->format('d M Y H:i') }}
                        </p>
                        <h3 class="mt-1 text-base font-semibold text-gray-900">
                            {{ $movement->ref_type ?? 'movement' }}
                            @if ($movement->ref_id)
                                #{{ $movement->ref_id }}
                            @endif
                        </h3>
                    </div>

                    @if ($movement->type === 'in')
                        <span class="shrink-0 rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">
                            Masuk
                        </span>
                    @else
                        <span class="shrink-0 rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-700">
                            Keluar
                        </span>
                    @endif
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-2xl bg-gray-50 p-3">
                        <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Amount</p>
                        <p class="mt-1 font-semibold {{ $movement->type === 'in' ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $movement->type === 'in' ? '+' : '-' }}
                            Rp {{ number_format($movement->amount, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-gray-50 p-3">
                        <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Saldo Sesudah</p>
                        <p class="mt-1 font-semibold text-gray-900">
                            Rp {{ number_format($movement->balance_after, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-gray-50 p-3">
                        <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Saldo Sebelum</p>
                        <p class="mt-1 font-medium text-gray-900">
                            Rp {{ number_format($movement->balance_before, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-gray-50 p-3">
                        <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Catatan</p>
                        <p class="mt-1 font-medium text-gray-900">
                            {{ $movement->note ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>
        @empty
            <div
                class="rounded-3xl border border-dashed border-gray-300 bg-white p-10 text-center text-sm text-gray-500 shadow-sm">
                Belum ada histori movement untuk rekening ini.
            </div>
        @endforelse
    </section>

    <section class="mt-6">
        <div class="rounded-3xl border border-gray-200 bg-white px-5 py-4 shadow-sm sm:px-6">
            {{ $movements->links() }}
        </div>
    </section>
@endsection
