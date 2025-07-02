@extends('layouts.app')

@section('content')

<div class="table-responsive">
    <div class="table-wrapper">

        <!-- Title -->
        <div class="table-title">
            <div class="row">
                <div class="col-md-6">
                    <h2>Purchase <b>Management</b></h2>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('purchases.create') }}" class="btn btn-secondary">
                        <i class="material-icons">&#xE147;</i> <span>Add Purchase</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-3 align-items-center">
            <div class="col-md-10 d-flex align-items-center">
                <label class="me-2 fw-semibold">Search:</label>
                <input type="text" id="searchInput" class="form-control" placeholder="Search by supplier, date or amount">
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

        <!-- Subtotal -->
        @php
        $subtotal = $purchases->sum('total_amount');
        @endphp

        <div class="alert alert-info fw-bold fs-5">
            <div class="d-flex justify-content-between">
                <span>Total Purchase Value:</span>
                <span>Rs {{ number_format($subtotal, 2) }}</span>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
        @endif

        <!-- Table -->
        @if($purchases->count())
        <table class="table table-striped table-hover" id="purchaseTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Supplier</th>
                    <th>Date</th>
                    <th>Total Amount</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchases as $purchase)
                <tr>
                    <td>{{ ($purchases->currentPage() - 1) * $purchases->perPage() + $loop->iteration }}</td>
                    <td>{{ $purchase->supplier->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($purchase->date)->format('Y-m-d') }}</td>
                    <td>{{ number_format($purchase->total_amount, 2) }}</td>
                    <td>
                        <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-sm btn-info" title="View">
                            <i class="material-icons">&#xE8F4;</i>
                        </a>
                        <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete purchase?')">
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
            {!! $purchases->appends(['per_page' => request('per_page')])->links('pagination::bootstrap-5') !!}
        </div>

        @else
        <div class="alert alert-info text-center">No purchases found. <a href="{{ route('purchases.create') }}">Create one</a>.</div>
        @endif
    </div>
</div>

<!-- JS Filter -->
<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchVal = this.value.toLowerCase();
        const rows = document.querySelectorAll('#purchaseTable tbody tr');

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