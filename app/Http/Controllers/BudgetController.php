<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    public function index()
    {
        $budgets = Budget::with('category')
            ->where('user_id', Auth::id())
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->paginate(10);

        $budgets->getCollection()->transform(function ($budget) {
            $spent = Transaction::where('user_id', Auth::id())
                ->where('category_id', $budget->category_id)
                ->where('type', 'expense')
                ->whereMonth('transaction_date', $budget->month)
                ->whereYear('transaction_date', $budget->year)
                ->sum('amount');

            $budget->spent_amount = (float) $spent;
            $budget->remaining_amount = (float) $budget->amount - (float) $spent;
            $budget->progress_percentage = (float) $budget->amount > 0
                ? min(100, max(0, ((float) $spent / (float) $budget->amount) * 100))
                : 0;

            if ($spent > $budget->amount) {
                $budget->budget_status = 'over';
            } elseif ((float) $budget->amount > 0 && $spent >= ((float) $budget->amount * 0.8)) {
                $budget->budget_status = 'warning';
            } else {
                $budget->budget_status = 'safe';
            }

            return $budget;
        });

        return view('budgets.index', compact('budgets'));
    }

    public function create()
    {
        $categories = Category::where('user_id', Auth::id())
            ->where('transaction_type', 'expense')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('budgets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'amount' => 'required|numeric|min:0',
            'enforcement_level' => 'required|in:soft,hard',
        ]);

        Category::where('id', $validated['category_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        Budget::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'category_id' => $validated['category_id'],
                'month' => $validated['month'],
                'year' => $validated['year'],
            ],
            [
                'amount' => $validated['amount'],
                'enforcement_level' => $validated['enforcement_level'],
            ]
        );

        return redirect()->route('budgets.index')
            ->with('success', 'Anggaran berhasil disimpan.');
    }

    public function show(Budget $budget)
    {
        abort_unless($budget->user_id === Auth::id(), 403);

        $budget->load('category');

        $spentAmount = Transaction::where('user_id', Auth::id())
            ->where('category_id', $budget->category_id)
            ->where('type', 'expense')
            ->whereMonth('transaction_date', $budget->month)
            ->whereYear('transaction_date', $budget->year)
            ->sum('amount');

        $remainingAmount = (float) $budget->amount - (float) $spentAmount;
        $progressPercentage = (float) $budget->amount > 0
            ? min(100, max(0, ((float) $spentAmount / (float) $budget->amount) * 100))
            : 0;

        if ($spentAmount > $budget->amount) {
            $budgetStatus = 'over';
        } elseif ((float) $budget->amount > 0 && $spentAmount >= ((float) $budget->amount * 0.8)) {
            $budgetStatus = 'warning';
        } else {
            $budgetStatus = 'safe';
        }

        return view('budgets.show', compact(
            'budget',
            'spentAmount',
            'remainingAmount',
            'progressPercentage',
            'budgetStatus'
        ));
    }

    public function edit(Budget $budget)
    {
        abort_unless($budget->user_id === Auth::id(), 403);

        $categories = Category::where('user_id', Auth::id())
            ->where('transaction_type', 'expense')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('budgets.edit', compact('budget', 'categories'));
    }

    public function update(Request $request, Budget $budget)
    {
        abort_unless($budget->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'amount' => 'required|numeric|min:0',
            'enforcement_level' => 'required|in:soft,hard',
        ]);

        Category::where('id', $validated['category_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $budget->update($validated);

        return redirect()->route('budgets.index')
            ->with('success', 'Anggaran berhasil diperbarui.');
    }

    public function destroy(Budget $budget)
    {
        abort_unless($budget->user_id === Auth::id(), 403);

        $budget->delete();

        return redirect()->route('budgets.index')
            ->with('success', 'Anggaran berhasil dihapus.');
    }
}