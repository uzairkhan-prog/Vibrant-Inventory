<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);
        $categoryId = $request->get('category_id'); // new filter

        // Load products with categories, optionally filter by category
        $productsQuery = Product::with('category');

        /* ================== SEARCH ================== */
        if ($request->filled('search')) {
            $search = $request->search;

            // 1. Normalize search: remove extra special chars except spaces
            $normalizedSearch = preg_replace('/[^A-Za-z0-9\s]/', ' ', $search);

            // 2. Split into words (keywords)
            $keywords = array_filter(explode(' ', $normalizedSearch));

            $productsQuery->where(function ($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $word = strtolower($word);
                    $q->where(function ($qq) use ($word) {
                        $qq->whereRaw("LOWER(REGEXP_REPLACE(name, '[^A-Za-z0-9]', '')) LIKE ?", ["%{$word}%"])
                            ->orWhereRaw("LOWER(REGEXP_REPLACE(packing, '[^A-Za-z0-9]', '')) LIKE ?", ["%{$word}%"])
                            ->orWhereRaw("LOWER(CAST(quantity AS CHAR)) LIKE ?", ["%{$word}%"])
                            ->orWhereRaw("LOWER(CAST(price_per_unit AS CHAR)) LIKE ?", ["%{$word}%"])
                            ->orWhereHas('category', function ($c) use ($word) {
                                $c->whereRaw("LOWER(REGEXP_REPLACE(name, '[^A-Za-z0-9]', '')) LIKE ?", ["%{$word}%"]);
                            });
                    });
                }
            });
        }
        /* ============================================ */

        if ($categoryId) {
            $productsQuery->where('category_id', $categoryId);
        }

        $products = $productsQuery->orderByDesc('created_at')->paginate($perPage)->withQueryString();

        // Calculate overall totals
        $totalQuery = Product::query();
        if ($categoryId) {
            $totalQuery->where('category_id', $categoryId);
        }
        $totalQuantity = (clone $totalQuery)->sum('quantity');
        $totalValue = (clone $totalQuery)
            ->selectRaw('SUM(quantity * price_per_unit) as total')
            ->value('total') ?? 0;

        // Aggregate data for chart: quantity and value by category
        $categoryAggregates = Product::leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->selectRaw("COALESCE(categories.name, 'Uncategorized') as category_name, SUM(products.quantity) as sum_qty, SUM(products.quantity * products.price_per_unit) as sum_value")
            ->groupBy('category_name')
            ->orderByDesc('sum_qty')
            ->get();

        $categoryLabels = $categoryAggregates->pluck('category_name')->toArray();
        $quantityData = $categoryAggregates->pluck('sum_qty')->map(fn($v) => (int)$v)->toArray();
        $valueData = $categoryAggregates->pluck('sum_value')->map(fn($v) => (float)$v)->toArray();

        // Get count of products per category
        $productsPerCategoryRaw = Product::leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->selectRaw("COALESCE(categories.name, 'Uncategorized') as category_name, COUNT(products.id) as prod_count")
            ->groupBy('category_name')
            ->get();

        $prodCountMap = $productsPerCategoryRaw->pluck('prod_count', 'category_name')->toArray();
        $productsPerCategory = array_map(fn($label) => $prodCountMap[$label] ?? 0, $categoryLabels);

        // Get all categories for dropdown
        $categories = Category::orderBy('name')->get();

        return view('products.index', compact(
            'products',
            'totalQuantity',
            'totalValue',
            'categoryLabels',
            'quantityData',
            'valueData',
            'productsPerCategory',
            'categories',
            'categoryId'
        ));
    }

    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'packing' => 'required|integer|min:1',
            'price_per_unit' => 'required|numeric',
            'quantity' => 'required|integer',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        Product::create($request->all());

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'packing' => 'required|integer|min:1',
            'price_per_unit' => 'required|numeric',
            'quantity' => 'required|integer',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $product->update($request->all());

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function show(Product $product)
    {
        // Load category relation for product
        $product->load('category');

        return view('products.show', compact('product'));
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    public function importCSV(Request $request)
    {
        try {
            if (!$request->hasFile('csv_file')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No file uploaded'
                ], 422);
            }

            $file = $request->file('csv_file');

            if (!$file->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file'
                ], 422);
            }

            $rows = array_map('str_getcsv', file($file->getRealPath()));

            if (count($rows) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'CSV file is empty'
                ], 422);
            }

            // Normalize headers
            $headers = array_map(fn($h) => strtolower(trim($h)), $rows[0]);
            unset($rows[0]);

            $requiredHeaders = ['name', 'packing', 'quantity', 'category', 'price'];

            foreach ($requiredHeaders as $col) {
                if (!in_array($col, $headers)) {
                    return response()->json([
                        'success' => false,
                        'message' => "Missing column: $col"
                    ], 422);
                }
            }

            $inserted = 0;

            foreach ($rows as $row) {

                if (count($row) !== count($headers)) {
                    continue;
                }

                $data = array_combine($headers, $row);

                // CATEGORY NAME → ID
                $categoryId = null;
                if (!empty($data['category'])) {
                    $category = Category::firstOrCreate([
                        'name' => trim($data['category'])
                    ]);
                    $categoryId = $category->id;
                }

                Product::create([
                    'name' => trim($data['name']),
                    'packing' => (int)$data['packing'],
                    'quantity' => (int)$data['quantity'],
                    'price_per_unit' => (float)$data['price'],
                    'category_id' => $categoryId,
                    'description' => null,
                ]);

                $inserted++;
            }

            return response()->json([
                'success' => true,
                'message' => "$inserted products imported successfully"
            ]);
        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => 'Import failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
