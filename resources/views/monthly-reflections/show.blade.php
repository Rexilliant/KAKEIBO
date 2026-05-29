@extends('layouts.app')

@section('page_title', 'Detail Refleksi Bulanan')
@section('page_subtitle', 'Lihat isi refleksi keuangan bulanan secara lengkap.')

@section('content')
    <section class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_380px]">
        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">Monthly reflection detail</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                        {{ \Carbon\Carbon::create()->month($monthlyReflection->month)->translatedFormat('F') }}
                        {{ $monthlyReflection->year }}
                    </h2>
                    <p class="mt-2 text-sm text-gray-500">
                        Refleksi bulanan untuk evaluasi kebiasaan finansial.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('monthly-reflections.edit', $monthlyReflection->id) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-blue-300 px-4 py-2.5 text-sm font-medium text-blue-700 transition hover:bg-blue-50">
                        Edit
                    </a>

                    <a href="{{ route('monthly-reflections.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Kembali
                    </a>
                </div>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Rencana Menabung</p>
                    <p class="mt-2 text-base font-semibold text-gray-900">
                        Rp {{ number_format($monthlyReflection->planned_saving, 0, ',', '.') }}
                    </p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Hasil Menabung</p>
                    <p class="mt-2 text-base font-semibold text-gray-900">
                        Rp {{ number_format($monthlyReflection->actual_saving, 0, ',', '.') }}
                    </p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4 sm:col-span-2">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Aku punya uang berapa bulan ini?</p>
                    <p class="mt-2 text-sm leading-6 text-gray-700">{{ $monthlyReflection->question_1_money_owned ?? '-' }}
                    </p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4 sm:col-span-2">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Aku mau menabung berapa dan untuk apa?</p>
                    <p class="mt-2 text-sm leading-6 text-gray-700">{{ $monthlyReflection->question_2_saving_goal ?? '-' }}
                    </p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4 sm:col-span-2">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Uangku paling banyak habis di mana?</p>
                    <p class="mt-2 text-sm leading-6 text-gray-700">
                        {{ $monthlyReflection->question_3_actual_spending ?? '-' }}</p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4 sm:col-span-2">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Pengeluaran paling gak perlu apa?</p>
                    <p class="mt-2 text-sm leading-6 text-gray-700">
                        {{ $monthlyReflection->question_4_unnecessary_expense ?? '-' }}</p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4 sm:col-span-2">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Apa yang mau aku perbaiki bulan depan?</p>
                    <p class="mt-2 text-sm leading-6 text-gray-700">
                        {{ $monthlyReflection->question_5_improvement_next_month ?? '-' }}</p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4 sm:col-span-2">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Keputusan finansial terbaik bulan ini?</p>
                    <p class="mt-2 text-sm leading-6 text-gray-700">
                        {{ $monthlyReflection->question_6_best_financial_decision ?? '-' }}</p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Mood</p>
                    <p class="mt-2 text-base font-semibold text-gray-900">{{ $monthlyReflection->mood ?? '-' }}</p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Catatan Mood</p>
                    <p class="mt-2 text-sm leading-6 text-gray-700">{{ $monthlyReflection->mood_note ?? '-' }}</p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4 sm:col-span-2">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Komitmen Bulan Depan</p>
                    <p class="mt-2 text-sm leading-6 text-gray-700">{{ $monthlyReflection->commitment_next_month ?? '-' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <h3 class="text-lg font-semibold tracking-tight text-gray-900">Metadata</h3>
            <div class="mt-4 space-y-3 text-sm">
                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Dibuat</p>
                    <p class="mt-1 font-medium text-gray-900">{{ $monthlyReflection->created_at?->format('d M Y H:i') }}
                    </p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Terakhir diubah</p>
                    <p class="mt-1 font-medium text-gray-900">{{ $monthlyReflection->updated_at?->format('d M Y H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection
