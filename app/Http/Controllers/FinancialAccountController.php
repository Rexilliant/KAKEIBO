<?php

namespace App\Http\Controllers;

use App\Models\FinancialAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinancialAccountController extends Controller
{
    public function index()
    {
        $accounts = FinancialAccount::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('financial-accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('financial-accounts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:bank,e_wallet,cash,other',
            'provider' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'balance' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['balance'] = $validated['balance'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active');

        FinancialAccount::create($validated);

        return redirect()->route('financial-accounts.index')
            ->with('success', 'Rekening berhasil ditambahkan.');
    }

    public function show(FinancialAccount $financialAccount)
    {
        abort_unless($financialAccount->user_id === Auth::id(), 403);

        return view('financial-accounts.show', compact('financialAccount'));
    }

    public function edit(FinancialAccount $financialAccount)
    {
        abort_unless($financialAccount->user_id === Auth::id(), 403);

        return view('financial-accounts.edit', compact('financialAccount'));
    }

    public function update(Request $request, FinancialAccount $financialAccount)
    {
        abort_unless($financialAccount->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:bank,e_wallet,cash,other',
            'provider' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'balance' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['balance'] = $validated['balance'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active');

        $financialAccount->update($validated);

        return redirect()->route('financial-accounts.index')
            ->with('success', 'Rekening berhasil diperbarui.');
    }

    public function destroy(FinancialAccount $financialAccount)
    {
        abort_unless($financialAccount->user_id === Auth::id(), 403);

        $financialAccount->delete();

        return redirect()->route('financial-accounts.index')
            ->with('success', 'Rekening berhasil dihapus.');
    }
}