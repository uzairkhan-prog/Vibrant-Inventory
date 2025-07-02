<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierPayment;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 5); // default 5
        $suppliers = Supplier::paginate($perPage);
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required',
            'phone'   => 'nullable',
            'email'   => 'nullable|email',
            'address' => 'nullable',
            'balance' => 'nullable|numeric',
        ]);

        Supplier::create($request->all());

        return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name'    => 'required',
            'phone'   => 'nullable',
            'email'   => 'nullable|email',
            'address' => 'nullable',
            'balance' => 'nullable|numeric',
        ]);

        $supplier->update($request->all());

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }


    public function show(Supplier $supplier)
    {
        $supplier->load('payments');
        $currentBalance = $supplier->current_balance;

        return view('suppliers.show', compact('supplier', 'currentBalance'));
    }

    public function storePayment(Request $request, Supplier $supplier)
    {
        $request->validate([
            'description'  => 'nullable|string',
            'payment_type' => 'required|string',
            'amount'       => 'required|numeric|min:0.01',
        ]);

        // Create payment record
        SupplierPayment::create([
            'supplier_id'  => $supplier->id,
            'description'  => $request->description,
            'payment_type' => $request->payment_type,
            'amount'       => $request->amount,
        ]);

        // Optionally, update balance if you want to track real-time balance on Supplier model (recommended to keep initial balance fixed)

        return redirect()->route('suppliers.show', $supplier)->with('success', 'Payment recorded successfully.');
    }
}
