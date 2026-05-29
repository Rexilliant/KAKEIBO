@extends('layouts.app')

@section('page_title', 'Buat Transfer')
@section('page_subtitle', 'Pindahkan uang antar rekening dengan aman.')

@section('content')
    <section class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_380px]">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-6">
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">New transfer</p>
                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                    Buat Transfer Antar Rekening
                </h2>
            </div>

            <form action="{{ route('account-transfers.store') }}" method="POST" class="space-y-5">
                @csrf

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Dari Rekening</label>
                        <select name="from_financial_account_id"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                            <option value="">Pilih rekening asal</option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}"
                                    {{ (string) old('from_financial_account_id') === (string) $account->id ? 'selected' : '' }}>
                                    {{ $account->name }} — Rp {{ number_format($account->balance, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                        @error('from_financial_account_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Ke Rekening</label>
                        <select name="to_financial_account_id"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                            <option value="">Pilih rekening tujuan</option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}"
                                    {{ (string) old('to_financial_account_id') === (string) $account->id ? 'selected' : '' }}>
                                    {{ $account->name }} — Rp {{ number_format($account->balance, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                        @error('to_financial_account_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Target Tabungan (Opsional)</label>
                    <select name="saving_target_id"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                        <option value="">Tidak digunakan</option>
                        @foreach ($savingTargets as $target)
                            <option value="{{ $target->id }}"
                                {{ (string) old('saving_target_id') === (string) $target->id ? 'selected' : '' }}>
                                {{ $target->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('saving_target_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Tanggal Transfer</label>
                        <input type="date" name="transfer_date"
                            value="{{ old('transfer_date', now()->format('Y-m-d')) }}"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                        @error('transfer_date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Nominal</label>
                        <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}"
                            placeholder="0"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                        @error('amount')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Catatan</label>
                    <textarea name="note" rows="4" placeholder="Tambahkan catatan transfer kalau perlu..."
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">{{ old('note') }}</textarea>
                    @error('note')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-3 border-t border-gray-200 pt-5 sm:flex-row">
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-black">
                        Simpan Transfer
                    </button>

                    <a href="{{ route('account-transfers.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <h3 class="text-lg font-semibold tracking-tight text-gray-900">Catatan</h3>
            <ul class="mt-4 space-y-3 text-sm text-gray-600">
                <li class="rounded-2xl bg-gray-50 px-4 py-3">Rekening asal dan tujuan wajib berbeda.</li>
                <li class="rounded-2xl bg-gray-50 px-4 py-3">Saldo rekening asal harus cukup.</li>
                <li class="rounded-2xl bg-gray-50 px-4 py-3">Transfer ini akan bikin 2 catatan ledger: keluar dan masuk.
                </li>
                <li class="rounded-2xl bg-gray-50 px-4 py-3">Kalau target tabungan dipilih, progress target akan otomatis
                    bertambah.</li>
            </ul>
        </div>
    </section>
@endsection
