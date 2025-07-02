<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 5); // Default 5
        $purchases = Purchase::with('supplier')->orderBy('date', 'desc')->paginate($perPage);
        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $products  = Product::all();
        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'date'        => 'required|date',
            'product_id.*' => 'required|exists:products,id',
            'quantity.*'  => 'required|integer|min:1',
            'price.*'     => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $purchase = Purchase::create([
                'supplier_id'  => $request->supplier_id,
                'total_amount' => 0,
                'date'         => $request->date,
            ]);

            $totalAmount = 0;

            foreach ($request->product_id as $index => $productId) {
                $quantity = $request->quantity[$index];
                $price    = $request->price[$index];
                $subtotal = $quantity * $price;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id'  => $productId,
                    'quantity'    => $quantity,
                    'price'       => $price,
                ]);

                // Update product stock
                $product = Product::find($productId);
                $product->quantity += $quantity;
                $product->save();

                $totalAmount += $subtotal;
            }

            // Update total
            $purchase->total_amount = $totalAmount;
            $purchase->save();
        });

        return redirect()->route('purchases.index')->with('success', 'Purchase created successfully.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load('items.product', 'supplier');
        return view('purchases.show', compact('purchase'));
    }

    public function destroy(Purchase $purchase)
    {
        DB::transaction(function () use ($purchase) {
            foreach ($purchase->items as $item) {
                // Revert stock
                $product = $item->product;
                $product->quantity -= $item->quantity;
                $product->save();

                $item->delete();
            }

            $purchase->delete();
        });

        return redirect()->route('purchases.index')->with('success', 'Purchase deleted successfully.');
    }
}
