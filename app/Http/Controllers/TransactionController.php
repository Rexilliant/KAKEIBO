<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\FinancialAccount;
use App\Models\FinancialAccountMovement;
use App\Models\MonthlyClosing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Models\TransactionAttachment;
use Illuminate\Support\Facades\Storage;


class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = Transaction::with(['category', 'financialAccount'])
            ->where('user_id', Auth::id())
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('type'), fn($q) => $q->where('type', $request->type))
            ->when($request->filled('category_id'), fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->filled('financial_account_id'), fn($q) => $q->where('financial_account_id', $request->financial_account_id))
            ->when($request->filled('month'), fn($q) => $q->whereMonth('transaction_date', $request->month))
            ->when($request->filled('year'), fn($q) => $q->whereYear('transaction_date', $request->year))
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('transaction_date', '>=', $request->date_from))
            ->when($request->filled('date_until'), fn($q) => $q->whereDate('transaction_date', '<=', $request->date_until))
            ->when($request->filled('amount_min'), fn($q) => $q->where('amount', '>=', $request->amount_min))
            ->when($request->filled('amount_max'), fn($q) => $q->where('amount', '<=', $request->amount_max))
            ->latest('transaction_date')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::where('user_id', Auth::id())
            ->where('is_active', true)
            ->orderBy('transaction_type')
            ->orderBy('name')
            ->get();

        $accounts = FinancialAccount::where('user_id', Auth::id())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $summaryQuery = Transaction::where('user_id', Auth::id())
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('type'), fn($q) => $q->where('type', $request->type))
            ->when($request->filled('category_id'), fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->filled('financial_account_id'), fn($q) => $q->where('financial_account_id', $request->financial_account_id))
            ->when($request->filled('month'), fn($q) => $q->whereMonth('transaction_date', $request->month))
            ->when($request->filled('year'), fn($q) => $q->whereYear('transaction_date', $request->year))
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('transaction_date', '>=', $request->date_from))
            ->when($request->filled('date_until'), fn($q) => $q->whereDate('transaction_date', '<=', $request->date_until))
            ->when($request->filled('amount_min'), fn($q) => $q->where('amount', '>=', $request->amount_min))
            ->when($request->filled('amount_max'), fn($q) => $q->where('amount', '<=', $request->amount_max));

        $filteredIncome = (clone $summaryQuery)->where('type', 'income')->sum('amount');
        $filteredExpense = (clone $summaryQuery)->where('type', 'expense')->sum('amount');
        $filteredCount = (clone $summaryQuery)->count();

        return view('transactions.index', compact(
            'transactions',
            'categories',
            'accounts',
            'filteredIncome',
            'filteredExpense',
            'filteredCount'
        ));
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

        $budgetData = $this->buildBudgetData();

        return view('transactions.create', compact('categories', 'accounts', 'budgetData'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'financial_account_id' => 'required|exists:financial_accounts,id',
            'category_id' => 'nullable|exists:categories,id',
            'transaction_date' => 'required|date',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'title' => 'required|string|max:255',
            'note' => 'nullable|string',
            'is_unexpected' => 'nullable|boolean',
            'affects_budget' => 'nullable|boolean',
        ]);

        $this->ensureMonthIsNotClosed($validated['transaction_date']);

        $account = FinancialAccount::where('id', $validated['financial_account_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!empty($validated['category_id'])) {
            $category = Category::where('id', $validated['category_id'])
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $this->ensureCategoryMatchesTransactionType($category, $validated['type']);
        }

        $this->enforceBudgetIfNeeded($validated);

        $validated['user_id'] = Auth::id();
        $validated['is_unexpected'] = $request->boolean('is_unexpected');
        $validated['affects_budget'] = $request->boolean('affects_budget', true);

        DB::transaction(function () use ($validated, $account) {
            $transaction = Transaction::create($validated);

            $freshAccount = FinancialAccount::lockForUpdate()->findOrFail($account->id);

            $this->applyTransactionEffect(
                $freshAccount,
                $validated['type'],
                $validated['amount'],
                $transaction->id
            );
        });

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil ditambahkan dan saldo rekening diperbarui.');
    }

    public function show(Transaction $transaction)
    {
        abort_unless($transaction->user_id === Auth::id(), 403);

        $transaction->load(['category', 'financialAccount', 'attachments']);

        $movementOut = FinancialAccountMovement::where('ref_type', 'transaction')
            ->where('ref_id', $transaction->id)
            ->where('type', 'out')
            ->latest('id')
            ->first();

        $movementIn = FinancialAccountMovement::where('ref_type', 'transaction')
            ->where('ref_id', $transaction->id)
            ->where('type', 'in')
            ->latest('id')
            ->first();

        return view('transactions.show', compact('transaction', 'movementOut', 'movementIn'));
    }

    public function edit(Transaction $transaction)
    {
        abort_unless($transaction->user_id === Auth::id(), 403);

        $categories = Category::where('user_id', Auth::id())
            ->where('is_active', true)
            ->orderBy('transaction_type')
            ->orderBy('name')
            ->get();

        $accounts = FinancialAccount::where('user_id', Auth::id())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $budgetData = $this->buildBudgetData($transaction);

        return view('transactions.edit', compact('transaction', 'categories', 'accounts', 'budgetData'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        abort_unless($transaction->user_id === Auth::id(), 403);

        $this->ensureTransactionMonthIsNotClosed($transaction);
        $this->ensureMonthIsNotClosed($request->transaction_date);

        $validated = $request->validate([
            'financial_account_id' => 'required|exists:financial_accounts,id',
            'category_id' => 'nullable|exists:categories,id',
            'transaction_date' => 'required|date',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'title' => 'required|string|max:255',
            'note' => 'nullable|string',
            'is_unexpected' => 'nullable|boolean',
            'affects_budget' => 'nullable|boolean',
        ]);

        $this->ensureMonthIsNotClosed($validated['transaction_date']);

        $newAccount = FinancialAccount::where('id', $validated['financial_account_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!empty($validated['category_id'])) {
            $category = Category::where('id', $validated['category_id'])
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $this->ensureCategoryMatchesTransactionType($category, $validated['type']);
        }

        $this->enforceBudgetIfNeeded($validated);

        $validated['is_unexpected'] = $request->boolean('is_unexpected');
        $validated['affects_budget'] = $request->boolean('affects_budget', true);

        DB::transaction(function () use ($transaction, $validated, $newAccount) {
            $oldAccount = FinancialAccount::lockForUpdate()->findOrFail($transaction->financial_account_id);

            $this->reverseTransactionEffect(
                $oldAccount,
                $transaction->type,
                $transaction->amount,
                $transaction->id
            );

            $freshNewAccount = FinancialAccount::lockForUpdate()->findOrFail($newAccount->id);

            $transaction->update($validated);

            $this->applyTransactionEffect(
                $freshNewAccount,
                $validated['type'],
                $validated['amount'],
                $transaction->id
            );
        });

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil diperbarui dan saldo rekening disesuaikan.');
    }

    public function destroy(Transaction $transaction)
    {
        abort_unless($transaction->user_id === Auth::id(), 403);

        // 🔥 TAMBAHAN WAJIB (lock bulan)
        $this->ensureTransactionMonthIsNotClosed($transaction);

        DB::transaction(function () use ($transaction) {
            $account = FinancialAccount::lockForUpdate()->findOrFail($transaction->financial_account_id);

            $this->reverseTransactionEffect(
                $account,
                $transaction->type,
                $transaction->amount,
                $transaction->id
            );

            $transaction->delete();
        });

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil dihapus dan saldo rekening dikembalikan.');
    }

    public function exportCsv(Request $request)
    {
        $query = Transaction::with(['category', 'financialAccount'])
            ->where('user_id', Auth::id())
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('type'), fn($q) => $q->where('type', $request->type))
            ->when($request->filled('category_id'), fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->filled('financial_account_id'), fn($q) => $q->where('financial_account_id', $request->financial_account_id))
            ->when($request->filled('month'), fn($q) => $q->whereMonth('transaction_date', $request->month))
            ->when($request->filled('year'), fn($q) => $q->whereYear('transaction_date', $request->year))
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('transaction_date', '>=', $request->date_from))
            ->when($request->filled('date_until'), fn($q) => $q->whereDate('transaction_date', '<=', $request->date_until))
            ->when($request->filled('amount_min'), fn($q) => $q->where('amount', '>=', $request->amount_min))
            ->when($request->filled('amount_max'), fn($q) => $q->where('amount', '<=', $request->amount_max));

        $fileName = 'laporan-transaksi.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'Tanggal',
                'Judul',
                'Kategori',
                'Rekening',
                'Tipe',
                'Nominal',
                'Catatan',
            ]);

            foreach ($query->orderBy('transaction_date')->orderBy('id')->cursor() as $trx) {
                fputcsv($handle, [
                    $trx->transaction_date,
                    $trx->title,
                    $trx->category->name ?? '-',
                    $trx->financialAccount->name ?? '-',
                    $trx->type,
                    $trx->amount,
                    $trx->note ?? '',
                ]);
            }

            fclose($handle);
        }, $fileName, $headers);
    }

    public function summaryReport(Request $request)
    {
        $userId = Auth::id();
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $baseQuery = Transaction::with(['category', 'financialAccount'])
            ->where('user_id', $userId)
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year);

        $totalIncome = (clone $baseQuery)
            ->where('type', 'income')
            ->sum('amount');

        $totalExpense = (clone $baseQuery)
            ->where('type', 'expense')
            ->sum('amount');

        $totalTransactions = (clone $baseQuery)->count();

        $netCashflow = (float) $totalIncome - (float) $totalExpense;

        $topExpenseCategories = Transaction::query()
            ->select('categories.name as category_name', DB::raw('SUM(transactions.amount) as total_amount'))
            ->join('categories', 'categories.id', '=', 'transactions.category_id')
            ->where('transactions.user_id', $userId)
            ->where('transactions.type', 'expense')
            ->whereMonth('transactions.transaction_date', $month)
            ->whereYear('transactions.transaction_date', $year)
            ->groupBy('categories.name')
            ->orderByDesc('total_amount')
            ->limit(5)
            ->get();

        $topIncomeCategories = Transaction::query()
            ->select('categories.name as category_name', DB::raw('SUM(transactions.amount) as total_amount'))
            ->join('categories', 'categories.id', '=', 'transactions.category_id')
            ->where('transactions.user_id', $userId)
            ->where('transactions.type', 'income')
            ->whereMonth('transactions.transaction_date', $month)
            ->whereYear('transactions.transaction_date', $year)
            ->groupBy('categories.name')
            ->orderByDesc('total_amount')
            ->limit(5)
            ->get();

        $largestExpense = (clone $baseQuery)
            ->where('type', 'expense')
            ->orderByDesc('amount')
            ->first();

        $largestIncome = (clone $baseQuery)
            ->where('type', 'income')
            ->orderByDesc('amount')
            ->first();

        $recentTransactions = (clone $baseQuery)
            ->latest('transaction_date')
            ->latest('id')
            ->take(10)
            ->get();

        return view('transactions.summary-report', compact(
            'month',
            'year',
            'totalIncome',
            'totalExpense',
            'totalTransactions',
            'netCashflow',
            'topExpenseCategories',
            'topIncomeCategories',
            'largestExpense',
            'largestIncome',
            'recentTransactions'
        ));
    }

    private function applyTransactionEffect(FinancialAccount $account, string $type, float|int|string $amount, ?int $refId = null): void
    {
        $amount = (float) $amount;
        $before = (float) $account->balance;

        if ($type === 'income') {
            $after = $before + $amount;

            $account->update([
                'balance' => $after,
            ]);

            FinancialAccountMovement::create([
                'financial_account_id' => $account->id,
                'user_id' => Auth::id(),
                'type' => 'in',
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $after,
                'ref_type' => 'transaction',
                'ref_id' => $refId,
                'note' => 'Income transaction',
            ]);

            return;
        }

        if ($before < $amount) {
            throw ValidationException::withMessages([
                'financial_account_id' => 'Saldo rekening yang dipilih tidak cukup untuk transaksi pengeluaran ini.',
            ]);
        }

        $after = $before - $amount;

        $account->update([
            'balance' => $after,
        ]);

        FinancialAccountMovement::create([
            'financial_account_id' => $account->id,
            'user_id' => Auth::id(),
            'type' => 'out',
            'amount' => $amount,
            'balance_before' => $before,
            'balance_after' => $after,
            'ref_type' => 'transaction',
            'ref_id' => $refId,
            'note' => 'Expense transaction',
        ]);
    }

    private function reverseTransactionEffect(FinancialAccount $account, string $type, float|int|string $amount, ?int $refId = null): void
    {
        $amount = (float) $amount;
        $before = (float) $account->balance;

        if ($type === 'income') {
            $after = $before - $amount;

            $account->update([
                'balance' => $after,
            ]);

            FinancialAccountMovement::create([
                'financial_account_id' => $account->id,
                'user_id' => Auth::id(),
                'type' => 'out',
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $after,
                'ref_type' => 'transaction_reversal',
                'ref_id' => $refId,
                'note' => 'Reversal income transaction',
            ]);

            return;
        }

        $after = $before + $amount;

        $account->update([
            'balance' => $after,
        ]);

        FinancialAccountMovement::create([
            'financial_account_id' => $account->id,
            'user_id' => Auth::id(),
            'type' => 'in',
            'amount' => $amount,
            'balance_before' => $before,
            'balance_after' => $after,
            'ref_type' => 'transaction_reversal',
            'ref_id' => $refId,
            'note' => 'Reversal expense transaction',
        ]);
    }

    private function buildBudgetData(?Transaction $editingTransaction = null): array
    {
        $userId = Auth::id();
        $now = now();

        $budgets = Budget::with('category')
            ->where('user_id', $userId)
            ->where('month', $now->month)
            ->where('year', $now->year)
            ->get();

        $result = [];

        foreach ($budgets as $budget) {
            $spentQuery = Transaction::where('user_id', $userId)
                ->where('category_id', $budget->category_id)
                ->where('type', 'expense')
                ->whereMonth('transaction_date', $budget->month)
                ->whereYear('transaction_date', $budget->year);

            if ($editingTransaction) {
                $spentQuery->where('id', '!=', $editingTransaction->id);
            }

            $spent = (float) $spentQuery->sum('amount');
            $remaining = (float) $budget->amount - $spent;

            $result[$budget->category_id] = [
                'category_name' => $budget->category->name ?? '-',
                'budget' => (float) $budget->amount,
                'spent' => $spent,
                'remaining' => $remaining,
                'month' => $budget->month,
                'year' => $budget->year,
                'enforcement_level' => $budget->enforcement_level,
            ];
        }

        return $result;
    }

    private function ensureCategoryMatchesTransactionType(Category $category, string $transactionType): void
    {
        if ($category->transaction_type !== $transactionType) {
            throw ValidationException::withMessages([
                'category_id' => 'Kategori yang dipilih tidak cocok dengan jenis transaksi.',
            ]);
        }
    }

    private function enforceBudgetIfNeeded(array $validated, ?Transaction $editingTransaction = null): void
    {
        if (($validated['type'] ?? null) !== 'expense') {
            return;
        }

        if (empty($validated['category_id'])) {
            return;
        }

        $transactionDate = \Carbon\Carbon::parse($validated['transaction_date']);

        $budget = Budget::where('user_id', Auth::id())
            ->where('category_id', $validated['category_id'])
            ->where('month', $transactionDate->month)
            ->where('year', $transactionDate->year)
            ->first();

        if (!$budget) {
            return;
        }

        $spentQuery = Transaction::where('user_id', Auth::id())
            ->where('category_id', $validated['category_id'])
            ->where('type', 'expense')
            ->whereMonth('transaction_date', $transactionDate->month)
            ->whereYear('transaction_date', $transactionDate->year);

        if ($editingTransaction) {
            $spentQuery->where('id', '!=', $editingTransaction->id);
        }

        $currentSpent = (float) $spentQuery->sum('amount');
        $projectedSpent = $currentSpent + (float) $validated['amount'];

        if ($budget->enforcement_level === 'hard' && $projectedSpent > (float) $budget->amount) {
            throw ValidationException::withMessages([
                'amount' => 'Transaksi ditolak karena melewati hard limit budget kategori ini.',
            ]);
        }
    }
    private function ensureMonthIsNotClosed(string $transactionDate): void
    {
        $date = Carbon::parse($transactionDate);

        $isClosed = MonthlyClosing::where('user_id', Auth::id())
            ->where('month', $date->month)
            ->where('year', $date->year)
            ->where('is_closed', true)
            ->exists();

        if ($isClosed) {
            throw ValidationException::withMessages([
                'transaction_date' => 'Bulan ini sudah di-close. Transaksi baru tidak boleh ditambahkan.',
            ]);
        }
    }

    private function ensureTransactionMonthIsNotClosed(Transaction $transaction): void
    {
        $date = Carbon::parse($transaction->transaction_date);

        $isClosed = MonthlyClosing::where('user_id', Auth::id())
            ->where('month', $date->month)
            ->where('year', $date->year)
            ->where('is_closed', true)
            ->exists();

        if ($isClosed) {
            throw ValidationException::withMessages([
                'transaction_date' => 'Transaksi ini berada di bulan yang sudah di-close, jadi tidak bisa diubah atau dihapus.',
            ]);
        }
    }

    public function uploadAttachment(Request $request, Transaction $transaction)
    {
        abort_unless($transaction->user_id === Auth::id(), 403);

        $request->validate([
            'files.*' => 'required|file|mimes:jpg,jpeg,png,pdf|max:3072',
        ]);

        foreach ($request->file('files', []) as $file) {
            $path = $file->store('transactions', 'public');

            TransactionAttachment::create([
                'transaction_id' => $transaction->id,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }

        return back()->with('success', 'File berhasil diupload.');
    }

    public function deleteAttachment(TransactionAttachment $attachment)
    {
        abort_unless($attachment->transaction->user_id === Auth::id(), 403);

        Storage::disk('public')->delete($attachment->file_path);

        $attachment->delete();

        return back()->with('success', 'File berhasil dihapus.');
    }
}