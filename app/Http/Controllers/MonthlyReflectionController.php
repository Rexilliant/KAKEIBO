<?php

namespace App\Http\Controllers;

use App\Models\MonthlyReflection;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MonthlyReflectionController extends Controller
{
    public function index()
    {
        $monthlyReflections = MonthlyReflection::where('user_id', Auth::id())
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->paginate(10);

        return view('monthly-reflections.index', compact('monthlyReflections'));
    }

    public function create(Request $request)
    {
        $userId = Auth::id();
        $selectedMonth = (int) $request->get('month', now()->month);
        $selectedYear = (int) $request->get('year', now()->year);

        $existingReflection = MonthlyReflection::where('user_id', $userId)
            ->where('month', $selectedMonth)
            ->where('year', $selectedYear)
            ->first();

        if ($existingReflection) {
            return redirect()->route('monthly-reflections.edit', $existingReflection->id)
                ->with('success', 'Refleksi untuk bulan ini sudah ada. Silakan edit yang sudah ada.');
        }

        $totalIncome = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereMonth('transaction_date', $selectedMonth)
            ->whereYear('transaction_date', $selectedYear)
            ->sum('amount');

        $totalExpense = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereMonth('transaction_date', $selectedMonth)
            ->whereYear('transaction_date', $selectedYear)
            ->sum('amount');

        $topCategory = Transaction::query()
            ->select('categories.name as category_name', DB::raw('SUM(transactions.amount) as total_amount'))
            ->join('categories', 'categories.id', '=', 'transactions.category_id')
            ->where('transactions.user_id', $userId)
            ->where('transactions.type', 'expense')
            ->whereMonth('transactions.transaction_date', $selectedMonth)
            ->whereYear('transactions.transaction_date', $selectedYear)
            ->groupBy('categories.name')
            ->orderByDesc('total_amount')
            ->first();

        $largestExpense = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereMonth('transaction_date', $selectedMonth)
            ->whereYear('transaction_date', $selectedYear)
            ->orderByDesc('amount')
            ->first();

        $reflectionInsights = [
            'total_income' => (float) $totalIncome,
            'total_expense' => (float) $totalExpense,
            'top_category_name' => $topCategory->category_name ?? '-',
            'top_category_amount' => (float) ($topCategory->total_amount ?? 0),
            'largest_expense_title' => $largestExpense->title ?? '-',
            'largest_expense_amount' => (float) ($largestExpense->amount ?? 0),
            'suggestion_money_owned' => 'Bulan ini aku punya pemasukan total Rp ' . number_format($totalIncome, 0, ',', '.') . ' dan pengeluaran Rp ' . number_format($totalExpense, 0, ',', '.') . '.',
            'suggestion_spending' => $topCategory
                ? 'Pengeluaran paling besar bulan ini ada di kategori ' . $topCategory->category_name . ' sebesar Rp ' . number_format($topCategory->total_amount, 0, ',', '.') . '.'
                : 'Belum ada kategori pengeluaran dominan bulan ini.',
            'suggestion_unnecessary' => $largestExpense
                ? 'Transaksi expense terbesar bulan ini adalah "' . $largestExpense->title . '" sebesar Rp ' . number_format($largestExpense->amount, 0, ',', '.') . '. Coba nilai apakah ini benar-benar perlu.'
                : 'Belum ada transaksi pengeluaran besar bulan ini.',
            'suggestion_improvement' => 'Bulan depan aku mau lebih hati-hati di kategori ' . ($topCategory->category_name ?? 'pengeluaran utama') . ' dan lebih disiplin soal prioritas.',
        ];

        return view('monthly-reflections.create', compact(
            'reflectionInsights',
            'selectedMonth',
            'selectedYear'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'planned_saving' => 'required|numeric|min:0',
            'actual_saving' => 'required|numeric|min:0',
            'question_1_money_owned' => 'nullable|string',
            'question_2_saving_goal' => 'nullable|string',
            'question_3_actual_spending' => 'nullable|string',
            'question_4_unnecessary_expense' => 'nullable|string',
            'question_5_improvement_next_month' => 'nullable|string',
            'question_6_best_financial_decision' => 'nullable|string',
            'mood' => 'nullable|string',
            'mood_note' => 'nullable|string',
            'commitment_next_month' => 'nullable|string',
        ]);

        $exists = MonthlyReflection::where('user_id', Auth::id())
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['month' => 'Refleksi untuk bulan ini sudah ada.'])
                ->withInput();
        }

        $validated['user_id'] = Auth::id();

        MonthlyReflection::create($validated);

        return redirect()->route('monthly-reflections.index')
            ->with('success', 'Refleksi berhasil disimpan.');
    }

    public function show(MonthlyReflection $monthlyReflection)
    {
        abort_unless($monthlyReflection->user_id === Auth::id(), 403);

        return view('monthly-reflections.show', compact('monthlyReflection'));
    }

    public function edit(MonthlyReflection $monthlyReflection)
    {
        abort_unless($monthlyReflection->user_id === Auth::id(), 403);

        return view('monthly-reflections.edit', compact('monthlyReflection'));
    }

    public function update(Request $request, MonthlyReflection $monthlyReflection)
    {
        abort_unless($monthlyReflection->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'planned_saving' => 'required|numeric|min:0',
            'actual_saving' => 'required|numeric|min:0',
            'question_1_money_owned' => 'nullable|string',
            'question_2_saving_goal' => 'nullable|string',
            'question_3_actual_spending' => 'nullable|string',
            'question_4_unnecessary_expense' => 'nullable|string',
            'question_5_improvement_next_month' => 'nullable|string',
            'question_6_best_financial_decision' => 'nullable|string',
            'mood' => 'nullable|string',
            'mood_note' => 'nullable|string',
            'commitment_next_month' => 'nullable|string',
        ]);

        $exists = MonthlyReflection::where('user_id', Auth::id())
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->where('id', '!=', $monthlyReflection->id)
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['month' => 'Refleksi untuk bulan dan tahun ini sudah ada.'])
                ->withInput();
        }

        $monthlyReflection->update($validated);

        return redirect()->route('monthly-reflections.index')
            ->with('success', 'Refleksi berhasil diperbarui.');
    }

    public function destroy(MonthlyReflection $monthlyReflection)
    {
        abort_unless($monthlyReflection->user_id === Auth::id(), 403);

        $monthlyReflection->delete();

        return redirect()->route('monthly-reflections.index')
            ->with('success', 'Refleksi berhasil dihapus.');
    }
}