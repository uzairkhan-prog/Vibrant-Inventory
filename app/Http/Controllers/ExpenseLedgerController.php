<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseName;
use Illuminate\Http\Request;

class ExpenseLedgerController extends Controller
{
    public function index(Request $request)
    {
        $expenseNames = ExpenseName::orderBy('name')->get();

        $expenseNameId = $request->expense_name_id;
        $fromDate      = $request->from_date;
        $toDate        = $request->to_date;

        $query = Expense::with('paymentType', 'expenseName')
            ->when($expenseNameId, function ($q) use ($expenseNameId) {
                $q->where('expense_name_id', $expenseNameId);
            })
            ->when($fromDate && $toDate, function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('created_at', [
                    \Carbon\Carbon::parse($fromDate)->startOfDay(),
                    \Carbon\Carbon::parse($toDate)->endOfDay(),
                ]);
            })
            ->orderBy('created_at')
            ->orderBy('id');

        $ledger = $query->get();

        $balance = 0;

        $ledger = $ledger->map(function ($expense) use (&$balance) {
            $balance += $expense->amount;

            return [
                'date'         => $expense->created_at,
                'expense_name' => $expense->expenseName->name ?? '-',
                'description'  => $expense->description,
                'payment_type' => $expense->paymentType->name ?? '-',
                'amount'       => $expense->amount,
                'balance'      => $balance,
            ];
        });

        $grandTotal = $ledger->sum('amount');

        return view('ledger.expenses', compact(
            'expenseNames',
            'expenseNameId',
            'ledger',
            'grandTotal',
            'fromDate',
            'toDate'
        ));
    }
}
