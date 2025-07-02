<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 5); // Default to 5
        $expenses = Expense::orderBy('created_at', 'desc')->paginate($perPage);
        $subtotal = $expenses->sum('amount'); // Only current page

        return view('expenses.index', compact('expenses', 'subtotal'));
    }

    public function create()
    {
        return view('expenses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_name' => 'required',
            'description'  => 'nullable',
            'payment_type' => 'required',
            'amount'       => 'required|numeric|min:0',
        ]);

        Expense::create($request->all());

        return redirect()->route('expenses.index')->with('success', 'Expense created successfully.');
    }

    public function edit(Expense $expense)
    {
        return view('expenses.edit', compact('expense'));
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'expense_name' => 'required',
            'description'  => 'nullable',
            'payment_type' => 'required',
            'amount'       => 'required|numeric|min:0',
        ]);

        $expense->update($request->all());

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }
}
