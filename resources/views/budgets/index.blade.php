@extends('layouts.app')

@section('page_title', 'Anggaran')
@section('page_subtitle', 'Kelola batas pengeluaran per kategori dan lihat actual spending-nya.')

@section('content')
    <section class="mb-6">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">Budget management</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                        Kelola Anggaran
                    </h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-500">
                        Sekarang budget lu gak cuma nominal. Langsung kelihatan juga actual spending, sisa anggaran, dan
                        statusnya.
                    </p>
                </div>

                <div>
                    <a href="{{ route('budgets.create') }}"
                        class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700">
                        + Tambah Anggaran
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Total Anggaran</p>
            <h3 class="mt-3 text-2xl font-semibold tracking-tight text-gray-900">{{ $budgets->total() }}</h3>
            <p class="mt-2 text-sm text-gray-500">Jumlah budget yang tersimpan.</p>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Halaman Saat Ini</p>
            <h3 class="mt-3 text-2xl font-semibold tracking-tight text-gray-900">{{ $budgets->currentPage() }}</h3>
            <p class="mt-2 text-sm text-gray-500">Posisi data yang sedang dibuka.</p>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Per Halaman</p>
            <h3 class="mt-3 text-2xl font-semibold tracking-tight text-gray-900">{{ $budgets->perPage() }}</h3>
            <p class="mt-2 text-sm text-gray-500">Jumlah item per halaman.</p>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Rentang Data</p>
            <h3 class="mt-3 text-2xl font-semibold tracking-tight text-gray-900">
                {{ $budgets->firstItem() ?? 0 }} - {{ $budgets->lastItem() ?? 0 }}
            </h3>
            <p class="mt-2 text-sm text-gray-500">Item yang sedang tampil sekarang.</p>
        </div>
    </section>

    <section class="hidden lg:block">
        <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-5">
                <h3 class="text-lg font-semibold tracking-tight text-gray-900">Daftar Anggaran</h3>
                <p class="mt-1 text-sm text-gray-500">Budget vs actual spending per kategori.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-[0.14em] text-gray-500">
                        <tr>
                            <th class="px-6 py-4">Kategori</th>
                            <th class="px-6 py-4">Periode</th>
                            <th class="px-6 py-4 text-right">Budget</th>
                            <th class="px-6 py-4 text-right">Actual</th>
                            <th class="px-6 py-4 text-right">Sisa</th>
                            <th class="px-6 py-4">Progress</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Mode</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($budgets as $budget)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ $budget->category->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ \Carbon\Carbon::create()->month($budget->month)->translatedFormat('F') }}
                                    {{ $budget->year }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right font-semibold text-gray-900">
                                    Rp {{ number_format($budget->amount, 0, ',', '.') }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right font-medium text-gray-900">
                                    Rp {{ number_format($budget->spent_amount, 0, ',', '.') }}
                                </td>
                                <td
                                    class="whitespace-nowrap px-6 py-4 text-right font-medium {{ $budget->remaining_amount < 0 ? 'text-red-600' : 'text-gray-900' }}">
                                    Rp {{ number_format($budget->remaining_amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="w-36">
                                        <div class="mb-2 flex items-center justify-between text-xs text-gray-500">
                                            <span>{{ number_format($budget->progress_percentage, 0) }}%</span>
                                        </div>
                                        <div class="h-2.5 w-full rounded-full bg-gray-200">
                                            <div class="h-2.5 rounded-full
                                                {{ $budget->budget_status === 'over' ? 'bg-red-500' : ($budget->budget_status === 'warning' ? 'bg-yellow-500' : 'bg-emerald-600') }}"
                                                style="width: {{ min(100, $budget->progress_percentage) }}%">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($budget->budget_status === 'over')
                                        <span
                                            class="rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-700">Over</span>
                                    @elseif ($budget->budget_status === 'warning')
                                        <span
                                            class="rounded-full bg-yellow-50 px-3 py-1 text-xs font-medium text-yellow-700">Hampir
                                            Habis</span>
                                    @else
                                        <span
                                            class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">Aman</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($budget->enforcement_level === 'hard')
                                        <span
                                            class="rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-700">Hard</span>
                                    @else
                                        <span
                                            class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">Soft</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('budgets.show', $budget->id) }}"
                                            class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50">
                                            Detail
                                        </a>

                                        <a href="{{ route('budgets.edit', $budget->id) }}"
                                            class="rounded-lg border border-blue-300 px-3 py-2 text-xs font-medium text-blue-700 transition hover:bg-blue-50">
                                            Edit
                                        </a>

                                        <form action="{{ route('budgets.destroy', $budget->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin mau hapus anggaran ini?')">
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
                                    Belum ada anggaran.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="space-y-4 lg:hidden">
        @forelse ($budgets as $budget)
            <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h3 class="truncate text-base font-semibold text-gray-900">{{ $budget->category->name ?? '-' }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ \Carbon\Carbon::create()->month($budget->month)->translatedFormat('F') }}
                            {{ $budget->year }}
                        </p>
                    </div>

                    @if ($budget->budget_status === 'over')
                        <span class="shrink-0 rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-700">Over</span>
                    @elseif ($budget->budget_status === 'warning')
                        <span
                            class="shrink-0 rounded-full bg-yellow-50 px-3 py-1 text-xs font-medium text-yellow-700">Warning</span>
                    @else
                        <span
                            class="shrink-0 rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">Aman</span>
                    @endif
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-2xl bg-gray-50 p-3">
                        <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Budget</p>
                        <p class="mt-1 font-medium text-gray-900">Rp {{ number_format($budget->amount, 0, ',', '.') }}</p>
                    </div>

                    <div class="rounded-2xl bg-gray-50 p-3">
                        <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Actual</p>
                        <p class="mt-1 font-medium text-gray-900">Rp
                            {{ number_format($budget->spent_amount, 0, ',', '.') }}</p>
                    </div>

                    <div class="rounded-2xl bg-gray-50 p-3">
                        <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Sisa</p>
                        <p class="mt-1 font-medium {{ $budget->remaining_amount < 0 ? 'text-red-600' : 'text-gray-900' }}">
                            Rp {{ number_format($budget->remaining_amount, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-gray-50 p-3">
                        <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Progress</p>
                        <p class="mt-1 font-medium text-gray-900">{{ number_format($budget->progress_percentage, 0) }}%</p>
                    </div>
                </div>

                <div class="mt-4 h-2.5 w-full rounded-full bg-gray-200">
                    <div class="h-2.5 rounded-full
                        {{ $budget->budget_status === 'over' ? 'bg-red-500' : ($budget->budget_status === 'warning' ? 'bg-yellow-500' : 'bg-emerald-600') }}"
                        style="width: {{ min(100, $budget->progress_percentage) }}%">
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-3 gap-2">
                    <a href="{{ route('budgets.show', $budget->id) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-3 py-2.5 text-xs font-medium text-gray-700 transition hover:bg-gray-50">
                        Detail
                    </a>

                    <a href="{{ route('budgets.edit', $budget->id) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-blue-300 px-3 py-2.5 text-xs font-medium text-blue-700 transition hover:bg-blue-50">
                        Edit
                    </a>

                    <form action="{{ route('budgets.destroy', $budget->id) }}" method="POST"
                        onsubmit="return confirm('Yakin mau hapus anggaran ini?')">
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
                Belum ada anggaran.
            </div>
        @endforelse
    </section>

    <section class="mt-6">
        <div class="rounded-3xl border border-gray-200 bg-white px-5 py-4 shadow-sm sm:px-6">
            {{ $budgets->links() }}
        </div>
    </section>
@endsection
