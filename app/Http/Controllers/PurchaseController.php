<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Response;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $perPage   = $request->get('per_page', 20);
        $search    = $request->get('search');
        $monthYear = $request->get('month_year');
        $fromDate  = $request->get('from_date');
        $toDate    = $request->get('to_date');

        $query = Purchase::with('supplier')->orderBy('date', 'desc');

        /* =========================
            DATE FILTER LOGIC
        ========================= */

        if ($fromDate || $toDate) {

            if ($fromDate && $toDate) {
                $query->whereBetween('date', [$fromDate, $toDate]);
            } elseif ($fromDate) {
                $query->whereDate('date', '>=', $fromDate);
            } elseif ($toDate) {
                $query->whereDate('date', '<=', $toDate);
            }

            $monthTotal = (clone $query)->sum('total_amount');
            $monthYear = 'custom';
        } elseif ($monthYear === 'all') {

            $monthTotal = Purchase::sum('total_amount');
        } elseif ($monthYear) {

            [$year, $month] = explode('-', $monthYear);

            $query->whereYear('date', $year)
                ->whereMonth('date', $month);

            $monthTotal = (clone $query)->sum('total_amount');
        } else {
            // DEFAULT = CURRENT MONTH (IMPORTANT FIX)
            $monthYear = now()->format('Y-m');

            [$year, $month] = explode('-', $monthYear);

            $query->whereYear('date', $year)
                ->whereMonth('date', $month);

            $monthTotal = (clone $query)->sum('total_amount');
        }

        /* =========================
            SEARCH
        ========================= */

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('total_amount', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($s) use ($search) {
                        $s->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $purchases = $query->paginate($perPage)->appends($request->all());

        /* =========================
            TOTALS
        ========================= */

        $allTimeTotal = Purchase::sum('total_amount');

        $months = Purchase::selectRaw("DATE_FORMAT(date, '%Y-%m') as month")
            ->distinct()
            ->orderBy('month', 'desc')
            ->pluck('month');

        return view('purchases.index', compact(
            'purchases',
            'monthYear',
            'months',
            'allTimeTotal',
            'monthTotal',
            'search',
            'fromDate',
            'toDate'
        ));
    }

    public function create()
    {
        $suppliers  = Supplier::all();
        $categories = Category::all(); // Load categories
        return view('purchases.create', compact('suppliers', 'categories'));
    }

    public function getProductsByCategory($categoryId)
    {
        $products = Product::where('category_id', $categoryId)->get(['id', 'name', 'quantity']);
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id'  => 'required|exists:suppliers,id',
            'date'         => 'required|date|after_or_equal:today',
            'product_id.*' => 'required|exists:products,id',
            'quantity.*'   => 'required|integer|min:1',
            'price.*'      => 'required|numeric|min:0',
            'discount.*'   => 'nullable|numeric|min:0',
            'tax.*'        => 'nullable|numeric|min:0',
        ]);

        try {

            DB::transaction(function () use ($request) {

                /* ======================================================
                CREATE PURCHASE
            ====================================================== */
                $purchase = Purchase::create([
                    'supplier_id'  => $request->supplier_id,
                    'total_amount' => 0,
                    'date'         => $request->date,
                ]);

                $totalAmount = 0;

                /* ======================================================
                PURCHASE ITEMS + UPDATE PRODUCT STOCK
            ====================================================== */
                foreach ($request->product_id as $index => $productId) {

                    $quantity = (float) $request->quantity[$index];
                    $price    = (float) $request->price[$index];
                    $discount = (float) ($request->discount[$index] ?? 0);
                    $tax      = (float) ($request->tax[$index] ?? 0);

                    // Lock product row for safe stock update
                    $product = Product::lockForUpdate()->findOrFail($productId);

                    /* ===============================
                    LINE CALCULATIONS
                =============================== */
                    $base = $quantity * $price;
                    $discountAmount = ($discount / 100) * $base;
                    $taxable = $base - $discountAmount;
                    $taxAmount = ($tax / 100) * $taxable;
                    $subtotal = $taxable + $taxAmount;

                    /* ===============================
                    CREATE PURCHASE ITEM
                =============================== */
                    PurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'product_id'  => $productId,
                        'quantity'    => $quantity,
                        'price'       => $price,
                        'discount'    => $discount,
                        'tax'         => $tax,
                    ]);

                    /* ======================================================
                    ✅ UPDATE PRODUCT STOCK & PRICE
                ====================================================== */

                    // Increase stock
                    $product->quantity += $quantity;

                    // Update last purchase price
                    $product->price_per_unit = $price;

                    $product->save();

                    $totalAmount += $subtotal;
                }

                /* ======================================================
                UPDATE PURCHASE TOTAL
            ====================================================== */
                $purchase->update([
                    'total_amount' => $totalAmount
                ]);

                /* ======================================================
                SUPPLIER LEDGER UPDATE
            ====================================================== */
                $supplier = Supplier::lockForUpdate()->findOrFail($request->supplier_id);

                $supplier->balance += $totalAmount;
                $supplier->save();

                /* ======================================================
                RECORD PAYMENT (DO NOT TOUCH BALANCE)
            ====================================================== */
                $paid = (float) ($request->paid_amount ?? 0);

                if ($paid > 0) {

                    SupplierPayment::create([
                        'supplier_id'  => $supplier->id,
                        'purchase_id'  => $purchase->id,
                        'payment_type' => 'Cash',
                        'description'  => 'Payment paid against Purchase #' . $purchase->id,
                        'amount'       => $paid,
                        'date'         => now(),
                    ]);
                }
            });
        } catch (\Exception $e) {

            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('purchases.index')
            ->with('success', 'Purchase created successfully & stock updated.');
    }

    public function edit(Purchase $purchase)
    {
        $suppliers = Supplier::all();
        $products  = Product::all();
        $categories = Category::all();

        $purchase->load('items.product');

        // Advance payments for this purchase only
        $advancePayment = SupplierPayment::where('purchase_id', $purchase->id)
            ->sum('amount');

        $balance = $purchase->total_amount - $advancePayment;
        if ($balance < 0) $balance = 0;

        return view('purchases.edit', compact('purchase', 'suppliers', 'products', 'categories', 'advancePayment', 'balance'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $request->validate([
            'supplier_id'  => 'required|exists:suppliers,id',
            'date'         => 'required|date',
            'product_id.*' => 'required|exists:products,id',
            'quantity.*'   => 'required|integer|min:1',
            'price.*'      => 'required|numeric|min:0',
            'discount.*'   => 'nullable|numeric|min:0',
            'tax.*'        => 'nullable|numeric|min:0',
            'advance'      => 'nullable|numeric|min:0',
        ]);

        try {

            DB::transaction(function () use ($request, $purchase) {

                /* ======================================================
                STEP 1: REVERSE OLD STOCK
            ====================================================== */

                foreach ($purchase->items as $oldItem) {

                    $product = Product::lockForUpdate()->find($oldItem->product_id);

                    if ($product) {
                        // subtract old quantity
                        $product->quantity -= $oldItem->quantity;

                        if ($product->quantity < 0) {
                            $product->quantity = 0;
                        }

                        $product->save();
                    }
                }

                /* ======================================================
                STEP 2: REVERSE OLD SUPPLIER LEDGER
            ====================================================== */

                $oldSupplier = Supplier::lockForUpdate()->findOrFail($purchase->supplier_id);

                $oldTotal = $purchase->total_amount;

                // remove purchase amount
                $oldSupplier->balance -= $oldTotal;

                if ($oldSupplier->balance < 0) {
                    $oldSupplier->balance = 0;
                }

                $oldSupplier->save();


                /* ======================================================
                STEP 3: DELETE OLD PAYMENTS & ITEMS
            ====================================================== */

                SupplierPayment::where('purchase_id', $purchase->id)->delete();
                $purchase->items()->delete();


                /* ======================================================
                STEP 4: ADD NEW ITEMS + UPDATE STOCK
            ====================================================== */

                $grandTotal = 0;

                foreach ($request->product_id as $index => $productId) {

                    $qty      = (float)$request->quantity[$index];
                    $price    = (float)$request->price[$index];
                    $discount = (float)($request->discount[$index] ?? 0);
                    $tax      = (float)($request->tax[$index] ?? 0);

                    $product = Product::lockForUpdate()->findOrFail($productId);

                    /* ---------- CALCULATIONS ---------- */
                    $base = $qty * $price;
                    $discountAmt = ($discount / 100) * $base;
                    $afterDiscount = $base - $discountAmt;
                    $taxAmt = ($tax / 100) * $afterDiscount;
                    $lineTotal = $afterDiscount + $taxAmt;

                    /* ---------- CREATE ITEM ---------- */
                    PurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'product_id'  => $productId,
                        'quantity'    => $qty,
                        'price'       => $price,
                        'discount'    => $discount,
                        'tax'         => $tax,
                    ]);

                    /* ======================================================
                    UPDATE PRODUCT STOCK & PRICE
                ====================================================== */

                    $product->quantity += $qty;

                    // update last purchase price
                    $product->price_per_unit = $price;

                    $product->save();

                    $grandTotal += $lineTotal;
                }


                /* ======================================================
                STEP 5: UPDATE PURCHASE
            ====================================================== */

                $purchase->update([
                    'supplier_id'  => $request->supplier_id,
                    'date'         => $request->date,
                    'total_amount' => round($grandTotal, 2),
                ]);


                /* ======================================================
                STEP 6: APPLY NEW SUPPLIER LEDGER
            ====================================================== */

                $newSupplier = Supplier::lockForUpdate()->findOrFail($request->supplier_id);

                $newSupplier->balance += $grandTotal;


                /* ======================================================
                STEP 7: APPLY NEW ADVANCE PAYMENT
            ====================================================== */

                $newAdvance = (float)($request->advance ?? 0);

                if ($newAdvance > 0) {

                    SupplierPayment::create([
                        'supplier_id'  => $newSupplier->id,
                        'purchase_id'  => $purchase->id,
                        'description'  => 'Advance paid against Purchase Invoice #' . $purchase->id,
                        'payment_type' => 'Cash',
                        'amount'       => $newAdvance,
                        'date'         => now(),
                    ]);

                    $newSupplier->balance -= $newAdvance;
                }

                if ($newSupplier->balance < 0) {
                    $newSupplier->balance = 0;
                }

                $newSupplier->save();
            });
        } catch (\Exception $e) {

            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('purchases.index')
            ->with('success', 'Purchase updated successfully & stock adjusted.');
    }

    public function show(Purchase $purchase)
    {
        // Load purchase items + supplier + only payments of THIS purchase
        $purchase->load([
            'items.product',
            'supplier',
            'supplier.payments' => function ($q) use ($purchase) {
                $q->where('purchase_id', $purchase->id);
            }
        ]);

        return view('purchases.show', compact('purchase'));
    }

    public function destroy(Purchase $purchase)
    {
        DB::transaction(function () use ($purchase) {
            foreach ($purchase->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->quantity -= $item->quantity;
                    $product->save();
                }

                $item->delete();
            }

            $purchase->delete();
        });

        return redirect()->route('purchases.index')->with('success', 'Purchase deleted successfully.');
    }

    public function exportInoviceCSV($id)
    {
        $purchase = Purchase::with(['items.product', 'supplier'])->findOrFail($id);

        $filename = 'purchase_invoice_' . $purchase->id . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($purchase) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['#', 'Product', 'Qty', 'Price', 'Discount (%)', 'Tax (%)', 'Subtotal']);

            $total = 0;
            foreach ($purchase->items as $index => $item) {
                $base = $item->quantity * $item->price;
                $discount = ($item->discount ?? 0) * $base / 100;
                $taxable = $base - $discount;
                $tax = ($item->tax ?? 0) * $taxable / 100;
                $subtotal = $taxable + $tax;
                $total += $subtotal;

                fputcsv($handle, [
                    $index + 1,
                    $item->product->name,
                    $item->quantity,
                    number_format($item->price, 2),
                    number_format($item->discount ?? 0, 2),
                    number_format($item->tax ?? 0, 2),
                    number_format($subtotal, 2)
                ]);
            }

            fputcsv($handle, ['', '', '', '', '', 'Total', number_format($total, 2)]);
            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportCsv()
    {
        $filename = 'purchase_report_' . date('Ymd_His') . '.csv';

        $response = new StreamedResponse(function () {
            $handle = fopen('php://output', 'w');

            // CSV Headers
            fputcsv($handle, [
                'Invoice #',
                'Invoice Date',
                'Supplier',
                'Product',
                'Quantity',
                'Price',
                'Discount %',
                'Tax %',
                'Subtotal',
                'Total Amount'
            ]);

            $purchases = Purchase::with('supplier', 'items.product')->orderBy('date', 'desc')->get();

            foreach ($purchases as $purchase) {
                foreach ($purchase->items as $item) {
                    $base = $item->quantity * $item->price;
                    $discountAmount = ($item->discount ?? 0) * $base / 100;
                    $taxable = $base - $discountAmount;
                    $taxAmount = ($item->tax ?? 0) * $taxable / 100;
                    $subtotal = $taxable + $taxAmount;

                    fputcsv($handle, [
                        $purchase->id,
                        $purchase->date->format('Y-m-d'),
                        $purchase->supplier->name,
                        $item->product->name,
                        $item->quantity,
                        number_format($item->price, 2),
                        number_format($item->discount ?? 0, 2),
                        number_format($item->tax ?? 0, 2),
                        number_format($subtotal, 2),
                        number_format($purchase->total_amount, 2),
                    ]);
                }
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', "attachment; filename=\"$filename\"");

        return $response;
    }
}
