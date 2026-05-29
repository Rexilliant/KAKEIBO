<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\FinancialAccount;
use App\Models\RecurringTransaction;
use App\Models\FinancialAccountMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\MonthlyClosing;

class RecurringTransactionController extends Controller
{
    public function index(Request $request)
    {
        $recurringTransactions = RecurringTransaction::with(['category', 'financialAccount'])
            ->where('user_id', Auth::id())
            ->when($request->filled('type'), fn($q) => $q->where('type', $request->type))
            ->when($request->filled('status'), function ($q) use ($request) {
                if ($request->status === 'active') {
                    $q->where('is_active', true);
                } elseif ($request->status === 'inactive') {
                    $q->where('is_active', false);
                }
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('recurring-transactions.index', compact('recurringTransactions'));
    }

    public function create()
    {
        $categories = Category::where('user_id', Auth::id())
            ->where('is_active', true)
            ->orderBy('transaction_type')
            ->orderBy('name')
            ->get();

        $accounts = FinancialAccount::where('user_id', Auth::id())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('recurring-transactions.create', compact('categories', 'accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'financial_account_id' => 'required|exists:financial_accounts,id',
            'category_id' => 'nullable|exists:categories,id',
            'title' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string',
            'frequency' => 'required|in:monthly',
            'start_date' => 'required|date',
            'next_run_date' => 'required|date',
            'is_active' => 'nullable|boolean',
            'affects_budget' => 'nullable|boolean',
            'is_unexpected' => 'nullable|boolean',
        ]);

        FinancialAccount::where('id', $validated['financial_account_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!empty($validated['category_id'])) {
            $category = Category::where('id', $validated['category_id'])
                ->where('user_id', Auth::id())
                ->firstOrFail();

            if ($category->transaction_type !== $validated['type']) {
                throw ValidationException::withMessages([
                    'category_id' => 'Kategori tidak cocok dengan jenis recurring transaction.',
                ]);
            }
        }

        $validated['user_id'] = Auth::id();
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['affects_budget'] = $request->boolean('affects_budget', true);
        $validated['is_unexpected'] = $request->boolean('is_unexpected', false);

        RecurringTransaction::create($validated);

        return redirect()->route('recurring-transactions.index')
            ->with('success', 'Recurring transaction berhasil dibuat.');
    }

    public function show(RecurringTransaction $recurringTransaction)
    {
        abort_unless($recurringTransaction->user_id === Auth::id(), 403);

        $recurringTransaction->load(['category', 'financialAccount']);

        return view('recurring-transactions.show', compact('recurringTransaction'));
    }

    public function updateStatus(Request $request, RecurringTransaction $recurringTransaction)
    {
        abort_unless($recurringTransaction->user_id === Auth::id(), 403);

        $recurringTransaction->update([
            'is_active' => !$recurringTransaction->is_active,
        ]);

        return redirect()->route('recurring-transactions.index')
            ->with('success', 'Status recurring transaction berhasil diperbarui.');
    }

    public function generateDueTransactions()
    {
        $today = now()->startOfDay();

        $dueRecurringTransactions = RecurringTransaction::with(['financialAccount', 'category'])
            ->where('user_id', Auth::id())
            ->where('is_active', true)
            ->whereDate('next_run_date', '<=', $today)
            ->get();

        $generatedCount = 0;

        DB::transaction(function () use ($dueRecurringTransactions, $today, &$generatedCount) {
            foreach ($dueRecurringTransactions as $recurring) {
                while ($recurring->next_run_date && $recurring->next_run_date->copy()->startOfDay()->lte($today)) {
                    $runDate = $recurring->next_run_date->copy();

                    $isClosed = MonthlyClosing::where('user_id', Auth::id())
                        ->where('month', $runDate->month)
                        ->where('year', $runDate->year)
                        ->where('is_closed', true)
                        ->exists();

                    if ($isClosed) {
                        $recurring->update([
                            'last_run_date' => $runDate,
                            'next_run_date' => $runDate->copy()->addMonth(),
                        ]);

                        continue;
                    }

                    $account = FinancialAccount::lockForUpdate()->findOrFail($recurring->financial_account_id);

                    $amount = (float) $recurring->amount;
                    $before = (float) $account->balance;

                    if ($recurring->type === 'expense' && $before < $amount) {
                        break;
                    }

                    $transaction = Transaction::create([
                        'user_id' => $recurring->user_id,
                        'financial_account_id' => $recurring->financial_account_id,
                        'category_id' => $recurring->category_id,
                        'transaction_date' => $runDate,
                        'type' => $recurring->type,
                        'amount' => $amount,
                        'title' => $recurring->title,
                        'note' => $recurring->note,
                        'is_unexpected' => $recurring->is_unexpected,
                        'affects_budget' => $recurring->affects_budget,
                    ]);

                    if ($recurring->type === 'income') {
                        $after = $before + $amount;

                        $account->update([
                            'balance' => $after,
                        ]);

                        FinancialAccountMovement::create([
                            'financial_account_id' => $account->id,
                            'user_id' => $recurring->user_id,
                            'type' => 'in',
                            'amount' => $amount,
                            'balance_before' => $before,
                            'balance_after' => $after,
                            'ref_type' => 'recurring_transaction',
                            'ref_id' => $transaction->id,
                            'note' => 'Generated recurring income',
                        ]);
                    } else {
                        $after = $before - $amount;

                        $account->update([
                            'balance' => $after,
                        ]);

                        FinancialAccountMovement::create([
                            'financial_account_id' => $account->id,
                            'user_id' => $recurring->user_id,
                            'type' => 'out',
                            'amount' => $amount,
                            'balance_before' => $before,
                            'balance_after' => $after,
                            'ref_type' => 'recurring_transaction',
                            'ref_id' => $transaction->id,
                            'note' => 'Generated recurring expense',
                        ]);
                    }

                    $recurring->update([
                        'last_run_date' => $runDate,
                        'next_run_date' => $runDate->copy()->addMonth(),
                    ]);

                    $generatedCount++;
                    $recurring->refresh();
                }
            }
        });

        return redirect()->route('recurring-transactions.index')
            ->with('success', $generatedCount . ' recurring transaction berhasil digenerate.');
    }

    public function destroy(RecurringTransaction $recurringTransaction)
    {
        abort_unless($recurringTransaction->user_id === Auth::id(), 403);

        $recurringTransaction->delete();

        return redirect()->route('recurring-transactions.index')
            ->with('success', 'Recurring transaction berhasil dihapus.');
    }
}