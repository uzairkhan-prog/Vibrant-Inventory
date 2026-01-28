@extends('layouts.app')

@section('content')

<div class="p-4 bg-white shadow rounded">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary mb-0">Product Ledger</h2>
    </div>

    <!-- Filters Row -->
    <form method="GET" action="">
        <div class="row mb-3 align-items-center">
            <div class="col-md-10 d-flex align-items-center">
                <label class="me-2 fw-semibold">Search:</label>
                <input type="text" id="searchInput" class="form-control w-100" placeholder="Search product...">
            </div>
            <div class="col-md-2 d-flex justify-content-end align-items-center">
                <label class="me-2 fw-semibold">Show</label>
                <select id="rowsPerPage" name="per_page" class="form-select w-auto">
                    @foreach ([20, 50, 100] as $value)
                    <option value="{{ $value }}" {{ request('per_page', $perPage) == $value ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                    @endforeach
                </select>
                <label class="ms-2 fw-semibold">entries</label>
            </div>
        </div>

        <div class="row mb-3 align-items-center">
            <div class="col-md-2 mt-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="show_all" value="1" {{ request('show_all') ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold">Show All Products</label>
                </div>
            </div>
            <div class="col-md-3">
                <label class="fw-semibold">Type:</label>
                <select name="type_filter" class="form-select">
                    <option value="">All</option>
                    <option value="sale" {{ request('type_filter') == 'sale' ? 'selected' : '' }}>Sale</option>
                    <option value="purchase" {{ request('type_filter') == 'purchase' ? 'selected' : '' }}>Purchase</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="fw-semibold">From:</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="fw-semibold">To:</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control">
            </div>
            <div class="col-md-3 mt-4 d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="{{ route('ledger.products') }}" class="btn btn-secondary">Clear</a>
            </div>
        </div>
    </form>

    @if($ledgerEntries->count())
    <div class="table-responsive">
        <div class="table-wrapper">
            <table class="table table-striped table-hover table-bordered align-middle text-center" id="ledgerTable">
                <thead class="table-light">
                    <tr>
                        <th>Invoice No</th>
                        <th>Type</th>
                        <th>Product Name</th>
                        <th>QTY</th>
                        <th>Unit Price</th>
                        <th>Invoice Value</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ledgerEntries as $entry)
                    <tr>
                        <td>#{{ $entry['invoice_no'] }}</td>
                        <td>{{ ucfirst($entry['type']) }}</td>
                        <td>{{ $entry['product_name'] }}</td>
                        <td>{{ $entry['qty'] }}</td>
                        <td>Rs {{ number_format($entry['unit_price'], 2) }}</td>
                        <td>Rs {{ number_format($entry['invoice_value'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-3">
        {!! $ledgerEntries->appends(request()->query())->links('pagination::bootstrap-5') !!}
    </div>

    <!-- Totals Section -->
    <div class="alert alert-info mt-4 p-3 fw-bold fs-5">
        <div class="row mb-2">
            <div class="col-md-6"><strong>Total Units Sold:</strong> {{ $total_sold_qty }}</div>
            <div class="col-md-6"><strong>Total Amount Sold:</strong> Rs {{ number_format($total_sold_value, 2) }}</div>
        </div>
        <div class="row">
            <div class="col-md-6"><strong>Total Units Purchased:</strong> {{ $total_purchase_qty }}</div>
            <div class="col-md-6"><strong>Total Amount Purchased:</strong> Rs {{ number_format($total_purchase_value, 2) }}</div>
        </div>
    </div>
    @else
    <div class="alert alert-info text-center">No ledger data found.</div>
    @endif
</div>

<script>
    // Search input
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const value = this.value.toLowerCase();
        document.querySelectorAll('#ledgerTable tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
        });
    });

    // Rows per page
    document.getElementById('rowsPerPage').addEventListener('change', function() {
        const selected = this.value;
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', selected);
        window.location.href = url.toString();
    });
</script>

@endsection