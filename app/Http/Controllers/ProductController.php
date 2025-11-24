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
        if ($categoryId) {
            $productsQuery->where('category_id', $categoryId);
        }
        $products = $productsQuery->paginate($perPage)->withQueryString();

        // Calculate overall totals
        $totalQuantity = Product::sum('quantity');
        $totalValue = Product::selectRaw('SUM(quantity * price_per_unit) as total')->value('total') ?? 0;

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
        $categories = \App\Models\Category::orderBy('name')->get();

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
            // 'quantity' => 'required|integer',
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
            // 'quantity' => 'required|integer',
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
        $file = $request->file('csv_file');
        $rows = array_map('str_getcsv', file($file->getRealPath()));

        $header = array_map('trim', $rows[0]); // CSV headers
        unset($rows[0]); // remove header row

        $inserted = 0;

        foreach ($rows as $row) {
            $data = array_combine($header, $row);

            Product::create([
                'name' => $data['name'] ?? null, // if missing -> null
                'packing' => $data['packing'] ?? 0,
                'price_per_unit' => is_numeric($data['price_per_unit'] ?? null) ? $data['price_per_unit'] : 0,
                // 'quantity' => is_numeric($data['quantity'] ?? null) ? $data['quantity'] : 0,
                'category_id' => null, // always null
                'description' => $data['description'] ?? null,
                'total' => (is_numeric($data['price_per_unit'] ?? null) ? $data['price_per_unit'] : 0)
                    * (is_numeric($data['quantity'] ?? null) ? $data['quantity'] : 0),
            ]);

            $inserted++;
        }

        return response()->json([
            'success' => true,
            'message' => "$inserted products imported successfully."
        ]);
    }
}
