@extends('layouts.app')

@section('page_title', 'Detail Closing')
@section('page_subtitle', 'Ringkasan bulan ini.')

@section('content')

    <div class="mb-6 flex flex-col gap-3 sm:flex-row">
        <a href="{{ route('monthly-closings.index') }}"
            class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
            Kembali
        </a>

        <form action="{{ route('monthly-closings.destroy', $monthlyClosing->id) }}" method="POST"
            onsubmit="return confirm('Yakin mau hapus closing bulan ini?')">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="inline-flex items-center justify-center rounded-xl border border-red-300 bg-white px-5 py-3 text-sm font-medium text-red-700 transition hover:bg-red-50">
                Hapus Closing
            </button>
        </form>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-3xl border bg-white p-5">
            <p class="text-sm text-gray-500">Income</p>
            <h3 class="text-xl font-bold text-emerald-600">
                Rp {{ number_format($monthlyClosing->total_income, 0, ',', '.') }}
            </h3>
        </div>

        <div class="rounded-3xl border bg-white p-5">
            <p class="text-sm text-gray-500">Expense</p>
            <h3 class="text-xl font-bold text-red-600">
                Rp {{ number_format($monthlyClosing->total_expense, 0, ',', '.') }}
            </h3>
        </div>

        <div class="rounded-3xl border bg-white p-5">
            <p class="text-sm text-gray-500">Net</p>
            <h3 class="text-xl font-bold">
                Rp {{ number_format($monthlyClosing->net_balance, 0, ',', '.') }}
            </h3>
        </div>
    </div>

@endsection
