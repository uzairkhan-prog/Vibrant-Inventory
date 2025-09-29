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
        $startDate   = $request->start_date ?? now()->subMonth()->toDateString();
        $endDate     = $request->end_date ?? now()->toDateString();
        $reportType  = $request->report_type ?? 'all';

        // Base summary
        $totalSales          = Sale::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');
        $totalPurchases      = Purchase::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');
        $productCount        = Product::count();
        $categoryCount       = Category::count();
        $supplierCount       = Supplier::count();
        $customerCount       = Customer::count();
        $totalExpenses       = Expense::whereBetween('created_at', [$startDate, $endDate])->sum('amount');
        $assetCount          = Asset::count();
        $totalSaleReturns    = SaleReturn::whereBetween('created_at', [$startDate, $endDate])->sum('amount_deducted');
        $totalCustomerBalance = Customer::sum('balance');

        // Fetch with pagination
        $sales     = collect();
        $purchases = collect();
        $expenses  = collect();

        if ($reportType === 'sales' || $reportType === 'all') {
            $sales = Sale::with(['customer', 'items.product.category'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('date', 'desc')
                ->paginate(10);
        }

        if ($reportType === 'purchases' || $reportType === 'all') {
            $purchases = Purchase::with(['supplier', 'items.product.category'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('date', 'desc')
                ->paginate(10);
        }

        if ($reportType === 'expenses' || $reportType === 'all') {
            $expenses = Expense::whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('date', 'desc')
                ->paginate(10);
        }

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
            'reportType',
            'sales',
            'purchases',
            'expenses'
        ));
    }
}
