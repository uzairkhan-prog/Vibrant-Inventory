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
use App\Models\CustomerPayment;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $reportType = $request->report_type ?? 'all';
        $startDate  = $request->start_date;
        $endDate    = $request->end_date;

        // Filters
        $salesQuery     = Sale::query();
        $purchasesQuery = Purchase::query();
        $expensesQuery  = Expense::query();

        if ($startDate && $endDate) {
            $salesQuery->whereBetween('created_at', [$startDate, $endDate]);
            $purchasesQuery->whereBetween('created_at', [$startDate, $endDate]);
            $expensesQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Totals
        $totalSales           = $salesQuery->clone()->sum('total_amount');
        $totalPurchases       = $purchasesQuery->clone()->sum('total_amount');
        $totalExpenses        = $expensesQuery->clone()->sum('amount');
        $productCount         = Product::count();
        $categoryCount        = Category::count();
        $supplierCount        = Supplier::count();
        $customerCount        = Customer::count();
        $assetCount           = Asset::count();
        $totalSaleReturns     = SaleReturn::when($startDate && $endDate, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
            ->sum('amount_deducted');
        $totalCustomerBalance = Customer::sum('balance');

        // Data
        $sales     = collect();
        $purchases = collect();
        $expenses  = collect();
        $customersLedger = collect();

        if ($reportType === 'sales' || $reportType === 'all') {
            $sales = $salesQuery->with(['customer', 'items.product.category'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }

        if ($reportType === 'purchases' || $reportType === 'all') {
            $purchases = $purchasesQuery->with(['supplier', 'items.product.category'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }

        if ($reportType === 'expenses' || $reportType === 'all') {
            $expenses = $expensesQuery->with('expenseName')
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }

        if ($reportType === 'customers' || $reportType === 'all') {
            $customersLedger = Customer::with([
                'sales.items.product',
                'payments'
            ])->paginate(10);
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
            'expenses',
            'customersLedger'
        ));
    }
}
