<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\SaleItem;
use App\Models\Sale;
use App\Models\PurchaseItem;
use App\Models\Purchase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProductLedgerController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::orderBy('name')->get();

        // Sales as plain array collection
        $sales = SaleItem::with(['sale.customer', 'product'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->created_at->format('Y-m-d'),
                    'type' => 'Sale',
                    'product_name' => $item->product->name ?? '-',
                    'qty' => $item->quantity,
                    'unit_price' => $item->price,
                    'invoice_no' => $item->sale_id,
                    'invoice_value' => $item->price * $item->quantity,
                ];
            });

        // Purchases as plain array collection
        $purchases = PurchaseItem::with(['purchase.supplier', 'product'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->created_at->format('Y-m-d'),
                    'type' => 'Purchase',
                    'product_name' => $item->product->name ?? '-',
                    'qty' => $item->quantity,
                    'unit_price' => $item->price,
                    'invoice_no' => $item->purchase_id,
                    'invoice_value' => $item->price * $item->quantity,
                ];
            });

        // Convert both to base collection before merging
        $merged = collect($sales)->merge($purchases)->sortByDesc('date')->values();

        // Handle pagination
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
        $total_sold_qty = collect($sales)->sum('qty');
        $total_sold_value = collect($sales)->sum('invoice_value');
        $total_purchase_qty = collect($purchases)->sum('qty');
        $total_purchase_value = collect($purchases)->sum('invoice_value');

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
