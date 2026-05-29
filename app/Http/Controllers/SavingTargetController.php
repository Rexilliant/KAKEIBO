<?php

namespace App\Http\Controllers;

use App\Models\SavingTarget;
use App\Models\FinancialAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SavingTargetController extends Controller
{
    public function index()
    {
        $savingTargets = SavingTarget::with('financialAccount')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('saving-targets.index', compact('savingTargets'));
    }

    public function create()
    {
        $accounts = FinancialAccount::where('user_id', Auth::id())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('saving-targets.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'financial_account_id' => 'nullable|exists:financial_accounts,id',
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0',
            'current_amount' => 'nullable|numeric|min:0',
            'deadline' => 'nullable|date',
            'note' => 'nullable|string',
            'status' => 'required|in:active,completed,cancelled',
        ]);

        if (!empty($validated['financial_account_id'])) {
            FinancialAccount::where('id', $validated['financial_account_id'])
                ->where('user_id', Auth::id())
                ->firstOrFail();
        }

        $validated['user_id'] = Auth::id();
        $validated['current_amount'] = $validated['current_amount'] ?? 0;

        SavingTarget::create($validated);

        return redirect()->route('saving-targets.index')
            ->with('success', 'Target tabungan berhasil ditambahkan.');
    }

    public function show(SavingTarget $savingTarget)
    {
        abort_unless($savingTarget->user_id === Auth::id(), 403);

        $savingTarget->load(['financialAccount', 'contributions']);

        return view('saving-targets.show', compact('savingTarget'));
    }

    public function edit(SavingTarget $savingTarget)
    {
        abort_unless($savingTarget->user_id === Auth::id(), 403);

        $accounts = FinancialAccount::where('user_id', Auth::id())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('saving-targets.edit', compact('savingTarget', 'accounts'));
    }

    public function update(Request $request, SavingTarget $savingTarget)
    {
        abort_unless($savingTarget->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'financial_account_id' => 'nullable|exists:financial_accounts,id',
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0',
            'deadline' => 'nullable|date',
            'note' => 'nullable|string',
            'status' => 'required|in:active,completed,cancelled',
        ]);

        if (!empty($validated['financial_account_id'])) {
            FinancialAccount::where('id', $validated['financial_account_id'])
                ->where('user_id', Auth::id())
                ->firstOrFail();
        }

        $savingTarget->update($validated);

        return redirect()->route('saving-targets.index')
            ->with('success', 'Target tabungan berhasil diperbarui.');
    }

    public function destroy(SavingTarget $savingTarget)
    {
        abort_unless($savingTarget->user_id === Auth::id(), 403);

        $savingTarget->delete();

        return redirect()->route('saving-targets.index')
            ->with('success', 'Target tabungan berhasil dihapus.');
    }
}