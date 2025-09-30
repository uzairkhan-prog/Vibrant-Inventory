@extends('layouts.app')

@section('content')
<div class="container py-5">

    <!-- Filters -->
    <div class="card mb-4 shadow">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="report_type" class="form-label">Report Type</label>
                    <select name="report_type" id="report_type" class="form-select">
                        <option value="all" {{ $reportType == 'all' ? 'selected' : '' }}>All Reports</option>
                        <option value="sales" {{ $reportType == 'sales' ? 'selected' : '' }}>Sales</option>
                        <option value="purchases" {{ $reportType == 'purchases' ? 'selected' : '' }}>Purchases</option>
                        <option value="expenses" {{ $reportType == 'expenses' ? 'selected' : '' }}>Expenses</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">End Date</label>
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
    <div class="row g-4 mb-4">
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
    </div>

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
                        <td colspan="8" class="text-end">Total Purchases:</td>
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
                        <td colspan="8" class="text-end">Total Sales:</td>
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
                        <td colspan="4" class="text-end">Total Expenses:</td>
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
</div>

@include('reports.modals')
@endsection