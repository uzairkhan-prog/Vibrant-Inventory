@extends('layouts.app')

@section('content')
<div class="table-responsive">
    <div class="table-wrapper">

        <!-- Title -->
        <div class="table-title">
            <div class="row">
                <div class="col-md-6">
                    <h2>Expense <b>Management</b></h2>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('expenses.create') }}" class="btn btn-secondary">
                        <i class="material-icons">&#xE147;</i> <span>Add New Expense</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="alert alert-success shadow-sm rounded-3 fs-6 fw-bold">
            <div class="d-flex justify-content-between">
                <span>Total Expense:</span>
                <span>Rs {{ number_format($subtotal, 2) }}</span>
            </div>
        </div>

        <!-- Filters Row -->
        <div class="row mb-3 align-items-center">
            <!-- Existing Search -->
            <div class="col-md-6 d-flex align-items-center">
                <label class="me-2 fw-semibold">Search:</label>
                <input type="text" id="searchInput" class="form-control" placeholder="Search expense name, description...">
            </div>

            <!-- Date Range Filter + Rows per page -->
            <div class="col-md-6 d-flex align-items-center justify-content-end">
                <form method="GET" action="{{ route('expenses.index') }}" class="d-flex align-items-center">
                    <label class="me-2 fw-semibold">From:</label>
                    <input type="date" name="from_date" class="form-control me-2" value="{{ $fromDate ?? '' }}">
                    <label class="me-2 fw-semibold">To:</label>
                    <input type="date" name="to_date" class="form-control me-2" value="{{ $toDate ?? '' }}">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <a href="{{ route('expenses.index') }}" class="btn btn-secondary me-3">Reset</a>
                    <label class="ms-2 me-2 fw-semibold">Show</label>
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

        @if($expenses->count())
        <table class="table table-striped table-hover" id="expenseTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Expense</th>
                    <th>Description</th>
                    <th>Payment Type</th>
                    <th>Amount</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($expenses as $expense)
                <tr>
                    <td>{{ ($expenses->currentPage() - 1) * $expenses->perPage() + $loop->iteration }}</td>
                    <td>{{ $expense->expenseName->name }}</td>
                    <td>{{ $expense->description }}</td>
                    <td>{{ $expense->paymentType->name ?? '-' }}</td>
                    <td>{{ number_format($expense->amount, 2) }}</td>
                    <td>
                        <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-success text-white" title="Edit">
                            Edit <i class="material-icons">&#xE254;</i>
                        </a>
                        <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this expense?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                Delete <i class="material-icons">&#xE872;</i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center mt-3">
            {!! $expenses->appends(request()->all())->links('pagination::bootstrap-5') !!}
        </div>
        @else
        <div class="alert alert-info text-center">No expenses found. <a href="{{ route('expenses.create') }}" class="text-dark">Create one</a>.</div>
        @endif
    </div>
</div>

<!-- Search + Rows per page JS -->
<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const val = this.value.toLowerCase();
        document.querySelectorAll('#expenseTable tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(val) ? '' : 'none';
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