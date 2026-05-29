@extends('layouts.app')

@section('page_title', 'Edit Kontribusi Tabungan')
@section('page_subtitle', 'Perbarui data kontribusi tabungan.')

@section('content')
    <section class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_380px]">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-6">
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">Edit contribution</p>
                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                    Edit Kontribusi Tabungan
                </h2>
            </div>

            <form action="{{ route('saving-contributions.update', $savingContribution->id) }}" method="POST"
                class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Target Tabungan</label>
                        <select name="saving_target_id"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                            @foreach ($savingTargets as $target)
                                <option value="{{ $target->id }}"
                                    {{ (string) old('saving_target_id', $savingContribution->saving_target_id) === (string) $target->id ? 'selected' : '' }}>
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
                            <option value="in" {{ old('type', $savingContribution->type) === 'in' ? 'selected' : '' }}>
                                Setor Dana
                            </option>
                            <option value="out" {{ old('type', $savingContribution->type) === 'out' ? 'selected' : '' }}>
                                Tarik Dana
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
                                {{ (string) old('account_transfer_id', $savingContribution->account_transfer_id ?? null) === (string) $transfer->id ? 'selected' : '' }}>

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
                            value="{{ old('contribution_date', $savingContribution->contribution_date?->format('Y-m-d')) }}"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                        @error('contribution_date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Amount</label>
                        <input type="number" step="0.01" min="0.01" name="amount"
                            value="{{ old('amount', $savingContribution->amount) }}"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">
                        @error('amount')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Catatan</label>
                    <textarea name="note" rows="4"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">{{ old('note', $savingContribution->note) }}</textarea>
                    @error('note')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-3 border-t border-gray-200 pt-5 sm:flex-row">
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-black">
                        Update Kontribusi
                    </button>

                    <a href="{{ route('saving-contributions.index') }}"
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
                    <p class="mt-1 font-medium text-gray-900">{{ $savingContribution->created_at?->format('d M Y H:i') }}
                    </p>
                </div>
                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Terakhir diubah</p>
                    <p class="mt-1 font-medium text-gray-900">{{ $savingContribution->updated_at?->format('d M Y H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <script>
        const select = document.getElementById('transferSelect');
        const detail = document.getElementById('transferDetail');

        const formatRupiah = (num) => {
            return 'Rp ' + Number(num).toLocaleString('id-ID');
        };

        select.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];

            if (!selected.value) {
                detail.classList.add('hidden');
                return;
            }

            document.getElementById('td-date').textContent = selected.dataset.date;
            document.getElementById('td-accounts').textContent =
            `${selected.dataset.from} → ${selected.dataset.to}`;
            document.getElementById('td-amount').textContent = formatRupiah(selected.dataset.amount);
            document.getElementById('td-used').textContent = formatRupiah(selected.dataset.used);
            document.getElementById('td-remaining').textContent = formatRupiah(selected.dataset.remaining);

            detail.classList.remove('hidden');
        });

        // trigger kalau ada selected awal
        if (select.value) {
            select.dispatchEvent(new Event('change'));
        }
    </script>
@endsection
