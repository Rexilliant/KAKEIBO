@extends('layouts.app')

@section('page_title', 'Transfer Antar Rekening')
@section('page_subtitle', 'Pindahkan saldo antar rekening tanpa bikin data berantakan.')

@section('content')
    <section class="mb-6">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">Account transfer</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                        Transfer Antar Rekening
                    </h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-500">
                        Pindahkan uang dari satu rekening ke rekening lain dengan ledger yang jelas.
                    </p>
                </div>

                <a href="{{ route('account-transfers.create') }}"
                    class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700">
                    + Buat Transfer
                </a>
            </div>
        </div>
    </section>

    <section class="mb-6">
        <form method="GET" action="{{ route('account-transfers.index') }}"
            class="grid gap-4 rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:grid-cols-3 sm:p-6">
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Bulan</label>
                <select name="month" class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                    <option value="">Semua bulan</option>
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}"
                            {{ (string) request('month') === (string) $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Tahun</label>
                <input type="number" name="year" value="{{ request('year') }}"
                    class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
            </div>

            <div class="flex items-end">
                <button type="submit"
                    class="w-full rounded-xl bg-gray-900 px-4 py-3 text-sm font-medium text-white hover:bg-black">
                    Filter
                </button>
            </div>
        </form>
    </section>

    <section class="hidden lg:block">
        <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-5">
                <h3 class="text-lg font-semibold tracking-tight text-gray-900">Daftar Transfer</h3>
                <p class="mt-1 text-sm text-gray-500">Versi desktop dengan tampilan tabel penuh.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-[0.14em] text-gray-500">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Dari</th>
                            <th class="px-6 py-4">Ke</th>
                            <th class="px-6 py-4 text-right">Nominal</th>
                            <th class="px-6 py-4">Catatan</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($transfers as $transfer)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-gray-600">
                                    {{ optional($transfer->transfer_date)->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ $transfer->fromAccount->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ $transfer->toAccount->name ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right font-semibold text-gray-900">
                                    Rp {{ number_format($transfer->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    <div class="max-w-xs truncate">{{ $transfer->note ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('account-transfers.show', $transfer->id) }}"
                                            class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50">
                                            Detail
                                        </a>

                                        <a href="{{ route('saving-contributions.create', ['account_transfer_id' => $transfer->id, 'type' => 'in']) }}"
                                            class="rounded-lg border border-emerald-300 px-3 py-2 text-xs font-medium text-emerald-700 transition hover:bg-emerald-50">
                                            Jadikan Kontribusi
                                        </a>

                                        <form action="{{ route('account-transfers.destroy', $transfer->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Yakin mau hapus transfer ini? Saldo akan dikembalikan.')">
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
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
                                    Belum ada transfer antar rekening.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="space-y-4 lg:hidden">
        @forelse ($transfers as $transfer)
            <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">
                            Rp {{ number_format($transfer->amount, 0, ',', '.') }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ optional($transfer->transfer_date)->format('d M Y') }}
                        </p>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-2xl bg-gray-50 p-3">
                        <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Dari</p>
                        <p class="mt-1 font-medium text-gray-900">{{ $transfer->fromAccount->name ?? '-' }}</p>
                    </div>

                    <div class="rounded-2xl bg-gray-50 p-3">
                        <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Ke</p>
                        <p class="mt-1 font-medium text-gray-900">{{ $transfer->toAccount->name ?? '-' }}</p>
                    </div>
                </div>

                @if ($transfer->note)
                    <div class="mt-4 rounded-2xl bg-gray-50 p-3 text-sm text-gray-600">
                        {{ $transfer->note }}
                    </div>
                @endif

                <div class="mt-4 grid grid-cols-3 gap-2">
                    <a href="{{ route('account-transfers.show', $transfer->id) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-3 py-2.5 text-xs font-medium text-gray-700 transition hover:bg-gray-50">
                        Detail
                    </a>

                    <a href="{{ route('saving-contributions.create', ['account_transfer_id' => $transfer->id, 'type' => 'in']) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-emerald-300 px-3 py-2.5 text-xs font-medium text-emerald-700 transition hover:bg-emerald-50">
                        Kontribusi
                    </a>

                    <form action="{{ route('account-transfers.destroy', $transfer->id) }}" method="POST"
                        onsubmit="return confirm('Yakin mau hapus transfer ini? Saldo akan dikembalikan.')">
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
                Belum ada transfer antar rekening.
            </div>
        @endforelse
    </section>

    <section class="mt-6">
        <div class="rounded-3xl border border-gray-200 bg-white px-5 py-4 shadow-sm sm:px-6">
            {{ $transfers->links() }}
        </div>
    </section>
@endsection