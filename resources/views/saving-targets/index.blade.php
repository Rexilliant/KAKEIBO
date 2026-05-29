@extends('layouts.app')

@section('page_title', 'Target Tabungan')
@section('page_subtitle', 'Kelola semua tujuan tabunganmu dengan rapi.')

@section('content')
    <section class="mb-6">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">Saving goals</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                        Kelola Target Tabungan
                    </h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-500">
                        Biar uang lu gak cuma numpuk tanpa arah. Kasih nama, kasih tujuan, lalu kejar dengan sadar.
                    </p>
                </div>

                <div>
                    <a href="{{ route('saving-targets.create') }}"
                        class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700">
                        + Tambah Target
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Total Target</p>
            <h3 class="mt-3 text-2xl font-semibold tracking-tight text-gray-900">{{ $savingTargets->total() }}</h3>
            <p class="mt-2 text-sm text-gray-500">Jumlah target tabungan yang tersimpan.</p>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Halaman Saat Ini</p>
            <h3 class="mt-3 text-2xl font-semibold tracking-tight text-gray-900">{{ $savingTargets->currentPage() }}</h3>
            <p class="mt-2 text-sm text-gray-500">Posisi data yang sedang dilihat.</p>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Per Halaman</p>
            <h3 class="mt-3 text-2xl font-semibold tracking-tight text-gray-900">{{ $savingTargets->perPage() }}</h3>
            <p class="mt-2 text-sm text-gray-500">Jumlah item per halaman.</p>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Rentang Data</p>
            <h3 class="mt-3 text-2xl font-semibold tracking-tight text-gray-900">
                {{ $savingTargets->firstItem() ?? 0 }} - {{ $savingTargets->lastItem() ?? 0 }}
            </h3>
            <p class="mt-2 text-sm text-gray-500">Baris yang sedang tampil sekarang.</p>
        </div>
    </section>

    <section class="hidden lg:block">
        <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-5">
                <h3 class="text-lg font-semibold tracking-tight text-gray-900">Daftar Target Tabungan</h3>
                <p class="mt-1 text-sm text-gray-500">Versi desktop dengan tampilan tabel penuh.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-[0.14em] text-gray-500">
                        <tr>
                            <th class="px-6 py-4">Nama</th>
                            <th class="px-6 py-4">Rekening</th>
                            <th class="px-6 py-4 text-right">Terkumpul</th>
                            <th class="px-6 py-4 text-right">Target</th>
                            <th class="px-6 py-4">Deadline</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($savingTargets as $target)
                            @php
                                $progress =
                                    $target->target_amount > 0
                                        ? min(100, ($target->current_amount / $target->target_amount) * 100)
                                        : 0;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $target->name }}</div>
                                    <div class="mt-1 h-2 w-32 rounded-full bg-gray-200">
                                        <div class="h-2 rounded-full bg-emerald-600" style="width: {{ $progress }}%">
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ $target->financialAccount->name ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right font-medium text-gray-900">
                                    Rp {{ number_format($target->current_amount, 0, ',', '.') }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right font-semibold text-gray-900">
                                    Rp {{ number_format($target->target_amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ $target->deadline?->format('d M Y') ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if ($target->status === 'active')
                                        <span
                                            class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">Aktif</span>
                                    @elseif ($target->status === 'completed')
                                        <span
                                            class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700">Selesai</span>
                                    @else
                                        <span
                                            class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">Dibatalkan</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('saving-targets.show', $target->id) }}"
                                            class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50">
                                            Detail
                                        </a>

                                        <a href="{{ route('saving-targets.edit', $target->id) }}"
                                            class="rounded-lg border border-blue-300 px-3 py-2 text-xs font-medium text-blue-700 transition hover:bg-blue-50">
                                            Edit
                                        </a>

                                        <form action="{{ route('saving-targets.destroy', $target->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin mau hapus target tabungan ini?')">
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
                                <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
                                    Belum ada target tabungan. Bikin satu dulu, biar uangmu punya alasan buat tinggal.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="space-y-4 lg:hidden">
        @forelse ($savingTargets as $target)
            @php
                $progress =
                    $target->target_amount > 0 ? min(100, ($target->current_amount / $target->target_amount) * 100) : 0;
            @endphp
            <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h3 class="truncate text-base font-semibold text-gray-900">{{ $target->name }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ $target->financialAccount->name ?? '-' }}</p>
                    </div>

                    @if ($target->status === 'active')
                        <span
                            class="shrink-0 rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">Aktif</span>
                    @elseif ($target->status === 'completed')
                        <span
                            class="shrink-0 rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700">Selesai</span>
                    @else
                        <span
                            class="shrink-0 rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">Batal</span>
                    @endif
                </div>

                <div class="mt-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Rp {{ number_format($target->current_amount, 0, ',', '.') }}</span>
                        <span class="font-medium text-gray-900">Rp
                            {{ number_format($target->target_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="mt-2 h-2.5 w-full rounded-full bg-gray-200">
                        <div class="h-2.5 rounded-full bg-emerald-600" style="width: {{ $progress }}%"></div>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-2xl bg-gray-50 p-3">
                        <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Deadline</p>
                        <p class="mt-1 font-medium text-gray-900">{{ $target->deadline?->format('d M Y') ?? '-' }}</p>
                    </div>

                    <div class="rounded-2xl bg-gray-50 p-3">
                        <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Progress</p>
                        <p class="mt-1 font-medium text-gray-900">{{ number_format($progress, 0) }}%</p>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-3 gap-2">
                    <a href="{{ route('saving-targets.show', $target->id) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-3 py-2.5 text-xs font-medium text-gray-700 transition hover:bg-gray-50">
                        Detail
                    </a>

                    <a href="{{ route('saving-targets.edit', $target->id) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-blue-300 px-3 py-2.5 text-xs font-medium text-blue-700 transition hover:bg-blue-50">
                        Edit
                    </a>

                    <form action="{{ route('saving-targets.destroy', $target->id) }}" method="POST"
                        onsubmit="return confirm('Yakin mau hapus target tabungan ini?')">
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
                Belum ada target tabungan. Bikin satu dulu, jangan cuma niat doang.
            </div>
        @endforelse
    </section>

    <section class="mt-6">
        <div class="rounded-3xl border border-gray-200 bg-white px-5 py-4 shadow-sm sm:px-6">
            {{ $savingTargets->links() }}
        </div>
    </section>
@endsection
