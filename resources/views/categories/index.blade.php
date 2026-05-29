@extends('layouts.app')

@section('page_title', 'Kategori')
@section('page_subtitle', 'Kelola kategori pemasukan dan pengeluaran.')

@section('content')
    <section class="mb-6">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-medium uppercase tracking-[0.18em] text-emerald-700">Category management</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                        Kelola Kategori
                    </h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-500">
                        Biar transaksi lu gak cuma numpuk angka doang. Kategori yang rapi bikin laporan dan kebiasaan
                        belanja kelihatan jelas.
                    </p>
                </div>

                <div>
                    <a href="{{ route('categories.create') }}"
                        class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700">
                        + Tambah Kategori
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Total Kategori</p>
            <h3 class="mt-3 text-2xl font-semibold tracking-tight text-gray-900">{{ $categories->total() }}</h3>
            <p class="mt-2 text-sm text-gray-500">Jumlah kategori tersimpan.</p>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Halaman Saat Ini</p>
            <h3 class="mt-3 text-2xl font-semibold tracking-tight text-gray-900">{{ $categories->currentPage() }}</h3>
            <p class="mt-2 text-sm text-gray-500">Posisi data yang sedang dibuka.</p>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Per Halaman</p>
            <h3 class="mt-3 text-2xl font-semibold tracking-tight text-gray-900">{{ $categories->perPage() }}</h3>
            <p class="mt-2 text-sm text-gray-500">Jumlah item per halaman.</p>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Rentang Data</p>
            <h3 class="mt-3 text-2xl font-semibold tracking-tight text-gray-900">
                {{ $categories->firstItem() ?? 0 }} - {{ $categories->lastItem() ?? 0 }}
            </h3>
            <p class="mt-2 text-sm text-gray-500">Item yang sedang tampil sekarang.</p>
        </div>
    </section>

    <section class="hidden lg:block">
        <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-5">
                <h3 class="text-lg font-semibold tracking-tight text-gray-900">Daftar Kategori</h3>
                <p class="mt-1 text-sm text-gray-500">Versi desktop dengan tampilan tabel penuh.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-[0.14em] text-gray-500">
                        <tr>
                            <th class="px-6 py-4">Nama</th>
                            <th class="px-6 py-4">Jenis</th>
                            <th class="px-6 py-4">Tipe Kakeibo</th>
                            <th class="px-6 py-4">Icon</th>
                            <th class="px-6 py-4">Color</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($categories as $category)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $category->name }}</td>
                                <td class="px-6 py-4">
                                    @if ($category->transaction_type === 'income')
                                        <span
                                            class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">
                                            Income
                                        </span>
                                    @else
                                        <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-700">
                                            Expense
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ ucfirst($category->kakeibo_type) }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $category->icon ?? '-' }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $category->color ?? '-' }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if ($category->is_active)
                                        <span
                                            class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">Aktif</span>
                                    @else
                                        <span
                                            class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('categories.show', $category->id) }}"
                                            class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50">
                                            Detail
                                        </a>

                                        <a href="{{ route('categories.edit', $category->id) }}"
                                            class="rounded-lg border border-blue-300 px-3 py-2 text-xs font-medium text-blue-700 transition hover:bg-blue-50">
                                            Edit
                                        </a>

                                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin mau hapus kategori ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rounded-lg border border-red-300 px-3 py-2 text-xs font-medium text-red-700 transition hover:bg-red-50">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
                                    Belum ada kategori.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="space-y-4 lg:hidden">
        @forelse ($categories as $category)
            <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h3 class="truncate text-base font-semibold text-gray-900">{{ $category->name }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ ucfirst($category->kakeibo_type) }}</p>
                    </div>

                    @if ($category->is_active)
                        <span
                            class="shrink-0 rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">Aktif</span>
                    @else
                        <span
                            class="shrink-0 rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">Nonaktif</span>
                    @endif
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-2xl bg-gray-50 p-3">
                        <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Jenis</p>
                        <p class="mt-1 font-medium text-gray-900">{{ ucfirst($category->transaction_type) }}</p>
                    </div>

                    <div class="rounded-2xl bg-gray-50 p-3">
                        <p class="text-xs uppercase tracking-[0.14em] text-gray-500">Color</p>
                        <p class="mt-1 font-medium text-gray-900">{{ $category->color ?? '-' }}</p>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-3 gap-2">
                    <a href="{{ route('categories.show', $category->id) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-3 py-2.5 text-xs font-medium text-gray-700 transition hover:bg-gray-50">
                        Detail
                    </a>

                    <a href="{{ route('categories.edit', $category->id) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-blue-300 px-3 py-2.5 text-xs font-medium text-blue-700 transition hover:bg-blue-50">
                        Edit
                    </a>

                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST"
                        onsubmit="return confirm('Yakin mau hapus kategori ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex w-full items-center justify-center rounded-xl border border-red-300 px-3 py-2.5 text-xs font-medium text-red-700 transition hover:bg-red-50">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div
                class="rounded-3xl border border-dashed border-gray-300 bg-white p-10 text-center text-sm text-gray-500 shadow-sm">
                Belum ada kategori.
            </div>
        @endforelse
    </section>

    <section class="mt-6">
        <div class="rounded-3xl border border-gray-200 bg-white px-5 py-4 shadow-sm sm:px-6">
            {{ $categories->links() }}
        </div>
    </section>
@endsection
