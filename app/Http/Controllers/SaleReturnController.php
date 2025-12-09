<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\SaleReturn;
use Illuminate\Http\Request;

class SaleReturnController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $search = $request->input('search'); // optional search input

        // Initialize empty collection for first load
        $returns = collect();

        // Only fetch records if any filter is applied
        if ($fromDate || $toDate || $search) {
            $query = SaleReturn::with(['customer', 'product'])->latest();

            // Date range filter
            if ($fromDate && $toDate) {
                $query->whereBetween('created_at', [$fromDate, $toDate]);
            } elseif ($fromDate) {
                $query->whereDate('created_at', '>=', $fromDate);
            } elseif ($toDate) {
                $query->whereDate('created_at', '<=', $toDate);
            }

            // Optional search filter by customer or product name
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('customer', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    })
                        ->orWhereHas('product', function ($q2) use ($search) {
                            $q2->where('name', 'like', "%{$search}%");
                        });
                });
            }

            $returns = $query->paginate($perPage)->appends($request->all());
        }

        return view('sale_returns.index', compact('returns', 'fromDate', 'toDate', 'search'));
    }

    public function create()
    {
        $customers = Customer::select('id', 'name', 'balance')->get();
        $products = Product::select('id', 'name', 'quantity', 'price_per_unit', 'packing')->get();

        return view('sale_returns.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'product_id' => 'required',
            'qty_return' => 'required|numeric|min:1',
            'amount_deducted' => 'required|numeric|min:0',
        ]);

        $customer = Customer::findOrFail($request->customer_id);
        $newBalance = $customer->balance - $request->amount_deducted;
        $customer->update(['balance' => $newBalance]);

        SaleReturn::create([
            'customer_id' => $request->customer_id,
            'product_id' => $request->product_id,
            'packing' => $request->packing,
            'qty_return' => $request->qty_return,
            'amount_deducted' => $request->amount_deducted,
            'total_after_return' => $newBalance,
        ]);

        return redirect()->route('sale-returns.index')->with('success', 'Return added and balance updated.');
    }

    public function edit($id)
    {
        $saleReturn = SaleReturn::findOrFail($id);
        $customers = Customer::select('id', 'name', 'balance')->get();
        $products = Product::select('id', 'name', 'quantity', 'price_per_unit', 'packing')->get();

        return view('sale_returns.edit', compact('saleReturn', 'customers', 'products'));
    }

    public function update(Request $request, SaleReturn $sale_return)
    {
        $request->validate([
            'customer_id' => 'required',
            'product_id' => 'required',
            'qty_return' => 'required|numeric|min:1',
            'amount_deducted' => 'required|numeric|min:0',
        ]);

        $oldAmount = $sale_return->amount_deducted;
        $customer = Customer::findOrFail($request->customer_id);
        $adjustedBalance = $customer->balance + $oldAmount - $request->amount_deducted;

        $customer->update(['balance' => $adjustedBalance]);

        $sale_return->update([
            'customer_id' => $request->customer_id,
            'product_id' => $request->product_id,
            'packing' => $request->packing,
            'qty_return' => $request->qty_return,
            'amount_deducted' => $request->amount_deducted,
            'total_after_return' => $adjustedBalance,
        ]);

        return redirect()->route('sale-returns.index')->with('success', 'Sale Return updated successfully.');
    }

    public function destroy(SaleReturn $sale_return)
    {
        $customer = $sale_return->customer;
        $customer->update(['balance' => $customer->balance + $sale_return->amount_deducted]);

        $sale_return->delete();

        return redirect()->route('sale-returns.index')->with('success', 'Sale Return deleted and balance adjusted.');
    }
}
