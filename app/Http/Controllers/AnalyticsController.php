<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Expense;
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

        if ($fromDate && $toDate) {
            $sales->whereBetween('created_at', [$fromDate, Carbon::parse($toDate)->endOfDay()]);
            $purchases->whereBetween('created_at', [$fromDate, Carbon::parse($toDate)->endOfDay()]);
            $expenses->whereBetween('created_at', [$fromDate, Carbon::parse($toDate)->endOfDay()]);
        }

        $totalSales = $sales->sum('total_amount');
        $totalPurchases = $purchases->sum('total_amount');
        $totalExpenses = $expenses->sum('amount');

        $purchaseItems = PurchaseItem::with('product')
            ->when($fromDate && $toDate, function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('created_at', [$fromDate, Carbon::parse($toDate)->endOfDay()]);
            })
            ->get();

        $purchaseQty = $purchaseItems->sum('quantity');
        $purchasePercent = $totalSales > 0 ? ($totalPurchases / $totalSales) * 100 : 0;

        $grossProfit = $totalSales - $totalPurchases;
        $netProfit = $grossProfit - $totalExpenses;

        $gpPercent = $totalSales > 0 ? ($grossProfit / $totalSales) * 100 : 0;
        $expensePercent = $totalSales > 0 ? ($totalExpenses / $totalSales) * 100 : 0;
        $npPercent = $totalSales > 0 ? ($netProfit / $totalSales) * 100 : 0;
        $overallPercent = $totalSales > 0 ? ($netProfit / $totalSales) * 100 : 0;

        return view('analytics.index', compact(
            'totalSales',
            'totalPurchases',
            'totalExpenses',
            'grossProfit',
            'netProfit',
            'gpPercent',
            'expensePercent',
            'npPercent',
            'overallPercent',
            'purchaseQty',
            'purchasePercent',
            'purchaseItems',
            'fromDate',
            'toDate'
        ));
    }
}
