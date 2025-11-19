<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\Ledger;
use App\Models\Asset;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // ===== Date Range Filter =====
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        // Build date-filtered queries
        $salesQuery = Sale::query();
        $purchasesQuery = Purchase::query();
        $expensesQuery = Expense::query();

        if ($fromDate && $toDate) {
            $salesQuery->whereBetween('created_at', [$fromDate, Carbon::parse($toDate)->endOfDay()]);
            $purchasesQuery->whereBetween('created_at', [$fromDate, Carbon::parse($toDate)->endOfDay()]);
            $expensesQuery->whereBetween('created_at', [$fromDate, Carbon::parse($toDate)->endOfDay()]);
        }

        // ===== Summaries =====
        $sumSalesAmount = $salesQuery->sum('total_amount');
        $sumPurchasesAmount = $purchasesQuery->sum('total_amount');
        $sumExpensesAmount = $expensesQuery->sum('amount');

        $sumAssetsAmount = Asset::sum('value');
        $totalLedgerBalance = Ledger::sum('balance');

        // ===== Monthly Charts =====
        $monthlySales = Sale::selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyPurchases = Purchase::selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // ===== Entity Counts =====
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalSuppliers = Supplier::count();
        $totalCustomers = Customer::count();
        $totalPurchases = Purchase::count();
        $totalSales = Sale::count();
        $totalExpenses = Expense::count();
        $totalAssets = Asset::count();
        $totalLedgers = Ledger::count();

        // ===== Product Statistics =====
        $perPage = $request->get('per_page', 20);
        $products = Product::with('category')->paginate($perPage);
        $totalQuantity = Product::sum('quantity');
        $totalValue = Product::selectRaw('SUM(quantity * price_per_unit) as total')->value('total') ?? 0;

        $categoryAggregates = Product::leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->selectRaw("COALESCE(categories.name, 'Uncategorized') as category_name, 
                         SUM(products.quantity) as sum_qty, 
                         SUM(products.quantity * products.price_per_unit) as sum_value")
            ->groupBy('category_name')
            ->orderByDesc('sum_qty')
            ->get();

        $categoryLabels = $categoryAggregates->pluck('category_name')->toArray();
        $quantityData = $categoryAggregates->pluck('sum_qty')->map(fn($v) => (int)$v)->toArray();
        $valueData = $categoryAggregates->pluck('sum_value')->map(fn($v) => (float)$v)->toArray();

        // ===== Return View =====
        return view('analytics.index', compact(
            'monthlySales',
            'monthlyPurchases',
            'totalProducts',
            'totalCategories',
            'totalSuppliers',
            'totalCustomers',
            'totalPurchases',
            'totalSales',
            'totalExpenses',
            'totalAssets',
            'totalLedgers',
            'sumPurchasesAmount',
            'sumSalesAmount',
            'sumExpensesAmount',
            'sumAssetsAmount',
            'totalLedgerBalance',
            'products',
            'totalQuantity',
            'totalValue',
            'categoryLabels',
            'quantityData',
            'valueData',
            'fromDate',
            'toDate'
        ));
    }
}
