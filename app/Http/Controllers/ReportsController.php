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
    public function index()
    {
        $totalSales = Sale::sum('total_amount');
        $totalPurchases = Purchase::sum('total_amount');
        $productCount = Product::count();
        $categoryCount = Category::count();
        $supplierCount = Supplier::count();
        $customerCount = Customer::count();
        $totalExpenses = Expense::sum('amount');
        $assetCount = Asset::count();
        $totalSaleReturns = SaleReturn::sum('amount_deducted');
        $totalCustomerBalance = Customer::sum('balance');

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
            'totalCustomerBalance'
        ));
    }
}
