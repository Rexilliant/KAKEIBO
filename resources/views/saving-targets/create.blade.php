@extends('layouts.app')

@section('page_title', 'Tambah Target Tabungan')
@section('page_subtitle', 'Buat target baru untuk uang yang mau lu kumpulkan.')

@section('content')
    <section class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_380px]">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-6">
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">New saving target</p>
                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                    Tambah Target Tabungan
                </h2>
                <p class="mt-2 text-sm leading-6 text-gray-500">
                    Kasih nama tujuanmu. Dana darurat, laptop, motor, atau sekadar bukti kalau lu masih punya arah.
                </p>
            </div>

            <form action="{{ route('saving-targets.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Nama Target</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        placeholder="Contoh: Dana Darurat / Beli Laptop / Liburan"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm placeholder:text-gray-400 focus:border-emerald-500 focus:ring-emerald-500">
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Target Nominal</label>
                        <input type="number" step="0.01" min="0" name="target_amount"
                            value="{{ old('target_amount') }}" placeholder="0"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Dana Awal</label>
                        <input type="number" step="0.01" min="0" name="current_amount"
                            value="{{ old('current_amount', 0) }}" placeholder="0"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm focus:border-emerald-500 focus:ring-emerald-500">
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
                                    {{ (string) old('financial_account_id') === (string) $account->id ? 'selected' : '' }}>
                                    {{ $account->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Deadline</label>
                        <input type="date" name="deadline" value="{{ old('deadline') }}"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Status</label>
                    <select name="status"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Catatan</label>
                    <textarea name="note" rows="4" placeholder="Tulis catatan kalau perlu..."
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">{{ old('note') }}</textarea>
                </div>

                <div class="flex flex-col gap-3 border-t border-gray-200 pt-5 sm:flex-row">
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-black">
                        Simpan Target
                    </button>

                    <a href="{{ route('saving-targets.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        <div class="space-y-6">
            <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                <h3 class="text-lg font-semibold tracking-tight text-gray-900">Contoh Target</h3>
                <div class="mt-4 space-y-3">
                    <div class="rounded-2xl bg-gray-50 p-4 text-sm text-gray-700">Dana Darurat</div>
                    <div class="rounded-2xl bg-gray-50 p-4 text-sm text-gray-700">Beli Laptop</div>
                    <div class="rounded-2xl bg-gray-50 p-4 text-sm text-gray-700">Liburan</div>
                    <div class="rounded-2xl bg-gray-50 p-4 text-sm text-gray-700">Modal Usaha</div>
                </div>
            </div>

            <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                <h3 class="text-lg font-semibold tracking-tight text-gray-900">Catatan</h3>
                <ul class="mt-4 space-y-3 text-sm text-gray-600">
                    <li class="rounded-2xl bg-gray-50 px-4 py-3">Target yang jelas bikin nabung lebih masuk akal.</li>
                    <li class="rounded-2xl bg-gray-50 px-4 py-3">Rekening bisa dikosongkan dulu kalau belum pasti.</li>
                    <li class="rounded-2xl bg-gray-50 px-4 py-3">Dana awal boleh 0 kalau baru mau mulai.</li>
                </ul>
            </div>
        </div>
    </section>
@endsection
