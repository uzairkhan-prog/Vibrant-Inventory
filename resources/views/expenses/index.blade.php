@extends('layouts.app')

@section('content')

<div class="p-3 bg-white shadow rounded">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary mb-0">Expense Management</h2>
        <form method="GET" action="{{ route('expenses.index') }}" class="d-flex align-items-center gap-2 p-3 bg-light rounded shadow-sm justify-content-center">
            <label class="fw-semibold text-secondary mb-0">Select Month:</label>

            <select name="month_year" class="form-select w-auto border-primary shadow-sm">
                <option value="all" {{ $monthYear == 'all' ? 'selected' : '' }}>
                    All Records
                </option>

                {{-- Dynamic Months --}}
                @foreach($months as $m)
                @php
                // Trim whitespace and safely parse the date
                $formattedMonth = \Carbon\Carbon::parse(trim($m) . '-01')->format('F Y');
                @endphp
                <option value="{{ $m }}" {{ $monthYear == $m ? 'selected' : '' }}>
                    {{ $formattedMonth }}
                </option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-primary shadow-sm">
                <i class="bi bi-funnel-fill me-1"></i> Filter
            </button>

            <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Reset</a>

        </form>

        <div>
            <a href="{{ route('expenses.create') }}" class="btn btn-secondary">
                <i class="material-icons me-1">&#xE147;</i> Add Expense
            </a>
        </div>
    </div>

    <div class="alert alert-success shadow-sm rounded-3 fs-6 fw-bold mb-4">
        <div class="d-flex justify-content-between">
            <span>Total Expense:</span>
            <span>Rs {{ number_format($grandTotal, 2) }}</span>
        </div>
    </div>

    <!-- Filters Row -->
    <div class="row mb-3 align-items-center gap-2">
        <!-- Existing Search -->
        <div class="col-xl-12 col-md-4 d-flex align-items-center">
            <label class="me-2 fw-semibold">Search:</label>
            <input type="text" id="searchInput" class="form-control w-100" placeholder="Search by customer, date or amount">
        </div>

        <!-- Date Filters -->
        <div class="col-xl-12 col-md-4 d-flex align-items-center justify-content-center">
            <form method="GET" action="{{ route('expenses.index') }}" class="d-flex align-items-center">
                <label class="me-2 fw-semibold">From:</label>
                <input type="date" name="from_date" class="form-control me-2" value="{{ $fromDate ?? '' }}">
                <label class="me-2 fw-semibold">To:</label>
                <input type="date" name="to_date" class="form-control me-2" value="{{ $toDate ?? '' }}">
                <br>
                <button type="submit" class="btn btn-primary me-2">Search</button>
                <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Reset</a>
                <label class="ms-3 me-2 fw-semibold">Show</label>
                <select id="rowsPerPage" name="per_page" class="form-select w-auto">
                    @foreach ([20, 50, 100] as $value)
                    <option value="{{ $value }}" {{ request('per_page') == $value ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    @if($expenses->count())
    <div class="table-responsive">
        <div class="table-wrapper">
            <table class="table table-striped table-hover table-bordered align-middle text-center" id="expenseTable">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Date</th>
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
                        <td>
                            {{ \Carbon\Carbon::parse($expense->created_at)->format('d M Y') }}
                            <!-- <br>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($expense->created_at)->format('h:i A') }}
                            </small> -->
                        </td>
                        <td>{{ $expense->expenseName->name }}</td>
                        <td>{{ $expense->description }}</td>
                        <td>{{ $expense->paymentType->name ?? '-' }}</td>
                        <td>Rs {{ number_format($expense->amount, 2) }}</td>
                        <td class="d-flex justify-content-center">
                            <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-success me-1 text-white" title="Edit">
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
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {!! $expenses->appends(request()->all())->links('pagination::bootstrap-5') !!}
    </div>

    @else
    <div class="alert alert-info text-center mt-4">No expenses found. <a href="{{ route('expenses.create') }}" class="text-dark">Create one</a>.</div>
    @endif
</div>

<script>
    // Search input
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const value = this.value.toLowerCase();
        document.querySelectorAll('#expenseTable tbody tr').forEach(row => {
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