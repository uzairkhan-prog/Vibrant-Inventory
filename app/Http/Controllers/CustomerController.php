<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);

        $customers = Customer::withSum('sales as total_sales', 'total_amount')
            ->withSum('payments as total_paid', 'amount')
            ->paginate($perPage);

        // Calculate Current Balance
        $customers->getCollection()->transform(function ($customer) {

            $sales = $customer->total_sales ?? 0;
            $paid  = $customer->total_paid ?? 0;

            $customer->current_balance = $sales - $paid;

            return $customer;
        });

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
        // Ledger
        $ledger = $this->buildCustomerLedger($customer);

        // Sales for payment dropdown
        $sales = Sale::where('customer_id', $customer->id)
            ->withSum('payments', 'amount')
            ->get();

        foreach ($sales as $sale) {
            $sale->remaining_amount = $sale->total_amount - ($sale->payments_sum_amount ?? 0);
        }

        // Current Balance = Last ledger balance
        $currentBalance = $ledger->last()['balance'] ?? 0;

        return view('customers.show', compact(
            'customer',
            'ledger',
            'sales',
            'currentBalance'
        ));
    }

    private function buildCustomerLedger(Customer $customer)
    {
        // Load relations
        $customer->load([
            'sales.items.product',
            'payments'
        ]);

        $ledger = collect();

        /*
        |------------------------------------------
        | 1) ADD SALES (DEBIT)  — ITEM WISE
        |------------------------------------------
        */
        foreach ($customer->sales as $sale) {

            foreach ($sale->items as $item) {

                $qty = $item->quantity;
                $unitPrice = $item->price;
                $discountPercent = $item->discount ?? 0;
                $taxPercent = $item->tax ?? 0;

                // Subtotal calculation per item
                $subtotal = ($qty * $unitPrice);
                $subtotal -= $subtotal * ($discountPercent / 100); // apply discount %
                $subtotal += $subtotal * ($taxPercent / 100); // apply tax %

                // Combine sale date + created_at time
                if ($sale->date) {
                    // Use sale date with created_at time
                    $saleTime = Carbon::parse($sale->created_at)->format('H:i:s');
                    $saleDate = Carbon::parse($sale->date . ' ' . $saleTime);
                } else {
                    $saleDate = Carbon::parse($sale->created_at);
                }

                $ledger->push([
                    'date'        => $saleDate->format('Y-m-d H:i:s'),
                    'type'        => 'sale',
                    'reference'   => 'Invoice #' . $sale->id,
                    'product'     => $item->product->name ?? '-',
                    'qty'         => $qty,
                    'price'       => $unitPrice,
                    'discount'    => $discountPercent,
                    'tax'         => $taxPercent,
                    'debit'       => round($subtotal, 2),
                    'credit'      => 0,
                ]);
            }
        }

        /*
        |------------------------------------------
        | 2) ADD PAYMENTS (CREDIT)
        |------------------------------------------
        */
        foreach ($customer->payments as $payment) {

            $paymentDate = $payment->date ? Carbon::parse($payment->date) : $payment->created_at;

            $ledger->push([
                'date'        => $paymentDate->format('Y-m-d H:i:s'),
                'type'        => 'payment',
                'reference'   => 'Receipt #' . $payment->id,
                'product'     => '-',
                'qty'         => '-',
                'price'       => '-',
                'discount'    => 0,
                'tax'         => 0,
                'debit'       => 0,
                'credit'      => (float) $payment->amount,
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
        */
        $running = 0;

        $ledger = $ledger->map(function ($row) use (&$running) {
            $running += $row['debit'];
            $running -= $row['credit'];
            $row['balance'] = round($running, 2);
            return $row;
        });

        return $ledger;
    }

    public function storePayment(Request $request, Customer $customer)
    {
        $request->validate([
            'description'  => 'nullable|string',
            'payment_type' => 'required|string',
            'amount'       => 'required|numeric|min:0.01',
            'sale_id'      => 'nullable|exists:sales,id',
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
            'sale_id'      => $request->sale_id,
            'description'  => $request->description,
            'payment_type' => $request->payment_type,
            'amount'       => $request->amount,
        ]);

        return redirect()->route('customers.show', $customer)->with('success', 'Payment recorded successfully.');
    }

    public function outstanding(Request $request)
    {
        $perPage = $request->get('per_page', 20);

        $customers = Customer::withSum(['sales as total_sales' => function ($q) {
            $q->select(DB::raw("COALESCE(SUM(total_amount),0)"));
        }], 'total_amount')
            ->withSum(['payments as total_paid' => function ($q) {
                $q->select(DB::raw("COALESCE(SUM(amount),0)"));
            }], 'amount')
            ->get()
            ->map(function ($customer) {

                $sales = $customer->total_sales ?? 0;
                $paid  = $customer->total_paid ?? 0;

                $customer->outstanding = $sales - $paid;

                return $customer;
            })
            ->filter(function ($customer) {
                return $customer->outstanding > 0;
            });

        /*
        |--------------------------------------------------------------------------
        | SORTING LOGIC
        |--------------------------------------------------------------------------
        | 1) Counter Sale always first
        | 2) Others by highest outstanding
        */
        $customers = $customers->sort(function ($a, $b) {

            // Counter Sale priority
            if (strtolower($a->name) == 'counter sale') return -1;
            if (strtolower($b->name) == 'counter sale') return 1;

            // then sort by outstanding (descending)
            return $b->outstanding <=> $a->outstanding;
        })->values();


        // Total Outstanding
        $totalOutstanding = $customers->sum('outstanding');

        // Manual Pagination
        $page = request()->get('page', 1);

        $customers = new \Illuminate\Pagination\LengthAwarePaginator(
            $customers->forPage($page, $perPage),
            $customers->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('customers.outstanding', compact('customers', 'totalOutstanding'));
    }
}
