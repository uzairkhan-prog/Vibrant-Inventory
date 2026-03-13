@extends('layouts.app')

@section('content')

<div class="p-4 bg-white shadow rounded">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary mb-0">Customers Ledger</h2>
        <!-- <div>
            <a href="{{ route('customers.create') }}" class="btn btn-secondary">
                <i class="material-icons me-1">&#xE147;</i> Add Customer
            </a>
        </div> -->
    </div>

    @php
    $subtotal = $customers
    ->where('name', '!=', 'Counter Sale')
    ->sum('balance');
    @endphp
    <div class="alert alert-success shadow-sm rounded-3 fs-6 fw-bold mb-4">
        <div class="d-flex justify-content-between">
            <span>Total Balance Value:</span>
            <span>Rs {{ number_format($subtotal, 2) }}</span>
        </div>
    </div>

    <!-- Filters Row -->
    <div class="row mb-3 align-items-center">
        <div class="col-md-10 d-flex align-items-center">
            <label class="me-2 fw-semibold">Search:</label>
            <input type="text" id="searchInput" class="form-control w-100" placeholder="Search by name, address or balance">
        </div>
        <div class="col-md-2 d-flex justify-content-end align-items-center">
            <label class="me-2 fw-semibold">Show</label>
            <select id="rowsPerPage" class="form-select w-auto">
                @foreach ([20, 50, 100] as $value)
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

    @if($customers->count())
    <div class="table-responsive">
        <div class="table-wrapper">
            <table class="table table-striped table-hover table-bordered align-middle text-center" id="customersTable">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Current Balance</th>
                        <!-- <th>Total Amount</th> -->
                        <th>Balance Details</th>
                        <!-- <th>Actions</th> -->
                    </tr>
                </thead>
                <tbody>
                    {{-- First, show Counter Sale on top --}}
                    @php
                    $counterCustomer = $customers->firstWhere('name', 'Counter Sale');
                    @endphp
                    @if($counterCustomer)
                    <tr>
                        <td style="background-color: #d4edda; color: #155724; font-weight: bold; font-size: 16px;">1</td>
                        <td style="background-color: #d4edda; color: #155724; font-weight: bold; font-size: 16px;">{{ $counterCustomer->name }}</td>
                        <td style="background-color: #d4edda; color: #155724; font-weight: bold; font-size: 16px;">{{ $counterCustomer->address }}</td>
                        <td style="background-color: #d4edda; color: #155724; font-weight: bold; font-size: 16px;">Rs 0.00</td>
                        <td style="background-color: #d4edda; color: #155724; font-weight: bold; font-size: 16px;">
                            <a href="{{ route('customers.show', $counterCustomer) }}" class="btn btn-sm btn-info text-white" title="View">
                                Balance <i class="material-icons">&#xE8F4;</i>
                            </a>
                        </td>
                    </tr>
                    @endif

                    {{-- Then, show the rest of the customers --}}
                    @foreach ($customers as $index => $customer)
                    @if($customer->name !== 'Counter Sale')
                    <tr>
                        <td>{{ ($customers->currentPage() - 1) * $customers->perPage() + $loop->iteration + ($counterCustomer ? 1 : 0) }}</td>
                        <td>{{ $customer->company_name }} ( {{ $customer->name }} )</td>
                        <td>{{ $customer->address }}</td>
                        <td>
                            Rs {{ number_format($customer->current_balance ?? 0, 2) }}
                        </td>
                        <!-- <td>Rs {{ number_format($customer->balance ?? 0, 2) }}</td> -->
                        <td>
                            <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-info text-white" title="View">
                                Balance <i class="material-icons">&#xE8F4;</i>
                            </a>
                        </td>
                        <!-- <td class="d-flex justify-content-center">
                            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-success me-1 text-white" title="Edit">
                                Edit <i class="material-icons">&#xE254;</i>
                            </a>
                            <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this customer?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    Delete <i class="material-icons">&#xE872;</i>
                                </button>
                            </form>
                        </td> -->
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {!! $customers->appends(['per_page' => request('per_page')])->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-info text-center mt-4">
        No customers found. <a href="{{ route('customers.create') }}" class="text-dark">Create one</a>.
    </div>
    @endif
</div>

<script>
    // Search input
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const value = this.value.toLowerCase();
        document.querySelectorAll('#customersTable tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
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