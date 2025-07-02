<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Vibrant Inventory Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Vibrant Engineering - Inventory Dashboard</h1>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </div>

        <div class="row mt-4">
            <div class="col-md-3">
                <a href="{{ route('products.index') }}" class="btn btn-primary w-100 mb-2">Products</a>
                <a href="{{ route('categories.index') }}" class="btn btn-primary w-100 mb-2">Categories</a>
                <a href="{{ route('suppliers.index') }}" class="btn btn-primary w-100 mb-2">Suppliers</a>
                <a href="{{ route('customers.index') }}" class="btn btn-primary w-100 mb-2">Customers</a>
                <a href="{{ route('purchases.index') }}" class="btn btn-success w-100 mb-2">Purchases</a>
                <a href="{{ route('sales.index') }}" class="btn btn-success w-100 mb-2">Sales</a>
                <a href="{{ route('expenses.index') }}" class="btn btn-warning w-100 mb-2">Expenses</a>
                <a href="{{ route('ledgers.index') }}" class="btn btn-info w-100 mb-2">Ledger</a>
                <a href="{{ route('assets.index') }}" class="btn btn-secondary w-100 mb-2">Assets</a>
            </div>

            <div class="col-md-9">
                <h2>Analytics (Coming soon...)</h2>
                <!-- You can add ApexCharts here -->
            </div>
        </div>
    </div>
</body>

</html>