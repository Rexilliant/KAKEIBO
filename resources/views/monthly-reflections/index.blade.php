@extends('layouts.app')

@section('page_title', 'Refleksi Bulanan')
@section('page_subtitle', 'Lihat dan kelola evaluasi keuangan bulananmu.')

@section('content')
    <section class="mb-6">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">Monthly reflection</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                        Refleksi Bulanan
                    </h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-500">
                        Ini bagian paling jujur dari Kakeibo. Bukan cuma lihat angka, tapi ngakuin pola dan bikin keputusan
                        yang lebih waras bulan depan.
                    </p>
                </div>

                <div>
                    <a href="{{ route('monthly-reflections.create') }}"
                        class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700">
                        + Tambah Refleksi
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Total Refleksi</p>
            <h3 class="mt-3 text-2xl font-semibold tracking-tight text-gray-900">{{ $monthlyReflections->total() }}</h3>
            <p class="mt-2 text-sm text-gray-500">Jumlah refleksi yang tersimpan.</p>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Halaman Saat Ini</p>
            <h3 class="mt-3 text-2xl font-semibold tracking-tight text-gray-900">{{ $monthlyReflections->currentPage() }}
            </h3>
            <p class="mt-2 text-sm text-gray-500">Posisi data yang sedang dibuka.</p>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Per Halaman</p>
            <h3 class="mt-3 text-2xl font-semibold tracking-tight text-gray-900">{{ $monthlyReflections->perPage() }}</h3>
            <p class="mt-2 text-sm text-gray-500">Jumlah item per halaman.</p>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Rentang Data</p>
            <h3 class="mt-3 text-2xl font-semibold tracking-tight text-gray-900">
                {{ $monthlyReflections->firstItem() ?? 0 }} - {{ $monthlyReflections->lastItem() ?? 0 }}
            </h3>
            <p class="mt-2 text-sm text-gray-500">Item yang sedang tampil sekarang.</p>
        </div>
    </section>

    <section class="hidden lg:block">
        <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-5">
                <h3 class="text-lg font-semibold tracking-tight text-gray-900">Daftar Refleksi</h3>
                <p class="mt-1 text-sm text-gray-500">Versi desktop dengan tampilan tabel penuh.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-[0.14em] text-gray-500">
                        <tr>
                            <th class="px-6 py-4">Periode</th>
                            <th class="px-6 py-4 text-right">Rencana Nabung</th>
                            <th class="px-6 py-4 text-right">Hasil Nabung</th>
                            <th class="px-6 py-4">Mood</th>
                            <th class="px-6 py-4">Komitmen</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($monthlyReflections as $reflection)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ \Carbon\Carbon::create()->month($reflection->month)->translatedFormat('F') }}
                                    {{ $reflection->year }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-gray-700">
                                    Rp {{ number_format($reflection->planned_saving, 0, ',', '.') }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right font-semibold text-gray-900">
                                    Rp {{ number_format($reflection->actual_saving, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if ($reflection->mood === 'calm')
                                        <span
                                            class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">Calm</span>
                                    @elseif ($reflection->mood === 'good')
                                        <span
                                            class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700">Good</span>
                                    @elseif ($reflection->mood === 'wasteful')
                                        <span
                                            class="rounded-full bg-yellow-50 px-3 py-1 text-xs font-medium text-yellow-700">Wasteful</span>
                                    @elseif ($reflection->mood === 'chaotic')
                                        <span
                                            class="rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-700">Chaotic</span>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    <div class="max-w-xs truncate">{{ $reflection->commitment_next_month ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('monthly-reflections.show', $reflection->id) }}"
                                            class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50">
                                            Detail
                                        </a>

                                        <a href="{{ route('monthly-reflections.edit', $reflection->id) }}"
                                            class="rounded-lg border border-blue-300 px-3 py-2 text-xs font-medium text-blue-700 transition hover:bg-blue-50">
                                            Edit
                                        </a>

                                        <form action="{{ route('monthly-reflections.destroy', $reflection->id) }}"
                                            method="POST" onsubmit="return confirm('Yakin mau hapus refleksi ini?')">
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
                                    Belum ada refleksi bulanan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="space-y-4 lg:hidden">
        @forelse ($monthlyReflections as $reflection)
            <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h3 class="truncate text-base font-semibold text-gray-900">
                            {{ \Carbon\Carbon::create()->month($reflection->month)->translatedFormat('F') }}
                            {{ $reflection->year }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Rencana: Rp {{ number_format($reflection->planned_saving, 0, ',', '.') }}
                        </p>
                    </div>

                    @if ($reflection->mood === 'calm')
                        <span
                            class="shrink-0 rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">Calm</span>
                    @elseif ($reflection->mood === 'good')
                        <span
                            class="shrink-0 rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700">Good</span>
                    @elseif ($reflection->mood === 'wasteful')
                        <span
                            class="shrink-0 rounded-full bg-yellow-50 px-3 py-1 text-xs font-medium text-yellow-700">Wasteful</span>
                    @elseif ($reflection->mood === 'chaotic')
                        <span
                            class="shrink-0 rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-700">Chaotic</span>
                    @else
                        <span class="shrink-0 rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">-</span>
                    @endif
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-2xl bg-gray-50 p-3">
                        <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Actual Saving</p>
                        <p class="mt-1 font-medium text-gray-900">Rp
                            {{ number_format($reflection->actual_saving, 0, ',', '.') }}</p>
                    </div>

                    <div class="rounded-2xl bg-gray-50 p-3">
                        <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Komitmen</p>
                        <p class="mt-1 font-medium text-gray-900">{{ $reflection->commitment_next_month ?? '-' }}</p>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-3 gap-2">
                    <a href="{{ route('monthly-reflections.show', $reflection->id) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-3 py-2.5 text-xs font-medium text-gray-700 transition hover:bg-gray-50">
                        Detail
                    </a>

                    <a href="{{ route('monthly-reflections.edit', $reflection->id) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-blue-300 px-3 py-2.5 text-xs font-medium text-blue-700 transition hover:bg-blue-50">
                        Edit
                    </a>

                    <form action="{{ route('monthly-reflections.destroy', $reflection->id) }}" method="POST"
                        onsubmit="return confirm('Yakin mau hapus refleksi ini?')">
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
                Belum ada refleksi bulanan.
            </div>
        @endforelse
    </section>

    <section class="mt-6">
        <div class="rounded-3xl border border-gray-200 bg-white px-5 py-4 shadow-sm sm:px-6">
            {{ $monthlyReflections->links() }}
        </div>
    </section>
@endsection
