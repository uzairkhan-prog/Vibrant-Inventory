<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SaleItem;
use App\Models\PurchaseItem;
use App\Models\SaleReturn;
use Illuminate\Http\Request;

class ProductLedgerController extends Controller
{

    public function index(Request $request)
    {
        $products = Product::orderBy('name')->get();

        $productId = $request->product_id;
        $fromDate  = $request->from_date;
        $toDate    = $request->to_date;

        $ledger = collect();
        $product = null;

        if ($productId) {

            $product = Product::with('category')->findOrFail($productId);

            /* ================= PURCHASES ================= */
            $purchases = PurchaseItem::with('purchase.supplier')
                ->where('product_id', $productId)
                ->when($fromDate && $toDate, function ($q) use ($fromDate, $toDate) {
                    $q->whereHas('purchase', function ($q2) use ($fromDate, $toDate) {
                        $q2->whereBetween('date', [$fromDate, $toDate]);
                    });
                })
                ->get()
                ->map(function ($item) {

                    $supplier = $item->purchase->supplier;

                    return [
                        'date' => $item->purchase->date,
                        'invoice_no' => 'Purchase - Invoice no: ' . $item->purchase->id,
                        'type' => 'Purchase',
                        'party' => $supplier
                            ? ($supplier->company_name ?? $supplier->name)
                            . ($supplier->name ? " ({$supplier->name})" : '')
                            : '-',
                        'qty_in' => $item->quantity,
                        'qty_out' => 0,
                        'price' => $item->price,
                        'total' => $item->quantity * $item->price,
                    ];
                });

            /* ================= SALES ================= */
            $sales = SaleItem::with('sale.customer')
                ->where('product_id', $productId)
                ->when($fromDate && $toDate, function ($q) use ($fromDate, $toDate) {
                    $q->whereHas('sale', function ($q2) use ($fromDate, $toDate) {
                        $q2->whereBetween('date', [$fromDate, $toDate]);
                    });
                })
                ->get()
                ->map(function ($item) {

                    $customer = $item->sale->customer;

                    return [
                        'date' => $item->sale->date,
                        'invoice_no' => 'Sale - Invoice no: ' . $item->sale->id,
                        'type' => 'Sale',
                        'party' => $customer
                            ? ($customer->company_name ?? '-')
                            . ($customer->name ? " ({$customer->name})" : '')
                            : '-',
                        'qty_in' => 0,
                        'qty_out' => $item->quantity,
                        'price' => $item->price,
                        'total' => $item->quantity * $item->price,
                    ];
                });

            /* ================= RETURNS ================= */
            $returns = SaleReturn::with('customer')
                ->where('product_id', $productId)
                ->when($fromDate && $toDate, function ($q) use ($fromDate, $toDate) {
                    $q->whereBetween('created_at', [$fromDate, $toDate]);
                })
                ->get()
                ->map(function ($return) {

                    $customer = $return->customer;

                    return [
                        'date' => $return->created_at,
                        'invoice_no' => 'Sale Return - Invoice no: ' . $return->id,
                        'type' => 'Sale Return',
                        'party' => $customer
                            ? ($customer->company_name ?? '-')
                            . ($customer->name ? " ({$customer->name})" : '')
                            : '-',
                        'qty_in' => $return->qty_return,
                        'qty_out' => 0,
                        'price' => $return->amount_deducted / max($return->qty_return, 1),
                        'total' => $return->amount_deducted,
                    ];
                });

            /* ================= MERGE ================= */
            $ledger = $purchases
                ->concat($sales)
                ->concat($returns)
                ->sortBy('date')
                ->values();

            /* ================= BALANCE ================= */
            $balance = 0;

            $ledger = $ledger->map(function ($row) use (&$balance) {
                $balance += $row['qty_in'];
                $balance -= $row['qty_out'];
                $row['balance'] = $balance;
                return $row;
            });
        }

        return view('ledger.products', compact('products', 'ledger', 'product'));
    }

    public function show($productId)
    {
        $product = Product::with('category')->findOrFail($productId);

        /* ================= PURCHASES ================= */
        $purchases = PurchaseItem::with('purchase.supplier')
            ->where('product_id', $productId)
            ->get()
            ->map(function ($item) {

                $supplier = $item->purchase->supplier;

                return [
                    'date' => $item->purchase->date,
                    'invoice_no' => 'Purchase - Invoice no: ' . $item->purchase->id,
                    'type' => 'Purchase',
                    'party' => $supplier
                        ? ($supplier->company_name ?? $supplier->name)
                        . ($supplier->name ? " ({$supplier->name})" : '')
                        : '-',
                    'qty_in' => $item->quantity,
                    'qty_out' => 0,
                    'price' => $item->price,
                    'total' => $item->quantity * $item->price,
                ];
            });

        /* ================= SALES ================= */
        $sales = SaleItem::with('sale.customer')
            ->where('product_id', $productId)
            ->get()
            ->map(function ($item) {

                $customer = $item->sale->customer;

                return [
                    'date' => $item->sale->date,
                    'invoice_no' => 'Sale - Invoice no: ' . $item->sale->id,
                    'type' => 'Sale',
                    'party' => $customer
                        ? ($customer->company_name ?? '-')
                        . ($customer->name ? " ({$customer->name})" : '')
                        : '-',
                    'qty_in' => 0,
                    'qty_out' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->quantity * $item->price,
                ];
            });

        /* ================= SALE RETURNS ================= */
        $returns = SaleReturn::with('customer')
            ->where('product_id', $productId)
            ->get()
            ->map(function ($return) {

                $customer = $return->customer;

                return [
                    'date' => $return->created_at,
                    'invoice_no' => 'Sale Return - Invoice no: ' . $return->id,
                    'type' => 'Sale Return',
                    'party' => $customer
                        ? ($customer->company_name ?? '-')
                        . ($customer->name ? " ({$customer->name})" : '')
                        : '-',
                    'qty_in' => $return->qty_return,
                    'qty_out' => 0,
                    'price' => $return->amount_deducted / max($return->qty_return, 1),
                    'total' => $return->amount_deducted,
                ];
            });

        /* ================= MERGE ================= */

        $ledger = $purchases
            ->concat($sales)
            ->concat($returns)
            ->sortBy('date')
            ->values();

        /* ================= RUNNING BALANCE ================= */

        $balance = 0;

        $ledger = $ledger->map(function ($row) use (&$balance) {

            $balance += $row['qty_in'];
            $balance -= $row['qty_out'];

            $row['balance'] = $balance;

            return $row;
        });

        return view('products.ledger', compact('product', 'ledger'));
    }
}
