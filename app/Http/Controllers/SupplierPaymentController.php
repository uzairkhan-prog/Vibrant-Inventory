<?php

namespace App\Http\Controllers;

use App\Models\SupplierPayment;
use Illuminate\Http\Request;

class SupplierPaymentController extends Controller
{
    public function edit(SupplierPayment $payment)
    {
        return view('supplier_payments.edit', compact('payment'));
    }

    public function update(Request $request, SupplierPayment $payment)
    {
        $request->validate([
            'description'  => 'nullable|string',
            'payment_type' => 'required|string',
            'amount'       => 'required|numeric|min:0.01',
        ]);

        $payment->update($request->only('description', 'payment_type', 'amount'));

        return redirect()->route('suppliers.show', $payment->supplier_id)->with('success', 'Payment updated successfully.');
    }

    public function destroy(SupplierPayment $payment)
    {
        $supplierId = $payment->supplier_id;
        $payment->delete();

        return redirect()->route('suppliers.show', $supplierId)->with('success', 'Payment deleted successfully.');
    }
}
