<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\SaleItem;
use App\Models\PurchaseItem;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductLedgerController extends Controller
{
    public function index(Request $request)
    {
        // Filters
        $from = $request->input('from_date');
        $to   = $request->input('to_date');
        $typeFilter = $request->input('type_filter'); // sale / purchase / null
        $showAll = $request->has('show_all');

        /* ============================================================
            FETCH SALES
        ============================================================ */
        $salesQuery = SaleItem::with(['sale.customer', 'product']);

        if ($from && $to) {
            $salesQuery->whereBetween('created_at', [
                $from . ' 00:00:00',
                $to . ' 23:59:59'
            ]);
        }

        // Apply type filter
        if ($typeFilter === 'sale') {
            // keep sales
        } elseif ($typeFilter === 'purchase') {
            // user wants only purchase → skip sales
            $sales = collect([]);
        }

        // Only fetch sales if necessary
        if (!isset($sales)) {
            $sales = $salesQuery->get()->map(function ($item) {
                return [
                    'date' => $item->created_at->format('Y-m-d'),
                    'type' => 'sale',
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name ?? '-',
                    'qty' => $item->quantity,
                    'unit_price' => $item->price,
                    'invoice_no' => $item->sale_id,
                    'invoice_value' => $item->price * $item->quantity,
                ];
            });
        }

        /* ============================================================
            FETCH PURCHASES
        ============================================================ */
        $purchaseQuery = PurchaseItem::with(['purchase.supplier', 'product']);

        if ($from && $to) {
            $purchaseQuery->whereBetween('created_at', [
                $from . ' 00:00:00',
                $to . ' 23:59:59'
            ]);
        }

        // Apply type filter
        if ($typeFilter === 'purchase') {
            // keep purchases only
        } elseif ($typeFilter === 'sale') {
            // user wants only sale → skip purchases
            $purchases = collect([]);
        }

        // Only fetch purchases if necessary
        if (!isset($purchases)) {
            $purchases = $purchaseQuery->get()->map(function ($item) {
                return [
                    'date' => $item->created_at->format('Y-m-d'),
                    'type' => 'purchase',
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name ?? '-',
                    'qty' => $item->quantity,
                    'unit_price' => $item->price,
                    'invoice_no' => $item->purchase_id,
                    'invoice_value' => $item->price * $item->quantity,
                ];
            });
        }

        /* ============================================================
            MERGE BOTH LISTS
        ============================================================ */
        $merged = collect($sales)->merge($purchases);

        // Remove duplicate product entries if "Show All" is unchecked
        if (!$showAll) {
            $merged = $merged->unique('product_id')->values();
        }

        // Sort by date desc
        $merged = $merged->sortByDesc('date')->values();

        /* ============================================================
            PAGINATION
        ============================================================ */
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = $request->input('per_page', 20);
        $currentItems = $merged->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $ledgerEntries = new LengthAwarePaginator(
            $currentItems,
            $merged->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query()
            ]
        );

        /* ============================================================
            TOTALS BASED ON FILTERED RESULTS
        ============================================================ */
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
            'perPage',
            'from',
            'to'
        ));
    }
}
