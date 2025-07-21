<?php

namespace App\Http\Controllers;

use App\Models\Ledger;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerLedgerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $perPage = $request->get('per_page', 20);

        $ledgers = Ledger::where('type', 'customer')
            ->when($search, function ($query, $search) {
                $query->whereHas('customer', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%$search%");
                });
            })
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage)
            ->appends(['search' => $search, 'per_page' => $perPage]);

        $total_balance = Ledger::where('type', 'customer')->sum('balance');

        return view('ledgers.customer-index', compact('ledgers', 'total_balance', 'search', 'perPage'));
    }

    public function create()
    {
        $customers = Customer::all();
        return view('ledgers.customer-create', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'reference_id' => 'required|integer',
            'balance'      => 'required|numeric',
        ]);

        Ledger::create([
            'type' => 'customer',
            'reference_id' => $request->reference_id,
            'balance' => $request->balance,
        ]);

        return redirect()->route('customer-ledgers.index')->with('success', 'Customer Ledger created.');
    }

    public function edit(Ledger $customer_ledger)
    {
        $customers = Customer::all();
        return view('ledgers.customer-edit', ['ledger' => $customer_ledger, 'customers' => $customers]);
    }

    public function update(Request $request, Ledger $customer_ledger)
    {
        $request->validate([
            'reference_id' => 'required|integer',
            'balance'      => 'required|numeric',
        ]);

        $customer_ledger->update([
            'reference_id' => $request->reference_id,
            'balance' => $request->balance,
        ]);

        return redirect()->route('customer-ledgers.index')->with('success', 'Customer Ledger updated.');
    }

    public function destroy(Ledger $customer_ledger)
    {
        $customer_ledger->delete();
        return redirect()->route('customer-ledgers.index')->with('success', 'Customer Ledger deleted.');
    }
}
