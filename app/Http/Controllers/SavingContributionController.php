<?php

namespace App\Http\Controllers;

use App\Models\AccountTransfer;
use App\Models\SavingTarget;
use App\Models\SavingContribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SavingContributionController extends Controller
{
    public function index()
    {
        $contributions = SavingContribution::with([
            'savingTarget',
            'financialAccount',
            'accountTransfer.fromAccount',
            'accountTransfer.toAccount',
        ])
            ->whereHas('savingTarget', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->latest('contribution_date')
            ->latest('id')
            ->paginate(10);

        $contributions->getCollection()->transform(function ($contribution) {
            $usedAmount = 0;
            $remainingAmount = 0;

            if ($contribution->accountTransfer) {
                $usedAmount = (float) SavingContribution::where('account_transfer_id', $contribution->account_transfer_id)
                    ->sum('amount');

                $remainingAmount = (float) $contribution->accountTransfer->amount - $usedAmount;
            }

            $contribution->used_amount = $usedAmount;
            $contribution->remaining_amount = $remainingAmount;

            return $contribution;
        });

        return view('saving-contributions.index', compact('contributions'));
    }

    public function create(Request $request)
    {
        $savingTargets = SavingTarget::where('user_id', Auth::id())
            ->orderBy('name')
            ->get();

        $selectedTransferId = $request->get('account_transfer_id');
        $selectedTargetId = $request->get('target');
        $selectedType = $request->get('type', 'in');

        $transfers = AccountTransfer::with(['fromAccount', 'toAccount'])
            ->where('user_id', Auth::id())
            ->latest('transfer_date')
            ->latest('id')
            ->get()
            ->map(function ($transfer) {
                $usedAmount = (float) SavingContribution::where('account_transfer_id', $transfer->id)
                    ->sum('amount');

                $remainingAmount = (float) $transfer->amount - $usedAmount;

                $transfer->used_amount = $usedAmount;
                $transfer->remaining_amount = $remainingAmount;

                return $transfer;
            })
            ->filter(function ($transfer) use ($selectedTransferId) {
                return $transfer->remaining_amount > 0
                    || (string) $transfer->id === (string) $selectedTransferId;
            })
            ->values();

        return view('saving-contributions.create', compact(
            'savingTargets',
            'transfers',
            'selectedTargetId',
            'selectedType',
            'selectedTransferId'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'saving_target_id' => 'required|exists:saving_targets,id',
            'account_transfer_id' => 'required|exists:account_transfers,id',
            'contribution_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:in,out',
            'note' => 'nullable|string',
        ]);

        $target = SavingTarget::where('id', $validated['saving_target_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $transfer = AccountTransfer::with(['fromAccount', 'toAccount'])
            ->where('id', $validated['account_transfer_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $amount = (float) $validated['amount'];

        $usedAmount = (float) SavingContribution::where('account_transfer_id', $transfer->id)
            ->sum('amount');

        $remainingAmount = (float) $transfer->amount - $usedAmount;

        if ($amount > $remainingAmount) {
            throw ValidationException::withMessages([
                'amount' => 'Nominal kontribusi melebihi sisa transfer yang tersedia. Sisa yang bisa dipakai: Rp ' . number_format($remainingAmount, 0, ',', '.'),
            ]);
        }

        DB::transaction(function () use ($validated, $target, $transfer, $amount) {
            $lockedTarget = SavingTarget::lockForUpdate()->findOrFail($target->id);

            if ($validated['type'] === 'out' && (float) $lockedTarget->current_amount < $amount) {
                throw ValidationException::withMessages([
                    'amount' => 'Saldo target tidak cukup untuk ditarik.',
                ]);
            }

            if ($validated['type'] === 'in') {
                $lockedTarget->increment('current_amount', $amount);
            } else {
                $lockedTarget->decrement('current_amount', $amount);
            }

            SavingContribution::create([
                'saving_target_id' => $lockedTarget->id,
                'account_transfer_id' => $transfer->id,
                'financial_account_id' => $transfer->to_financial_account_id,
                'contribution_date' => $validated['contribution_date'],
                'amount' => $amount,
                'type' => $validated['type'],
                'note' => $validated['note'] ?? null,
            ]);
        });

        return redirect()->route('saving-contributions.index')
            ->with('success', 'Kontribusi berhasil disimpan.');
    }

    public function show(SavingContribution $savingContribution)
    {
        $savingContribution->load([
            'savingTarget',
            'financialAccount',
            'accountTransfer.fromAccount',
            'accountTransfer.toAccount',
        ]);

        abort_unless($savingContribution->savingTarget->user_id === Auth::id(), 403);

        $usedAmount = 0;
        $remainingAmount = 0;

        if ($savingContribution->accountTransfer) {
            $usedAmount = (float) SavingContribution::where('account_transfer_id', $savingContribution->account_transfer_id)
                ->sum('amount');

            $remainingAmount = (float) $savingContribution->accountTransfer->amount - $usedAmount;
        }

        return view('saving-contributions.show', compact(
            'savingContribution',
            'usedAmount',
            'remainingAmount'
        ));
    }

    public function edit(SavingContribution $savingContribution)
    {
        $savingContribution->load([
            'savingTarget',
            'accountTransfer',
        ]);

        abort_unless($savingContribution->savingTarget->user_id === Auth::id(), 403);

        $savingTargets = SavingTarget::where('user_id', Auth::id())
            ->orderBy('name')
            ->get();

        $transfers = AccountTransfer::with(['fromAccount', 'toAccount'])
            ->where('user_id', Auth::id())
            ->latest('transfer_date')
            ->latest('id')
            ->get()
            ->map(function ($transfer) use ($savingContribution) {
                $usedAmount = (float) SavingContribution::where('account_transfer_id', $transfer->id)
                    ->where('id', '!=', $savingContribution->id)
                    ->sum('amount');

                $remainingAmount = (float) $transfer->amount - $usedAmount;

                $transfer->used_amount = $usedAmount;
                $transfer->remaining_amount = $remainingAmount;

                return $transfer;
            })
            ->filter(function ($transfer) use ($savingContribution) {
                return $transfer->remaining_amount > 0
                    || (string) $transfer->id === (string) $savingContribution->account_transfer_id;
            })
            ->values();

        return view('saving-contributions.edit', compact(
            'savingContribution',
            'savingTargets',
            'transfers'
        ));
    }

    public function update(Request $request, SavingContribution $savingContribution)
    {
        $savingContribution->load('savingTarget');

        abort_unless($savingContribution->savingTarget->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'saving_target_id' => 'required|exists:saving_targets,id',
            'account_transfer_id' => 'required|exists:account_transfers,id',
            'contribution_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:in,out',
            'note' => 'nullable|string',
        ]);

        $newTarget = SavingTarget::where('id', $validated['saving_target_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $transfer = AccountTransfer::with(['fromAccount', 'toAccount'])
            ->where('id', $validated['account_transfer_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $amount = (float) $validated['amount'];

        $usedAmount = (float) SavingContribution::where('account_transfer_id', $transfer->id)
            ->where('id', '!=', $savingContribution->id)
            ->sum('amount');

        $remainingAmount = (float) $transfer->amount - $usedAmount;

        if ($amount > $remainingAmount) {
            throw ValidationException::withMessages([
                'amount' => 'Nominal kontribusi melebihi sisa transfer yang tersedia. Sisa yang bisa dipakai: Rp ' . number_format($remainingAmount, 0, ',', '.'),
            ]);
        }

        DB::transaction(function () use ($savingContribution, $validated, $newTarget, $transfer, $amount) {
            $oldTarget = SavingTarget::lockForUpdate()->findOrFail($savingContribution->saving_target_id);

            if ($savingContribution->type === 'in') {
                if ((float) $oldTarget->current_amount < (float) $savingContribution->amount) {
                    throw ValidationException::withMessages([
                        'amount' => 'Rollback gagal karena saldo target lama tidak cukup.',
                    ]);
                }

                $oldTarget->decrement('current_amount', (float) $savingContribution->amount);
            } else {
                $oldTarget->increment('current_amount', (float) $savingContribution->amount);
            }

            $lockedNewTarget = SavingTarget::lockForUpdate()->findOrFail($newTarget->id);

            if ($validated['type'] === 'out' && (float) $lockedNewTarget->current_amount < $amount) {
                throw ValidationException::withMessages([
                    'amount' => 'Saldo target tidak cukup untuk ditarik.',
                ]);
            }

            if ($validated['type'] === 'in') {
                $lockedNewTarget->increment('current_amount', $amount);
            } else {
                $lockedNewTarget->decrement('current_amount', $amount);
            }

            $savingContribution->update([
                'saving_target_id' => $lockedNewTarget->id,
                'account_transfer_id' => $transfer->id,
                'financial_account_id' => $transfer->to_financial_account_id,
                'contribution_date' => $validated['contribution_date'],
                'amount' => $amount,
                'type' => $validated['type'],
                'note' => $validated['note'] ?? null,
            ]);
        });

        return redirect()->route('saving-contributions.index')
            ->with('success', 'Kontribusi berhasil diperbarui.');
    }

    public function destroy(SavingContribution $savingContribution)
    {
        $savingContribution->load('savingTarget');

        abort_unless($savingContribution->savingTarget->user_id === Auth::id(), 403);

        DB::transaction(function () use ($savingContribution) {
            $target = SavingTarget::lockForUpdate()->findOrFail($savingContribution->saving_target_id);
            $amount = (float) $savingContribution->amount;

            if ($savingContribution->type === 'in') {
                if ((float) $target->current_amount < $amount) {
                    throw ValidationException::withMessages([
                        'amount' => 'Saldo target tidak cukup untuk rollback.',
                    ]);
                }

                $target->decrement('current_amount', $amount);
            } else {
                $target->increment('current_amount', $amount);
            }

            $savingContribution->delete();
        });

        return redirect()->route('saving-contributions.index')
            ->with('success', 'Kontribusi berhasil dihapus.');
    }
}