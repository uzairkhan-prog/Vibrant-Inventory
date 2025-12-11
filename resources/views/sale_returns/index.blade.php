@extends('layouts.app')

@section('content')

<div class="p-4 bg-white shadow rounded">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary mb-0">Sale Returns Management</h2>
        <div>
            <a href="{{ route('sale-returns.create') }}" class="btn btn-secondary">
                <i class="material-icons me-1">&#xE147;</i> Add Sale Return
            </a>
        </div>
    </div>

    @php $subtotal = $returns->sum('total_after_return'); @endphp
    <div class="alert alert-success shadow-sm rounded-3 fs-6 fw-bold mb-4">
        <div class="d-flex justify-content-between">
            <span>Total Returned Value:</span>
            <span>Rs {{ number_format($subtotal, 2) }}</span>
        </div>
    </div>

    <!-- Filters Row -->
    <div class="row mb-3 align-items-center">
        <!-- Search -->
        <div class="col-md-4 d-flex align-items-center">
            <label class="me-2 fw-semibold">Search:</label>
            <input type="text" id="searchInput" class="form-control w-100" placeholder="Search by customer or product">
        </div>

        <!-- Date + Per Page Filters -->
        <div class="col-md-8 d-flex align-items-center justify-content-end">
            <form method="GET" action="{{ route('sale-returns.index') }}" class="d-flex align-items-center">
                <label class="me-2 fw-semibold">From:</label>
                <input type="date" name="from_date" class="form-control me-2" value="{{ $fromDate ?? '' }}">
                <label class="me-2 fw-semibold">To:</label>
                <input type="date" name="to_date" class="form-control me-2" value="{{ $toDate ?? '' }}">
                <button type="submit" class="btn btn-primary me-2">Search</button>
                <a href="{{ route('sale-returns.index') }}" class="btn btn-secondary me-3">Reset</a>
                <label class="ms-2 me-2 fw-semibold">Show</label>
                <select id="rowsPerPage" name="per_page" class="form-select w-auto">
                    @foreach ([20, 50, 100] as $value)
                    <option value="{{ $value }}" {{ request('per_page') == $value ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    @if($returns->count())
    <div class="table-responsive">
        <div class="table-wrapper">
            <table class="table table-striped table-hover table-bordered align-middle text-center" id="returnsTable">
                <thead class="table-light">
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
                        <td class="d-flex justify-content-center">
                            <a href="{{ route('sale-returns.edit', $r) }}" class="btn btn-sm btn-success me-1 text-white">
                                Edit <i class="material-icons">&#xE254;</i>
                            </a>
                            <form action="{{ route('sale-returns.destroy', $r) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this return?')">
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

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {!! $returns->appends(request()->all())->links('pagination::bootstrap-5') !!}
    </div>

    @else
    <div class="alert alert-info text-center mt-4">No sale returns found. <a href="{{ route('sale-returns.create') }}" class="text-dark">Add one</a>.</div>
    @endif
</div>

<script>
    // Search input
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const value = this.value.toLowerCase();
        const rows = document.querySelectorAll('#returnsTable tbody tr');

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(value) ? '' : 'none';
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