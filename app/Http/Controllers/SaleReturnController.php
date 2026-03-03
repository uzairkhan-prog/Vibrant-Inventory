<?php

namespace App\Http\Controllers;

use App\Models\SaleReturn;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\CustomerPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleReturnController extends Controller
{
    /* =============================
       INDEX
    ============================= */
    public function index()
    {
        $returns = SaleReturn::with(['customer', 'product'])->latest()->paginate(20);
        return view('sale_returns.index', compact('returns'));
    }

    /* =============================
       CREATE
    ============================= */
    public function create()
    {
        $customers = Customer::whereHas('sales')->select('id', 'name', 'balance')->get();
        return view('sale_returns.create', compact('customers'));
    }

    /* =============================
       STORE
    ============================= */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id'     => 'required|exists:customers,id',
            'product_id'      => 'required|exists:products,id',
            'sale_id'         => 'required|exists:sales,id',
            'qty_return'      => 'required|integer|min:1',
            'amount_deducted' => 'required|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($request) {

                $customer = Customer::lockForUpdate()->findOrFail($request->customer_id);
                $product  = Product::lockForUpdate()->findOrFail($request->product_id);

                // Increase Product Stock
                $product->quantity += $request->qty_return;
                $product->save();

                // Update Sale Item
                $saleItem = SaleItem::where('sale_id', $request->sale_id)
                    ->where('product_id', $request->product_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $saleItem->quantity -= $request->qty_return;
                $saleItem->save();

                // Update Sale Total Amount
                $sale = Sale::lockForUpdate()->findOrFail($request->sale_id);
                $sale->total_amount -= $request->amount_deducted;
                $sale->save();

                // Deduct Customer Balance
                $customer->balance -= $request->amount_deducted;
                if ($customer->balance < 0) $customer->balance = 0;
                $customer->save();

                // Update Customer Payment (deduct and set description)
                $returnAmount = (float) $request->amount_deducted;
                $payments = CustomerPayment::where('sale_id', $sale->id)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();

                foreach ($payments as $payment) {
                    if ($returnAmount <= 0) break;
                    $availableAmount = $payment->amount;
                    if ($availableAmount <= 0) continue;

                    $deductNow = min($availableAmount, $returnAmount);
                    $payment->amount -= $deductNow;

                    // Updated description
                    $payment->description =
                        "Sale Return - Reduced Rs {$deductNow} from Invoice #{$sale->id} for {$request->qty_return} unit(s) of {$product->name}";

                    $payment->save();
                    $returnAmount -= $deductNow;
                }

                // Save Sale Return
                SaleReturn::create([
                    'customer_id'        => $request->customer_id,
                    'product_id'         => $request->product_id,
                    'sale_id'            => $request->sale_id,
                    'packing'            => $request->packing,
                    'qty_return'         => $request->qty_return,
                    'amount_deducted'    => $request->amount_deducted,
                    'total_after_return' => $customer->balance,
                ]);
            });
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('sale-returns.index')->with('success', 'Sale return recorded successfully.');
    }

    /* =============================
       EDIT
    ============================= */
    public function edit(SaleReturn $sale_return)
    {
        $customers = Customer::whereHas('sales')->select('id', 'name', 'balance')->get();
        return view('sale_returns.edit', compact('sale_return', 'customers'));
    }

    /* =============================
       UPDATE
    ============================= */
    public function update(Request $request, SaleReturn $sale_return)
    {
        $request->validate([
            'customer_id'     => 'required|exists:customers,id',
            'product_id'      => 'required|exists:products,id',
            'sale_id'         => 'required|exists:sales,id',
            'qty_return'      => 'required|integer|min:1',
            'amount_deducted' => 'required|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($request, $sale_return) {

                // Reverse previous return (Product, SaleItem, Sale, Customer Balance)
                $oldCustomer = Customer::lockForUpdate()->findOrFail($sale_return->customer_id);
                $oldProduct  = Product::lockForUpdate()->findOrFail($sale_return->product_id);

                $oldProduct->quantity -= $sale_return->qty_return;
                $oldProduct->save();

                $oldCustomer->balance += $sale_return->amount_deducted;
                $oldCustomer->save();

                $oldSaleItem = SaleItem::where('sale_id', $sale_return->sale_id)
                    ->where('product_id', $sale_return->product_id)
                    ->lockForUpdate()
                    ->first();

                if ($oldSaleItem) {
                    $oldSaleItem->quantity += $sale_return->qty_return;
                    $oldSaleItem->save();
                }

                $oldSale = Sale::lockForUpdate()->findOrFail($sale_return->sale_id);
                $oldSale->total_amount += $sale_return->amount_deducted;
                $oldSale->save();

                // New return
                $newCustomer = Customer::lockForUpdate()->findOrFail($request->customer_id);
                $newProduct  = Product::lockForUpdate()->findOrFail($request->product_id);

                $newProduct->quantity += $request->qty_return;
                $newProduct->save();

                $newSaleItem = SaleItem::where('sale_id', $request->sale_id)
                    ->where('product_id', $request->product_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $newSaleItem->quantity -= $request->qty_return;
                $newSaleItem->save();

                $newSale = Sale::lockForUpdate()->findOrFail($request->sale_id);
                $newSale->total_amount -= $request->amount_deducted;
                $newSale->save();

                $newCustomer->balance -= $request->amount_deducted;
                if ($newCustomer->balance < 0) $newCustomer->balance = 0;
                $newCustomer->save();

                // Deduct from CustomerPayment (like store) with updated description
                $returnAmount = (float) $request->amount_deducted;
                $payments = CustomerPayment::where('sale_id', $request->sale_id)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();

                foreach ($payments as $payment) {
                    if ($returnAmount <= 0) break;
                    $availableAmount = $payment->amount;
                    if ($availableAmount <= 0) continue;

                    $deductNow = min($availableAmount, $returnAmount);
                    $payment->amount -= $deductNow;

                    // Updated description
                    $payment->description =
                        "Sale Return - Reduced Rs {$deductNow} from Invoice #{$request->sale_id} for {$request->qty_return} unit(s) of {$newProduct->name}";

                    $payment->save();
                    $returnAmount -= $deductNow;
                }

                // Update SaleReturn
                $sale_return->update([
                    'customer_id'        => $request->customer_id,
                    'product_id'         => $request->product_id,
                    'sale_id'            => $request->sale_id,
                    'packing'            => $request->packing,
                    'qty_return'         => $request->qty_return,
                    'amount_deducted'    => $request->amount_deducted,
                    'total_after_return' => $newCustomer->balance,
                ]);
            });
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('sale-returns.index')->with('success', 'Sale Return updated successfully.');
    }

    /* =============================
       DESTROY
    ============================= */
    public function destroy(SaleReturn $sale_return)
    {
        try {
            DB::transaction(function () use ($sale_return) {

                $customer = Customer::lockForUpdate()->findOrFail($sale_return->customer_id);
                $product  = Product::lockForUpdate()->findOrFail($sale_return->product_id);

                $product->quantity -= $sale_return->qty_return;
                $product->save();

                $customer->balance += $sale_return->amount_deducted;
                $customer->save();

                $saleItem = SaleItem::where('sale_id', $sale_return->sale_id)
                    ->where('product_id', $sale_return->product_id)
                    ->lockForUpdate()
                    ->first();

                if ($saleItem) {
                    $saleItem->quantity += $sale_return->qty_return;
                    // $saleItem->price += $sale_return->amount_deducted;
                    $saleItem->save();
                }

                $sale = Sale::lockForUpdate()->findOrFail($sale_return->sale_id);
                $sale->total_amount += $sale_return->amount_deducted;
                $sale->save();

                // Update CustomerPayment for revert
                $payments = CustomerPayment::where('sale_id', $sale_return->sale_id)->lockForUpdate()->get();
                foreach ($payments as $payment) {
                    $payment->amount += $sale_return->amount_deducted;
                    $payment->description = "Advance received against Sale Invoice #{$sale_return->sale_id}";
                    $payment->save();
                }

                $sale_return->delete();
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('sale-returns.index')->with('success', 'Sale Return deleted.');
    }

    /* =============================
       AJAX: Products of selected customer
    ============================= */
    public function getCustomerProducts($customer_id)
    {
        $products = DB::table('sale_items as si')
            ->join('sales as s', 'si.sale_id', '=', 's.id')
            ->join('products as p', 'si.product_id', '=', 'p.id')
            ->where('s.customer_id', $customer_id)
            ->select(
                'p.id',
                'p.name',
                'p.packing',
                'si.quantity as quantity',
                'si.price as price_per_unit',
                's.id as sale_id'
            )
            ->get();

        return response()->json($products);
    }
}
