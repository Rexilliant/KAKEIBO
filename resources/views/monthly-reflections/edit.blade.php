@extends('layouts.app')

@section('page_title', 'Edit Refleksi Bulanan')
@section('page_subtitle', 'Perbarui refleksi keuangan bulanan yang sudah ada.')

@section('content')
    <section class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_380px]">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-6">
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">Edit monthly reflection</p>
                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                    Edit Refleksi Bulanan
                </h2>
            </div>

            <form action="{{ route('monthly-reflections.update', $monthlyReflection->id) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Bulan</label>
                        <select name="month" class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ (string) old('month', $monthlyReflection->month) === (string) $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Tahun</label>
                        <input type="number" name="year" value="{{ old('year', $monthlyReflection->year) }}"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Rencana Menabung</label>
                        <input type="number" step="0.01" min="0" name="planned_saving" value="{{ old('planned_saving', $monthlyReflection->planned_saving) }}"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Hasil Menabung</label>
                        <input type="number" step="0.01" min="0" name="actual_saving" value="{{ old('actual_saving', $monthlyReflection->actual_saving) }}"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Aku punya uang berapa bulan ini?</label>
                    <textarea name="question_1_money_owned" rows="3"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">{{ old('question_1_money_owned', $monthlyReflection->question_1_money_owned) }}</textarea>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Aku mau menabung berapa dan untuk apa?</label>
                    <textarea name="question_2_saving_goal" rows="3"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">{{ old('question_2_saving_goal', $monthlyReflection->question_2_saving_goal) }}</textarea>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Uangku paling banyak habis di mana?</label>
                    <textarea name="question_3_actual_spending" rows="3"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">{{ old('question_3_actual_spending', $monthlyReflection->question_3_actual_spending) }}</textarea>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Pengeluaran paling gak perlu apa?</label>
                    <textarea name="question_4_unnecessary_expense" rows="3"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">{{ old('question_4_unnecessary_expense', $monthlyReflection->question_4_unnecessary_expense) }}</textarea>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Apa yang mau aku perbaiki bulan depan?</label>
                    <textarea name="question_5_improvement_next_month" rows="3"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">{{ old('question_5_improvement_next_month', $monthlyReflection->question_5_improvement_next_month) }}</textarea>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Keputusan finansial terbaik bulan ini?</label>
                    <textarea name="question_6_best_financial_decision" rows="3"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">{{ old('question_6_best_financial_decision', $monthlyReflection->question_6_best_financial_decision) }}</textarea>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Mood Finansial</label>
                        <select name="mood" class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                            <option value="">Pilih mood</option>
                            <option value="calm" {{ old('mood', $monthlyReflection->mood) === 'calm' ? 'selected' : '' }}>Calm</option>
                            <option value="good" {{ old('mood', $monthlyReflection->mood) === 'good' ? 'selected' : '' }}>Good</option>
                            <option value="wasteful" {{ old('mood', $monthlyReflection->mood) === 'wasteful' ? 'selected' : '' }}>Wasteful</option>
                            <option value="chaotic" {{ old('mood', $monthlyReflection->mood) === 'chaotic' ? 'selected' : '' }}>Chaotic</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Catatan Mood</label>
                        <input type="text" name="mood_note" value="{{ old('mood_note', $monthlyReflection->mood_note) }}"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Komitmen Bulan Depan</label>
                    <textarea name="commitment_next_month" rows="3"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">{{ old('commitment_next_month', $monthlyReflection->commitment_next_month) }}</textarea>
                </div>

                <div class="flex flex-col gap-3 border-t border-gray-200 pt-5 sm:flex-row">
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-black">
                        Update Refleksi
                    </button>

                    <a href="{{ route('monthly-reflections.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <h3 class="text-lg font-semibold tracking-tight text-gray-900">Info</h3>
            <div class="mt-4 space-y-3 text-sm">
                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Dibuat</p>
                    <p class="mt-1 font-medium text-gray-900">{{ $monthlyReflection->created_at?->format('d M Y H:i') }}</p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Terakhir diubah</p>
                    <p class="mt-1 font-medium text-gray-900">{{ $monthlyReflection->updated_at?->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>
    </section>
@endsection