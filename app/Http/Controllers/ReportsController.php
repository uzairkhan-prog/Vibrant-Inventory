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
use Illuminate\Pagination\LengthAwarePaginator;

class ReportsController extends Controller
{
    /**
     * Return an empty paginator (so blade can still call links()/hasPages()).
     */
    protected function emptyPaginator()
    {
        return new LengthAwarePaginator([], 0, 10, 1, [
            'path'  => request()->url(),
            'query' => request()->query(),
        ]);
    }

    public function index(Request $request)
    {
        $reportType = $request->report_type ?? 'all';
        $startDate  = $request->start_date;
        $endDate    = $request->end_date;

        // Base queries
        $purchasesQuery  = Purchase::query();
        $salesQuery      = Sale::query();
        $saleReturnQuery = SaleReturn::query();
        $expensesQuery   = Expense::query();
        $assetsQuery     = Asset::query(); // ✅ added

        // ✅ Apply date filters globally
        if ($startDate && $endDate) {
            $purchasesQuery->whereBetween('created_at', [$startDate, $endDate]);
            $salesQuery->whereBetween('created_at', [$startDate, $endDate]);
            $saleReturnQuery->whereBetween('created_at', [$startDate, $endDate]);
            $expensesQuery->whereBetween('created_at', [$startDate, $endDate]);
            $assetsQuery->whereBetween('date', [$startDate, $endDate]); // ✅ assets use "date"
        } elseif ($startDate) {
            $purchasesQuery->whereDate('created_at', '>=', $startDate);
            $salesQuery->whereDate('created_at', '>=', $startDate);
            $saleReturnQuery->whereDate('created_at', '>=', $startDate);
            $expensesQuery->whereDate('created_at', '>=', $startDate);
            $assetsQuery->whereDate('date', '>=', $startDate);
        } elseif ($endDate) {
            $purchasesQuery->whereDate('created_at', '<=', $endDate);
            $salesQuery->whereDate('created_at', '<=', $endDate);
            $saleReturnQuery->whereDate('created_at', '<=', $endDate);
            $expensesQuery->whereDate('created_at', '<=', $endDate);
            $assetsQuery->whereDate('date', '<=', $endDate);
        }

        // ✅ Totals
        $totalPurchases       = (clone $purchasesQuery)->sum('total_amount');
        $totalSales           = (clone $salesQuery)->sum('total_amount');
        $totalSaleReturns     = (clone $saleReturnQuery)->sum('amount_deducted');
        $totalExpenses        = (clone $expensesQuery)->sum('amount');
        $totalAssets          = (clone $assetsQuery)->sum('value'); // ✅ added
        $productCount         = Product::count();
        $categoryCount        = Category::count();
        $supplierCount        = Supplier::count();
        $customerCount        = Customer::count();
        $assetCount           = Asset::count();
        $totalCustomerBalance = Customer::sum('balance');
        $totalSupplierBalance = Supplier::sum('balance');

        // ✅ Default paginators
        $productsLedger  = $this->emptyPaginator();
        $purchases       = $this->emptyPaginator();
        $sales           = $this->emptyPaginator();
        $saleReturns     = $this->emptyPaginator();
        $expenses        = $this->emptyPaginator();
        $assetsLedger    = $this->emptyPaginator(); // ✅ added
        $customersLedger = $this->emptyPaginator();
        $suppliersLedger = $this->emptyPaginator();

        // ✅ Products Ledger (with categories too)
        if ($reportType === 'products' || $reportType === 'all') {
            $productsLedger = Product::with([
                'category',
                'purchaseItems.product.category',
                'saleItems.product.category',
                'purchaseItems' => function ($q) use ($startDate, $endDate) {
                    if ($startDate && $endDate) {
                        $q->whereHas('purchase', fn($p) => $p->whereBetween('created_at', [$startDate, $endDate]));
                    } elseif ($startDate) {
                        $q->whereHas('purchase', fn($p) => $p->whereDate('created_at', '>=', $startDate));
                    } elseif ($endDate) {
                        $q->whereHas('purchase', fn($p) => $p->whereDate('created_at', '<=', $endDate));
                    }
                },
                'saleItems' => function ($q) use ($startDate, $endDate) {
                    if ($startDate && $endDate) {
                        $q->whereHas('sale', fn($s) => $s->whereBetween('created_at', [$startDate, $endDate]));
                    } elseif ($startDate) {
                        $q->whereHas('sale', fn($s) => $s->whereDate('created_at', '>=', $startDate));
                    } elseif ($endDate) {
                        $q->whereHas('sale', fn($s) => $s->whereDate('created_at', '<=', $endDate));
                    }
                }
            ])->paginate(10);
        }

        // ✅ Purchases
        if ($reportType === 'purchases' || $reportType === 'all') {
            $purchases = $purchasesQuery->with(['items.product.category', 'supplier'])->paginate(10);
        }

        // ✅ Sales
        if ($reportType === 'sales' || $reportType === 'all') {
            $sales = $salesQuery->with(['items.product.category', 'customer'])->paginate(10);
        }

        // ✅ Sale Returns
        if ($reportType === 'returns' || $reportType === 'all') {
            $saleReturns = $saleReturnQuery->with(['items.product.category', 'sale.customer'])->paginate(10);
        }

        // ✅ Expenses
        if ($reportType === 'expenses' || $reportType === 'all') {
            $expenses = $expensesQuery->with('expenseName')->paginate(10);
        }

        // ✅ Assets
        if ($reportType === 'assets' || $reportType === 'all') {
            $assetsLedger = $assetsQuery->paginate(10);
        }

        // ✅ Customers Ledger
        if ($reportType === 'customers' || $reportType === 'all') {
            $customersLedger = Customer::with([
                'sales' => function ($q) use ($startDate, $endDate) {
                    if ($startDate && $endDate) $q->whereBetween('created_at', [$startDate, $endDate]);
                    elseif ($startDate) $q->whereDate('created_at', '>=', $startDate);
                    elseif ($endDate) $q->whereDate('created_at', '<=', $endDate);
                },
                'sales.items.product.category',
                'payments' => function ($q) use ($startDate, $endDate) {
                    if ($startDate && $endDate) $q->whereBetween('created_at', [$startDate, $endDate]);
                    elseif ($startDate) $q->whereDate('created_at', '>=', $startDate);
                    elseif ($endDate) $q->whereDate('created_at', '<=', $endDate);
                }
            ])->paginate(10);
        }

        // ✅ Suppliers Ledger
        if ($reportType === 'suppliers' || $reportType === 'all') {
            $suppliersLedger = Supplier::with([
                'purchases' => function ($q) use ($startDate, $endDate) {
                    if ($startDate && $endDate) $q->whereBetween('created_at', [$startDate, $endDate]);
                    elseif ($startDate) $q->whereDate('created_at', '>=', $startDate);
                    elseif ($endDate) $q->whereDate('created_at', '<=', $endDate);
                },
                'purchases.items.product.category',
                'payments' => function ($q) use ($startDate, $endDate) {
                    if ($startDate && $endDate) $q->whereBetween('created_at', [$startDate, $endDate]);
                    elseif ($startDate) $q->whereDate('created_at', '>=', $startDate);
                    elseif ($endDate) $q->whereDate('created_at', '<=', $endDate);
                }
            ])->paginate(10);
        }

        return view('reports.index', compact(
            'reportType',
            'startDate',
            'endDate',
            'productsLedger',
            'purchases',
            'sales',
            'saleReturns',
            'expenses',
            'assetsLedger',   // ✅ added
            'customersLedger',
            'suppliersLedger',
            'totalPurchases',
            'totalSales',
            'totalSaleReturns',
            'totalExpenses',
            'totalAssets',    // ✅ added
            'productCount',
            'categoryCount',
            'supplierCount',
            'customerCount',
            'assetCount',
            'totalCustomerBalance',
            'totalSupplierBalance'
        ));
    }
}
