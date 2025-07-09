@extends('layouts.app')

@section('content')

<div class="table-responsive">
    <div class="table-wrapper">

        <!-- Title -->
        <div class="table-title">
            <div class="row">
                <div class="col-md-6">
                    <h2>Ledger <b>Management</b></h2>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('ledgers.create') }}" class="btn btn-secondary">
                        <i class="material-icons">&#xE147;</i> <span>Add Ledger Entry</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Subtotal -->
        <div class="alert alert-success shadow-sm rounded-3 fs-6 fw-bold">
            <div class="d-flex justify-content-between">
                <span>Total Balance:</span>
                <span>Rs {{ number_format($total_balance, 2) }}</span>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-3 align-items-center">
            <div class="col-md-10 d-flex align-items-center">
                <label class="me-2 fw-semibold">Search:</label>
                <input type="text" id="searchInput" class="form-control" placeholder="Search by Customer/Supplier name..." value="{{ $search ?? '' }}">
            </div>
            <div class="col-md-2 d-flex justify-content-end align-items-center">
                <label class="me-2 fw-semibold">Show</label>
                <select id="rowsPerPage" class="form-select w-auto">
                    @foreach ([5, 10, 50, 100] as $value)
                    <option value="{{ $value }}" {{ ($perPage == $value) ? 'selected' : '' }}>{{ $value }}</option>
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
        @if($ledgers->count())
        <table class="table table-striped table-hover" id="ledgerTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Type</th>
                    <th>Name</th>
                    <th>Balance</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ledgers as $ledger)
                <tr>
                    <td>{{ ($ledgers->currentPage() - 1) * $ledgers->perPage() + $loop->iteration }}</td>
                    <td>{{ ucfirst($ledger->type) }}</td>
                    <td>
                        @if($ledger->type == 'customer')
                        {{ \App\Models\Customer::find($ledger->reference_id)?->name }}
                        @else
                        {{ \App\Models\Supplier::find($ledger->reference_id)?->name }}
                        @endif
                    </td>
                    <td>{{ number_format($ledger->balance, 2) }}</td>
                    <td>{{ $ledger->updated_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <a href="{{ route('ledgers.edit', $ledger) }}" class="btn btn-sm btn-warning text-white" title="Edit">
                            <i class="material-icons">&#xE254;</i>
                        </a>
                        <form action="{{ route('ledgers.destroy', $ledger) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete ledger entry?')">
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
            {!! $ledgers->links('pagination::bootstrap-5') !!}
        </div>

        @else
        <div class="alert alert-info text-center">No ledger entries found. <a href="{{ route('ledgers.create') }}">Create one</a>.</div>
        @endif

    </div>
</div>

<!-- Search and PerPage Scripts -->
<script>
    // Client-side filter on table rows for instant UI feedback
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const val = this.value.toLowerCase();
        document.querySelectorAll('#ledgerTable tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(val) ? '' : 'none';
        });
    });

    // Change pagination size and reload page with query param
    document.getElementById('rowsPerPage').addEventListener('change', function() {
        const selected = this.value;
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', selected);
        window.location.href = url.toString();
    });
</script>

@endsection