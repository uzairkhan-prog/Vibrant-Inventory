<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Expense;
use App\Models\Ledger;
use App\Models\Asset;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1️⃣ User
        User::firstOrCreate(
            ['email' => 'admin@vibrantengineering.pk'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'), // default password
            ]
        );

        // 2️⃣ Categories
        $categories = [
            'Packaging & Sealing' => 'All types of packaging and sealing machines',
            'Filling Machines' => 'Automatic and manual filling machines',
            'Printing Solutions' => 'Coding, printing, labeling',
            'Processing Machines' => 'Processing equipment for industries',
        ];

        $categoryIds = [];

        foreach ($categories as $name => $description) {
            $category = Category::firstOrCreate(
                ['name' => $name],
                ['description' => $description]
            );
            $categoryIds[$name] = $category->id;
        }

        // 3️⃣ Products
        $products = [
            [
                'category_id' => $categoryIds['Packaging & Sealing'],
                'name' => 'Shrink Wrapping Machine',
                'sku' => 'PACK-001',
                'description' => 'Automatic shrink wrapping machine',
                'quantity' => 20,
                'price_per_unit' => 250000,
            ],
            [
                'category_id' => $categoryIds['Filling Machines'],
                'name' => 'Liquid Filling Machine',
                'sku' => 'FILL-001',
                'description' => '2-nozzle liquid filling machine',
                'quantity' => 10,
                'price_per_unit' => 180000,
            ],
            [
                'category_id' => $categoryIds['Printing Solutions'],
                'name' => 'Inkjet Date Coder',
                'sku' => 'PRINT-001',
                'description' => 'High resolution inkjet date coder',
                'quantity' => 8,
                'price_per_unit' => 95000,
            ],
            [
                'category_id' => $categoryIds['Processing Machines'],
                'name' => 'Powder Mixer',
                'sku' => 'PROC-001',
                'description' => 'Industrial powder mixer',
                'quantity' => 5,
                'price_per_unit' => 320000,
            ],
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(
                ['sku' => $productData['sku']],
                $productData
            );
        }

        // 4️⃣ Suppliers
        $supplier1 = Supplier::firstOrCreate(
            ['email' => 'supplier1@vibrantengineering.pk'],
            [
                'name' => 'ABC Engineering Suppliers',
                'phone' => '03001234567',
                'address' => 'Karachi Industrial Area',
                'balance' => 0,
            ]
        );

        // 5️⃣ Customers
        $customer1 = Customer::firstOrCreate(
            ['email' => 'customer1@pakfoods.pk'],
            [
                'name' => 'Pak Foods Pvt Ltd',
                'phone' => '03111234567',
                'address' => 'Sundar Industrial Estate, Lahore',
                'balance' => 0,
            ]
        );

        // 6️⃣ Purchase
        $purchase = Purchase::create([
            'supplier_id' => $supplier1->id,
            'total_amount' => 400000,
            'date' => Carbon::now(),
        ]);

        PurchaseItem::create([
            'purchase_id' => $purchase->id,
            'product_id' => Product::where('sku', 'PACK-001')->first()->id,
            'quantity' => 5,
            'price' => 250000,
        ]);

        // 7️⃣ Sale
        $sale = Sale::create([
            'customer_id' => $customer1->id,
            'total_amount' => 350000,
            'date' => Carbon::now(),
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => Product::where('sku', 'FILL-001')->first()->id,
            'quantity' => 2,
            'price' => 180000,
        ]);

        // 8️⃣ Expenses
        Expense::create([
            'title' => 'Office Rent',
            'amount' => 50000,
            'category' => 'Rent',
            'date' => Carbon::now(),
        ]);

        Expense::create([
            'title' => 'Utility Bills',
            'amount' => 15000,
            'category' => 'Utilities',
            'date' => Carbon::now(),
        ]);

        // 9️⃣ Assets
        Asset::create([
            'title' => 'Warehouse Forklift',
            'value' => 750000,
            'date' => Carbon::now(),
        ]);

        // 🔟 Ledger Example
        Ledger::create([
            'reference_id' => $customer1->id,
            'type' => 'customer',
            'balance' => 100000,
        ]);

        Ledger::create([
            'reference_id' => $supplier1->id,
            'type' => 'supplier',
            'balance' => -200000,
        ]);
    }
}
