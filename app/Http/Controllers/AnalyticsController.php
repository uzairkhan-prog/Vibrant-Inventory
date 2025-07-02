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
        // Monthly Sales
        $monthlySales = Sale::selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Monthly Purchases
        $monthlyPurchases = Purchase::selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Summary Counts
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalSuppliers = Supplier::count();
        $totalCustomers = Customer::count();
        $totalPurchases = Purchase::count();
        $totalSales = Sale::count();
        $totalExpenses = Expense::count();
        $totalAssets = Asset::count();
        $totalLedgers = Ledger::count();

        // Return view with all data
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
            'totalLedgers'
        ));
    }
}
