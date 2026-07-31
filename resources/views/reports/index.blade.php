@extends('layouts.app')

@section('content')

<div id="reportContent">

    <!-- Filters -->
    <div class="card mb-3 shadow">
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
                        <option value="agents" {{ $reportType == 'agents' ? 'selected' : '' }}>Agents</option>
                        <option value="assets" {{ $reportType == 'assets' ? 'selected' : '' }}>Assets</option>
                    </select>
                </div>

                <!-- Product -->
                <div class="col-md-3" id="productDropdownWrapper" style="display:none;">
                    <label class="form-label">Product</label>
                    <select name="product_id" id="product_id" class="form-select select2">
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
                    <select name="customer_id" id="customer_id" class="form-select select2">
                        <option value="">All Customers</option>
                        @foreach($customersList as $c)
                        <option value="{{ $c->id }}" {{ $customerId == $c->id ? 'selected' : '' }}>
                            {{ $c->company_name }} ( {{ $c->name }} )
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Supplier -->
                <div class="col-md-3" id="supplierDropdownWrapper" style="display:none;">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" id="supplier_id" class="form-select select2">
                        <option value="">All Suppliers</option>
                        @foreach($suppliersList as $s)
                        <option value="{{ $s->id }}" {{ $supplierId == $s->id ? 'selected' : '' }}>
                            {{ $s->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Agent -->
                <div class="col-md-3" id="agentDropdownWrapper" style="display:none;">
                    <label class="form-label">Agent</label>
                    <select name="agent_id" id="agent_id" class="form-select select2">
                        <option value="">All Agents</option>
                        @foreach($agentsList as $a)
                        <option value="{{ $a->id }}" {{ $agentId == $a->id ? 'selected' : '' }}>
                            {{ $a->name }}
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
                    <button type="submit" class="btn btn-primary w-100">Search</button>
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
        <div class="card-header d-flex justify-content-between align-items-center bg-light py-3">
            <h5>Products Report</h5>
            <div class="d-flex align-items-center gap-2">
                <form method="GET" class="d-flex align-items-center gap-2 m-0">
                    <select name="product_type"
                        class="form-select form-select-sm shadow-sm border-primary"
                        style="min-width: 150px;"
                        onchange="this.form.submit()">

                        <option value="">All Types</option>
                        <option value="purchases" {{ request('product_type')=='purchases'?'selected':'' }}>Purchases</option>
                        <option value="sales" {{ request('product_type')=='sales'?'selected':'' }}>Sales</option>
                    </select>
                    <input type="hidden" name="report_type" value="{{ $reportType }}">
                    <input type="hidden" name="product_id" value="{{ request('product_id') }}">
                    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                </form>
                <button class="btn btn-primary btn-sm shadow-sm px-3"
                    data-bs-toggle="modal"
                    data-bs-target="#productsModal">
                    Export
                </button>
            </div>
        </div>

        <div class="card-body table-responsive">
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
                        <td class="text-start p-2">{{ $item->product->name ?? 'N/A' }}</td>
                        <td class="text-start p-2">{{ $item->product->category->name ?? 'N/A' }}</td>
                        <td class="text-start p-2">{{ $item->quantity ?? 'N/A' }}</td>
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
                        <td class="text-start p-2">{{ $item->product->name ?? 'N/A' }}</td>
                        <td class="text-start p-2">{{ $item->product->category->name ?? 'N/A' }}</td>
                        <td class="text-start p-2">{{ $item->quantity ?? 'N/A' }}</td>
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
                        <td class="text-start p-2">{{ $expense->description ?? 'N/A' }}</td>
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
        <div class="card-body table-responsive">
            @forelse($customersLedger as $customer)
            <div class="mb-5">
                <h6 class="fw-bold">
                    {{ $customer->company_name }} ( {{ $customer->name }} )
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
                            <th class="text-start p-2">Tax (%)</th>
                            <th class="text-start p-2">Discount (%)</th>
                            <th class="text-end p-2">Debit (+)</th>
                            <th class="text-end p-2">Credit (-)</th>
                            <th class="text-end p-2">Balance</th>
                        </tr>
                    </thead>
                    <tbody>

                        @php
                        $totalDebit = 0;
                        $totalCredit = 0;
                        @endphp

                        @forelse($customer->ledger as $row)

                        @php
                        $totalDebit += $row['debit'];
                        $totalCredit += $row['credit'];
                        @endphp

                        <tr>

                            <td class="text-start p-2">
                                {{ \Carbon\Carbon::parse($row['date'])->format('Y-m-d') }}
                            </td>

                            <td class="text-start p-2">

                                @if($row['type']=='sale')
                                <span class="badge bg-success">Sale</span>
                                @else
                                <span class="badge bg-primary">Payment</span>
                                @endif

                            </td>

                            <td class="text-start p-2">{{ $row['reference'] }}</td>

                            <td class="text-start p-2">{{ $row['product'] }}</td>

                            <td class="text-start p-2">
                                {{ is_numeric($row['qty']) ? $row['qty'] : '-' }}
                            </td>

                            <td class="text-start p-2">
                                {{ is_numeric($row['price']) ? number_format($row['price'],2) : '-' }}
                            </td>

                            <td class="text-start p-2">
                                {{ is_numeric($row['tax']) ? number_format($row['tax'],2) : '-' }}
                            </td>

                            <td class="text-start p-2">
                                {{ is_numeric($row['discount']) ? number_format($row['discount'],2) : '-' }}
                            </td>

                            <td class="text-end p-2 text-success fw-bold">
                                @if($row['debit']>0)
                                +{{ number_format($row['debit'],2) }}
                                @endif
                            </td>

                            <td class="text-end p-2 text-danger fw-bold">
                                @if($row['credit']>0)
                                -{{ number_format($row['credit'],2) }}
                                @endif
                            </td>

                            <td class="text-end p-2 fw-bold">
                                {{ number_format($row['balance'],2) }}
                            </td>

                        </tr>

                        @empty

                        <tr>
                            <td colspan="11" class="text-center text-muted">
                                No records found.
                            </td>
                        </tr>

                        @endforelse

                    </tbody>

                    {{-- Totals Footer --}}
                    <tfoot class="table-light fw-bold">
                        <tr>

                            <td colspan="8" class="text-end p-2">Totals:</td>

                            <td class="text-end text-success">
                                +{{ number_format($totalDebit,2) }}
                            </td>

                            <td class="text-end text-danger">
                                -{{ number_format($totalCredit,2) }}
                            </td>

                            <td class="text-end">
                                {{ number_format($customer->ledger->last()['balance'] ?? 0 ,2) }}
                            </td>

                        </tr>
                    </tfoot>
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
        <div class="card-body table-responsive">
            @forelse($suppliersLedger as $supplier)
            <div class="mb-5">
                <h6 class="fw-bold">
                    {{ $supplier->company_name }} ( {{ $supplier->name }} )
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
                        $totalDebit = 0;
                        $totalCredit = 0;
                        @endphp

                        @forelse($supplier->ledger as $row)

                        @php
                        $totalDebit += $row['debit'];
                        $totalCredit += $row['credit'];
                        @endphp

                        <tr>

                            <td class="text-start p-2">
                                {{ \Carbon\Carbon::parse($row['date'])->format('Y-m-d') }}
                            </td>

                            <td class="text-start p-2">

                                @if($row['type']=='purchase')
                                <span class="badge bg-success">Purchase</span>
                                @else
                                <span class="badge bg-primary">Payment</span>
                                @endif

                            </td>

                            <td class="text-start p-2">{{ $row['reference'] }}</td>

                            <td class="text-start p-2">{{ $row['product'] }}</td>

                            <td class="text-start p-2">
                                {{ is_numeric($row['qty']) ? $row['qty'] : '-' }}
                            </td>

                            <td class="text-start p-2">
                                {{ is_numeric($row['price']) ? number_format($row['price'],2) : '-' }}
                            </td>

                            <td class="text-start p-2">
                                {{ number_format($row['tax'],2) }}
                            </td>

                            <td class="text-start p-2">
                                {{ number_format($row['discount'],2) }}
                            </td>

                            <td class="text-end p-2 text-success fw-bold">
                                @if($row['debit']>0)
                                +{{ number_format($row['debit'],2) }}
                                @endif
                            </td>

                            <td class="text-end p-2 text-danger fw-bold">
                                @if($row['credit']>0)
                                -{{ number_format($row['credit'],2) }}
                                @endif
                            </td>

                            <td class="text-end p-2 fw-bold">
                                {{ number_format($row['balance'],2) }}
                            </td>

                        </tr>

                        @empty

                        <tr>
                            <td colspan="11" class="text-center text-muted">
                                No records found for this supplier.
                            </td>
                        </tr>

                        @endforelse

                    </tbody>

                    {{-- Totals Footer --}}
                    <tfoot class="table-light fw-bold">
                        <tr>

                            <td colspan="8" class="text-end p-2">Totals:</td>

                            <td class="text-end text-success">
                                +{{ number_format($totalDebit,2) }}
                            </td>

                            <td class="text-end text-danger">
                                -{{ number_format($totalCredit,2) }}
                            </td>

                            <td class="text-end">
                                {{ number_format($supplier->ledger->last()['balance'] ?? 0,2) }}
                            </td>

                        </tr>
                    </tfoot>
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

    <!-- Agents Report -->
    @if($reportType == 'agents' || $reportType == 'all')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
            <h5>Agents Report</h5>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#agentsModal">Export</button>
        </div>
        <div class="card-body table-responsive">
            @forelse($agentsLedger as $agent)
            <div class="mb-5">
                <h6 class="fw-bold">
                    {{ $agent->name }}
                    <span class="text-muted">(Total Sales: {{ number_format($agent->ledger->last()['balance'] ?? 0,2) }})</span>
                </h6>

                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-start p-2">Date</th>
                            <th class="text-start p-2">Type</th>
                            <th class="text-start p-2">Invoice</th>
                            <th class="text-start p-2">Customer</th>
                            <th class="text-start p-2">Product</th>
                            <th class="text-start p-2">Qty</th>
                            <th class="text-start p-2">Price</th>
                            <th class="text-end p-2">Amount</th>
                            <th class="text-end p-2">Running Total</th>
                        </tr>
                    </thead>
                    <tbody>

                        @php
                        $totalAgentSales = 0;
                        @endphp

                        @forelse($agent->ledger as $row)

                        @php
                        $totalAgentSales += $row['debit'];
                        @endphp

                        <tr>
                            <td class="text-start p-2">{{ \Carbon\Carbon::parse($row['date'])->format('Y-m-d') }}</td>
                            <td class="text-start p-2"><span class="badge bg-success">Sale</span></td>
                            <td class="text-start p-2">{{ $row['reference'] }}</td>
                            <td class="text-start p-2">{{ $row['customer'] }}</td>
                            <td class="text-start p-2">{{ $row['product'] }}</td>
                            <td class="text-start p-2">{{ is_numeric($row['qty']) ? $row['qty'] : '-' }}</td>
                            <td class="text-start p-2">{{ is_numeric($row['price']) ? number_format($row['price'],2) : '-' }}</td>
                            <td class="text-end p-2 text-success fw-bold">{{ number_format($row['debit'],2) }}</td>
                            <td class="text-end p-2 fw-bold">{{ number_format($row['balance'],2) }}</td>
                        </tr>

                        @empty

                        <tr>
                            <td colspan="9" class="text-center text-muted">No sales found for this agent.</td>
                        </tr>

                        @endforelse

                    </tbody>

                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="7" class="text-end p-2">Total Sales:</td>
                            <td class="text-end text-success">{{ number_format($totalAgentSales,2) }}</td>
                            <td class="text-end">{{ number_format($agent->ledger->last()['balance'] ?? 0,2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @empty
            <div class="alert alert-warning">No agents found.</div>
            @endforelse
            {{-- Pagination --}}
            @if ($agentsLedger->hasPages())
            <div class="d-flex justify-content-center">
                {!! $agentsLedger->appends(request()->all())->links('pagination::bootstrap-5') !!}
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
                        <td class="text-start p-2">{{ $asset->title ?? 'N/A' }}</td>
                        <td class="text-start p-2">{{ $asset->date ?? 'N/A' }}</td>
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

    <!-- Dashboard Report -->
    @if($reportType == 'dashboard')
    @include('analytics.css')
    <div class="p-4 bg-white shadow rounded-4">
        <h3 class="section-title">Profit & Loss Summary</h3>

        <div class="row summary-box">
            <!-- LEFT SUMMARY -->
            <div class="col-md-6 border-end">
                <strong>Total Sales:</strong> <span class="label-line"></span>
                {{ number_format($D_totalSales, 2) }} <span class="percent-value">(100%)</span><br>

                <strong>Total Sale Return:</strong> <span class="label-line"></span>
                {{ number_format($D_totalSaleReturn, 2) }}<br>

                <strong>COGS:</strong> <span class="label-line"></span>
                {{ number_format($D_adjustedCOGS, 2) }}
                <span class="percent-value">(
                    {{ $D_adjustedSales > 0 ? number_format(($D_adjustedCOGS / $D_adjustedSales) * 100, 2) : '0' }}%
                    )</span><br>

                <strong>Gross Profit:</strong> <span class="label-line"></span>
                {{ number_format($D_grossProfit, 2) }}
                <span class="percent-value">({{ number_format($D_gpPercent, 2) }}%)</span><br>

                <strong>Expenses:</strong> <span class="label-line"></span>
                {{ number_format($D_totalExpenses, 2) }}
                <span class="percent-value">({{ number_format($D_expensePercent, 2) }}%)</span><br>

                <hr>

                <strong>Net Profit:</strong> <span class="label-line"></span>
                {{ number_format($D_netProfit, 2) }}
                <span class="percent-value">({{ number_format($D_npPercent, 2) }}%)</span><br>
            </div>

            <!-- RIGHT CHART -->
            <div class="col-md-6 d-flex justify-content-center align-items-center">
                <div class="chart-card">
                    <h5 class="fw-bold text-center">Profit % Breakdown</h5>
                    <canvas id="donutChart" height="260"></canvas>
                </div>
            </div>
        </div>
        <hr>
        <h3 class="section-title">Percent Summary</h3>
        <div class="row">
            <div class="col-md-6 summary-box border-end">
                <strong>Gross Profit %:</strong> <span class="label-line"></span>
                <span class="percent-value">{{ number_format($D_gpPercent, 2) }}%</span><br>
                <strong>Expenses %:</strong> <span class="label-line"></span>
                <span class="percent-value">{{ number_format($D_expensePercent, 2) }}%</span><br>
                <hr>
                <strong>Net Profit %:</strong> <span class="label-line"></span>
                <span class="percent-value">{{ number_format($D_npPercent, 2) }}%</span><br>
            </div>
            <!-- Purchases SUMMARY -->
            <div class="col-md-6 summary-box">
                <strong>Purchases:</strong> <span class="label-line"></span>
                {{ number_format($D_totalPurchases, 2) }}
                <span class="percent-value">(
                    {{ $D_adjustedSales > 0 ? number_format(($D_totalPurchases / $D_adjustedSales) * 100, 2) : '0' }}%
                    )</span><br>
                <strong>Purchased Qty:</strong> <span class="label-line"></span> {{ $D_purchaseQty }}<br>
                <strong>Sale Qty:</strong> <span class="label-line"></span> {{ $D_saleQty }}<br>
                <strong>Purchase %:</strong> <span class="label-line"></span>
                <span class="percent-value">{{ number_format($D_purchasePercent, 2) }}%</span>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        new Chart(document.getElementById('donutChart'), {
            type: 'doughnut',
            data: {
                labels: ['Gross Profit %', 'Expenses %', 'Net Profit %', 'Sale Return %'],
                datasets: [{
                    data: [
                        {{ $D_gpPercent }},
                        {{ $D_expensePercent }},
                        {{ $D_npPercent }},
                        {{ $D_adjustedSales > 0 ? number_format(($D_totalSaleReturn / $D_adjustedSales) * 100, 2) : 0 }}
                    ],
                    backgroundColor: ['#4caf50', '#f44336', '#2196f3', '#ff9800'],
                    borderColor: '#fff',
                    borderWidth: 3,
                    hoverOffset: 10
                }]
            },
            options: {
                plugins: { legend: { position: 'bottom' } },
                layout: { padding: 20 }
            }
        });
    </script>
    @endif

</div>

@include('reports.modals')

<script>
    document.addEventListener("DOMContentLoaded", function() {

        const reportType = document.getElementById('report_type');
        const customerDD = document.getElementById('customerDropdownWrapper');
        const supplierDD = document.getElementById('supplierDropdownWrapper');
        const agentDD = document.getElementById('agentDropdownWrapper');
        const customer = document.getElementById('customer_id');
        const supplier = document.getElementById('supplier_id');
        const agent = document.getElementById('agent_id');

        function updateUI() {

            // Hide all first
            customerDD.style.display = "none";
            supplierDD.style.display = "none";
            agentDD.style.display = "none";

            let rt = reportType.value;
            let hasCustomer = customer.value !== "";
            let hasSupplier = supplier.value !== "";
            let hasAgent = agent.value !== "";

            // 🔥 PRIORITY 1: If customer selected → show only customer
            if (hasCustomer) {
                customerDD.style.display = "block";
                supplier.value = ""; // reset supplier
                agent.value = ""; // reset agent
                return;
            }

            // 🔥 PRIORITY 2: If supplier selected → show only supplier
            if (hasSupplier) {
                supplierDD.style.display = "block";
                customer.value = ""; // reset customer
                agent.value = ""; // reset agent
                return;
            }

            // 🔥 PRIORITY 3: If agent selected → show only agent
            if (hasAgent) {
                agentDD.style.display = "block";
                customer.value = ""; // reset customer
                supplier.value = ""; // reset supplier
                return;
            }

            // 🔥 PRIORITY 4: Show based on Report Type
            switch (rt) {

                case "customers":
                case "sales":
                    customerDD.style.display = "block";
                    break;

                case "suppliers":
                case "purchases":
                    supplierDD.style.display = "block";
                    break;

                case "agents":
                    agentDD.style.display = "block";
                    break;
            }
        }

        // When selecting customer → hide supplier & agent
        customer.addEventListener("change", () => {
            supplier.value = "";
            agent.value = "";
            updateUI();
        });

        // When selecting supplier → hide customer & agent
        supplier.addEventListener("change", () => {
            customer.value = "";
            agent.value = "";
            updateUI();
        });

        // When selecting agent → hide customer & supplier
        agent.addEventListener("change", () => {
            customer.value = "";
            supplier.value = "";
            updateUI();
        });

        // When changing report type
        reportType.addEventListener("change", () => {
            customer.value = "";
            supplier.value = "";
            agent.value = "";
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