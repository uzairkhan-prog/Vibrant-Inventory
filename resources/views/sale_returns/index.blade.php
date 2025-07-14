@extends('layouts.app')

@section('content')
<div class="table-responsive">
    <div class="table-wrapper">

        <!-- Title -->
        <div class="table-title">
            <div class="row">
                <div class="col-md-6">
                    <h2>Sale <b>Returns</b></h2>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('sale-returns.create') }}" class="btn btn-secondary">
                        <i class="material-icons">&#xE147;</i> <span>Add Sale Return</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-3 align-items-center">
            <div class="col-md-10 d-flex align-items-center">
                <label class="me-2 fw-semibold">Search:</label>
                <input type="text" id="searchInput" class="form-control" placeholder="Search by customer or product">
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
        @if($returns->count())
        <table class="table table-striped table-hover" id="returnsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Packing</th>
                    <th>Qty</th>
                    <th>Deducted</th>
                    <th>Total After</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($returns as $index => $r)
                <tr>
                    <td>{{ ($returns->currentPage() - 1) * $returns->perPage() + $loop->iteration }}</td>
                    <td>{{ $r->created_at->format('Y-m-d') }}</td>
                    <td>{{ $r->customer->name }}</td>
                    <td>{{ $r->product->name }}</td>
                    <td>{{ $r->packing }}</td>
                    <td>{{ $r->qty_return }}</td>
                    <td>Rs {{ number_format($r->amount_deducted, 2) }}</td>
                    <td>Rs {{ number_format($r->total_after_return, 2) }}</td>
                    <td>
                        <a href="{{ route('sale-returns.edit', $r) }}" class="btn btn-sm btn-success text-white" title="Edit">
                            Edit
                            <i class="material-icons">&#xE254;</i>
                        </a>
                        <form action="{{ route('sale-returns.destroy', $r) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this return?')">
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

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-3">
            {!! $returns->appends(['per_page' => request('per_page')])->links('pagination::bootstrap-5') !!}
        </div>

        @else
        <div class="alert alert-info text-center">No sale returns found. <a href="{{ route('sale-returns.create') }}">Add one</a>.</div>
        @endif
    </div>
</div>

<!-- Search + PerPage JS -->
<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const value = this.value.toLowerCase();
        const rows = document.querySelectorAll('#returnsTable tbody tr');

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