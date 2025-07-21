<?php

namespace App\Http\Controllers;

use App\Models\Ledger;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierLedgerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $perPage = $request->get('per_page', 20);

        $ledgers = Ledger::where('type', 'supplier')
            ->when($search, function ($query, $search) {
                $query->whereHas('supplier', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%$search%");
                });
            })
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage)
            ->appends(['search' => $search, 'per_page' => $perPage]);

        $total_balance = Ledger::where('type', 'supplier')->sum('balance');

        return view('ledgers.supplier-index', compact('ledgers', 'total_balance', 'search', 'perPage'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        return view('ledgers.supplier-create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'reference_id' => 'required|integer',
            'balance'      => 'required|numeric',
        ]);

        Ledger::create([
            'type' => 'supplier',
            'reference_id' => $request->reference_id,
            'balance' => $request->balance,
        ]);

        return redirect()->route('supplier-ledgers.index')->with('success', 'Supplier Ledger created.');
    }

    public function edit(Ledger $supplier_ledger)
    {
        $suppliers = Supplier::all();
        return view('ledgers.supplier-edit', ['ledger' => $supplier_ledger, 'suppliers' => $suppliers]);
    }

    public function update(Request $request, Ledger $supplier_ledger)
    {
        $request->validate([
            'reference_id' => 'required|integer',
            'balance'      => 'required|numeric',
        ]);

        $supplier_ledger->update([
            'reference_id' => $request->reference_id,
            'balance' => $request->balance,
        ]);

        return redirect()->route('supplier-ledgers.index')->with('success', 'Supplier Ledger updated.');
    }

    public function destroy(Ledger $supplier_ledger)
    {
        $supplier_ledger->delete();
        return redirect()->route('supplier-ledgers.index')->with('success', 'Supplier Ledger deleted.');
    }
}
