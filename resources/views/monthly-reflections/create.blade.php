@extends('layouts.app')

@section('page_title', 'Tambah Refleksi Bulanan')
@section('page_subtitle', 'Isi evaluasi keuanganmu untuk satu bulan.')

@section('content')
    <section class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_380px]">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-6">
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">New monthly reflection</p>
                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                    Tambah Refleksi Bulanan
                </h2>
                <p class="mt-2 text-sm leading-6 text-gray-500">
                    Bagian ini bukan soal tampil keren, tapi soal jujur. Lihat keputusanmu bulan ini, lalu perbaiki yang
                    perlu.
                </p>
            </div>

            <div class="mb-6 grid gap-4 md:grid-cols-2">
                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Total Pemasukan Bulan Ini</p>
                    <p class="mt-2 text-lg font-semibold text-gray-900">
                        Rp {{ number_format($reflectionInsights['total_income'] ?? 0, 0, ',', '.') }}
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Total Pengeluaran Bulan Ini</p>
                    <p class="mt-2 text-lg font-semibold text-gray-900">
                        Rp {{ number_format($reflectionInsights['total_expense'] ?? 0, 0, ',', '.') }}
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-yellow-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-yellow-700">Kategori Paling Boros</p>
                    <p class="mt-2 text-sm font-semibold text-yellow-900">
                        {{ $reflectionInsights['top_category_name'] ?? '-' }}
                    </p>
                    <p class="mt-1 text-sm text-yellow-800">
                        Rp {{ number_format($reflectionInsights['top_category_amount'] ?? 0, 0, ',', '.') }}
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-red-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-red-700">Expense Terbesar</p>
                    <p class="mt-2 text-sm font-semibold text-red-900">
                        {{ $reflectionInsights['largest_expense_title'] ?? '-' }}
                    </p>
                    <p class="mt-1 text-sm text-red-800">
                        Rp {{ number_format($reflectionInsights['largest_expense_amount'] ?? 0, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <form action="{{ route('monthly-reflections.store') }}" method="POST" class="space-y-5">
                @csrf

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Bulan</label>
                        <select name="month"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}"
                                    {{ (string) old('month', $selectedMonth ?? now()->month) === (string) $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Tahun</label>
                        <input type="number" name="year" value="{{ old('year', $selectedYear ?? now()->year) }}"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Rencana Menabung</label>
                        <input type="number" step="0.01" min="0" name="planned_saving"
                            value="{{ old('planned_saving', 0) }}"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Hasil Menabung</label>
                        <input type="number" step="0.01" min="0" name="actual_saving"
                            value="{{ old('actual_saving', 0) }}"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Aku punya uang berapa bulan ini?</label>
                    <textarea name="question_1_money_owned" rows="3"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">{{ old('question_1_money_owned', $reflectionInsights['suggestion_money_owned'] ?? '') }}</textarea>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Aku mau menabung berapa dan untuk
                        apa?</label>
                    <textarea name="question_2_saving_goal" rows="3"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">{{ old('question_2_saving_goal') }}</textarea>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Uangku paling banyak habis di mana?</label>
                    <textarea name="question_3_actual_spending" rows="3"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">{{ old('question_3_actual_spending', $reflectionInsights['suggestion_spending'] ?? '') }}</textarea>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Pengeluaran paling gak perlu apa?</label>
                    <textarea name="question_4_unnecessary_expense" rows="3"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">{{ old('question_4_unnecessary_expense', $reflectionInsights['suggestion_unnecessary'] ?? '') }}</textarea>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Apa yang mau aku perbaiki bulan
                        depan?</label>
                    <textarea name="question_5_improvement_next_month" rows="3"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">{{ old('question_5_improvement_next_month', $reflectionInsights['suggestion_improvement'] ?? '') }}</textarea>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Keputusan finansial terbaik bulan
                        ini?</label>
                    <textarea name="question_6_best_financial_decision" rows="3"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">{{ old('question_6_best_financial_decision') }}</textarea>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Mood Finansial</label>
                        <select name="mood"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                            <option value="">Pilih mood</option>
                            <option value="calm" {{ old('mood') === 'calm' ? 'selected' : '' }}>Calm</option>
                            <option value="good" {{ old('mood') === 'good' ? 'selected' : '' }}>Good</option>
                            <option value="wasteful" {{ old('mood') === 'wasteful' ? 'selected' : '' }}>Wasteful</option>
                            <option value="chaotic" {{ old('mood') === 'chaotic' ? 'selected' : '' }}>Chaotic</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Catatan Mood</label>
                        <input type="text" name="mood_note" value="{{ old('mood_note') }}"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Komitmen Bulan Depan</label>
                    <textarea name="commitment_next_month" rows="3"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">{{ old('commitment_next_month') }}</textarea>
                </div>

                <div class="flex flex-col gap-3 border-t border-gray-200 pt-5 sm:flex-row">
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-black">
                        Simpan Refleksi
                    </button>

                    <a href="{{ route('monthly-reflections.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <h3 class="text-lg font-semibold tracking-tight text-gray-900">Catatan</h3>
            <ul class="mt-4 space-y-3 text-sm text-gray-600">
                <li class="rounded-2xl bg-gray-50 px-4 py-3">Refleksi ini inti dari Kakeibo: sadar, jujur, dan membaik.</li>
                <li class="rounded-2xl bg-gray-50 px-4 py-3">Gak perlu puitis, yang penting jujur.</li>
                <li class="rounded-2xl bg-gray-50 px-4 py-3">Jawaban pendek tapi jelas lebih berguna daripada motivasi
                    palsu.</li>
            </ul>
        </div>
    </section>
@endsection
