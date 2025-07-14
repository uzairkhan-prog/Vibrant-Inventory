@extends('layouts.app')

@section('content')

<div class="table-responsive">
    <div class="table-wrapper">

        <!-- Title -->
        <div class="table-title">
            <div class="row">
                <div class="col-md-6">
                    <h2>Stock <b>Management</b></h2>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('products.create') }}" class="btn btn-secondary">
                        <i class="material-icons">&#xE147;</i> <span>Add Product</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Subtotal -->
        @php
        $subtotal = 0;
        foreach ($products as $product) {
        $subtotal += $product->price_per_unit * $product->quantity;
        }
        @endphp

        <div class="alert alert-success shadow-sm rounded-3 fs-6 fw-bold">
            <div class="d-flex justify-content-between">
                <span>Total Stock Value:</span>
                <span>Rs {{ number_format($subtotal, 2) }}</span>
            </div>
        </div>

        <!-- Filter and Length Controls -->
        <div class="row mb-3 align-items-center">
            <div class="col-md-10 d-flex align-items-center">
                <label class="me-2 fw-semibold">Search:</label>
                <input type="text" id="searchInput" class="form-control" placeholder="Search product...">
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

        <!-- Success -->
        @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
        @endif

        <!-- Product Table -->
        @if($products->count())
        <table class="table table-striped table-hover" id="productTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Packing</th>
                    <th>Rate</th>
                    <th>Total QTY</th>
                    <th>Value</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                <tr>
                    <td>{{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->packing }}</td>
                    <td>{{ number_format($product->price_per_unit, 2) }}</td>
                    <td>{{ $product->quantity }}</td>
                    <td>{{ number_format($product->price_per_unit * $product->quantity, 2) }}</td>
                    <td>
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-success text-white" title="Edit">
                            Edit
                            <i class="material-icons">&#xE254;</i>
                        </a>
                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this product?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                Delete
                                <i class="material-icons">&#xE872;</i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Laravel Pagination -->
        <div class="d-flex justify-content-center mt-3">
            {!! $products->links('pagination::bootstrap-5') !!}
        </div>
        @else
        <div class="alert alert-info text-center">No products found. <a href="{{ route('products.create') }}">Create one</a>.</div>
        @endif

    </div>
</div>

<!-- JS: Search & Limit Rows -->
<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('#productTable tbody tr');

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(searchValue) ? '' : 'none';
        });
    });

    document.getElementById('rowsPerPage').addEventListener('change', function() {
        window.location.href = `?per_page=${this.value}`;
    });
</script>

@endsection