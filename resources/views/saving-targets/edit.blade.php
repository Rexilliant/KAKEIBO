@extends('layouts.app')

@section('page_title', 'Edit Target Tabungan')
@section('page_subtitle', 'Perbarui target tabungan yang sudah ada.')

@section('content')
    <section class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_380px]">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-6">
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">Edit saving target</p>
                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                    Edit Target Tabungan
                </h2>
            </div>

            <form action="{{ route('saving-targets.update', $savingTarget->id) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Nama Target</label>
                    <input type="text" name="name" value="{{ old('name', $savingTarget->name) }}"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Target Nominal</label>
                        <input type="number" step="0.01" min="0" name="target_amount"
                            value="{{ old('target_amount', $savingTarget->target_amount) }}"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Dana Terkumpul</label>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900">
                            Rp {{ number_format($savingTarget->current_amount, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Rekening</label>
                        <select name="financial_account_id"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                            <option value="">Pilih rekening</option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}"
                                    {{ (string) old('financial_account_id', $savingTarget->financial_account_id) === (string) $account->id ? 'selected' : '' }}>
                                    {{ $account->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Deadline</label>
                        <input type="date" name="deadline"
                            value="{{ old('deadline', $savingTarget->deadline?->format('Y-m-d')) }}"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Status</label>
                    <select name="status"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                        <option value="active" {{ old('status', $savingTarget->status) === 'active' ? 'selected' : '' }}>
                            Aktif</option>
                        <option value="completed"
                            {{ old('status', $savingTarget->status) === 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled"
                            {{ old('status', $savingTarget->status) === 'cancelled' ? 'selected' : '' }}>Dibatalkan
                        </option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Catatan</label>
                    <textarea name="note" rows="4"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">{{ old('note', $savingTarget->note) }}</textarea>
                </div>

                <div class="flex flex-col gap-3 border-t border-gray-200 pt-5 sm:flex-row">
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-black">
                        Update Target
                    </button>

                    <a href="{{ route('saving-targets.index') }}"
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
                    <p class="mt-1 font-medium text-gray-900">{{ $savingTarget->created_at?->format('d M Y H:i') }}</p>
                </div>
                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Terakhir diubah</p>
                    <p class="mt-1 font-medium text-gray-900">{{ $savingTarget->updated_at?->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>
    </section>
@endsection
