@extends('layouts.app')

@section('page_title', 'Monthly Closing')
@section('page_subtitle', 'Tutup bulan dan evaluasi keuangan lu.')

@section('content')

    <section class="mb-6">
        <form action="{{ route('monthly-closings.close') }}" method="POST"
            class="grid gap-4 rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:grid-cols-3">
            @csrf

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Bulan</label>
                <input type="number" name="month" value="{{ now()->month }}"
                    class="block w-full rounded-xl border border-gray-300 px-4 py-3 text-sm">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Tahun</label>
                <input type="number" name="year" value="{{ now()->year }}"
                    class="block w-full rounded-xl border border-gray-300 px-4 py-3 text-sm">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full rounded-xl bg-black px-4 py-3 text-sm text-white">
                    Tutup Bulan
                </button>
            </div>
        </form>
    </section>

    <section>
        <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left">Periode</th>
                        <th class="px-6 py-4 text-left">Income</th>
                        <th class="px-6 py-4 text-left">Expense</th>
                        <th class="px-6 py-4 text-left">Net</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($closings as $c)
                        <tr class="border-t">
                            <td class="px-6 py-4">{{ $c->month }}/{{ $c->year }}</td>
                            <td class="px-6 py-4 text-emerald-600">
                                Rp {{ number_format($c->total_income, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-red-600">
                                Rp {{ number_format($c->total_expense, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 font-semibold">
                                Rp {{ number_format($c->net_balance, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('monthly-closings.show', $c->id) }}"
                                        class="rounded-lg border border-blue-300 px-3 py-2 text-xs font-medium text-blue-700 transition hover:bg-blue-50">
                                        Detail
                                    </a>

                                    <form action="{{ route('monthly-closings.destroy', $c->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin mau hapus closing bulan ini?')">
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
                            <td colspan="5" class="py-10 text-center text-gray-500">
                                Belum ada closing
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

@endsection
