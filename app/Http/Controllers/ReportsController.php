<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Asset;
use App\Models\SaleReturn;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

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
        // Hide all data from starting page
        // $reportType  = $request->report_type ?? 'all';
        $reportType  = $request->report_type ?? 'dashboard';
        $startDate   = $request->start_date;
        $endDate     = $request->end_date;
        $customerId  = $request->customer_id;
        $supplierId  = $request->supplier_id;

        // Base queries (we will apply customer/supplier filters where appropriate)
        $purchasesQuery  = Purchase::query();
        $salesQuery      = Sale::query();
        $saleReturnQuery = SaleReturn::query();
        $expensesQuery   = Expense::query();
        $assetsQuery     = Asset::query();

        // Apply date filters globally (keeps behaviour you had)
        if ($startDate && $endDate) {
            $purchasesQuery->whereBetween('date', [$startDate, $endDate]);
            $salesQuery->whereBetween('date', [$startDate, $endDate]);
            $saleReturnQuery->whereBetween('created_at', [$startDate, $endDate]);
            $expensesQuery->whereBetween('created_at', [$startDate, $endDate]);
            $assetsQuery->whereDate('date', '>=', $startDate)->whereDate('date', '<=', $endDate);
        } elseif ($startDate) {
            $purchasesQuery->whereDate('date', '>=', $startDate);
            $salesQuery->whereDate('date', '>=', $startDate);
            $saleReturnQuery->whereDate('created_at', '>=', $startDate);
            $expensesQuery->whereDate('created_at', '>=', $startDate);
            $assetsQuery->whereDate('date', '>=', $startDate);
        } elseif ($endDate) {
            $purchasesQuery->whereDate('date', '<=', $endDate);
            $salesQuery->whereDate('date', '<=', $endDate);
            $saleReturnQuery->whereDate('created_at', '<=', $endDate);
            $expensesQuery->whereDate('created_at', '<=', $endDate);
            $assetsQuery->whereDate('date', '<=', $endDate);
        }

        // Apply customer / supplier filters to sales & purchases queries as well
        if ($customerId) {
            // if a customer filter is selected, apply it to sales
            $salesQuery->where('customer_id', $customerId);
            // Also apply to sale returns (if you want)
            $saleReturnQuery->whereHas('sale', fn($q) => $q->where('customer_id', $customerId));
        }

        if ($supplierId) {
            // if supplier filter selected, apply it to purchases
            $purchasesQuery->where('supplier_id', $supplierId);
        }

        // Totals (clone queries so pagination later is unaffected)
        $totalPurchases       = (clone $purchasesQuery)->sum('total_amount');
        $totalSales           = (clone $salesQuery)->sum('total_amount');
        $totalSaleReturns     = (clone $saleReturnQuery)->sum('amount_deducted');
        $totalExpenses        = (clone $expensesQuery)->sum('amount');
        $totalAssets          = (clone $assetsQuery)->sum('value');
        $productCount         = Product::count();
        $categoryCount        = Category::count();
        $supplierCount        = Supplier::count();
        $customerCount        = Customer::count();
        $assetCount           = Asset::count();
        $totalCustomerBalance = Customer::sum('balance');
        $totalSupplierBalance = Supplier::sum('balance');

        // Default empty paginators
        $productsLedger  = $this->emptyPaginator();
        $purchases       = $this->emptyPaginator();
        $sales           = $this->emptyPaginator();
        $saleReturns     = $this->emptyPaginator();
        $expenses        = $this->emptyPaginator();
        $assetsLedger    = $this->emptyPaginator();
        $customersLedger = $this->emptyPaginator();
        $suppliersLedger = $this->emptyPaginator();

        // Products ledger (respecting product_id + type filters)
        if ($reportType === 'products' || $reportType === 'all') {

            // NEW: purchase/sale type filter
            $typeFilter = $request->product_type;   // values: purchases | sales | null

            $productsQuery = Product::with([
                'category',

                // Purchase items
                'purchaseItems.product.category',
                'purchaseItems' => function ($q) use ($startDate, $endDate, $typeFilter) {

                    // ❌ If user selected "Sales Only", then remove purchases
                    if ($typeFilter === 'sales') {
                        return $q->whereNull('id'); // returns empty
                    }

                    // Date filtering
                    if ($startDate && $endDate) {
                        $q->whereHas(
                            'purchase',
                            fn($p) =>
                            $p->whereBetween('created_at', [$startDate, $endDate])
                        );
                    } elseif ($startDate) {
                        $q->whereHas(
                            'purchase',
                            fn($p) =>
                            $p->whereDate('created_at', '>=', $startDate)
                        );
                    } elseif ($endDate) {
                        $q->whereHas(
                            'purchase',
                            fn($p) =>
                            $p->whereDate('created_at', '<=', $endDate)
                        );
                    }
                },

                // Sale items
                'saleItems.product.category',
                'saleItems' => function ($q) use ($startDate, $endDate, $typeFilter) {

                    // ❌ If user selected "Purchases Only", then remove sales
                    if ($typeFilter === 'purchases') {
                        return $q->whereNull('id'); // returns empty
                    }

                    // Date filtering
                    if ($startDate && $endDate) {
                        $q->whereHas(
                            'sale',
                            fn($s) =>
                            $s->whereBetween('created_at', [$startDate, $endDate])
                        );
                    } elseif ($startDate) {
                        $q->whereHas(
                            'sale',
                            fn($s) =>
                            $s->whereDate('created_at', '>=', $startDate)
                        );
                    } elseif ($endDate) {
                        $q->whereHas(
                            'sale',
                            fn($s) =>
                            $s->whereDate('created_at', '<=', $endDate)
                        );
                    }
                }
            ]);

            // Product filter
            if ($request->product_id) {
                $productsQuery->where('id', $request->product_id);
            }

            // Final result
            $productsLedger = $productsQuery->paginate(10);
        }

        // Purchases (respecting supplier filter if provided)
        if ($reportType === 'purchases' || $reportType === 'all') {
            $purchases = $purchasesQuery->with(['items.product.category', 'supplier'])->paginate(10);
        }

        // Sales (respecting customer filter if provided)
        if ($reportType === 'sales' || $reportType === 'all') {
            $sales = $salesQuery->with(['items.product.category', 'customer'])->paginate(10);
        }

        // Sale returns
        if ($reportType === 'returns' || $reportType === 'all') {
            $saleReturns = $saleReturnQuery->with(['items.product.category', 'sale.customer'])->paginate(10);
        }

        // Expenses
        if ($reportType === 'expenses' || $reportType === 'all') {
            $expenses = $expensesQuery->with('expenseName')->paginate(10);
        }

        // Assets
        if ($reportType === 'assets' || $reportType === 'all') {
            $assetsLedger = $assetsQuery->paginate(10);
        }

        // Customers ledger (if report_type customers OR all)
        if ($reportType === 'customers' || $reportType === 'all') {
            $customersQuery = Customer::with([
                'sales' => function ($q) use ($startDate, $endDate, $customerId) {
                    if ($startDate && $endDate) $q->whereBetween('created_at', [$startDate, $endDate]);
                    elseif ($startDate) $q->whereDate('created_at', '>=', $startDate);
                    elseif ($endDate) $q->whereDate('created_at', '<=', $endDate);

                    if ($customerId) $q->where('customer_id', $customerId); // optional, safe
                },
                'sales.items.product.category',
                'payments' => function ($q) use ($startDate, $endDate) {
                    if ($startDate && $endDate) $q->whereBetween('created_at', [$startDate, $endDate]);
                    elseif ($startDate) $q->whereDate('created_at', '>=', $startDate);
                    elseif ($endDate) $q->whereDate('created_at', '<=', $endDate);
                }
            ]);

            if ($customerId) {
                $customersQuery->where('id', $customerId);
            }

            $customersLedger = $customersQuery->paginate(10);
        }

        // Suppliers ledger
        if ($reportType === 'suppliers' || $reportType === 'all') {
            $suppliersQuery = Supplier::with([
                'purchases' => function ($q) use ($startDate, $endDate, $supplierId) {
                    if ($startDate && $endDate) $q->whereBetween('created_at', [$startDate, $endDate]);
                    elseif ($startDate) $q->whereDate('created_at', '>=', $startDate);
                    elseif ($endDate) $q->whereDate('created_at', '<=', $endDate);

                    if ($supplierId) $q->where('supplier_id', $supplierId); // optional, safe
                },
                'purchases.items.product.category',
                'payments' => function ($q) use ($startDate, $endDate) {
                    if ($startDate && $endDate) $q->whereBetween('created_at', [$startDate, $endDate]);
                    elseif ($startDate) $q->whereDate('created_at', '>=', $startDate);
                    elseif ($endDate) $q->whereDate('created_at', '<=', $endDate);
                }
            ]);

            if ($supplierId) {
                $suppliersQuery->where('id', $supplierId);
            }

            $suppliersLedger = $suppliersQuery->paginate(10);
        }

        // dropdown source lists
        $customersList = Customer::orderBy('name')->get();
        $suppliersList = Supplier::orderBy('name')->get();
        // Add products list for dropdown
        $productsList = Product::orderBy('name')->get();

        // ====== DASHBOARD DEFAULTS ======
        $D_totalSales       = 0;
        $D_totalPurchases   = 0;
        $D_totalExpenses    = 0;
        $D_grossProfit      = 0;
        $D_netProfit        = 0;
        $D_gpPercent        = 0;
        $D_expensePercent   = 0;
        $D_npPercent        = 0;
        $D_overallPercent   = 0;
        $D_purchaseQty      = 0;
        $D_saleQty          = 0;
        $D_purchasePercent  = 0;
        $D_purchaseItems    = collect([]);
        $D_totalSaleReturn  = 0;
        $D_returnCOGS       = 0;
        $D_returnQty        = 0;
        $D_adjustedSales    = 0;
        $D_adjustedCOGS     = 0;

        if ($reportType === 'dashboard') {

            // Default: current month (same as Analytics)
            if (!$startDate && !$endDate) {
                $startDate = Carbon::now()->startOfMonth()->toDateString();
                $endDate   = Carbon::now()->endOfMonth()->toDateString();
            }

            $salesQuery       = Sale::with('items.product');
            $saleReturnsQuery = SaleReturn::with('product');
            $expensesQuery    = Expense::query();
            $purchasesQuery   = Purchase::with('items');

            // Apply filters
            if ($startDate && $endDate) {

                $salesQuery->whereBetween('date', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);

                $saleReturnsQuery->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);

                $expensesQuery->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);

                $purchasesQuery->whereBetween('date', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);
            }

            $D_sales       = $salesQuery->get();
            $D_saleReturns = $saleReturnsQuery->get();
            $D_expenses    = $expensesQuery->get();
            $D_purchases   = $purchasesQuery->get();

            // Totals
            $D_totalSales      = $D_sales->sum('total_amount');
            $D_totalPurchases  = $D_purchases->sum('total_amount');
            $D_totalExpenses   = $D_expenses->sum('amount');
            $D_totalSaleReturn = $D_saleReturns->sum('amount_deducted');
            $D_returnQty       = $D_saleReturns->sum('qty_return');

            // COGS
            $D_totalCOGS = 0;
            foreach ($D_sales as $sale) {
                foreach ($sale->items as $item) {
                    if ($item->product) {
                        $D_totalCOGS += $item->quantity * $item->product->price_per_unit;
                    }
                }
            }

            // Return COGS
            $D_returnCOGS = 0;
            foreach ($D_saleReturns as $ret) {
                if ($ret->product) {
                    $D_returnCOGS += $ret->qty_return * $ret->product->price_per_unit;
                }
            }

            // Adjusted calculations
            $D_adjustedSales = $D_totalSales - $D_totalSaleReturn;
            $D_adjustedCOGS  = $D_totalCOGS - $D_returnCOGS;

            // Profit
            $D_grossProfit = $D_adjustedSales - $D_adjustedCOGS;
            $D_netProfit   = $D_grossProfit - $D_totalExpenses;

            // Percentages
            $D_gpPercent = $D_adjustedSales > 0 ? ($D_grossProfit / $D_adjustedSales) * 100 : 0;
            $D_expensePercent = $D_adjustedSales > 0 ? ($D_totalExpenses / $D_adjustedSales) * 100 : 0;
            $D_npPercent = $D_adjustedSales > 0 ? ($D_netProfit / $D_adjustedSales) * 100 : 0;

            // Quantities
            $D_purchaseQty = $D_purchases->flatMap(fn($p) => $p->items)->sum('quantity');
            $D_saleQty     = $D_sales->flatMap(fn($s) => $s->items)->sum('quantity');

            // Purchase %
            $D_purchasePercent = $D_adjustedSales > 0
                ? ($D_totalPurchases / $D_adjustedSales) * 100
                : 0;

            $D_purchaseItems = $D_sales->flatMap(fn($s) => $s->items);
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
            'assetsLedger',
            'customersLedger',
            'suppliersLedger',
            'totalPurchases',
            'totalSales',
            'totalSaleReturns',
            'totalExpenses',
            'totalAssets',
            'productCount',
            'categoryCount',
            'supplierCount',
            'customerCount',
            'assetCount',
            'totalCustomerBalance',
            'totalSupplierBalance',
            'customerId',
            'supplierId',
            'customersList',
            'suppliersList',
            'productsList',
            'D_totalSales',
            'D_totalPurchases',
            'D_totalExpenses',
            'D_grossProfit',
            'D_netProfit',
            'D_gpPercent',
            'D_expensePercent',
            'D_npPercent',
            'D_overallPercent',
            'D_purchaseQty',
            'D_saleQty',
            'D_purchasePercent',
            'D_purchaseItems',
            'D_totalSaleReturn',
            'D_returnCOGS',
            'D_returnQty',
            'D_adjustedSales',
            'D_adjustedCOGS'
        ));
    }
}
