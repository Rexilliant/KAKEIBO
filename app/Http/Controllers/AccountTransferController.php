<?php

namespace App\Http\Controllers;

use App\Models\AccountTransfer;
use App\Models\FinancialAccount;
use App\Models\FinancialAccountMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\MonthlyClosing;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use App\Models\SavingContribution;
use App\Models\SavingTarget;

class AccountTransferController extends Controller
{
    public function index(Request $request)
    {
        $transfers = AccountTransfer::with(['fromAccount', 'toAccount'])
            ->where('user_id', Auth::id())
            ->when($request->filled('month'), function ($query) use ($request) {
                $query->whereMonth('transfer_date', $request->month);
            })
            ->when($request->filled('year'), function ($query) use ($request) {
                $query->whereYear('transfer_date', $request->year);
            })
            ->latest('transfer_date')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('account-transfers.index', compact('transfers'));
    }

    public function create()
    {
        $accounts = FinancialAccount::where('user_id', Auth::id())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $savingTargets = SavingTarget::where('user_id', Auth::id())
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('account-transfers.create', compact('accounts', 'savingTargets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_financial_account_id' => 'required|exists:financial_accounts,id',
            'to_financial_account_id' => 'required|exists:financial_accounts,id|different:from_financial_account_id',
            'saving_target_id' => 'nullable|exists:saving_targets,id',
            'transfer_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string',
        ]);

        $this->ensureMonthIsNotClosed($validated['transfer_date']);

        $fromAccount = FinancialAccount::where('id', $validated['from_financial_account_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $toAccount = FinancialAccount::where('id', $validated['to_financial_account_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $savingTarget = null;
        if (!empty($validated['saving_target_id'])) {
            $savingTarget = SavingTarget::where('id', $validated['saving_target_id'])
                ->where('user_id', Auth::id())
                ->firstOrFail();
        }

        DB::transaction(function () use ($validated, $fromAccount, $toAccount, $savingTarget) {
            $lockedFrom = FinancialAccount::lockForUpdate()->findOrFail($fromAccount->id);
            $lockedTo = FinancialAccount::lockForUpdate()->findOrFail($toAccount->id);

            $lockedTarget = null;
            if ($savingTarget) {
                $lockedTarget = SavingTarget::lockForUpdate()->findOrFail($savingTarget->id);
            }

            $amount = (float) $validated['amount'];

            if ((float) $lockedFrom->balance < $amount) {
                abort(422, 'Saldo rekening asal tidak cukup untuk transfer ini.');
            }

            $transfer = AccountTransfer::create([
                'user_id' => Auth::id(),
                'from_financial_account_id' => $lockedFrom->id,
                'to_financial_account_id' => $lockedTo->id,
                'saving_target_id' => $lockedTarget?->id,
                'transfer_date' => $validated['transfer_date'],
                'amount' => $amount,
                'note' => $validated['note'] ?? null,
            ]);

            $fromBefore = (float) $lockedFrom->balance;
            $fromAfter = $fromBefore - $amount;

            $lockedFrom->update([
                'balance' => $fromAfter,
            ]);

            FinancialAccountMovement::create([
                'financial_account_id' => $lockedFrom->id,
                'user_id' => Auth::id(),
                'type' => 'out',
                'amount' => $amount,
                'balance_before' => $fromBefore,
                'balance_after' => $fromAfter,
                'ref_type' => 'account_transfer',
                'ref_id' => $transfer->id,
                'note' => 'Transfer ke rekening: ' . ($lockedTo->name ?? '-'),
            ]);

            $toBefore = (float) $lockedTo->balance;
            $toAfter = $toBefore + $amount;

            $lockedTo->update([
                'balance' => $toAfter,
            ]);

            FinancialAccountMovement::create([
                'financial_account_id' => $lockedTo->id,
                'user_id' => Auth::id(),
                'type' => 'in',
                'amount' => $amount,
                'balance_before' => $toBefore,
                'balance_after' => $toAfter,
                'ref_type' => 'account_transfer',
                'ref_id' => $transfer->id,
                'note' => 'Transfer dari rekening: ' . ($lockedFrom->name ?? '-'),
            ]);

            if ($lockedTarget) {
                $lockedTarget->increment('current_amount', $amount);

                SavingContribution::create([
                    'saving_target_id' => $lockedTarget->id,
                    'account_transfer_id' => $transfer->id,
                    'financial_account_id' => $lockedTo->id,
                    'contribution_date' => $validated['transfer_date'],
                    'amount' => $amount,
                    'type' => 'in',
                    'note' => $validated['note'] ?? null,
                ]);
            }
        });

        return redirect()->route('account-transfers.index')
            ->with('success', 'Transfer antar rekening berhasil disimpan.');
    }

    public function show(AccountTransfer $accountTransfer)
    {
        abort_unless($accountTransfer->user_id === Auth::id(), 403);

        $accountTransfer->load(['fromAccount', 'toAccount']);

        return view('account-transfers.show', compact('accountTransfer'));
    }

    public function destroy(AccountTransfer $accountTransfer)
    {
        abort_unless($accountTransfer->user_id === Auth::id(), 403);

        $this->ensureTransferMonthIsNotClosed($accountTransfer);

        DB::transaction(function () use ($accountTransfer) {
            $fromAccount = FinancialAccount::lockForUpdate()->findOrFail($accountTransfer->from_financial_account_id);
            $toAccount = FinancialAccount::lockForUpdate()->findOrFail($accountTransfer->to_financial_account_id);

            $amount = (float) $accountTransfer->amount;

            if ((float) $toAccount->balance < $amount) {
                abort(422, 'Saldo rekening tujuan tidak cukup untuk rollback transfer ini.');
            }

            $fromBefore = (float) $fromAccount->balance;
            $fromAfter = $fromBefore + $amount;

            $fromAccount->update([
                'balance' => $fromAfter,
            ]);

            FinancialAccountMovement::create([
                'financial_account_id' => $fromAccount->id,
                'user_id' => Auth::id(),
                'type' => 'in',
                'amount' => $amount,
                'balance_before' => $fromBefore,
                'balance_after' => $fromAfter,
                'ref_type' => 'account_transfer_reversal',
                'ref_id' => $accountTransfer->id,
                'note' => 'Reversal transfer dari rekening: ' . ($toAccount->name ?? '-'),
            ]);

            $toBefore = (float) $toAccount->balance;
            $toAfter = $toBefore - $amount;

            $toAccount->update([
                'balance' => $toAfter,
            ]);

            FinancialAccountMovement::create([
                'financial_account_id' => $toAccount->id,
                'user_id' => Auth::id(),
                'type' => 'out',
                'amount' => $amount,
                'balance_before' => $toBefore,
                'balance_after' => $toAfter,
                'ref_type' => 'account_transfer_reversal',
                'ref_id' => $accountTransfer->id,
                'note' => 'Reversal transfer ke rekening: ' . ($fromAccount->name ?? '-'),
            ]);

            $accountTransfer->delete();
        });

        return redirect()->route('account-transfers.index')
            ->with('success', 'Transfer berhasil dihapus dan saldo dikembalikan.');
    }

    private function ensureMonthIsNotClosed(string $transferDate): void
    {
        $date = Carbon::parse($transferDate);

        $isClosed = MonthlyClosing::where('user_id', Auth::id())
            ->where('month', $date->month)
            ->where('year', $date->year)
            ->where('is_closed', true)
            ->exists();

        if ($isClosed) {
            throw ValidationException::withMessages([
                'transfer_date' => 'Bulan ini sudah di-close. Transfer tidak bisa ditambahkan atau dihapus.',
            ]);
        }
    }

    private function ensureTransferMonthIsNotClosed(AccountTransfer $accountTransfer): void
    {
        $date = Carbon::parse($accountTransfer->transfer_date);

        $isClosed = MonthlyClosing::where('user_id', Auth::id())
            ->where('month', $date->month)
            ->where('year', $date->year)
            ->where('is_closed', true)
            ->exists();

        if ($isClosed) {
            throw ValidationException::withMessages([
                'transfer_date' => 'Transfer ini berada di bulan yang sudah di-close, jadi tidak bisa dihapus.',
            ]);
        }
    }
}