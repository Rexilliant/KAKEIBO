@extends('layouts.app')

@section('page_title', 'Tambah Kontribusi Tabungan')
@section('page_subtitle', 'Setor atau tarik dana dari target tabungan.')

@section('content')
    <section class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_380px]">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-6">
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">New contribution</p>
                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                    Tambah Kontribusi Tabungan
                </h2>
                <p class="mt-2 text-sm leading-6 text-gray-500">
                    Pilih transfer yang sudah ada, lalu kaitkan ke target tabungan yang sesuai.
                </p>
            </div>

            <form action="{{ route('saving-contributions.store') }}" method="POST" class="space-y-5">
                @csrf

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Target Tabungan</label>
                        <select name="saving_target_id"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                            <option value="">Pilih target</option>
                            @foreach ($savingTargets as $target)
                                <option value="{{ $target->id }}"
                                    {{ (string) old('saving_target_id', $selectedTargetId) === (string) $target->id ? 'selected' : '' }}>
                                    {{ $target->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('saving_target_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Tipe</label>
                        <select name="type"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                            <option value="in" {{ old('type', $selectedType) === 'in' ? 'selected' : '' }}>Setor Dana
                            </option>
                            <option value="out" {{ old('type', $selectedType) === 'out' ? 'selected' : '' }}>Tarik Dana
                            </option>
                        </select>
                        @error('type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Pilih Transfer</label>
                    <select name="account_transfer_id" id="transferSelect"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                        <option value="">Pilih transfer</option>
                        @foreach ($transfers as $transfer)
                            <option value="{{ $transfer->id }}" data-amount="{{ $transfer->amount }}"
                                data-used="{{ $transfer->used_amount }}"
                                data-remaining="{{ $transfer->remaining_amount }}"
                                data-from="{{ $transfer->fromAccount->name ?? '-' }}"
                                data-to="{{ $transfer->toAccount->name ?? '-' }}"
                                data-date="{{ optional($transfer->transfer_date)->format('d M Y') }}"
                                {{ (string) old('account_transfer_id', $selectedTransferId) === (string) $transfer->id ? 'selected' : '' }}>

                                {{ optional($transfer->transfer_date)->format('d M Y') }}
                                — Rp {{ number_format($transfer->amount, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>

                    @error('account_transfer_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div id="transferDetail" class="hidden mt-4 rounded-2xl bg-gray-50 p-4 text-sm">
                    <div class="grid grid-cols-2 gap-3">

                        <div>
                            <p class="text-xs text-gray-500">Tanggal</p>
                            <p id="td-date" class="font-semibold text-gray-900"></p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500">Rekening</p>
                            <p id="td-accounts" class="font-semibold text-gray-900"></p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500">Total Transfer</p>
                            <p id="td-amount" class="font-semibold text-gray-900"></p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500">Terpakai</p>
                            <p id="td-used" class="font-semibold text-gray-900"></p>
                        </div>

                        <div class="col-span-2">
                            <p class="text-xs text-gray-500">Sisa</p>
                            <p id="td-remaining" class="font-semibold text-emerald-600"></p>
                        </div>

                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Tanggal</label>
                        <input type="date" name="contribution_date"
                            value="{{ old('contribution_date', now()->format('Y-m-d')) }}"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                        @error('contribution_date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Amount</label>
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
                    <textarea name="note" rows="4" placeholder="Tambahkan catatan kalau perlu..."
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">{{ old('note') }}</textarea>
                    @error('note')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-3 border-t border-gray-200 pt-5 sm:flex-row">
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-black">
                        Simpan Kontribusi
                    </button>

                    <a href="{{ route('saving-contributions.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        <div class="space-y-6">
            <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                <h3 class="text-lg font-semibold tracking-tight text-gray-900">Panduan</h3>
                <div class="mt-4 space-y-4">
                    <div class="rounded-2xl bg-emerald-50 p-4">
                        <p class="text-xs uppercase tracking-[0.14em] text-emerald-700">Setor</p>
                        <p class="mt-2 text-sm leading-6 text-emerald-900">
                            Tambah nominal target dari transfer yang sudah dilakukan.
                        </p>
                    </div>

                    <div class="rounded-2xl bg-red-50 p-4">
                        <p class="text-xs uppercase tracking-[0.14em] text-red-700">Tarik</p>
                        <p class="mt-2 text-sm leading-6 text-red-900">
                            Kurangi nominal target dari transfer yang sudah dilakukan.
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                <h3 class="text-lg font-semibold tracking-tight text-gray-900">Catatan</h3>
                <ul class="mt-4 space-y-3 text-sm text-gray-600">
                    <li class="rounded-2xl bg-gray-50 px-4 py-3">Transfer saldo tetap diurus oleh fitur transfer dana.</li>
                    <li class="rounded-2xl bg-gray-50 px-4 py-3">Kontribusi saving hanya mencatat hubungan transfer ke
                        target.</li>
                    <li class="rounded-2xl bg-gray-50 px-4 py-3">Nominal kontribusi tidak boleh melebihi nominal transfer.
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <script>
        const select = document.getElementById('transferSelect');
        const detail = document.getElementById('transferDetail');
        const amountInput = document.getElementById('amountInput');

        const formatRupiah = (num) => {
            return 'Rp ' + Number(num).toLocaleString('id-ID');
        };

        select.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];

            if (!selected.value) {
                detail.classList.add('hidden');
                return;
            }

            const remaining = parseFloat(selected.dataset.remaining);

            document.getElementById('td-date').textContent = selected.dataset.date;
            document.getElementById('td-accounts').textContent =
                `${selected.dataset.from} → ${selected.dataset.to}`;
            document.getElementById('td-amount').textContent =
                formatRupiah(selected.dataset.amount);
            document.getElementById('td-used').textContent =
                formatRupiah(selected.dataset.used);
            document.getElementById('td-remaining').textContent =
                formatRupiah(remaining);

            // 🔥 auto isi nominal (biar user ga salah)
            if (amountInput) {
                amountInput.value = remaining;
            }

            detail.classList.remove('hidden');
        });

        // trigger kalau ada selected awal
        if (select.value) {
            select.dispatchEvent(new Event('change'));
        }
    </script>
@endsection
