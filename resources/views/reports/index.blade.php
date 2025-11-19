@extends('layouts.app')

@section('content')
<div class="container py-5">

    <!-- Filters -->
    <div class="card mb-4 shadow">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.index') }}" class="row g-3" id="reportsFilterForm">

                <!-- Report Type -->
                <div class="col-md-3">
                    <label class="form-label">Report Type</label>
                    <select name="report_type" id="report_type" class="form-select">
                        <option value="all" {{ $reportType == 'all' ? 'selected' : '' }}>All Reports</option>
                        <option value="products" {{ $reportType == 'products' ? 'selected' : '' }}>Products</option>
                        <option value="purchases" {{ $reportType == 'purchases' ? 'selected' : '' }}>Purchases</option>
                        <option value="sales" {{ $reportType == 'sales' ? 'selected' : '' }}>Sales</option>
                        <option value="expenses" {{ $reportType == 'expenses' ? 'selected' : '' }}>Expenses</option>
                        <option value="customers" {{ $reportType == 'customers' ? 'selected' : '' }}>Customers</option>
                        <option value="suppliers" {{ $reportType == 'suppliers' ? 'selected' : '' }}>Suppliers</option>
                        <option value="assets" {{ $reportType == 'assets' ? 'selected' : '' }}>Assets</option>
                    </select>
                </div>

                <!-- Product -->
                <div class="col-md-3" id="productDropdownWrapper" style="display:none;">
                    <label class="form-label">Product</label>
                    <select name="product_id" id="product_id" class="form-select">
                        <option value="">All Products</option>
                        @foreach($productsList as $p)
                        <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Customer -->
                <div class="col-md-3" id="customerDropdownWrapper" style="display:none;">
                    <label class="form-label">Customer</label>
                    <select name="customer_id" id="customer_id" class="form-select">
                        <option value="">All Customers</option>
                        @foreach($customersList as $c)
                        <option value="{{ $c->id }}" {{ $customerId == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Supplier -->
                <div class="col-md-3" id="supplierDropdownWrapper" style="display:none;">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" id="supplier_id" class="form-select">
                        <option value="">All Suppliers</option>
                        @foreach($suppliersList as $s)
                        <option value="{{ $s->id }}" {{ $supplierId == $s->id ? 'selected' : '' }}>
                            {{ $s->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Dates -->
                <div class="col-md-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>

                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                    <a href="{{ route('reports.index') }}" class="btn btn-danger w-100">Clear</a>
                </div>

            </form>
        </div>
    </div>

    <!-- Report Summary Cards -->
    <!-- <div class="row g-4 mb-4">
        @if($reportType == 'purchases' || $reportType == 'all')
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h6 class="text-muted">Total Purchases</h6>
                    <h4 class="fw-bold">{{ number_format($totalPurchases, 2) }}</h4>
                    <button class="btn btn-sm btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#purchasesModal">Export</button>
                </div>
            </div>
        </div>
        @endif

        @if($reportType == 'sales' || $reportType == 'all')
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h6 class="text-muted">Total Sales</h6>
                    <h4 class="fw-bold">{{ number_format($totalSales, 2) }}</h4>
                    <button class="btn btn-sm btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#salesModal">Export</button>
                </div>
            </div>
        </div>
        @endif

        @if($reportType == 'expenses' || $reportType == 'all')
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h6 class="text-muted">Total Expenses</h6>
                    <h4 class="fw-bold">{{ number_format($totalExpenses, 2) }}</h4>
                    <button class="btn btn-sm btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#expensesModal">Export</button>
                </div>
            </div>
        </div>
        @endif
    </div> -->

    <!-- Products Ledger Report -->
    @if($reportType == 'products' || $reportType == 'all')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
            <h5>Products Report</h5>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#productsModal">Export</button>
        </div>
        <div class="card-body">
            @forelse($productsLedger as $product)
            <div class="mb-5">
                <h6 class="fw-bold">
                    {{ $product->name }}
                    <span class="text-muted">(Stock: {{ $product->quantity }})</span>
                    <span class="badge bg-info ms-2">{{ $product->category->name ?? 'Uncategorized' }}</span>
                </h6>

                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-start p-2">Date</th>
                            <th class="text-start p-2">Type</th>
                            <th class="text-start p-2">Invoice</th>
                            <th class="text-start p-2">Category</th> {{-- ✅ Added --}}
                            <th class="text-start p-2">Qty</th>
                            <th class="text-start p-2">Price</th>
                            <th class="text-start p-2">Total</th>
                            <th class="text-end p-2">Running Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $runningStock = 0;
                        $hasRows = false;
                        @endphp

                        {{-- Purchases --}}
                        @foreach($product->purchaseItems as $item)
                        @php
                        $hasRows = true;
                        $runningStock += $item->quantity;
                        $lineTotal = $item->quantity * $item->price;
                        @endphp
                        <tr>
                            <td class="text-start p-2">{{ $item->purchase->created_at->format('Y-m-d') }}</td>
                            <td class="text-start p-2"><span class="badge bg-success">Purchase</span></td>
                            <td class="text-start p-2">#{{ $item->purchase->id }}</td>
                            <td class="text-start p-2">{{ $item->product->category->name ?? 'N/A' }}</td> {{-- ✅ --}}
                            <td class="text-start p-2">+{{ $item->quantity }}</td>
                            <td class="text-start p-2">{{ number_format($item->price,2) }}</td>
                            <td class="text-start p-2">{{ number_format($lineTotal,2) }}</td>
                            <td class="text-end p-2 fw-bold">{{ $runningStock }}</td>
                        </tr>
                        @endforeach

                        {{-- Sales --}}
                        @foreach($product->saleItems as $item)
                        @php
                        $hasRows = true;
                        $runningStock -= $item->quantity;
                        $lineTotal = $item->quantity * $item->price;
                        @endphp
                        <tr>
                            <td class="text-start p-2">{{ $item->sale->created_at->format('Y-m-d') }}</td>
                            <td class="text-start p-2"><span class="badge bg-danger">Sale</span></td>
                            <td class="text-start p-2">#{{ $item->sale->id }}</td>
                            <td class="text-start p-2">{{ $item->product->category->name ?? 'N/A' }}</td> {{-- ✅ --}}
                            <td class="text-start p-2">-{{ $item->quantity }}</td>
                            <td class="text-start p-2">{{ number_format($item->price,2) }}</td>
                            <td class="text-start p-2">{{ number_format($lineTotal,2) }}</td>
                            <td class="text-end p-2 fw-bold">{{ $runningStock }}</td>
                        </tr>
                        @endforeach

                        {{-- If no records --}}
                        @unless($hasRows)
                        <tr>
                            <td colspan="8" class="text-center text-muted">No records found for this product.</td>
                        </tr>
                        @endunless
                    </tbody>
                </table>
            </div>
            @empty
            <div class="alert alert-warning">No products found.</div>
            @endforelse

            {{-- Pagination --}}
            @if ($productsLedger instanceof \Illuminate\Contracts\Pagination\Paginator && $productsLedger->hasPages())
            <div class="d-flex justify-content-center">
                {!! $productsLedger->appends(request()->all())->links('pagination::bootstrap-5') !!}
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Purchases Report -->
    @if($reportType == 'purchases' || $reportType == 'all')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
            <h5>Purchases Report</h5>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#purchasesModal">Export</button>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th class="text-start p-2">ID</th>
                        <th class="text-start p-2">Supplier</th>
                        <th class="text-start p-2">Product</th>
                        <th class="text-start p-2">Category</th>
                        <th class="text-start p-2">Quantity</th>
                        <th class="text-start p-2">Unit Price</th>
                        <th class="text-start p-2">Discount</th>
                        <th class="text-start p-2">Tax</th>
                        <th class="text-start p-2">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @php $purchaseRow = 1; @endphp
                    @forelse($purchases as $purchase)
                    @foreach($purchase->items as $item)
                    <tr>
                        <td class="text-start p-2">{{ $purchaseRow++ }}</td>
                        <td class="text-start p-2">{{ $purchase->supplier->name ?? 'N/A' }}</td>
                        <td class="text-start p-2">{{ $item->product->name }}</td>
                        <td class="text-start p-2">{{ $item->product->category->name ?? 'N/A' }}</td>
                        <td class="text-start p-2">{{ $item->quantity }}</td>
                        <td class="text-start p-2">{{ number_format($item->price, 2) }}</td>
                        <td class="text-start p-2">{{ number_format($item->discount, 2) }}</td>
                        <td class="text-start p-2">{{ number_format($item->tax, 2) }}</td>
                        <td class="text-start p-2">{{ number_format(($item->quantity * $item->price) - $item->discount + $item->tax, 2) }}</td>
                    </tr>
                    @endforeach
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">No purchases found.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="table-total">
                        <td colspan="8" class="text-end p-2">Total Purchases:</td>
                        <td class="text-start p-2">{{ number_format($totalPurchases, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
            @if ($purchases->hasPages())
            <div class="d-flex justify-content-center">
                {!! $purchases->appends(request()->all())->links('pagination::bootstrap-5') !!}
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Sales Report -->
    @if($reportType == 'sales' || $reportType == 'all')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
            <h5>Sales Report</h5>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#salesModal">Export</button>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th class="text-start p-2">ID</th>
                        <th class="text-start p-2">Customer</th>
                        <th class="text-start p-2">Product</th>
                        <th class="text-start p-2">Category</th>
                        <th class="text-start p-2">Quantity</th>
                        <th class="text-start p-2">Unit Price</th>
                        <th class="text-start p-2">Discount</th>
                        <th class="text-start p-2">Tax</th>
                        <th class="text-start p-2">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @php $saleRow = 1; @endphp
                    @forelse($sales as $sale)
                    @foreach($sale->items as $item)
                    <tr>
                        <td class="text-start p-2">{{ $saleRow++ }}</td>
                        <td class="text-start p-2">{{ $sale->customer->name ?? 'N/A' }}</td>
                        <td class="text-start p-2">{{ $item->product->name }}</td>
                        <td class="text-start p-2">{{ $item->product->category->name ?? 'N/A' }}</td>
                        <td class="text-start p-2">{{ $item->quantity }}</td>
                        <td class="text-start p-2">{{ number_format($item->price, 2) }}</td>
                        <td class="text-start p-2">{{ number_format($item->discount, 2) }}</td>
                        <td class="text-start p-2">{{ number_format($item->tax, 2) }}</td>
                        <td class="text-start p-2">{{ number_format(($item->quantity * $item->price) - $item->discount + $item->tax, 2) }}</td>
                    </tr>
                    @endforeach
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">No sales found.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="table-total">
                        <td colspan="8" class="text-end p-2">Total Sales:</td>
                        <td class="text-start p-2">{{ number_format($totalSales, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
            @if ($sales->hasPages())
            <div class="d-flex justify-content-center">
                {!! $sales->appends(request()->all())->links('pagination::bootstrap-5') !!}
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Sale Return Report -->
    {{-- @if($reportType == 'returns' || $reportType == 'all') --}}
    <!-- <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
            <h5>Sale Returns Report</h5>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#returnsModal">Export</button>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th class="text-start p-2">ID</th>
                        <th class="text-start p-2">Customer</th>
                        <th class="text-start p-2">Product</th>
                        <th class="text-start p-2">Quantity</th>
                        <th class="text-start p-2">Unit Price</th>
                        <th class="text-start p-2">Tax</th>
                        <th class="text-start p-2">Discount</th>
                        <th class="text-start p-2">Amount Deducted</th>
                    </tr>
                </thead>
                <tbody>
                    @php $returnRow = 1; @endphp
                    @forelse($saleReturns as $return)
                    @foreach($return->items as $item)
                    <tr>
                        <td class="text-start p-2">{{ $returnRow++ }}</td>
                        <td class="text-start p-2">{{ $return->sale->customer->name ?? 'N/A' }}</td>
                        <td class="text-start p-2">{{ $item->product->name }}</td>
                        <td class="text-start p-2">{{ $item->quantity }}</td>
                        <td class="text-start p-2">{{ number_format($item->price,2) }}</td>
                        <td class="text-start p-2">{{ number_format($item->tax,2) }}</td>
                        <td class="text-start p-2">{{ number_format($item->discount,2) }}</td>
                        <td class="text-start p-2">{{ number_format($return->amount_deducted,2) }}</td>
                    </tr>
                    @endforeach
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">No sale returns found.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="table-total">
                        <td colspan="7" class="text-end p-2">Total Sale Returns:</td>
                        <td class="text-start p-2">{{ number_format($totalSaleReturns, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
            @if ($saleReturns->hasPages())
            <div class="d-flex justify-content-center">
                {!! $saleReturns->appends(request()->all())->links('pagination::bootstrap-5') !!}
            </div>
            @endif
        </div>
    </div> -->
    {{-- @endif --}}

    <!-- Expenses Report -->
    @if($reportType == 'expenses' || $reportType == 'all')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
            <h5>Expenses Report</h5>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#expensesModal">Export</button>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th class="text-start p-2">ID</th>
                        <th class="text-start p-2">Date</th>
                        <th class="text-start p-2">Expense Name</th>
                        <th class="text-start p-2">Description</th>
                        <th class="text-start p-2">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @php $expenseRow = 1; @endphp
                    @forelse($expenses as $expense)
                    <tr>
                        <td class="text-start p-2">{{ $expenseRow++ }}</td>
                        <td class="text-start p-2">{{ $expense->created_at->format('Y-m-d') }}</td>
                        <td class="text-start p-2">{{ $expense->expenseName->name ?? 'N/A' }}</td>
                        <td class="text-start p-2">{{ $expense->description }}</td>
                        <td class="text-start p-2">{{ number_format($expense->amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No expenses found.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="table-total">
                        <td colspan="4" class="text-end p-2">Total Expenses:</td>
                        <td class="text-start p-2">{{ number_format($totalExpenses, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
            @if ($expenses->hasPages())
            <div class="d-flex justify-content-center">
                {!! $expenses->appends(request()->all())->links('pagination::bootstrap-5') !!}
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Customers Report -->
    @if($reportType == 'customers' || $reportType == 'all')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
            <h5>Customers Report</h5>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#customersModal">Export</button>
        </div>
        <div class="card-body">
            @forelse($customersLedger as $customer)
            <div class="mb-5">
                <h6 class="fw-bold">
                    {{ $customer->name }}
                    <span class="text-muted">(Closing Balance: {{ number_format($customer->balance,2) }})</span>
                </h6>

                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-start p-2">Date</th>
                            <th class="text-start p-2">Type</th>
                            <th class="text-start p-2">Invoice/Receipt</th>
                            <th class="text-start p-2">Product</th>
                            <th class="text-start p-2">Qty</th>
                            <th class="text-start p-2">Price</th>
                            <th class="text-start p-2">Tax</th>
                            <th class="text-start p-2">Discount</th>
                            <th class="text-end p-2">Debit (+)</th>
                            <th class="text-end p-2">Credit (-)</th>
                            <th class="text-end p-2">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $runningBalance = 0;
                        $hasRows = false;
                        $totalDebit = 0;
                        $totalCredit = 0;
                        @endphp

                        {{-- Sales --}}
                        @foreach($customer->sales as $sale)
                        @foreach($sale->items as $item)
                        @php
                        $hasRows = true;
                        $lineTotal = ($item->quantity * $item->price) + $sale->tax - $sale->discount;
                        $runningBalance += $lineTotal;
                        $totalDebit += $lineTotal;
                        @endphp
                        <tr>
                            <td class="text-start p-2">{{ $sale->created_at->format('Y-m-d') }}</td>
                            <td class="text-start p-2"><span class="badge bg-success">Sale</span></td>
                            <td class="text-start p-2">Invoice #{{ $sale->id }}</td>
                            <td class="text-start p-2">{{ $item->product->name }}</td>
                            <td class="text-start p-2">{{ $item->quantity }}</td>
                            <td class="text-start p-2">{{ number_format($item->price,2) }}</td>
                            <td class="text-start p-2">{{ number_format($sale->tax,2) }}</td>
                            <td class="text-start p-2">{{ number_format($sale->discount,2) }}</td>
                            <td class="text-end p-2 text-success fw-bold">+{{ number_format($lineTotal,2) }}</td>
                            <td class="text-end p-2">-</td>
                            <td class="text-end p-2 fw-bold">{{ number_format($runningBalance,2) }}</td>
                        </tr>
                        @endforeach
                        @endforeach

                        {{-- Payments --}}
                        @foreach($customer->payments as $payment)
                        @php
                        $hasRows = true;
                        $runningBalance -= $payment->amount;
                        $totalCredit += $payment->amount;
                        @endphp
                        <tr>
                            <td class="text-start p-2">{{ $payment->created_at->format('Y-m-d') }}</td>
                            <td class="text-start p-2"><span class="badge bg-primary">Payment</span></td>
                            <td class="text-start p-2">Receipt #{{ $payment->id }}</td>
                            <td class="text-start p-2">-</td>
                            <td class="text-start p-2">-</td>
                            <td class="text-start p-2">-</td>
                            <td class="text-start p-2">-</td>
                            <td class="text-start p-2">-</td>
                            <td class="text-end p-2">-</td>
                            <td class="text-end p-2 text-danger fw-bold">-{{ number_format($payment->amount,2) }}</td>
                            <td class="text-end p-2 fw-bold">{{ number_format($runningBalance,2) }}</td>
                        </tr>
                        @endforeach

                        {{-- If no records --}}
                        @unless($hasRows)
                        <tr>
                            <td colspan="11" class="text-center text-muted">No records found for this customer.</td>
                        </tr>
                        @endunless
                    </tbody>

                    {{-- Totals Footer --}}
                    @if($hasRows)
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="8" class="text-end p-2">Totals:</td>
                            <td class="text-end p-2 text-success">+{{ number_format($totalDebit,2) }}</td>
                            <td class="text-end p-2 text-danger">-{{ number_format($totalCredit,2) }}</td>
                            <td class="text-end p-2">{{ number_format($runningBalance,2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
            @empty
            <div class="alert alert-warning">No customers found.</div>
            @endforelse

            {{-- Pagination --}}
            @if ($customersLedger->hasPages())
            <div class="d-flex justify-content-center">
                {!! $customersLedger->appends(request()->all())->links('pagination::bootstrap-5') !!}
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Suppliers Report -->
    @if($reportType == 'suppliers' || $reportType == 'all')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
            <h5>Suppliers Report</h5>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#suppliersModal">Export</button>
        </div>
        <div class="card-body">
            @forelse($suppliersLedger as $supplier)
            <div class="mb-5">
                <h6 class="fw-bold">
                    {{ $supplier->name }}
                    <span class="text-muted">(Closing Balance: {{ number_format($supplier->balance,2) }})</span>
                </h6>

                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-start p-2">Date</th>
                            <th class="text-start p-2">Type</th>
                            <th class="text-start p-2">Invoice/Receipt</th>
                            <th class="text-start p-2">Product</th>
                            <th class="text-start p-2">Qty</th>
                            <th class="text-start p-2">Price</th>
                            <th class="text-start p-2">Tax</th>
                            <th class="text-start p-2">Discount</th>
                            <th class="text-end p-2">Debit (+)</th>
                            <th class="text-end p-2">Credit (-)</th>
                            <th class="text-end p-2">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $runningBalance = 0;
                        $hasRows = false;
                        $totalDebit = 0;
                        $totalCredit = 0;
                        @endphp

                        {{-- Purchases --}}
                        @foreach($supplier->purchases as $purchase)
                        @foreach($purchase->items as $item)
                        @php
                        $hasRows = true;
                        $lineTotal = ($item->quantity * $item->price) - $item->discount + $item->tax;
                        $runningBalance += $lineTotal;
                        $totalDebit += $lineTotal;
                        @endphp
                        <tr>
                            <td class="text-start p-2">{{ $purchase->date }}</td>
                            <td class="text-start p-2"><span class="badge bg-success">Purchase</span></td>
                            <td class="text-start p-2">Invoice #{{ $purchase->id }}</td>
                            <td class="text-start p-2">{{ $item->product->name }}</td>
                            <td class="text-start p-2">{{ $item->quantity }}</td>
                            <td class="text-start p-2">{{ number_format($item->price,2) }}</td>
                            <td class="text-start p-2">{{ number_format($item->tax,2) }}</td>
                            <td class="text-start p-2">{{ number_format($item->discount,2) }}</td>
                            <td class="text-end p-2 text-success fw-bold">+{{ number_format($lineTotal,2) }}</td>
                            <td class="text-end p-2">-</td>
                            <td class="text-end p-2 fw-bold">{{ number_format($runningBalance,2) }}</td>
                        </tr>
                        @endforeach
                        @endforeach

                        {{-- Payments --}}
                        @foreach($supplier->payments as $payment)
                        @php
                        $hasRows = true;
                        $runningBalance -= $payment->amount;
                        $totalCredit += $payment->amount;
                        @endphp
                        <tr>
                            <td class="text-start p-2">{{ $payment->created_at->format('Y-m-d') }}</td>
                            <td class="text-start p-2"><span class="badge bg-primary">Payment</span></td>
                            <td class="text-start p-2">Receipt #{{ $payment->id }}</td>
                            <td class="text-start p-2">-</td>
                            <td class="text-start p-2">-</td>
                            <td class="text-start p-2">-</td>
                            <td class="text-start p-2">-</td>
                            <td class="text-start p-2">-</td>
                            <td class="text-end p-2">-</td>
                            <td class="text-end p-2 text-danger fw-bold">-{{ number_format($payment->amount,2) }}</td>
                            <td class="text-end p-2 fw-bold">{{ number_format($runningBalance,2) }}</td>
                        </tr>
                        @endforeach

                        {{-- If no records --}}
                        @unless($hasRows)
                        <tr>
                            <td colspan="11" class="text-center text-muted">No records found for this supplier.</td>
                        </tr>
                        @endunless
                    </tbody>

                    {{-- Totals Footer --}}
                    @if($hasRows)
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="8" class="text-end p-2">Totals:</td>
                            <td class="text-end p-2 text-success">+{{ number_format($totalDebit,2) }}</td>
                            <td class="text-end p-2 text-danger">-{{ number_format($totalCredit,2) }}</td>
                            <td class="text-end p-2">{{ number_format($runningBalance,2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
            @empty
            <div class="alert alert-warning">No suppliers found.</div>
            @endforelse

            {{-- Pagination --}}
            @if ($suppliersLedger->hasPages())
            <div class="d-flex justify-content-center">
                {!! $suppliersLedger->appends(request()->all())->links('pagination::bootstrap-5') !!}
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Assets Report -->
    @if($reportType == 'assets' || $reportType == 'all')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
            <h5>Assets Report</h5>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#assetsModal">Export</button>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th class="text-start p-2">ID</th>
                        <th class="text-start p-2">Title</th>
                        <th class="text-start p-2">Date</th>
                        <th class="text-start p-2">Value</th>
                    </tr>
                </thead>
                <tbody>
                    @php $assetRow = 1; @endphp
                    @forelse($assetsLedger as $asset)
                    <tr>
                        <td class="text-start p-2">{{ $assetRow++ }}</td>
                        <td class="text-start p-2">{{ $asset->title }}</td>
                        <td class="text-start p-2">{{ $asset->date }}</td>
                        <td class="text-start p-2">{{ number_format($asset->value, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No assets found.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="table-total">
                        <td colspan="3" class="text-end p-2">Total Assets Value:</td>
                        <td class="text-start p-2">{{ number_format($totalAssets, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
            @if ($assetsLedger->hasPages())
            <div class="d-flex justify-content-center">
                {!! $assetsLedger->appends(request()->all())->links('pagination::bootstrap-5') !!}
            </div>
            @endif
        </div>
    </div>
    @endif

</div>

@include('reports.modals')

<script>
    document.addEventListener("DOMContentLoaded", function() {

        const reportType = document.getElementById('report_type');
        const customerDD = document.getElementById('customerDropdownWrapper');
        const supplierDD = document.getElementById('supplierDropdownWrapper');
        const customer = document.getElementById('customer_id');
        const supplier = document.getElementById('supplier_id');

        function updateUI() {

            // Hide both first
            customerDD.style.display = "none";
            supplierDD.style.display = "none";

            let rt = reportType.value;
            let hasCustomer = customer.value !== "";
            let hasSupplier = supplier.value !== "";

            // 🔥 PRIORITY 1: If customer selected → show only customer
            if (hasCustomer) {
                customerDD.style.display = "block";
                supplier.value = ""; // reset supplier
                return;
            }

            // 🔥 PRIORITY 2: If supplier selected → show only supplier
            if (hasSupplier) {
                supplierDD.style.display = "block";
                customer.value = ""; // reset customer
                return;
            }

            // 🔥 PRIORITY 3: Show based on Report Type
            switch (rt) {

                case "customers":
                case "sales":
                    customerDD.style.display = "block";
                    break;

                case "suppliers":
                case "purchases":
                    supplierDD.style.display = "block";
                    break;
            }
        }

        // When selecting customer → hide supplier
        customer.addEventListener("change", () => {
            supplier.value = "";
            updateUI();
        });

        // When selecting supplier → hide customer
        supplier.addEventListener("change", () => {
            customer.value = "";
            updateUI();
        });

        // When changing report type
        reportType.addEventListener("change", () => {
            customer.value = "";
            supplier.value = "";
            updateUI();
        });

        updateUI(); // run on load
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const reportType = document.getElementById('report_type');
        const productDD = document.getElementById('productDropdownWrapper');

        function toggleProductDropdown() {
            if (reportType.value === 'products') {
                productDD.style.display = "block";
            } else {
                productDD.style.display = "none";
            }
        }

        reportType.addEventListener('change', toggleProductDropdown);
        toggleProductDropdown(); // initial load
    });
</script>

@endsection