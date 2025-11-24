<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerPayment;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20); // default 20
        $customers = Customer::paginate($perPage);
        return view('customers.index', compact('customers'));
    }

    public function details(Request $request)
    {
        $perPage = $request->get('per_page', 20); // default 20
        $customers = Customer::paginate($perPage);
        return view('customers.details', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required',
            'company_name' => 'nullable',
            'phone'   => 'nullable',
            'email'   => 'nullable|email',
            'address' => 'nullable',
            'balance' => 'nullable|numeric',
        ]);

        Customer::create($request->all());

        return redirect()->route('customers.details')->with('success', 'Customer created successfully.');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name'    => 'required',
            'company_name' => 'nullable',
            'phone'   => 'nullable',
            'email'   => 'nullable|email',
            'address' => 'nullable',
            'balance' => 'nullable|numeric',
        ]);

        $customer->update($request->all());

        return redirect()->route('customers.details')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.details')->with('success', 'Customer deleted successfully.');
    }

    public function show(Customer $customer)
    {
        $customer->load('payments');
        $currentBalance = $customer->current_balance;

        return view('customers.show', compact('customer', 'currentBalance'));
    }

    public function storePayment(Request $request, Customer $customer)
    {
        $request->validate([
            'description'  => 'nullable|string',
            'payment_type' => 'required|string',
            'amount'       => 'required|numeric|min:0.01',
        ]);

        $currentBalance = $customer->current_balance;

        if ($currentBalance <= 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Payment not allowed. Customer balance is zero.');
        }

        if ($request->amount > $currentBalance) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Payment amount cannot be greater than current balance (Rs ' . number_format($currentBalance, 2) . ').');
        }

        CustomerPayment::create([
            'customer_id'  => $customer->id,
            'description'  => $request->description,
            'payment_type' => $request->payment_type,
            'amount'       => $request->amount,
        ]);

        return redirect()->route('customers.show', $customer)->with('success', 'Payment recorded successfully.');
    }
}
