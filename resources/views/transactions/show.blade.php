@extends('layouts.app')

@section('page_title', 'Detail Transaksi')
@section('page_subtitle', 'Lihat detail lengkap transaksi.')

@section('content')
    <section class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_380px]">
        <div class="rounded-3xl border bg-white p-6 shadow-sm">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-semibold">{{ $transaction->title }}</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ optional($transaction->transaction_date)->format('d M Y') }}
                    </p>
                </div>

                <span
                    class="text-sm px-3 py-1 rounded-full
                {{ $transaction->type === 'income' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                    {{ $transaction->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                </span>
            </div>

            <div class="mt-6 grid sm:grid-cols-2 gap-4">
                <div class="bg-gray-50 p-4 rounded-2xl">
                    <p class="text-xs text-gray-500">Nominal</p>
                    <p
                        class="mt-2 text-lg font-semibold
                    {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-red-600' }}">
                        Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                    </p>
                </div>

                <div class="bg-gray-50 p-4 rounded-2xl">
                    <p class="text-xs text-gray-500">Rekening</p>
                    <p class="mt-2 font-medium">
                        {{ $transaction->financialAccount->name ?? '-' }}
                    </p>
                </div>

                <div class="bg-gray-50 p-4 rounded-2xl">
                    <p class="text-xs text-gray-500">Kategori</p>
                    <p class="mt-2 font-medium">
                        {{ $transaction->category->name ?? '-' }}
                    </p>
                </div>

                <div class="bg-gray-50 p-4 rounded-2xl">
                    <p class="text-xs text-gray-500">Catatan</p>
                    <p class="mt-2 text-sm text-gray-700">
                        {{ $transaction->note ?? '-' }}
                    </p>
                </div>
            </div>

            <div class="mt-6 rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900">Lampiran</h3>

                <form action="{{ route('transactions.attachments.upload', $transaction->id) }}" method="POST"
                    enctype="multipart/form-data" class="mt-4 space-y-3">
                    @csrf

                    <input type="file" name="files[]" multiple
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm">

                    <button type="submit" class="rounded-xl bg-black px-4 py-2 text-white text-sm">
                        Upload
                    </button>

                    <p class="text-xs text-gray-500">
                        Format: JPG, PNG, PDF · Max 3MB
                    </p>
                </form>
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($transaction->attachments as $file)
                    <div class="rounded-2xl border border-gray-200 p-4 bg-gray-50">
                        <p class="text-sm font-medium text-gray-900 truncate">
                            {{ $file->file_name }}
                        </p>

                        <p class="text-xs text-gray-500 mt-1">
                            {{ number_format($file->file_size / 1024, 1) }} KB
                        </p>

                        <div class="mt-3 flex gap-2">
                            @if (Str::startsWith($file->file_type, 'image'))
                                <img src="{{ asset('storage/' . $file->file_path) }}"
                                    class="mt-2 rounded-xl max-h-40 object-cover">
                            @endif

                            <form action="{{ route('transactions.attachments.delete', $file->id) }}" method="POST"
                                onsubmit="return confirm('Hapus file ini?')">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="text-xs text-red-600">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Belum ada lampiran.</p>
                @endforelse
            </div>


            <div class="mt-6 flex gap-3">
                <a href="{{ route('transactions.edit', $transaction->id) }}"
                    class="px-4 py-2 rounded-xl border text-sm">Edit</a>

                <form method="POST" action="{{ route('transactions.destroy', $transaction->id) }}">
                    @csrf
                    @method('DELETE')
                    <button class="px-4 py-2 rounded-xl border text-red-600 text-sm">
                        Hapus
                    </button>
                </form>
            </div>
        </div>

        <div class="rounded-3xl border bg-white p-5 shadow-sm">
            <h3 class="font-semibold">Insight</h3>
            <p class="mt-3 text-sm text-gray-600">
                Transaksi ini memengaruhi saldo rekening secara langsung.
            </p>
        </div>

        <div class="rounded-2xl bg-gray-50 p-4 sm:col-span-2">
            <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Detail Transfer</p>

            @php
                $movementOut = \App\Models\FinancialAccountMovement::where('ref_type', 'transaction')
                    ->where('ref_id', $transaction->id)
                    ->where('type', 'out')
                    ->latest('id')
                    ->first();

                $movementIn = \App\Models\FinancialAccountMovement::where('ref_type', 'transaction')
                    ->where('ref_id', $transaction->id)
                    ->where('type', 'in')
                    ->latest('id')
                    ->first();
            @endphp

            <div class="mt-2 text-sm text-gray-700">
                <div>
                    <strong>Dari:</strong>
                    {{ optional($movementOut?->financialAccount)->name ?? '-' }}
                </div>

                <div>
                    <strong>Ke:</strong>
                    {{ optional($movementIn?->financialAccount)->name ?? '-' }}
                </div>
            </div>
        </div>
    </section>
@endsection
