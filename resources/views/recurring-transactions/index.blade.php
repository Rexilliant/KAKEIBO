@extends('layouts.app')

@section('page_title', 'Recurring Transactions')
@section('page_subtitle', 'Automasi transaksi rutin biar hidup lu gak isi form yang sama terus.')

@section('content')
    <section class="mb-6">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">Recurring transaction</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                        Transaksi Rutin
                    </h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-500">
                        Cocok untuk gaji, listrik, sewa, internet, cicilan, dan pengeluaran rutin lain.
                    </p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('recurring-transactions.generate-due') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Generate Due
                    </a>

                    <a href="{{ route('recurring-transactions.create') }}"
                        class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700">
                        + Tambah Rutin
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-6">
        <form method="GET" action="{{ route('recurring-transactions.index') }}"
            class="grid gap-4 rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:grid-cols-3 sm:p-6">
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Jenis</label>
                <select name="type" class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                    <option value="">Semua jenis</option>
                    <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Income</option>
                    <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Expense</option>
                </select>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                    <option value="">Semua status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
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
                <h3 class="text-lg font-semibold tracking-tight text-gray-900">Daftar Transaksi Rutin</h3>
                <p class="mt-1 text-sm text-gray-500">Versi desktop dengan tampilan tabel penuh.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-[0.14em] text-gray-500">
                        <tr>
                            <th class="px-6 py-4">Judul</th>
                            <th class="px-6 py-4">Jenis</th>
                            <th class="px-6 py-4">Rekening</th>
                            <th class="px-6 py-4">Next Run</th>
                            <th class="px-6 py-4 text-right">Nominal</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($recurringTransactions as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $item->title }}</div>
                                    <div class="mt-1 text-xs text-gray-500">{{ $item->category->name ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($item->type === 'income')
                                        <span
                                            class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">Income</span>
                                    @else
                                        <span
                                            class="rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-700">Expense</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $item->financialAccount->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ optional($item->next_run_date)->format('d M Y') }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right font-semibold text-gray-900">
                                    Rp {{ number_format($item->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($item->is_active)
                                        <span
                                            class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">Aktif</span>
                                    @else
                                        <span
                                            class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('recurring-transactions.show', $item->id) }}"
                                            class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50">
                                            Detail
                                        </a>

                                        <form action="{{ route('recurring-transactions.toggle-status', $item->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="rounded-lg border border-blue-300 px-3 py-2 text-xs font-medium text-blue-700 transition hover:bg-blue-50">
                                                {{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>

                                        <form action="{{ route('recurring-transactions.destroy', $item->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Yakin mau hapus recurring transaction ini?')">
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
                                    Belum ada recurring transaction.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="space-y-4 lg:hidden">
        @forelse ($recurringTransactions as $item)
            <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h3 class="truncate text-base font-semibold text-gray-900">{{ $item->title }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ $item->financialAccount->name ?? '-' }}</p>
                    </div>

                    @if ($item->is_active)
                        <span
                            class="shrink-0 rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">Aktif</span>
                    @else
                        <span
                            class="shrink-0 rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">Nonaktif</span>
                    @endif
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-2xl bg-gray-50 p-3">
                        <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Jenis</p>
                        <p class="mt-1 font-medium text-gray-900">{{ ucfirst($item->type) }}</p>
                    </div>

                    <div class="rounded-2xl bg-gray-50 p-3">
                        <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Next Run</p>
                        <p class="mt-1 font-medium text-gray-900">{{ optional($item->next_run_date)->format('d M Y') }}</p>
                    </div>

                    <div class="rounded-2xl bg-gray-50 p-3 col-span-2">
                        <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Nominal</p>
                        <p class="mt-1 font-medium text-gray-900">Rp {{ number_format($item->amount, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-3 gap-2">
                    <a href="{{ route('recurring-transactions.show', $item->id) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-3 py-2.5 text-xs font-medium text-gray-700 transition hover:bg-gray-50">
                        Detail
                    </a>

                    <form action="{{ route('recurring-transactions.toggle-status', $item->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="inline-flex w-full items-center justify-center rounded-xl border border-blue-300 px-3 py-2.5 text-xs font-medium text-blue-700 transition hover:bg-blue-50">
                            {{ $item->is_active ? 'Off' : 'On' }}
                        </button>
                    </form>

                    <form action="{{ route('recurring-transactions.destroy', $item->id) }}" method="POST"
                        onsubmit="return confirm('Yakin mau hapus recurring transaction ini?')">
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
                Belum ada recurring transaction.
            </div>
        @endforelse
    </section>

    <section class="mt-6">
        <div class="rounded-3xl border border-gray-200 bg-white px-5 py-4 shadow-sm sm:px-6">
            {{ $recurringTransactions->links() }}
        </div>
    </section>
@endsection
