<?php

namespace App\Http\Controllers;

use App\Models\PurchaseReturn;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurchaseReturnController extends Controller
{
    /* =============================
       INDEX
    ============================= */
    public function index(Request $request)
    {
        $perPage   = $request->get('per_page', 20);
        $search    = $request->get('search');
        $monthYear = $request->get('month_year');
        $fromDate  = $request->get('from_date');
        $toDate    = $request->get('to_date');

        $query = PurchaseReturn::with(['supplier', 'product'])
            ->orderBy('created_at', 'desc');

        /* ===============================
        DATE / MONTH FILTER
        =============================== */
        if ($fromDate || $toDate) {

            if ($fromDate && $toDate) {
                $query->whereDate('created_at', '>=', $fromDate)
                    ->whereDate('created_at', '<=', $toDate);
            } elseif ($fromDate) {
                $query->whereDate('created_at', '>=', $fromDate);
            } elseif ($toDate) {
                $query->whereDate('created_at', '<=', $toDate);
            }

            $monthTotal = (clone $query)->sum('amount_deducted');
            $monthYear  = 'custom';
        } elseif ($monthYear && $monthYear !== 'all') {

            [$year, $month] = explode('-', $monthYear);

            $query->whereYear('created_at', $year)
                ->whereMonth('created_at', $month);

            $monthTotal = (clone $query)->sum('amount_deducted');
        } elseif ($monthYear === 'all') {

            $monthTotal = PurchaseReturn::sum('amount_deducted');
        } else {

            // Default = current month
            $monthYear = Carbon::now()->format('Y-m');

            [$year, $month] = explode('-', $monthYear);

            $query->whereYear('created_at', $year)
                ->whereMonth('created_at', $month);

            $monthTotal = (clone $query)->sum('amount_deducted');
        }

        /* ===============================
        SEARCH FILTER
        =============================== */
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('purchase_id', 'like', "%{$search}%")
                    ->orWhere('amount_deducted', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($s) use ($search) {
                        $s->where('name', 'like', "%{$search}%")
                            ->orWhere('company_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('product', function ($p) use ($search) {
                        $p->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $returns = $query->paginate($perPage)->appends($request->all());

        $allTimeTotal = PurchaseReturn::sum('amount_deducted');

        $months = PurchaseReturn::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month")
            ->distinct()
            ->orderBy('month', 'desc')
            ->pluck('month');

        return view('purchase_returns.index', compact(
            'returns',
            'monthYear',
            'months',
            'allTimeTotal',
            'monthTotal',
            'search',
            'fromDate',
            'toDate'
        ));
    }

    /* =============================
       CREATE
    ============================= */
    public function create()
    {
        $suppliers = Supplier::whereHas('purchases')->select('id', 'name', 'company_name', 'balance')->get();
        return view('purchase_returns.create', compact('suppliers'));
    }

    /* =============================
       STORE
    ============================= */
    public function store(Request $request)
    {
        $this->validateReturn($request);

        try {
            DB::transaction(function () use ($request) {
                $this->applyReturn($request);
            });
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('purchase-returns.index')->with('success', 'Purchase return recorded successfully.');
    }

    /* =============================
       EDIT
    ============================= */
    public function edit(PurchaseReturn $purchase_return)
    {
        $purchase_return->load('product', 'purchase');
        $suppliers = Supplier::whereHas('purchases')->select('id', 'name', 'company_name', 'balance')->get();
        return view('purchase_returns.edit', compact('purchase_return', 'suppliers'));
    }

    /* =============================
       UPDATE
    ============================= */
    public function update(Request $request, PurchaseReturn $purchase_return)
    {
        $this->validateReturn($request);

        try {
            DB::transaction(function () use ($request, $purchase_return) {
                // Undo the previous return, then apply the new values
                $this->reverseReturn($purchase_return);
                $this->applyReturn($request, $purchase_return);
            });
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('purchase-returns.index')->with('success', 'Purchase return updated successfully.');
    }

    /* =============================
       DESTROY
    ============================= */
    public function destroy(PurchaseReturn $purchase_return)
    {
        try {
            DB::transaction(function () use ($purchase_return) {
                $this->reverseReturn($purchase_return);
                $purchase_return->delete();
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('purchase-returns.index')->with('success', 'Purchase return deleted.');
    }

    /* =============================
       AJAX: Purchased products of selected supplier
    ============================= */
    public function getSupplierProducts(Request $request, $supplier_id)
    {
        // On edit, the return being edited must not count against its own returnable qty
        $excludeId = $request->get('exclude');

        $items = PurchaseItem::with(['product', 'purchase'])
            ->whereHas('purchase', fn($q) => $q->where('supplier_id', $supplier_id))
            ->get();

        $returned = PurchaseReturn::where('supplier_id', $supplier_id)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->selectRaw('purchase_id, product_id, SUM(qty_return) as qty')
            ->groupBy('purchase_id', 'product_id')
            ->get()
            ->keyBy(fn($r) => $r->purchase_id . ':' . $r->product_id);

        // The return being edited is reversed before re-applying, so its qty is back in stock
        $editing = $excludeId ? PurchaseReturn::find($excludeId) : null;

        $products = $items->map(function ($item) use ($returned, $editing) {
            $key = $item->purchase_id . ':' . $item->product_id;
            $alreadyReturned = (int) ($returned[$key]->qty ?? 0);
            $stock = (int) ($item->product->quantity ?? 0);
            if ($editing && (int) $editing->product_id === (int) $item->product_id) {
                $stock += (int) $editing->qty_return;
            }

            return [
                'key'          => $key,
                'id'           => $item->product_id,
                'name'         => $item->product->name ?? '-',
                'packing'      => $item->product->packing ?? '',
                'purchase_id'  => $item->purchase_id,
                'date'         => $item->purchase->date ? Carbon::parse($item->purchase->date)->format('Y-m-d') : '',
                'purchased'    => (int) $item->quantity,
                'returned'     => $alreadyReturned,
                'returnable'   => max((int) $item->quantity - $alreadyReturned, 0),
                'stock'        => $stock,
                'unit_cost'    => round($this->unitCost($item), 2),
            ];
        })->values();

        return response()->json($products);
    }

    /* =============================
       HELPERS
    ============================= */
    private function validateReturn(Request $request)
    {
        $request->validate([
            'return_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'product_id'  => 'required|exists:products,id',
            'purchase_id' => 'required|exists:purchases,id',
            'qty_return'  => 'required|integer|min:1',
            'cut_amount'  => 'nullable|numeric|min:0',
        ]);
    }

    // Net cost per unit on the purchase invoice (after discount %, plus tax %)
    private function unitCost(PurchaseItem $item): float
    {
        $price    = (float) $item->price;
        $discount = (float) ($item->discount ?? 0);
        $tax      = (float) ($item->tax ?? 0);

        $afterDiscount = $price - ($price * $discount / 100);
        return $afterDiscount + ($afterDiscount * $tax / 100);
    }

    private function applyReturn(Request $request, ?PurchaseReturn $existing = null)
    {
        $qty = (int) $request->qty_return;
        $cut = (float) ($request->cut_amount ?? 0);

        $purchase = Purchase::lockForUpdate()->findOrFail($request->purchase_id);
        if ((int) $purchase->supplier_id !== (int) $request->supplier_id) {
            throw new \Exception("Purchase #{$purchase->id} does not belong to the selected supplier.");
        }

        $purchaseItem = PurchaseItem::where('purchase_id', $purchase->id)
            ->where('product_id', $request->product_id)
            ->lockForUpdate()
            ->first();
        if (!$purchaseItem) {
            throw new \Exception("This product is not on Purchase #{$purchase->id}.");
        }

        $product = Product::lockForUpdate()->findOrFail($request->product_id);

        // Can't return more than was purchased on this invoice (minus earlier returns)
        $alreadyReturned = PurchaseReturn::where('purchase_id', $purchase->id)
            ->where('product_id', $product->id)
            ->when($existing, fn($q) => $q->where('id', '!=', $existing->id))
            ->sum('qty_return');
        $returnable = $purchaseItem->quantity - $alreadyReturned;
        if ($qty > $returnable) {
            throw new \Exception("Only {$returnable} unit(s) of '{$product->name}' can be returned against Purchase #{$purchase->id}.");
        }

        // Can't send back stock that is no longer on hand
        if ($product->quantity < $qty) {
            throw new \Exception("Stock not available for {$product->name}. Available: {$product->quantity}");
        }

        $unitCost   = round($this->unitCost($purchaseItem), 2);
        $totalValue = round($qty * $unitCost, 2);
        if ($cut > $totalValue) {
            throw new \Exception("Deduction amount cannot exceed the return value (Rs {$totalValue}).");
        }
        $netCredit = round($totalValue - $cut, 2);

        // Decrease Product Stock
        $product->quantity -= $qty;
        $product->save();

        // Reduce Purchase Total
        $purchase->total_amount -= $netCredit;
        $purchase->save();

        // Reduce what we owe the Supplier
        $supplier = Supplier::lockForUpdate()->findOrFail($request->supplier_id);
        $supplier->balance -= $netCredit;
        $supplier->save();

        $data = [
            'supplier_id'        => $supplier->id,
            'product_id'         => $product->id,
            'purchase_id'        => $purchase->id,
            'packing'            => $request->packing,
            'qty_return'         => $qty,
            'unit_cost'          => $unitCost,
            'cut_amount'         => $cut,
            'amount_deducted'    => $netCredit,
            'total_after_return' => $supplier->balance,
            'created_at'         => $request->return_date,
            'updated_at'         => $request->return_date,
        ];

        $existing ? $existing->update($data) : PurchaseReturn::create($data);
    }

    private function reverseReturn(PurchaseReturn $purchaseReturn)
    {
        $product = Product::lockForUpdate()->find($purchaseReturn->product_id);
        if ($product) {
            $product->quantity += $purchaseReturn->qty_return;
            $product->save();
        }

        $purchase = Purchase::lockForUpdate()->find($purchaseReturn->purchase_id);
        if ($purchase) {
            $purchase->total_amount += $purchaseReturn->amount_deducted;
            $purchase->save();
        }

        $supplier = Supplier::lockForUpdate()->find($purchaseReturn->supplier_id);
        if ($supplier) {
            $supplier->balance += $purchaseReturn->amount_deducted;
            $supplier->save();
        }
    }
}
