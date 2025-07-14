<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use DB;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20); // default to 5
        $sales = Sale::with('customer')->orderBy('date', 'desc')->paginate($perPage);
        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $customers = Customer::all();
        $products  = Product::all();
        return view('sales.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'date'        => 'required|date',
            'description' => 'nullable|string|max:255',
            'product_id.*' => 'required|exists:products,id',
            'quantity.*'  => 'required|integer|min:1',
            'price.*'     => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $sale = Sale::create([
                'customer_id'  => $request->customer_id,
                'total_amount' => 0,
                'date'         => $request->date,
                'description'  => $request->description,  // Add this
            ]);

            $totalAmount = 0;

            foreach ($request->product_id as $index => $productId) {
                $quantity = $request->quantity[$index];
                $price    = $request->price[$index];
                $subtotal = $quantity * $price;
                $description    = $request->description;

                $product = Product::find($productId);

                if ($product->quantity < $quantity) {
                    throw new \Exception("Not enough stock for product: {$product->name}");
                }

                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $productId,
                    'quantity'   => $quantity,
                    'price'      => $price,
                    'description'      => $description,
                ]);

                // Decrease product stock
                $product->quantity -= $quantity;
                $product->save();

                $totalAmount += $subtotal;
            }

            // Update total
            $sale->total_amount = $totalAmount;
            $sale->save();
        });

        return redirect()->route('sales.index')->with('success', 'Sale created successfully.');
    }

    public function show(Sale $sale)
    {
        $sale->load('items.product', 'customer');
        return view('sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        $customers = Customer::all();
        $products  = Product::all();
        $sale->load('items.product');
        return view('sales.edit', compact('sale', 'customers', 'products'));
    }

    public function update(Request $request, Sale $sale)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'date'        => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);

        $sale->update([
            'customer_id' => $request->customer_id,
            'date'        => $request->date,
            'description' => $request->description,
        ]);

        return redirect()->route('sales.index')->with('success', 'Sale updated successfully.');
    }

    public function destroy(Sale $sale)
    {
        DB::transaction(function () use ($sale) {
            foreach ($sale->items as $item) {
                // Revert stock
                $product = $item->product;
                $product->quantity += $item->quantity;
                $product->save();

                $item->delete();
            }

            $sale->delete();
        });

        return redirect()->route('sales.index')->with('success', 'Sale deleted successfully.');
    }
}
