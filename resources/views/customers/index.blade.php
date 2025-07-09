@extends('layouts.app')

@section('content')

<div class="table-responsive">
    <div class="table-wrapper">

        <!-- Title -->
        <div class="table-title">
            <div class="row">
                <div class="col-md-6">
                    <h2>Customer <b>Management</b></h2>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('customers.create') }}" class="btn btn-secondary">
                        <i class="material-icons">&#xE147;</i> <span>Add New Customer</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Subtotal Row -->
        @php
        $subtotal = 0;
        foreach ($customers as $customer) {
        $subtotal += $customer->balance ?? 0;
        }
        @endphp

        <div class="alert alert-success shadow-sm rounded-3 fs-6 fw-bold">
            <div class="d-flex justify-content-between">
                <span>Total Balance Value:</span>
                <span>Rs {{ number_format($subtotal, 2) }}</span>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-3 align-items-center">
            <div class="col-md-10 d-flex align-items-center">
                <label class="me-2 fw-semibold">Search:</label>
                <input type="text" id="searchInput" class="form-control" placeholder="Search by name, address or balance">
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
        @if($customers->count())
        <table class="table table-striped table-hover" id="customersTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Date</th>
                    <th>Balance</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customers as $customer)
                <tr>
                    <td>{{ ($customers->currentPage() - 1) * $customers->perPage() + $loop->iteration }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->address }}</td>
                    <td>{{ \Carbon\Carbon::parse($customer->created_at)->format('Y-m-d') }}</td>
                    <td>{{ number_format($customer->balance ?? 0, 2) }}</td>
                    <td>
                        <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-info text-white" title="View Profile">
                            <i class="material-icons">&#xE8F4;</i>
                        </a>
                        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-warning text-white" title="Edit">
                            <i class="material-icons">&#xE254;</i>
                        </a>
                        <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this customer?')">
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
            {!! $customers->appends(['per_page' => request('per_page')])->links('pagination::bootstrap-5') !!}
        </div>

        @else
        <div class="alert alert-info text-center">No customers found. <a href="{{ route('customers.create') }}">Create one</a>.</div>
        @endif
    </div>
</div>

<!-- Search + PerPage JS -->
<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const value = this.value.toLowerCase();
        const rows = document.querySelectorAll('#customersTable tbody tr');

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