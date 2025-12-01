<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\SaleReturn;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $sales = Sale::query();
        $purchases = Purchase::query();
        $expenses = Expense::query();
        $saleReturns = SaleReturn::query();

        if ($fromDate && $toDate) {
            $sales->whereBetween('created_at', [$fromDate, Carbon::parse($toDate)->endOfDay()]);
            $purchases->whereBetween('created_at', [$fromDate, Carbon::parse($toDate)->endOfDay()]);
            $expenses->whereBetween('created_at', [$fromDate, Carbon::parse($toDate)->endOfDay()]);
            $saleReturns->whereBetween('created_at', [$fromDate, Carbon::parse($toDate)->endOfDay()]);
        }

        $totalSales = $sales->sum('total_amount');

        /** =========================
         *   SALE RETURN ADDED
         *  ========================= */
        $totalSaleReturn = $saleReturns->sum('amount_deducted');
        $returnQty = $saleReturns->sum('qty_return');

        // COGS of returned items
        $returnCOGS = 0;
        foreach ($saleReturns->get() as $ret) {
            if ($ret->product) {
                $returnCOGS += $ret->qty_return * $ret->product->purchase_price;
                // Add returned quantity back to stock
                $ret->product->increment('quantity', $ret->qty_return);
            }
        }

        $totalPurchases = $purchases->sum('total_amount');
        $totalExpenses = $expenses->sum('amount');

        // Adjusted Sales & COGS for P&L
        $adjustedSales = $totalSales - $totalSaleReturn;
        $adjustedCOGS = $totalPurchases - $returnCOGS;

        $grossProfit = $adjustedSales - $adjustedCOGS;
        $netProfit = $grossProfit - $totalExpenses;

        $purchaseItems = PurchaseItem::with('product')
            ->when($fromDate && $toDate, function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('created_at', [$fromDate, Carbon::parse($toDate)->endOfDay()]);
            })
            ->get();

        $purchaseQty = $purchaseItems->sum('quantity');

        $gpPercent = $adjustedSales > 0 ? ($grossProfit / $adjustedSales) * 100 : 0;
        $expensePercent = $adjustedSales > 0 ? ($totalExpenses / $adjustedSales) * 100 : 0;
        $npPercent = $adjustedSales > 0 ? ($netProfit / $adjustedSales) * 100 : 0;
        $purchasePercent = $adjustedSales > 0 ? ($totalPurchases / $adjustedSales) * 100 : 0;

        return view('analytics.index', compact(
            'totalSales',
            'totalSaleReturn',
            'returnCOGS',
            'returnQty',
            'adjustedSales',
            'adjustedCOGS',
            'totalPurchases',
            'totalExpenses',
            'grossProfit',
            'netProfit',
            'gpPercent',
            'expensePercent',
            'npPercent',
            'purchasePercent',
            'purchaseQty',
            'purchaseItems',
            'fromDate',
            'toDate'
        ));
    }
}
