<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Asset;
use App\Models\SaleReturn;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date ?? now()->subMonth()->toDateString();
        $endDate   = $request->end_date ?? now()->toDateString();

        $totalSales = Sale::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');
        $totalPurchases = Purchase::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');
        $productCount = Product::count();
        $categoryCount = Category::count();
        $supplierCount = Supplier::count();
        $customerCount = Customer::count();
        $totalExpenses = Expense::whereBetween('created_at', [$startDate, $endDate])->sum('amount');
        $assetCount = Asset::count();
        $totalSaleReturns = SaleReturn::whereBetween('created_at', [$startDate, $endDate])->sum('amount_deducted');
        $totalCustomerBalance = Customer::sum('balance');

        // Fetch sales with items, products, categories, and customer
        $sales = Sale::with(['customer', 'items.product.category'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        // Fetch purchases with items, products, categories, and supplier
        $purchases = Purchase::with(['supplier', 'items.product.category'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        return view('reports.index', compact(
            'totalSales',
            'totalPurchases',
            'productCount',
            'categoryCount',
            'supplierCount',
            'customerCount',
            'totalExpenses',
            'assetCount',
            'totalSaleReturns',
            'totalCustomerBalance',
            'startDate',
            'endDate',
            'sales',
            'purchases'
        ));
    }
}
