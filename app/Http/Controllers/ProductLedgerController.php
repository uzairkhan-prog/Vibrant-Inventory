<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\SaleItem;
use App\Models\Sale;
use App\Models\PurchaseItem;
use App\Models\Purchase;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductLedgerController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::orderBy('name')->get();

        $sales = SaleItem::with(['sale.customer', 'product'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->created_at->format('Y-m-d'),
                    'type' => 'Sale',
                    'party' => $item->sale->customer->name ?? '-',
                    'qty'  => $item->quantity,
                    'invoice_no' => $item->sale_id,
                    'invoice_value' => $item->price * $item->quantity,
                ];
            });

        $purchases = PurchaseItem::with(['purchase.supplier', 'product'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->created_at->format('Y-m-d'),
                    'type' => 'Purchase',
                    'party' => $item->purchase->supplier->name ?? '-',
                    'qty'  => $item->quantity,
                    'invoice_no' => $item->purchase_id,
                    'invoice_value' => $item->price * $item->quantity,
                ];
            });

        $merged = $sales->merge($purchases)->sortByDesc('date')->values();

        // Handle pagination with per_page option
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = $request->input('per_page', 20);
        $currentItems = $merged->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $ledgerEntries = new LengthAwarePaginator(
            $currentItems,
            $merged->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Totals
        $total_sold_qty = $sales->sum('qty');
        $total_sold_value = $sales->sum('invoice_value');
        $total_purchase_qty = $purchases->sum('qty');
        $total_purchase_value = $purchases->sum('invoice_value');

        return view('ledger.products', compact(
            'ledgerEntries',
            'total_sold_qty',
            'total_sold_value',
            'total_purchase_qty',
            'total_purchase_value',
            'perPage'
        ));
    }
}
