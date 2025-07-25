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
        $perPage = $request->get('per_page', 20);
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
            'discount.*'  => 'nullable|numeric|min:0',
            'tax.*'       => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $purchase = Purchase::create([
                'supplier_id'  => $request->supplier_id,
                'total_amount' => 0,
                'date'         => $request->date,
            ]);

            $totalAmount = 0;

            foreach ($request->product_id as $index => $productId) {
                $quantity = (float) $request->quantity[$index];
                $price    = (float) $request->price[$index];
                $discount = (float) ($request->discount[$index] ?? 0);
                $tax      = (float) ($request->tax[$index] ?? 0);

                $base = $quantity * $price;
                $discountAmount = ($discount / 100) * $base;
                $taxable = $base - $discountAmount;
                $taxAmount = ($tax / 100) * $taxable;
                $subtotal = $taxable + $taxAmount;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id'  => $productId,
                    'quantity'    => $quantity,
                    'price'       => $price,
                    'discount'    => $discount,
                    'tax'         => $tax,
                ]);

                $product = Product::find($productId);
                $product->quantity += $quantity;
                $product->save();

                $totalAmount += $subtotal;
            }

            $purchase->update(['total_amount' => $totalAmount]);
        });

        return redirect()->route('purchases.index')->with('success', 'Purchase created successfully.');
    }

    public function edit(Purchase $purchase)
    {
        $suppliers = Supplier::all();
        $products  = Product::all();
        $purchase->load('items.product');

        return view('purchases.edit', compact('purchase', 'suppliers', 'products'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'date'        => 'required|date',
            'product_id.*' => 'required|exists:products,id',
            'quantity.*'  => 'required|integer|min:1',
            'price.*'     => 'required|numeric|min:0',
            'discount.*'  => 'nullable|numeric|min:0',
            'tax.*'       => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $purchase) {
            // Revert previous stock
            foreach ($purchase->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->quantity -= $item->quantity;
                    $product->save();
                }
            }

            // Delete old items
            $purchase->items()->delete();

            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'date'        => $request->date,
                'total_amount' => 0,
            ]);

            $totalAmount = 0;

            foreach ($request->product_id as $index => $productId) {
                $quantity = (float) $request->quantity[$index];
                $price    = (float) $request->price[$index];
                $discount = (float) ($request->discount[$index] ?? 0);
                $tax      = (float) ($request->tax[$index] ?? 0);

                $base = $quantity * $price;
                $discountAmount = ($discount / 100) * $base;
                $taxable = $base - $discountAmount;
                $taxAmount = ($tax / 100) * $taxable;
                $subtotal = $taxable + $taxAmount;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id'  => $productId,
                    'quantity'    => $quantity,
                    'price'       => $price,
                    'discount'    => $discount,
                    'tax'         => $tax,
                ]);

                // Update product stock
                $product = Product::find($productId);
                $product->quantity += $quantity;
                $product->save();

                $totalAmount += $subtotal;
            }

            $purchase->update(['total_amount' => $totalAmount]);
        });

        return redirect()->route('purchases.index')->with('success', 'Purchase updated successfully.');
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
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->quantity -= $item->quantity;
                    $product->save();
                }

                $item->delete();
            }

            $purchase->delete();
        });

        return redirect()->route('purchases.index')->with('success', 'Purchase deleted successfully.');
    }
}
