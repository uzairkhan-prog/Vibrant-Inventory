<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\SaleReturn;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // Default current month
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        if (!$fromDate && !$toDate) {
            $fromDate = Carbon::now()->startOfMonth()->toDateString();
            $toDate = Carbon::now()->endOfMonth()->toDateString();
        }

        // Fetch data
        $sales = Sale::query()->with('items.product');
        $saleReturns = SaleReturn::query()->with('product');
        $expenses = Expense::query();
        $purchases = Purchase::query();

        // Apply Date Filters
        if ($fromDate && $toDate) {

            $sales->whereBetween('date', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay()
            ]);

            $saleReturns->whereBetween('created_at', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay()
            ]);

            $expenses->whereBetween('created_at', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay()
            ]);

            $purchases->whereBetween('date', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay()
            ]);
        }

        $sales = $sales->get();
        $saleReturns = $saleReturns->get();
        $expenses = $expenses->get();
        $purchases = $purchases->get();

        // Total Sales
        $totalSales = $sales->sum('total_amount');

        // Total Purchases
        $totalPurchases = $purchases->sum('total_amount');

        // Sale Returns
        $totalSaleReturn = $saleReturns->sum('amount_deducted');
        $returnQty = $saleReturns->sum('qty_return');

        // COGS
        $totalCOGS = 0;
        foreach ($sales as $sale) {
            foreach ($sale->items as $item) {
                if ($item->product) {
                    $totalCOGS += $item->quantity * $item->product->price_per_unit;
                }
            }
        }

        // Return COGS
        $returnCOGS = 0;
        foreach ($saleReturns as $ret) {
            if ($ret->product) {
                $returnCOGS += $ret->qty_return * $ret->product->price_per_unit;
            }
        }

        $adjustedSales = $totalSales - $totalSaleReturn;
        $adjustedCOGS = $totalCOGS - $returnCOGS;

        // Profit
        $totalExpenses = $expenses->sum('amount');
        $grossProfit = $adjustedSales - $adjustedCOGS;
        $netProfit = $grossProfit - $totalExpenses;

        $gpPercent = $adjustedSales > 0 ? ($grossProfit / $adjustedSales) * 100 : 0;
        $expensePercent = $adjustedSales > 0 ? ($totalExpenses / $adjustedSales) * 100 : 0;
        $npPercent = $adjustedSales > 0 ? ($netProfit / $adjustedSales) * 100 : 0;

        // Quantity sold
        $purchaseQty = $purchases->flatMap(fn($p) => $p->items)->sum('quantity');
        $saleQty = $sales->flatMap(fn($s) => $s->items)->sum('quantity');

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
            'saleQty',
            'fromDate',
            'toDate'
        ));
    }
}
