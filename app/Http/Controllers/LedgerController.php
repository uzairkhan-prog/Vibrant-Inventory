<?php

namespace App\Http\Controllers;

use App\Models\Ledger;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $perPage = $request->get('per_page', 20); // Default 5 entries per page

        // Query with search filter on related Customer/Supplier name
        $ledgers = Ledger::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    // Search by related customer or supplier name
                    $q->whereHas('customer', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%$search%");
                    })
                        ->orWhereHas('supplier', function ($q2) use ($search) {
                            $q2->where('name', 'like', "%$search%");
                        });
                });
            })
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage)
            ->appends(['search' => $search, 'per_page' => $perPage]);

        // Calculate total balance (all ledgers)
        $total_balance = Ledger::sum('balance');

        return view('ledgers.index', compact('ledgers', 'total_balance', 'search', 'perPage'));
    }

    public function create()
    {
        $customers = Customer::all();
        $suppliers = Supplier::all();
        return view('ledgers.create', compact('customers', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'reference_id' => 'required|integer',
            'type'         => 'required|in:customer,supplier',
            'balance'      => 'required|numeric',
        ]);

        Ledger::create($request->all());

        return redirect()->route('ledgers.index')->with('success', 'Ledger entry created successfully.');
    }

    public function edit(Ledger $ledger)
    {
        $customers = Customer::all();
        $suppliers = Supplier::all();
        return view('ledgers.edit', compact('ledger', 'customers', 'suppliers'));
    }

    public function update(Request $request, Ledger $ledger)
    {
        $request->validate([
            'reference_id' => 'required|integer',
            'type'         => 'required|in:customer,supplier',
            'balance'      => 'required|numeric',
        ]);

        $ledger->update($request->all());

        return redirect()->route('ledgers.index')->with('success', 'Ledger entry updated successfully.');
    }

    public function destroy(Ledger $ledger)
    {
        $ledger->delete();

        return redirect()->route('ledgers.index')->with('success', 'Ledger entry deleted successfully.');
    }
}
