<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\Sale;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20); // default 20
        $customers = Customer::paginate($perPage);
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
        $customer->load('payments');

        // Fetch this customer's sales
        $sales = Sale::where('customer_id', $customer->id)
            ->withSum('payments', 'amount')
            ->get();

        // Calculate remaining amount
        foreach ($sales as $sale) {
            $sale->remaining_amount = $sale->total_amount - ($sale->payments_sum_amount ?? 0);
        }

        $currentBalance = $customer->current_balance;

        return view('customers.show', compact('customer', 'currentBalance', 'sales'));
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

        // Customers who actually owe money (sales > payments)
        $customers = Customer::withSum(['sales as total_sales' => function ($q) {
            $q->select(\DB::raw("COALESCE(SUM(total_amount),0)"));
        }], 'total_amount')
            ->withSum(['payments as total_paid' => function ($q) {
                $q->select(\DB::raw("COALESCE(SUM(amount),0)"));
            }], 'amount')
            ->get()
            ->map(function ($customer) {

                $sales = $customer->total_sales ?? 0;
                $paid  = $customer->total_paid ?? 0;

                $customer->outstanding = $sales - $paid;

                return $customer;
            })
            ->filter(function ($customer) {
                // only customers with remaining balance and not counter sale
                return $customer->outstanding > 0 && $customer->name !== 'Counter Sale';
            });

        // Total Outstanding Amount
        $totalOutstanding = $customers->sum('outstanding');

        // paginate collection manually
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
