@extends('layouts.app')

@section('content')

<div class="p-4 bg-white shadow rounded">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary mb-0">Purchase Invoice Management</h2>
        <div>
            <a href="{{ route('purchases.create') }}" class="btn btn-secondary">
                <i class="material-icons me-1">&#xE147;</i> Add Purchase
            </a>
        </div>
    </div>

    @php $subtotal = $purchases->sum('total_amount'); @endphp
    <div class="alert alert-success shadow-sm rounded-3 fs-6 fw-bold mb-4">
        <div class="d-flex justify-content-between">
            <span>Total Purchase Value:</span>
            <span>Rs {{ number_format($subtotal, 2) }}</span>
        </div>
    </div>

    <!-- Filters Row -->
    <div class="row mb-3 align-items-center">
        <!-- Existing Search -->
        <div class="col-md-4 d-flex align-items-center">
            <label class="me-2 fw-semibold">Search:</label>
            <input type="text" id="searchInput" class="form-control w-100" placeholder="Search by supplier, date or amount">
        </div>

        <!-- Date Filters -->
        <div class="col-md-8 d-flex align-items-center justify-content-end">
            <form method="GET" action="{{ route('purchases.index') }}" class="d-flex align-items-center">
                <label class="me-2 fw-semibold">From:</label>
                <input type="date" name="from_date" class="form-control me-2" value="{{ $fromDate ?? '' }}">
                <label class="me-2 fw-semibold">To:</label>
                <input type="date" name="to_date" class="form-control me-2" value="{{ $toDate ?? '' }}">
                <button type="submit" class="btn btn-primary me-2">Search</button>
                <a href="{{ route('purchases.index') }}" class="btn btn-secondary me-3">Reset</a>
                <label class="ms-3 me-2 fw-semibold">Show</label>
                <select id="rowsPerPage" name="per_page" class="form-select w-auto">
                    @foreach ([20, 50, 100] as $value)
                    <option value="{{ $value }}" {{ request('per_page') == $value ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    @if($purchases->count())
    <div class="table-responsive">
        <div class="table-wrapper">
            <table class="table table-striped table-hover table-bordered align-middle text-center" id="purchaseTable">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Invoice no</th>
                        <th>Supplier</th>
                        <th>Total Amount</th>
                        <th>View Invoice</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchases as $purchase)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($purchase->date)->format('Y-m-d') }}</td>
                        <td>{{ $purchase->id }}</td>
                        <td>{{ $purchase->supplier->name }}</td>
                        <td>Rs {{ number_format($purchase->total_amount, 2) }}</td>
                        <td>
                            <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-sm btn-info me-1">
                                Invoice <i class="material-icons">&#xE8F4;</i>
                            </a>
                        </td>
                        <td class="d-flex justify-content-center">
                            <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-sm btn-warning me-1">
                                Edit <i class="material-icons">&#xE3C9;</i>
                            </a>
                            <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete purchase?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    Delete <i class="material-icons">&#xE872;</i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {!! $purchases->appends(request()->all())->links('pagination::bootstrap-5') !!}
    </div>
    @else
    <div class="alert alert-info text-center mt-4">No purchases found. <a href="{{ route('purchases.create') }}" class="text-dark">Create one</a>.</div>
    @endif
</div>

<script>
    // Existing search input
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchVal = this.value.toLowerCase();
        const rows = document.querySelectorAll('#purchaseTable tbody tr');
        rows.forEach(row => {
            const rowText = row.innerText.toLowerCase();
            row.style.display = rowText.includes(searchVal) ? '' : 'none';
        });
    });

    // Rows per page select
    document.getElementById('rowsPerPage').addEventListener('change', function() {
        const selected = this.value;
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', selected);
        window.location.href = url.toString();
    });
</script>

@endsection