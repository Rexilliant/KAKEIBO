<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Transaction;
use App\Models\SavingTarget;
use App\Models\FinancialAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $selectedMonth = (int) $request->get('month', now()->month);
        $selectedYear = (int) $request->get('year', now()->year);

        $periodDate = Carbon::create($selectedYear, $selectedMonth, 1);
        $previousPeriod = $periodDate->copy()->subMonth();

        $baseTransactionQuery = Transaction::with(['category', 'financialAccount'])
            ->where('user_id', $user->id)
            ->whereMonth('transaction_date', $selectedMonth)
            ->whereYear('transaction_date', $selectedYear);

        $income = (float) (clone $baseTransactionQuery)
            ->where('type', 'income')
            ->sum('amount');

        $expense = (float) (clone $baseTransactionQuery)
            ->where('type', 'expense')
            ->sum('amount');

        $netCashflow = $income - $expense;
        $transactionCount = (clone $baseTransactionQuery)->count();

        $latestTransactions = (clone $baseTransactionQuery)
            ->latest('transaction_date')
            ->latest('id')
            ->take(10)
            ->get();

        $previousIncome = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereMonth('transaction_date', $previousPeriod->month)
            ->whereYear('transaction_date', $previousPeriod->year)
            ->sum('amount');

        $previousExpense = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereMonth('transaction_date', $previousPeriod->month)
            ->whereYear('transaction_date', $previousPeriod->year)
            ->sum('amount');

        $previousNetCashflow = $previousIncome - $previousExpense;

        $incomeDelta = $income - $previousIncome;
        $expenseDelta = $expense - $previousExpense;
        $netCashflowDelta = $netCashflow - $previousNetCashflow;

        $incomeDeltaPercent = $previousIncome > 0 ? ($incomeDelta / $previousIncome) * 100 : null;
        $expenseDeltaPercent = $previousExpense > 0 ? ($expenseDelta / $previousExpense) * 100 : null;
        $netCashflowDeltaPercent = $previousNetCashflow != 0 ? ($netCashflowDelta / abs($previousNetCashflow)) * 100 : null;

        $totalBalance = (float) FinancialAccount::where('user_id', $user->id)
            ->where('is_active', true)
            ->sum('balance');

        $allocatedBalance = (float) SavingTarget::where('user_id', $user->id)
            ->where('status', 'active')
            ->sum('current_amount');

        $availableBalance = $totalBalance - $allocatedBalance;

        $allocationPercentage = $totalBalance > 0
            ? min(100, max(0, ($allocatedBalance / $totalBalance) * 100))
            : 0;

        $balanceWarning = null;
        if ($availableBalance < 0) {
            $balanceWarning = 'danger';
        } elseif ($availableBalance == 0) {
            $balanceWarning = 'empty';
        } elseif ($totalBalance > 0 && ($availableBalance / $totalBalance) <= 0.2) {
            $balanceWarning = 'low';
        }

        $savingTargets = SavingTarget::with('financialAccount')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->latest()
            ->take(5)
            ->get();

        $budgetWarnings = [];
        $budgetUtilizations = [];

        $budgets = Budget::with('category')
            ->where('user_id', $user->id)
            ->where('month', $selectedMonth)
            ->where('year', $selectedYear)
            ->get();

        foreach ($budgets as $budget) {
            $spent = (float) Transaction::where('user_id', $user->id)
                ->where('category_id', $budget->category_id)
                ->where('type', 'expense')
                ->whereMonth('transaction_date', $budget->month)
                ->whereYear('transaction_date', $budget->year)
                ->sum('amount');

            $remaining = (float) $budget->amount - $spent;
            $progress = (float) $budget->amount > 0 ? min(100, max(0, ($spent / (float) $budget->amount) * 100)) : 0;

            $status = 'safe';
            if ($spent > (float) $budget->amount) {
                $status = 'over';
            } elseif ((float) $budget->amount > 0 && $spent >= ((float) $budget->amount * 0.8)) {
                $status = 'warning';
            }

            $budgetUtilizations[] = [
                'category' => $budget->category->name ?? '-',
                'budget' => (float) $budget->amount,
                'spent' => $spent,
                'remaining' => $remaining,
                'progress' => $progress,
                'status' => $status,
            ];

            if ($status === 'over') {
                $budgetWarnings[] = [
                    'category' => $budget->category->name ?? '-',
                    'budget' => (float) $budget->amount,
                    'spent' => $spent,
                    'over' => $spent - (float) $budget->amount,
                    'status' => 'over',
                ];
            } elseif ($status === 'warning') {
                $budgetWarnings[] = [
                    'category' => $budget->category->name ?? '-',
                    'budget' => (float) $budget->amount,
                    'spent' => $spent,
                    'over' => 0,
                    'status' => 'warning',
                ];
            }
        }

        usort($budgetUtilizations, fn($a, $b) => $b['progress'] <=> $a['progress']);
        $budgetUtilizations = array_slice($budgetUtilizations, 0, 5);

        $cashflowChart = [
            'labels' => ['Pemasukan', 'Pengeluaran'],
            'values' => [$income, $expense],
        ];

        $topExpenseCategories = Transaction::query()
            ->select('categories.name as category_name', DB::raw('SUM(transactions.amount) as total_amount'))
            ->join('categories', 'categories.id', '=', 'transactions.category_id')
            ->where('transactions.user_id', $user->id)
            ->where('transactions.type', 'expense')
            ->whereMonth('transactions.transaction_date', $selectedMonth)
            ->whereYear('transactions.transaction_date', $selectedYear)
            ->groupBy('categories.name')
            ->orderByDesc('total_amount')
            ->limit(5)
            ->get();

        $expenseCategoryChart = [
            'labels' => $topExpenseCategories->pluck('category_name')->values(),
            'values' => $topExpenseCategories->pluck('total_amount')->map(fn($v) => (float) $v)->values(),
        ];

        $topAccounts = Transaction::query()
            ->select('financial_accounts.name as account_name', DB::raw('COUNT(transactions.id) as total_transactions'), DB::raw('SUM(transactions.amount) as total_amount'))
            ->join('financial_accounts', 'financial_accounts.id', '=', 'transactions.financial_account_id')
            ->where('transactions.user_id', $user->id)
            ->whereMonth('transactions.transaction_date', $selectedMonth)
            ->whereYear('transactions.transaction_date', $selectedYear)
            ->groupBy('financial_accounts.name')
            ->orderByDesc('total_transactions')
            ->limit(5)
            ->get();

        $largestExpense = (clone $baseTransactionQuery)
            ->where('type', 'expense')
            ->orderByDesc('amount')
            ->first();

        $largestIncome = (clone $baseTransactionQuery)
            ->where('type', 'income')
            ->orderByDesc('amount')
            ->first();

        $burnRate = $income > 0 ? (($expense / $income) * 100) : 0;
        $savingRate = $income > 0 ? (($netCashflow / $income) * 100) : 0;

        $smartInsights = [];

        if ($income == 0 && $expense > 0) {
            $smartInsights[] = 'Tidak ada pemasukan tercatat di periode ini, tapi ada pengeluaran. Cek apakah income belum masuk.';
        }

        if ($burnRate >= 100 && $income > 0) {
            $smartInsights[] = 'Pengeluaran sudah menyamai atau melebihi pemasukan periode ini. Cashflow lagi panas.';
        } elseif ($burnRate >= 80 && $income > 0) {
            $smartInsights[] = 'Burn rate sudah tinggi. Ruang napas finansial mulai sempit.';
        }

        if ($savingRate > 20) {
            $smartInsights[] = 'Saving rate periode ini sehat. Masih ada ruang sisa yang layak.';
        } elseif ($savingRate < 0 && $income > 0) {
            $smartInsights[] = 'Saving rate negatif. Lu lagi nombok hidup, bukan nyimpen.';
        }

        if ($largestExpense) {
            $smartInsights[] = 'Expense terbesar periode ini adalah "' . $largestExpense->title . '" sebesar Rp ' . number_format($largestExpense->amount, 0, ',', '.') . '.';
        }

        if ($topExpenseCategories->count() > 0) {
            $topCategory = $topExpenseCategories->first();
            $smartInsights[] = 'Kategori pengeluaran paling dominan adalah ' . $topCategory->category_name . ' sebesar Rp ' . number_format($topCategory->total_amount, 0, ',', '.') . '.';
        }

        if ($incomeDelta > 0) {
            $smartInsights[] = 'Pemasukan naik dibanding bulan lalu sebesar Rp ' . number_format($incomeDelta, 0, ',', '.') . '.';
        } elseif ($incomeDelta < 0) {
            $smartInsights[] = 'Pemasukan turun dibanding bulan lalu sebesar Rp ' . number_format(abs($incomeDelta), 0, ',', '.') . '.';
        }

        if ($expenseDelta > 0) {
            $smartInsights[] = 'Pengeluaran naik dibanding bulan lalu sebesar Rp ' . number_format($expenseDelta, 0, ',', '.') . '.';
        } elseif ($expenseDelta < 0) {
            $smartInsights[] = 'Pengeluaran turun dibanding bulan lalu sebesar Rp ' . number_format(abs($expenseDelta), 0, ',', '.') . '.';
        }

        $trendPeriods = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = $periodDate->copy()->subMonths($i);

            $trendIncome = (float) Transaction::where('user_id', $user->id)
                ->where('type', 'income')
                ->whereMonth('transaction_date', $date->month)
                ->whereYear('transaction_date', $date->year)
                ->sum('amount');

            $trendExpense = (float) Transaction::where('user_id', $user->id)
                ->where('type', 'expense')
                ->whereMonth('transaction_date', $date->month)
                ->whereYear('transaction_date', $date->year)
                ->sum('amount');

            $trendPeriods[] = [
                'label' => $date->translatedFormat('M Y'),
                'income' => $trendIncome,
                'expense' => $trendExpense,
                'net' => $trendIncome - $trendExpense,
            ];
        }

        $trendChart = [
            'labels' => collect($trendPeriods)->pluck('label')->values(),
            'income' => collect($trendPeriods)->pluck('income')->values(),
            'expense' => collect($trendPeriods)->pluck('expense')->values(),
            'net' => collect($trendPeriods)->pluck('net')->values(),
        ];

        // Forecast akhir bulan
        $daysInMonth = $periodDate->daysInMonth;
        $currentDay = now()->month === $selectedMonth && now()->year === $selectedYear
            ? now()->day
            : $daysInMonth;

        $forecastIncome = $currentDay > 0 ? ($income / $currentDay) * $daysInMonth : 0;
        $forecastExpense = $currentDay > 0 ? ($expense / $currentDay) * $daysInMonth : 0;
        $forecastNetCashflow = $forecastIncome - $forecastExpense;

        // Financial Health Score
        $financialHealthScore = 50;

        if ($savingRate > 20) {
            $financialHealthScore += 20;
        } elseif ($savingRate > 0) {
            $financialHealthScore += 10;
        } else {
            $financialHealthScore -= 15;
        }

        if ($burnRate <= 60 && $income > 0) {
            $financialHealthScore += 15;
        } elseif ($burnRate <= 80 && $income > 0) {
            $financialHealthScore += 8;
        } elseif ($burnRate > 100 && $income > 0) {
            $financialHealthScore -= 15;
        }

        if ($availableBalance > 0) {
            $financialHealthScore += 10;
        } else {
            $financialHealthScore -= 10;
        }

        if (count($budgetWarnings) === 0) {
            $financialHealthScore += 10;
        } else {
            $financialHealthScore -= min(15, count($budgetWarnings) * 5);
        }

        $financialHealthScore = max(0, min(100, $financialHealthScore));

        if ($financialHealthScore >= 80) {
            $financialHealthLabel = 'Excellent';
        } elseif ($financialHealthScore >= 65) {
            $financialHealthLabel = 'Good';
        } elseif ($financialHealthScore >= 50) {
            $financialHealthLabel = 'Fair';
        } elseif ($financialHealthScore >= 35) {
            $financialHealthLabel = 'Weak';
        } else {
            $financialHealthLabel = 'Critical';
        }

        // Goal completion forecast
        $goalForecasts = [];
        foreach ($savingTargets as $target) {
            $remainingTarget = max(0, (float) $target->target_amount - (float) $target->current_amount);

            $monthlyAverageContribution = (float) Transaction::where('user_id', $user->id)
                ->where('type', 'income')
                ->whereMonth('transaction_date', $selectedMonth)
                ->whereYear('transaction_date', $selectedYear)
                ->sum('amount');

            $estimatedMonthlySavingCapacity = max(0, $monthlyAverageContribution * 0.2);

            $monthsToGoal = $estimatedMonthlySavingCapacity > 0
                ? ceil($remainingTarget / $estimatedMonthlySavingCapacity)
                : null;

            $goalForecasts[] = [
                'name' => $target->name,
                'current_amount' => (float) $target->current_amount,
                'target_amount' => (float) $target->target_amount,
                'remaining' => $remainingTarget,
                'months_to_goal' => $monthsToGoal,
                'estimated_monthly_capacity' => $estimatedMonthlySavingCapacity,
            ];
        }

        return view('dashboard.index', compact(
            'selectedMonth',
            'selectedYear',
            'income',
            'expense',
            'netCashflow',
            'transactionCount',
            'latestTransactions',
            'totalBalance',
            'allocatedBalance',
            'availableBalance',
            'allocationPercentage',
            'balanceWarning',
            'savingTargets',
            'budgetWarnings',
            'budgetUtilizations',
            'cashflowChart',
            'expenseCategoryChart',
            'topAccounts',
            'largestExpense',
            'largestIncome',
            'burnRate',
            'savingRate',
            'smartInsights',
            'previousIncome',
            'previousExpense',
            'previousNetCashflow',
            'incomeDelta',
            'expenseDelta',
            'netCashflowDelta',
            'incomeDeltaPercent',
            'expenseDeltaPercent',
            'netCashflowDeltaPercent',
            'trendChart',
            'forecastIncome',
            'forecastExpense',
            'forecastNetCashflow',
            'financialHealthScore',
            'financialHealthLabel',
            'goalForecasts'
        ));
    }

    public function exportReportCsv(Request $request)
    {
        $user = Auth::user();

        $selectedMonth = (int) $request->get('month', now()->month);
        $selectedYear = (int) $request->get('year', now()->year);

        $income = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereMonth('transaction_date', $selectedMonth)
            ->whereYear('transaction_date', $selectedYear)
            ->sum('amount');

        $expense = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereMonth('transaction_date', $selectedMonth)
            ->whereYear('transaction_date', $selectedYear)
            ->sum('amount');

        $netCashflow = $income - $expense;

        $totalBalance = (float) FinancialAccount::where('user_id', $user->id)
            ->where('is_active', true)
            ->sum('balance');

        $allocatedBalance = (float) SavingTarget::where('user_id', $user->id)
            ->where('status', 'active')
            ->sum('current_amount');

        $availableBalance = $totalBalance - $allocatedBalance;

        $fileName = 'dashboard-report-' . $selectedMonth . '-' . $selectedYear . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        return response()->streamDownload(function () use ($selectedMonth, $selectedYear, $income, $expense, $netCashflow, $totalBalance, $allocatedBalance, $availableBalance) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, ['Dashboard Report']);
            fputcsv($handle, ['Periode', Carbon::create()->month($selectedMonth)->translatedFormat('F') . ' ' . $selectedYear]);
            fputcsv($handle, []);
            fputcsv($handle, ['Metric', 'Value']);
            fputcsv($handle, ['Total Pemasukan', $income]);
            fputcsv($handle, ['Total Pengeluaran', $expense]);
            fputcsv($handle, ['Net Cashflow', $netCashflow]);
            fputcsv($handle, ['Saldo Rekening Aktif', $totalBalance]);
            fputcsv($handle, ['Dana Teralokasi', $allocatedBalance]);
            fputcsv($handle, ['Available Balance', $availableBalance]);

            fclose($handle);
        }, $fileName, $headers);
    }
}