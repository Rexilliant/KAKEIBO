<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinancialAccount;
use Illuminate\Support\Facades\Auth;

class FinancialAccountLedgerController extends Controller
{
    public function index(Request $request, FinancialAccount $financialAccount)
    {
        abort_unless($financialAccount->user_id === Auth::id(), 403);

        $movements = $financialAccount->movements()
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('type', $request->type);
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->date_to);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('financial-accounts.ledger', compact('financialAccount', 'movements'));
    }
}