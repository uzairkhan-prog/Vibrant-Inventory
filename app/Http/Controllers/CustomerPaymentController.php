<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerPayment;
use Illuminate\Http\Request;

class CustomerPaymentController extends Controller
{

    public function store(Request $request, Customer $customer)
    {
        $request->validate([
            'date' => 'required|date',
            'description' => 'nullable|string',
            'payment_mode' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
        ]);

        CustomerPayment::create([
            'customer_id' => $customer->id,
            'date' => $request->date,
            'description' => $request->description,
            'payment_mode' => $request->payment_mode,
            'amount' => $request->amount,
        ]);

        $customer->balance -= $request->amount;
        $customer->save();

        return back()->with('success', 'Payment recorded successfully.');
    }

    public function edit(CustomerPayment $payment)
    {
        return view('customers.payments.edit', compact('payment'));
    }

    public function update(Request $request, CustomerPayment $payment)
    {
        $request->validate([
            'date' => 'required|date',
            'description' => 'nullable|string',
            'payment_mode' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $originalAmount = $payment->amount;

        $payment->update($request->all());

        $difference = $originalAmount - $payment->amount;

        $payment->customer->balance += $difference;
        $payment->customer->save();

        return redirect()->route('customers.show', $payment->customer)->with('success', 'Payment updated.');
    }

    public function destroy(CustomerPayment $payment)
    {
        $customer = $payment->customer;
        $customer->balance += $payment->amount;
        $customer->save();

        $payment->delete();

        return back()->with('success', 'Payment deleted.');
    }
}
