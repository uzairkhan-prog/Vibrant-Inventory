<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseName;
use App\Models\PaymentType;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);

        $query = Expense::with('paymentType', 'expenseName')->orderBy('created_at', 'desc');

        // Date range filter
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        if ($fromDate && $toDate) {
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        } elseif ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        $expenses = $query->paginate($perPage)->appends($request->all());
        $subtotal = $expenses->sum('amount'); // Sum only current page

        return view('expenses.index', compact('expenses', 'subtotal', 'fromDate', 'toDate'));
    }

    public function create()
    {
        $expenseNames = ExpenseName::all();
        $paymentTypes = PaymentType::all();
        return view('expenses.create', compact('paymentTypes', 'expenseNames'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_name_id'    => 'required|string|max:255',
            'payment_type_id' => 'required|exists:payment_types,id',
            'amount'          => 'required|numeric|min:0',
            'description'     => 'nullable|string',
        ]);

        Expense::create([
            'expense_name_id'    => $request->expense_name_id,
            'payment_type_id' => $request->payment_type_id,
            'amount'          => $request->amount,
            'description'     => $request->description,
        ]);

        return redirect()->route('expenses.index')->with('success', 'Expense created successfully.');
    }

    public function edit(Expense $expense)
    {
        $expenseNames = ExpenseName::all();
        $paymentTypes = PaymentType::all();
        return view('expenses.edit', compact('expense', 'paymentTypes', 'expenseNames'));
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'expense_name_id'    => 'required|string|max:255',
            'payment_type_id' => 'required|exists:payment_types,id',
            'amount'          => 'required|numeric|min:0',
            'description'     => 'nullable|string',
        ]);

        $expense->update([
            'expense_name_id'    => $request->expense_name_id,
            'payment_type_id' => $request->payment_type_id,
            'amount'          => $request->amount,
            'description'     => $request->description,
        ]);

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }
}
