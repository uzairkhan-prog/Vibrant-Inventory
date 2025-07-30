<?php

namespace App\Http\Controllers;

use App\Models\CustomerPayment;
use Illuminate\Http\Request;

class CustomerPaymentController extends Controller
{
    public function edit(CustomerPayment $payment)
    {
        return view('customer_payments.edit', compact('payment'));
    }

    public function update(Request $request, CustomerPayment $payment)
    {
        $request->validate([
            'description'  => 'nullable|string',
            'payment_type' => 'required|string',
            'amount'       => 'required|numeric|min:0.01',
        ]);

        $payment->update($request->only('description', 'payment_type', 'amount'));

        return redirect()->route('customers.show', $payment->customer_id)->with('success', 'Payment updated successfully.');
    }

    public function destroy(CustomerPayment $payment)
    {
        $customerId = $payment->customer_id;
        $payment->delete();

        return redirect()->route('customers.show', $customerId)->with('success', 'Payment deleted successfully.');
    }
}
