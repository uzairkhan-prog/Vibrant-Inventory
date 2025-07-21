<?php

namespace App\Http\Controllers;

use App\Models\PaymentType;
use Illuminate\Http\Request;

class PaymentTypeController extends Controller
{
    /**
     * Display a listing of the payment types.
     */
    public function index()
    {
        $paymentTypes = PaymentType::latest()->get();
        return view('expenses.payment-types.index', compact('paymentTypes'));
    }

    /**
     * Show the form for creating a new payment type.
     */
    public function create()
    {
        return view('expenses.payment-types.create');
    }

    /**
     * Store a newly created payment type in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:payment_types,name'
        ]);

        PaymentType::create($request->only('name'));

        return redirect()->route('payment-types.index')->with('success', 'Payment type created successfully.');
    }

    /**
     * Show the form for editing the specified payment type.
     */
    public function edit(PaymentType $paymentType)
    {
        return view('expenses.payment-types.edit', compact('paymentType'));
    }

    /**
     * Update the specified payment type in storage.
     */
    public function update(Request $request, PaymentType $paymentType)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:payment_types,name,' . $paymentType->id
        ]);

        $paymentType->update($request->only('name'));

        return redirect()->route('payment-types.index')->with('success', 'Payment type updated successfully.');
    }

    /**
     * Remove the specified payment type from storage.
     */
    public function destroy(PaymentType $paymentType)
    {
        $paymentType->delete();

        return redirect()->route('payment-types.index')->with('success', 'Payment type deleted successfully.');
    }
}
