@extends('layouts.app')

@section('page_title', 'Tambah Rekening')
@section('page_subtitle', 'Tambahkan tempat penyimpanan uang baru.')

@section('content')
    <section class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_380px]">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-6">
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">New account</p>
                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                    Tambah Rekening
                </h2>
                <p class="mt-2 text-sm leading-6 text-gray-500">
                    Tambahkan rekening bank, e-wallet, cash, atau tempat lain buat nyimpen uang. Biar sistem ini gak
                    nebak-nebak duitmu lagi nongkrong di mana.
                </p>
            </div>

            <form action="{{ route('financial-accounts.store') }}" method="POST" class="space-y-5">
                @csrf

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="name" class="mb-2 block text-sm font-medium text-gray-700">Nama Rekening</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                            placeholder="Contoh: BCA Utama / Cash Harian / DANA Pribadi"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label for="type" class="mb-2 block text-sm font-medium text-gray-700">Tipe</label>
                        <select id="type" name="type"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="bank" {{ old('type') === 'bank' ? 'selected' : '' }}>Bank</option>
                            <option value="e_wallet" {{ old('type') === 'e_wallet' ? 'selected' : '' }}>E-Wallet</option>
                            <option value="cash" {{ old('type') === 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="other" {{ old('type') === 'other' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="provider" class="mb-2 block text-sm font-medium text-gray-700">Provider</label>
                        <input type="text" id="provider" name="provider" value="{{ old('provider') }}"
                            placeholder="Contoh: BCA / Mandiri / DANA / OVO"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label for="account_number" class="mb-2 block text-sm font-medium text-gray-700">Nomor Rekening /
                            Akun</label>
                        <input type="text" id="account_number" name="account_number" value="{{ old('account_number') }}"
                            placeholder="Opsional"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                </div>

                <div>
                    <label for="balance" class="mb-2 block text-sm font-medium text-gray-700">Saldo Awal</label>
                    <input type="number" step="0.01" min="0" id="balance" name="balance"
                        value="{{ old('balance', 0) }}" placeholder="0"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 focus:border-emerald-500 focus:ring-emerald-500">
                </div>

                <label class="flex items-start gap-3 rounded-2xl border border-gray-200 bg-gray-50 p-4">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}
                        class="mt-1 h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Rekening aktif</p>
                        <p class="mt-1 text-sm leading-6 text-gray-500">
                            Rekening aktif akan muncul di pilihan transaksi, target tabungan, dan kontribusi saving.
                        </p>
                    </div>
                </label>

                <div class="flex flex-col gap-3 border-t border-gray-200 pt-5 sm:flex-row">
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-black">
                        Simpan Rekening
                    </button>

                    <a href="{{ route('financial-accounts.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        <div class="space-y-6">
            <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                <h3 class="text-lg font-semibold tracking-tight text-gray-900">Contoh Rekening</h3>
                <div class="mt-4 space-y-3">
                    <div class="rounded-2xl bg-gray-50 p-4 text-sm text-gray-700">BCA Utama — Bank</div>
                    <div class="rounded-2xl bg-gray-50 p-4 text-sm text-gray-700">Cash Harian — Cash</div>
                    <div class="rounded-2xl bg-gray-50 p-4 text-sm text-gray-700">DANA Pribadi — E-Wallet</div>
                    <div class="rounded-2xl bg-gray-50 p-4 text-sm text-gray-700">Jago Saving — Bank</div>
                </div>
            </div>

            <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                <h3 class="text-lg font-semibold tracking-tight text-gray-900">Catatan</h3>
                <ul class="mt-4 space-y-3 text-sm text-gray-600">
                    <li class="rounded-2xl bg-gray-50 px-4 py-3">Nama rekening harus gampang dikenali.</li>
                    <li class="rounded-2xl bg-gray-50 px-4 py-3">Provider boleh kosong kalau tipe cash.</li>
                    <li class="rounded-2xl bg-gray-50 px-4 py-3">Saldo awal membantu dashboard tampil lebih masuk akal.</li>
                </ul>
            </div>
        </div>
    </section>
@endsection
