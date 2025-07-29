<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Response;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);
        $sales = Sale::with('customer')->orderBy('date', 'desc')->paginate($perPage);
        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $customers = Customer::all();
        $products = Product::all();
        return view('sales.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id'   => 'required|exists:customers,id',
            'date'          => 'required|date',
            'product_id.*'  => 'required|exists:products,id',
            'quantity.*'    => 'required|integer|min:1',
            'price.*'       => 'required|numeric|min:0',
            'discount.*'    => 'nullable|numeric|min:0|max:100',
            'tax.*'         => 'nullable|numeric|min:0|max:100',
        ]);

        DB::transaction(function () use ($request) {
            $sale = Sale::create([
                'customer_id'  => $request->customer_id,
                'total_amount' => 0,
                'date'         => $request->date,
            ]);

            $totalAmount = 0;

            foreach ($request->product_id as $index => $productId) {
                $quantity = $request->quantity[$index];
                $price = $request->price[$index];
                $discount = $request->discount[$index] ?? 0;
                $tax = $request->tax[$index] ?? 0;

                $product = Product::findOrFail($productId);

                if ($product->quantity < $quantity) {
                    throw new \Exception("Not enough stock for product: {$product->name}");
                }

                $baseTotal = $quantity * $price;
                $discountAmount = ($discount / 100) * $baseTotal;
                $afterDiscount = $baseTotal - $discountAmount;
                $taxAmount = ($tax / 100) * $afterDiscount;
                $lineTotal = $afterDiscount + $taxAmount;

                SaleItem::create([
                    'sale_id'   => $sale->id,
                    'product_id' => $productId,
                    'quantity'  => $quantity,
                    'price'     => $price,
                    'discount'  => $discount,
                    'tax'       => $tax,
                ]);

                // Decrease product stock
                $product->quantity -= $quantity;
                $product->save();

                $totalAmount += $lineTotal;
            }

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
        $products = Product::all();
        $sale->load('items.product');
        return view('sales.edit', compact('sale', 'customers', 'products'));
    }

    public function update(Request $request, Sale $sale)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'date'        => 'required|date',
            'product_id.*' => 'required|exists:products,id',
            'quantity.*'  => 'required|integer|min:1',
            'price.*'     => 'required|numeric|min:0',
            'discount.*'  => 'nullable|numeric|min:0',
            'tax.*'       => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $sale) {
            // Revert previous stock
            foreach ($sale->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->quantity += $item->quantity;
                    $product->save();
                }
            }

            // Delete old items
            $sale->items()->delete();

            // Update sale header
            $sale->update([
                'customer_id'  => $request->customer_id,
                'date'         => $request->date,
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

                // Create new sale item
                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $productId,
                    'quantity'   => $quantity,
                    'price'      => $price,
                    'discount'   => $discount,
                    'tax'        => $tax,
                ]);

                // Deduct sold stock
                $product = Product::find($productId);
                $product->quantity -= $quantity;
                $product->save();

                $totalAmount += $subtotal;
            }

            $sale->update(['total_amount' => $totalAmount]);
        });

        return redirect()->route('sales.index')->with('success', 'Sale updated successfully.');
    }

    public function destroy(Sale $sale)
    {
        DB::transaction(function () use ($sale) {
            foreach ($sale->items as $item) {
                $product = $item->product;
                $product->quantity += $item->quantity;
                $product->save();

                $item->delete();
            }

            $sale->delete();
        });

        return redirect()->route('sales.index')->with('success', 'Sale deleted successfully.');
    }

    public function exportInoviceCSV($id)
    {
        $sale = Sale::with(['items.product', 'customer'])->findOrFail($id);

        $filename = 'sale_invoice_' . $sale->id . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($sale) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['#', 'Product', 'Qty', 'Price', 'Discount (%)', 'Tax (%)', 'Subtotal']);

            $total = 0;
            foreach ($sale->items as $index => $item) {
                $base = $item->quantity * $item->price;
                $discount = ($item->discount ?? 0) * $base / 100;
                $taxable = $base - $discount;
                $tax = ($item->tax ?? 0) * $taxable / 100;
                $subtotal = $taxable + $tax;
                $total += $subtotal;

                fputcsv($handle, [
                    $index + 1,
                    $item->product->name,
                    $item->quantity,
                    number_format($item->price, 2),
                    number_format($item->discount ?? 0, 2),
                    number_format($item->tax ?? 0, 2),
                    number_format($subtotal, 2)
                ]);
            }

            fputcsv($handle, ['', '', '', '', '', 'Total', number_format($total, 2)]);
            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportCsv()
    {
        $filename = 'sale_report_' . date('Ymd_His') . '.csv';

        $response = new StreamedResponse(function () {
            $handle = fopen('php://output', 'w');

            // CSV Headers
            fputcsv($handle, [
                'Invoice #',
                'Invoice Date',
                'Supplier',
                'Product',
                'Quantity',
                'Price',
                'Discount %',
                'Tax %',
                'Subtotal',
                'Total Amount'
            ]);

            $sales = sale::with('customer', 'items.product')->orderBy('date', 'desc')->get();

            foreach ($sales as $sale) {
                foreach ($sale->items as $item) {
                    $base = $item->quantity * $item->price;
                    $discountAmount = ($item->discount ?? 0) * $base / 100;
                    $taxable = $base - $discountAmount;
                    $taxAmount = ($item->tax ?? 0) * $taxable / 100;
                    $subtotal = $taxable + $taxAmount;

                    fputcsv($handle, [
                        $sale->id,
                        $sale->date->format('Y-m-d'),
                        $sale->supplier->name,
                        $item->product->name,
                        $item->quantity,
                        number_format($item->price, 2),
                        number_format($item->discount ?? 0, 2),
                        number_format($item->tax ?? 0, 2),
                        number_format($subtotal, 2),
                        number_format($sale->total_amount, 2),
                    ]);
                }
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', "attachment; filename=\"$filename\"");

        return $response;
    }
}
