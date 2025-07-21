<?php

namespace App\Http\Controllers;

use App\Models\ExpenseName;
use Illuminate\Http\Request;

class ExpenseNameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $expenseNames = ExpenseName::latest()->get();
        return view('expenses.expense-name.index', compact('expenseNames'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('expenses.expense-name.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:payment_types,name'
        ]);

        ExpenseName::create($request->only('name'));

        return redirect()->route('expense-name.index')->with('success', 'Expense Name type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ExpenseName $expenseName)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExpenseName $expenseName)
    {
        return view('expenses.expense-name.edit', compact('expenseName'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExpenseName $expenseName)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:payment_types,name,' . $expenseName->id
        ]);

        $expenseName->update($request->only('name'));

        return redirect()->route('expense-name.index')->with('success', 'Expense Name type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExpenseName $expenseName)
    {
        $expenseName->delete();

        return redirect()->route('expense-name.index')->with('success', 'Expense Name type deleted successfully.');
    }
}
