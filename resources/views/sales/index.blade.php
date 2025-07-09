@extends('layouts.app')

@section('content')

<div class="table-responsive">
    <div class="table-wrapper">

        <!-- Title -->
        <div class="table-title">
            <div class="row">
                <div class="col-md-6">
                    <h2>Sales <b>Management</b></h2>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('sales.create') }}" class="btn btn-secondary">
                        <i class="material-icons">&#xE147;</i> <span>Add Sale</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Subtotal Row -->
        @php
        $subtotal = 0;
        foreach ($sales as $sale) {
        $subtotal += $sale->total_amount ?? 0;
        }
        @endphp

        <div class="alert alert-success shadow-sm rounded-3 fs-6 fw-bold">
            <div class="d-flex justify-content-between">
                <span>Total Sales Value:</span>
                <span>Rs {{ number_format($subtotal, 2) }}</span>
            </div>
        </div>

        <!-- Filters: Search + Per Page -->
        <div class="row mb-3 align-items-center">
            <div class="col-md-10 d-flex align-items-center">
                <label class="me-2 fw-semibold">Search:</label>
                <input type="text" id="searchInput" class="form-control" placeholder="Search by customer, date, or description">
            </div>
            <div class="col-md-2 d-flex justify-content-end align-items-center">
                <label class="me-2 fw-semibold">Show</label>
                <select id="rowsPerPage" class="form-select w-auto">
                    @foreach ([5, 10, 50, 100] as $value)
                    <option value="{{ $value }}" {{ request('per_page') == $value ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
                <label class="ms-2 fw-semibold">entries</label>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
        @endif

        <!-- Table -->
        @if($sales->count())
        <table class="table table-striped table-hover" id="salesTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Total Amount</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sales as $sale)
                <tr>
                    <td>{{ ($sales->currentPage() - 1) * $sales->perPage() + $loop->iteration }}</td>
                    <td>{{ $sale->customer->name }}</td>
                    <td>{{ $sale->description }}</td>
                    <td>{{ \Carbon\Carbon::parse($sale->date)->format('Y-m-d') }}</td>
                    <td>{{ number_format($sale->total_amount ?? 0, 2) }}</td>
                    <td>
                        <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-info" title="View">
                            <i class="material-icons">&#xE8F4;</i>
                        </a>
                        <a href="{{ route('sales.edit', $sale) }}" class="btn btn-sm btn-warning text-white" title="Edit">
                            <i class="material-icons">&#xE254;</i>
                        </a>
                        <form action="{{ route('sales.destroy', $sale) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete sale?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                <i class="material-icons">&#xE872;</i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-3">
            {!! $sales->appends(['per_page' => request('per_page')])->links('pagination::bootstrap-5') !!}
        </div>

        @else
        <div class="alert alert-info text-center">No sales found. <a href="{{ route('sales.create') }}">Create one</a>.</div>
        @endif
    </div>
</div>

<!-- Search and Pagination JS -->
<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const value = this.value.toLowerCase();
        const rows = document.querySelectorAll('#salesTable tbody tr');

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(value) ? '' : 'none';
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