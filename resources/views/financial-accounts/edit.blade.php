@extends('layouts.app')

@section('page_title', 'Edit Rekening')
@section('page_subtitle', 'Perbarui data rekening yang sudah ada.')

@section('content')
    <section class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_380px]">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-6">
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">Edit account</p>
                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                    Edit Rekening
                </h2>
            </div>

            <form action="{{ route('financial-accounts.update', $financialAccount->id) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="name" class="mb-2 block text-sm font-medium text-gray-700">Nama Rekening</label>
                        <input type="text" id="name" name="name"
                            value="{{ old('name', $financialAccount->name) }}"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label for="type" class="mb-2 block text-sm font-medium text-gray-700">Tipe</label>
                        <select id="type" name="type"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="bank" {{ old('type', $financialAccount->type) === 'bank' ? 'selected' : '' }}>
                                Bank</option>
                            <option value="e_wallet"
                                {{ old('type', $financialAccount->type) === 'e_wallet' ? 'selected' : '' }}>E-Wallet
                            </option>
                            <option value="cash" {{ old('type', $financialAccount->type) === 'cash' ? 'selected' : '' }}>
                                Cash</option>
                            <option value="other" {{ old('type', $financialAccount->type) === 'other' ? 'selected' : '' }}>
                                Lainnya</option>
                        </select>
                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="provider" class="mb-2 block text-sm font-medium text-gray-700">Provider</label>
                        <input type="text" id="provider" name="provider"
                            value="{{ old('provider', $financialAccount->provider) }}"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label for="account_number" class="mb-2 block text-sm font-medium text-gray-700">Nomor Rekening /
                            Akun</label>
                        <input type="text" id="account_number" name="account_number"
                            value="{{ old('account_number', $financialAccount->account_number) }}"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                </div>

                <div>
                    <label for="balance" class="mb-2 block text-sm font-medium text-gray-700">Saldo</label>
                    <input type="number" step="0.01" min="0" id="balance" name="balance"
                        value="{{ old('balance', $financialAccount->balance) }}"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-emerald-500 focus:ring-emerald-500">
                </div>

                <label class="flex items-start gap-3 rounded-2xl border border-gray-200 bg-gray-50 p-4">
                    <input type="checkbox" name="is_active" value="1"
                        {{ old('is_active', $financialAccount->is_active) ? 'checked' : '' }}
                        class="mt-1 h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Rekening aktif</p>
                        <p class="mt-1 text-sm leading-6 text-gray-500">Kalau nonaktif, rekening ini gak akan muncul di
                            pilihan form lain.</p>
                    </div>
                </label>

                <div class="flex flex-col gap-3 border-t border-gray-200 pt-5 sm:flex-row">
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-black">
                        Update Rekening
                    </button>

                    <a href="{{ route('financial-accounts.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <h3 class="text-lg font-semibold tracking-tight text-gray-900">Info Rekening</h3>
            <div class="mt-4 space-y-3 text-sm">
                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Dibuat</p>
                    <p class="mt-1 font-medium text-gray-900">{{ $financialAccount->created_at?->format('d M Y H:i') }}</p>
                </div>
                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Terakhir diubah</p>
                    <p class="mt-1 font-medium text-gray-900">{{ $financialAccount->updated_at?->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>
    </section>
@endsection
