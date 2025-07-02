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
    public function index()
    {
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
            'totalLedgerBalance'
        ));
    }
}
