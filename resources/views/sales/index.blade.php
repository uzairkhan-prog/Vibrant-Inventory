@extends('layouts.app')

@section('content')

<div class="sales-wrapper p-4 my-5 bg-white shadow rounded">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary mb-0">Sales Invoice Management</h2>
        <div>
            <a href="{{ route('sales.create') }}" class="btn btn-secondary">
                <i class="material-icons me-1">&#xE147;</i> Add Sale
            </a>
        </div>
    </div>

    @php $subtotal = $sales->sum('total_amount'); @endphp
    <div class="alert alert-success shadow-sm rounded-3 fs-6 fw-bold mb-4">
        <div class="d-flex justify-content-between">
            <span>Total Sales Value:</span>
            <span>Rs {{ number_format($subtotal, 2) }}</span>
        </div>
    </div>

    <div class="row mb-3 align-items-center">
        <div class="col-md-9 d-flex align-items-center">
            <label class="me-2 fw-semibold">Search:</label>
            <input type="text" id="searchInput" class="form-control w-100" placeholder="Search by customer, date or amount">
        </div>
        <div class="col-md-3 d-flex align-items-center justify-content-end">
            <label class="me-2 fw-semibold">Show</label>
            <select id="rowsPerPage" class="form-select w-auto">
                @foreach ([5, 10, 50, 100] as $value)
                <option value="{{ $value }}" {{ request('per_page') == $value ? 'selected' : '' }}>{{ $value }}</option>
                @endforeach
            </select>
            <label class="ms-2 fw-semibold">entries</label>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    @if($sales->count())
    <div class="table-responsive">
        <table class="table table-striped table-hover table-bordered align-middle text-center" id="salesTable">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Total Amount</th>
                    <th>Date</th>
                    <th>Sale Invoices</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sales as $sale)
                <tr>
                    <td>{{ ($sales->currentPage() - 1) * $sales->perPage() + $loop->iteration }}</td>
                    <td>{{ $sale->customer->name }}</td>
                    <td>Rs {{ number_format($sale->total_amount ?? 0, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($sale->date)->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-info me-1">
                            Invoice <i class="material-icons">&#xE8F4;</i>
                        </a>
                    </td>
                    <td class="d-flex justify-content-center">
                        <a href="{{ route('sales.edit', $sale) }}" class="btn btn-sm btn-warning me-1">
                            Edit <i class="material-icons">&#xE3C9;</i>
                        </a>
                        <form action="{{ route('sales.destroy', $sale) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete sale?')">
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

    <div class="d-flex justify-content-center mt-4">
        {!! $sales->appends(['per_page' => request('per_page')])->links('pagination::bootstrap-5') !!}
    </div>

    @else
    <div class="alert alert-info text-center mt-4">No sales found. <a href="{{ route('sales.create') }}">Create one</a>.</div>
    @endif
</div>

<style>
    .sales-wrapper {
        max-width: 1200px;
        margin: auto;
    }

    .table th,
    .table td {
        font-size: 0.875rem;
        vertical-align: middle;
    }

    .btn-info {
        background-color: #0dcaf0;
        border: none;
        color: #fff;
        font-weight: 500;
    }

    .btn-info:hover {
        background-color: #0bbbe6;
    }

    .btn-danger {
        font-weight: 500;
    }

    .form-select,
    .form-control {
        font-size: 0.85rem;
    }

    .material-icons {
        vertical-align: middle;
        font-size: 1rem;
    }
</style>

<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchVal = this.value.toLowerCase();
        const rows = document.querySelectorAll('#salesTable tbody tr');
        rows.forEach(row => {
            const rowText = row.innerText.toLowerCase();
            row.style.display = rowText.includes(searchVal) ? '' : 'none';
        });
    });

    document.getElementById('rowsPerPage').addEventListener('change', function() {
        const selected = this.value;
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', selected);
        window.location.href = url.toString();
    });
</script>

@endsection