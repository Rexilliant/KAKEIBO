<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\MonthlyClosing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonthlyClosingController extends Controller
{
    public function index()
    {
        $closings = MonthlyClosing::where('user_id', Auth::id())
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->paginate(10);

        return view('monthly-closings.index', compact('closings'));
    }

    public function closeMonth(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
        ]);

        $userId = Auth::id();

        $exists = MonthlyClosing::where('user_id', $userId)
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'month' => 'Bulan ini sudah ditutup.',
            ]);
        }

        $transactions = Transaction::where('user_id', $userId)
            ->whereMonth('transaction_date', $validated['month'])
            ->whereYear('transaction_date', $validated['year']);

        $totalIncome = (clone $transactions)
            ->where('type', 'income')
            ->sum('amount');

        $totalExpense = (clone $transactions)
            ->where('type', 'expense')
            ->sum('amount');

        $net = (float) $totalIncome - (float) $totalExpense;

        MonthlyClosing::create([
            'user_id' => $userId,
            'month' => $validated['month'],
            'year' => $validated['year'],
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'net_balance' => $net,
            'is_closed' => true,
        ]);

        return redirect()->route('monthly-reflections.create', [
            'month' => $validated['month'],
            'year' => $validated['year'],
        ])
            ->with('success', 'Bulan berhasil ditutup. Sekarang isi refleksi bulanannya.');
    }

    public function show(MonthlyClosing $monthlyClosing)
    {
        abort_unless($monthlyClosing->user_id === Auth::id(), 403);

        return view('monthly-closings.show', compact('monthlyClosing'));
    }

    public function destroy(MonthlyClosing $monthlyClosing)
    {
        abort_unless($monthlyClosing->user_id === Auth::id(), 403);

        $hasReflection = \App\Models\MonthlyReflection::where('user_id', Auth::id())
            ->where('month', $monthlyClosing->month)
            ->where('year', $monthlyClosing->year)
            ->exists();

        if ($hasReflection) {
            return redirect()->route('monthly-closings.index')
                ->withErrors([
                    'month' => 'Closing tidak bisa dihapus karena refleksi bulanannya sudah ada.',
                ]);
        }

        $monthlyClosing->delete();

        return redirect()->route('monthly-closings.index')
            ->with('success', 'Closing bulanan berhasil dihapus.');
    }
}