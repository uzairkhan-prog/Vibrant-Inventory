<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\Purchase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);

        // pagination data
        $suppliers = Supplier::orderBy('name')->paginate($perPage);

        // FULL ledger total (not page total)
        $totalSupplierBalance = Supplier::sum('balance');

        return view('suppliers.index', compact('suppliers', 'totalSupplierBalance'));
    }

    public function details(Request $request)
    {
        $perPage = $request->get('per_page', 20);
        $suppliers = Supplier::paginate($perPage);
        return view('suppliers.details', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
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

        Supplier::create($request->all());

        return redirect()->route('suppliers.details')
            ->with('success', 'Supplier created successfully.');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name'    => 'required',
            'company_name' => 'nullable',
            'phone'   => 'nullable',
            'email'   => 'nullable|email',
            'address' => 'nullable',
            'balance' => 'nullable|numeric',
        ]);

        $supplier->update($request->all());

        return redirect()->route('suppliers.details')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('suppliers.details')
            ->with('success', 'Supplier deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW SUPPLIER (Same as Customer Show Scenario)
    |--------------------------------------------------------------------------
    */

    public function show(Supplier $supplier)
    {
        $ledger = $this->buildSupplierLedger($supplier);

        // Purchases for dropdown (same as sales in customer)
        $purchases = Purchase::where('supplier_id', $supplier->id)
            ->withSum('payments', 'amount')
            ->get();

        foreach ($purchases as $purchase) {
            $purchase->remaining_amount =
                $purchase->total_amount - ($purchase->payments_sum_amount ?? 0);
        }

        $currentBalance = $ledger->last()['balance'] ?? 0;

        return view('suppliers.show', compact(
            'supplier',
            'ledger',
            'purchases',
            'currentBalance'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | SUPPLIER LEDGER (Accounts Payable Logic)
    |--------------------------------------------------------------------------
    */

    private function buildSupplierLedger(Supplier $supplier)
    {
        $supplier->load([
            'purchases.items.product',
            'payments'
        ]);

        $ledger = collect();

        /*
        |------------------------------------------
        | 1) ADD PURCHASES (DEBIT - You Owe Supplier)
        |------------------------------------------
        */
        foreach ($supplier->purchases as $purchase) {

            foreach ($purchase->items as $item) {

                $qty = $item->quantity;
                $unitPrice = $item->price;
                $discountPercent = $item->discount ?? 0;
                $taxPercent = $item->tax ?? 0;

                $subtotal = ($qty * $unitPrice);
                $subtotal -= $subtotal * ($discountPercent / 100);
                $subtotal += $subtotal * ($taxPercent / 100);

                if ($purchase->date) {
                    $time = Carbon::parse($purchase->created_at)->format('H:i:s');
                    $date = Carbon::parse($purchase->date . ' ' . $time);
                } else {
                    $date = Carbon::parse($purchase->created_at);
                }

                $ledger->push([
                    'date'      => $date->format('Y-m-d H:i:s'),
                    'type'      => 'purchase',
                    'reference' => 'Purchase #' . $purchase->id,
                    'product'   => $item->product->name ?? '-',
                    'qty'       => $qty,
                    'price'     => $unitPrice,
                    'discount'  => $discountPercent,
                    'tax'       => $taxPercent,
                    'debit'     => round($subtotal, 2), // DEBIT = liability increase
                    'credit'    => 0,
                ]);
            }
        }

        /*
        |------------------------------------------
        | 2) ADD PAYMENTS (CREDIT - You Paid Supplier)
        |------------------------------------------
        */
        foreach ($supplier->payments as $payment) {

            $paymentDate = $payment->date
                ? Carbon::parse($payment->date)
                : $payment->created_at;

            $ledger->push([
                'date'      => $paymentDate->format('Y-m-d H:i:s'),
                'type'      => 'payment',
                'reference' => 'Payment #' . $payment->id,
                'product'   => '-',
                'qty'       => '-',
                'price'     => '-',
                'discount'  => 0,
                'tax'       => 0,
                'debit'     => 0,
                'credit'    => (float) $payment->amount, // CREDIT reduces liability
            ]);
        }

        /*
        |------------------------------------------
        | 3) SORT BY DATE
        |------------------------------------------
        */
        $ledger = $ledger->sortBy('date')->values();

        /*
        |------------------------------------------
        | 4) RUNNING BALANCE
        |------------------------------------------
        | Same structure as Customer
        |------------------------------------------
        */
        $running = 0;

        $ledger = $ledger->map(function ($row) use (&$running) {
            $running += $row['debit'];   // purchases increase what you owe
            $running -= $row['credit'];  // payments decrease what you owe
            $row['balance'] = round($running, 2);
            return $row;
        });

        return $ledger;
    }

    /*
    |--------------------------------------------------------------------------
    | STORE PAYMENT (Same Validation Logic as Customer)
    |--------------------------------------------------------------------------
    */

    public function storePayment(Request $request, Supplier $supplier)
    {
        $request->validate([
            'description'  => 'nullable|string',
            'payment_type' => 'required|string',
            'amount'       => 'required|numeric|min:0.01',
            'purchase_id'  => 'nullable|exists:purchases,id',
        ]);

        // Recalculate current balance properly (not from DB column)
        $ledger = $this->buildSupplierLedger($supplier);
        $currentBalance = $ledger->last()['balance'] ?? 0;

        if ($currentBalance <= 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Payment not allowed. Supplier balance is zero.');
        }

        if ($request->amount > $currentBalance) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Payment amount cannot be greater than current balance (Rs '
                    . number_format($currentBalance, 2) . ').');
        }

        SupplierPayment::create([
            'supplier_id'  => $supplier->id,
            'purchase_id'  => $request->purchase_id,
            'description'  => $request->description,
            'payment_type' => $request->payment_type,
            'amount'       => $request->amount,
        ]);

        return redirect()->route('suppliers.show', $supplier)
            ->with('success', 'Payment recorded successfully.');
    }

    public function outstanding(Request $request)
    {
        $perPage = $request->get('per_page', 20);

        $suppliers = Supplier::withSum(['purchases as total_purchases' => function ($q) {
            $q->select(DB::raw("COALESCE(SUM(total_amount),0)"));
        }], 'total_amount')
            ->withSum(['payments as total_paid' => function ($q) {
                $q->select(DB::raw("COALESCE(SUM(amount),0)"));
            }], 'amount')
            ->get()
            ->map(function ($supplier) {

                $purchases = $supplier->total_purchases ?? 0;
                $paid  = $supplier->total_paid ?? 0;

                $supplier->outstanding = $purchases - $paid;

                return $supplier;
            })
            ->filter(function ($supplier) {
                return $supplier->outstanding > 0;
            });

        /*
        |--------------------------------------------------------------------------
        | SORTING LOGIC
        |--------------------------------------------------------------------------
        | 1) Counter Sale always first
        | 2) Others by highest outstanding
        */
        $suppliers = $suppliers->sort(function ($a, $b) {

            // Counter Sale priority
            if (strtolower($a->name) == 'counter sale') return -1;
            if (strtolower($b->name) == 'counter sale') return 1;

            // then sort by outstanding (descending)
            return $b->outstanding <=> $a->outstanding;
        })->values();


        // Total Outstanding
        $totalOutstanding = $suppliers->sum('outstanding');

        // Manual Pagination
        $page = request()->get('page', 1);

        $suppliers = new \Illuminate\Pagination\LengthAwarePaginator(
            $suppliers->forPage($page, $perPage),
            $suppliers->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('suppliers.outstanding', compact('suppliers', 'totalOutstanding'));
    }
}
