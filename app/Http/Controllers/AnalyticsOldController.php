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

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // Existing Analytics Data
        $monthlySales = Sale::selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyPurchases = Purchase::selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalSuppliers = Supplier::count();
        $totalCustomers = Customer::count();
        $totalPurchases = Purchase::count();
        $totalSales = Sale::count();
        $totalExpenses = Expense::count();
        $totalAssets = Asset::count();
        $totalLedgers = Ledger::count();

        $sumPurchasesAmount = Purchase::sum('total_amount');
        $sumSalesAmount = Sale::sum('total_amount');
        $sumExpensesAmount = Expense::sum('amount');
        $sumAssetsAmount = Asset::sum('value');
        $totalLedgerBalance = Ledger::sum('balance');

        // Chart-Specific Data
        $perPage = $request->get('per_page', 20);
        $products = Product::with('category')->paginate($perPage);
        $totalQuantity = Product::sum('quantity');
        $totalValue = Product::selectRaw('SUM(quantity * price_per_unit) as total')->value('total') ?? 0;

        $categoryAggregates = Product::leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->selectRaw("COALESCE(categories.name, 'Uncategorized') as category_name, SUM(products.quantity) as sum_qty, SUM(products.quantity * products.price_per_unit) as sum_value")
            ->groupBy('category_name')
            ->orderByDesc('sum_qty')
            ->get();

        $categoryLabels = $categoryAggregates->pluck('category_name')->toArray();
        $quantityData = $categoryAggregates->pluck('sum_qty')->map(fn($v) => (int)$v)->toArray();
        $valueData = $categoryAggregates->pluck('sum_value')->map(fn($v) => (float)$v)->toArray();

        $productsPerCategoryRaw = Product::leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->selectRaw("COALESCE(categories.name, 'Uncategorized') as category_name, COUNT(products.id) as prod_count")
            ->groupBy('category_name')
            ->get();

        $prodCountMap = $productsPerCategoryRaw->pluck('prod_count', 'category_name')->toArray();
        $productsPerCategory = array_map(fn($label) => $prodCountMap[$label] ?? 0, $categoryLabels);

        // Return unified view
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
            'productsPerCategory'
        ));
    }
}
