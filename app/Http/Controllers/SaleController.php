<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Response;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $search = $request->input('search'); // optional search input

        // Initialize empty collection for first load
        $sales = collect();

        // Only fetch records if any filter is applied
        if ($fromDate || $toDate || $search) {
            $query = Sale::with('customer')->orderBy('date', 'desc');

            // Date range filter
            if ($fromDate && $toDate) {
                $query->whereBetween('date', [$fromDate, $toDate]);
            } elseif ($fromDate) {
                $query->whereDate('date', '>=', $fromDate);
            } elseif ($toDate) {
                $query->whereDate('date', '<=', $toDate);
            }

            // Optional search filter by customer name
            if ($search) {
                $query->whereHas('customer', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            }

            $sales = $query->paginate($perPage)->appends($request->all());
        }

        return view('sales.index', compact('sales', 'fromDate', 'toDate', 'search'));
    }


    public function create()
    {
        $customers = Customer::all();

        // Instead of using products.quantity, calculate stock from purchases & purchase_items
        $products = Product::with(['purchaseItems' => function ($q) {
            $q->select('product_id', 'quantity');
        }])->get();

        foreach ($products as $product) {
            $product->fifo_stock = $product->purchaseItems->sum('quantity');
        }

        return view('sales.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'   => 'required|exists:customers,id',
            'date'          => 'required|date',
            'product_id.*'  => 'required|exists:products,id',
            'quantity.*'    => 'required|integer|min:1',
            'price.*'       => 'required|numeric|min:0',
            'discount.*'    => 'nullable|numeric|min:0|max:100',
            'tax.*'         => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $sale = Sale::create([
                    'customer_id'  => $request->customer_id,
                    'total_amount' => 0,
                    'date'         => $request->date,
                ]);

                $totalAmount = 0;

                foreach ($request->product_id as $index => $productId) {
                    $quantity = (int) $request->quantity[$index];
                    $price = $request->price[$index];
                    $discount = $request->discount[$index] ?? 0;
                    $tax = $request->tax[$index] ?? 0;

                    // Check available qty across purchase_items (FIFO source)
                    $available = $this->getAvailableQtyFromPurchaseItems($productId);
                    if ($available < $quantity) {
                        throw new \Exception("Not enough stock for product Available: {$available}, Requested: {$quantity}");
                    }

                    // Allocate/decrement from purchase_items (FIFO)
                    $this->allocateFromPurchaseItems($productId, $quantity);

                    // Calculate totals
                    $baseTotal = $quantity * $price;
                    $discountAmount = ($discount / 100) * $baseTotal;
                    $afterDiscount = $baseTotal - $discountAmount;
                    $taxAmount = ($tax / 100) * $afterDiscount;
                    $lineTotal = $afterDiscount + $taxAmount;

                    SaleItem::create([
                        'sale_id'    => $sale->id,
                        'product_id' => $productId,
                        'quantity'   => $quantity,
                        'price'      => $price,
                        'discount'   => $discount,
                        'tax'        => $tax,
                    ]);

                    $totalAmount += $lineTotal;
                }

                $sale->total_amount = $totalAmount;
                $sale->save();
            });
        } catch (\Exception $e) {
            return Redirect::back()
                ->withInput()
                ->withErrors(['stock_error' => $e->getMessage()]);
        }

        return redirect()->route('sales.index')->with('success', 'Sale created successfully.');
    }

    public function show(Sale $sale)
    {
        $sale->load('items.product', 'customer');
        return view('sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        $customers = Customer::all();

        // Instead of using products.quantity, calculate stock from purchases & purchase_items
        $products = Product::with(['purchaseItems' => function ($q) {
            $q->select('product_id', 'quantity');
        }])->get();

        foreach ($products as $product) {
            $product->fifo_stock = $product->purchaseItems->sum('quantity');
        }
        $sale->load('items.product');
        return view('sales.edit', compact('sale', 'customers', 'products'));
    }

    public function update(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'customer_id'   => 'required|exists:customers,id',
            'date'          => 'required|date',
            'product_id.*'  => 'required|exists:products,id',
            'quantity.*'    => 'required|integer|min:1',
            'price.*'       => 'required|numeric|min:0',
            'discount.*'    => 'nullable|numeric|min:0|max:100',
            'tax.*'         => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            DB::transaction(function () use ($request, $sale) {
                // 1) Restore stock back from existing sale items into purchase_items (reverse of FIFO)
                foreach ($sale->items as $oldItem) {
                    $this->restoreToPurchaseItems($oldItem->product_id, $oldItem->quantity);
                }

                // 2) Remove old sale items
                $sale->items()->delete();

                // 3) Update sale main record
                $sale->update([
                    'customer_id'  => $request->customer_id,
                    'date'         => $request->date,
                    'total_amount' => 0,
                ]);

                $totalAmount = 0;

                // 4) Re-create items and allocate from purchase_items FIFO
                foreach ($request->product_id as $index => $productId) {
                    $quantity = (int) $request->quantity[$index];
                    $price = $request->price[$index];
                    $discount = $request->discount[$index] ?? 0;
                    $tax = $request->tax[$index] ?? 0;

                    // Check available qty across purchase_items (after restoration above)
                    $available = $this->getAvailableQtyFromPurchaseItems($productId);
                    if ($available < $quantity) {
                        throw new \Exception("Not enough stock for product Available: {$available}, Requested: {$quantity}");
                    }

                    // Allocate/decrement from purchase_items (FIFO)
                    $this->allocateFromPurchaseItems($productId, $quantity);

                    $baseTotal = $quantity * $price;
                    $discountAmount = ($discount / 100) * $baseTotal;
                    $afterDiscount = $baseTotal - $discountAmount;
                    $taxAmount = ($tax / 100) * $afterDiscount;
                    $lineTotal = $afterDiscount + $taxAmount;

                    SaleItem::create([
                        'sale_id'    => $sale->id,
                        'product_id' => $productId,
                        'quantity'   => $quantity,
                        'price'      => $price,
                        'discount'   => $discount,
                        'tax'        => $tax,
                    ]);

                    $totalAmount += $lineTotal;
                }

                $sale->total_amount = $totalAmount;
                $sale->save();
            });
        } catch (\Exception $e) {
            return Redirect::back()
                ->withInput()
                ->withErrors(['stock_error' => $e->getMessage()]);
        }

        return redirect()->route('sales.index')->with('success', 'Sale updated successfully.');
    }

    public function destroy(Sale $sale)
    {
        DB::transaction(function () use ($sale) {
            // Restore each sale item quantity back into purchase_items
            foreach ($sale->items as $item) {
                $this->restoreToPurchaseItems($item->product_id, $item->quantity);
                $item->delete();
            }

            $sale->delete();
        });

        return redirect()->route('sales.index')->with('success', 'Sale deleted successfully.');
    }

    /**
     * Sum of available quantity across purchase_items for given product.
     *
     * @param int $productId
     * @return int
     */
    protected function getAvailableQtyFromPurchaseItems($productId)
    {
        return (int) PurchaseItem::where('product_id', $productId)->sum('quantity');
    }

    /**
     * Allocate quantity from purchase_items using FIFO (oldest purchase_items first).
     * This reduces purchase_items.quantity as stock is consumed.
     *
     * @param int $productId
     * @param int $requiredQty
     * @return void
     * @throws \Exception
     */
    protected function allocateFromPurchaseItems($productId, $requiredQty)
    {
        $remaining = $requiredQty;

        // order by id ascending (oldest first). If you keep a purchase date column, you may prefer orderBy('created_at') or purchase date.
        $purchaseItems = PurchaseItem::where('product_id', $productId)
            ->where('quantity', '>', 0)
            ->orderBy('id', 'asc')
            ->lockForUpdate()
            ->get();

        foreach ($purchaseItems as $pItem) {
            if ($remaining <= 0) break;

            if ($pItem->quantity <= 0) continue;

            if ($pItem->quantity >= $remaining) {
                // consume part or all from this purchase item
                $pItem->quantity = $pItem->quantity - $remaining;
                $pItem->save();
                $remaining = 0;
                break;
            } else {
                // consume full purchase item and continue
                $remaining -= $pItem->quantity;
                $pItem->quantity = 0;
                $pItem->save();
            }
        }

        if ($remaining > 0) {
            // This should not happen because caller checked availability, but keep safe fallback.
            throw new \Exception("Unable to allocate required quantity ({$requiredQty}) for product ID {$productId}. Unallocated: {$remaining}");
        }
    }

    /**
     * Restore quantity back to purchase_items (reverse order: newest first).
     * This adds quantities back to purchase_items to restore available stock.
     *
     * @param int $productId
     * @param int $qtyToRestore
     * @return void
     * @throws \Exception
     */
    protected function restoreToPurchaseItems($productId, $qtyToRestore)
    {
        $remaining = $qtyToRestore;

        // Reverse order – add back to latest purchase items first.
        $purchaseItems = PurchaseItem::where('product_id', $productId)
            ->orderBy('id', 'desc')
            ->lockForUpdate()
            ->get();

        // If there are existing purchase_items, add back to them (newest first)
        foreach ($purchaseItems as $pItem) {
            if ($remaining <= 0) break;

            // We increment quantity on the purchase item.
            // This keeps totals consistent. It does NOT restore original per-purchase breakdown exactly
            // unless you implement an allocation ledger (recommended for precise traceability).
            $pItem->quantity = $pItem->quantity + $remaining;
            $pItem->save();
            $remaining = 0;
            break;
        }

        // If no purchase_items exist for product (shouldn't happen), create a fallback purchase_item
        if ($remaining > 0) {
            // Create a synthetic fallback purchase_item to hold restored qty
            PurchaseItem::create([
                'purchase_id' => null, // null: synthetic — adjust if you prefer a particular purchase_id
                'product_id'  => $productId,
                'quantity'    => $remaining,
                'price'       => 0,
                'discount'    => 0,
                'tax'         => 0,
            ]);
            $remaining = 0;
        }
    }

    public function exportInoviceCSV($id)
    {
        $sale = Sale::with(['items.product', 'customer'])->findOrFail($id);

        $filename = 'sale_invoice_' . $sale->id . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($sale) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['#', 'Product', 'Qty', 'Price', 'Discount (%)', 'Tax (%)', 'Subtotal']);

            $total = 0;
            foreach ($sale->items as $index => $item) {
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
        $filename = 'sale_report_' . date('Ymd_His') . '.csv';

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

            $sales = Sale::with('customer', 'items.product')->orderBy('date', 'desc')->get();

            foreach ($sales as $sale) {
                foreach ($sale->items as $item) {
                    $base = $item->quantity * $item->price;
                    $discountAmount = ($item->discount ?? 0) * $base / 100;
                    $taxable = $base - $discountAmount;
                    $taxAmount = ($item->tax ?? 0) * $taxable / 100;
                    $subtotal = $taxable + $taxAmount;

                    fputcsv($handle, [
                        $sale->id,
                        $sale->date->format('Y-m-d'),
                        $sale->supplier->name ?? '',
                        $item->product->name,
                        $item->quantity,
                        number_format($item->price, 2),
                        number_format($item->discount ?? 0, 2),
                        number_format($item->tax ?? 0, 2),
                        number_format($subtotal, 2),
                        number_format($sale->total_amount, 2),
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
