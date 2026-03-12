@extends('layouts.app')

@section('content')

<div class="p-4 bg-white shadow rounded">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-danger mb-0">Outstanding Suppliers (Debtors)</h2>
        <a href="{{ route('suppliers.index') }}" class="btn btn-success">
            Supplier Ledger
        </a>
    </div>

    {{-- TOTAL OUTSTANDING --}}
    <div class="alert alert-danger shadow-sm rounded-3 fs-5 fw-bold mb-4">
        <div class="d-flex justify-content-between">
            <span>Total Outstanding Amount:</span>
            <span>Rs {{ number_format($totalOutstanding, 2) }}</span>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-3 align-items-center">
        <div class="col-md-10 d-flex align-items-center">
            <label class="me-2 fw-semibold">Search:</label>
            <input type="text" id="searchInput" class="form-control w-100" placeholder="Search supplier">
        </div>

        <div class="col-md-2 d-flex justify-content-end align-items-center">
            <label class="me-2 fw-semibold">Show</label>
            <select id="rowsPerPage" class="form-select w-auto">
                @foreach ([20, 50, 100] as $value)
                <option value="{{ $value }}" {{ request('per_page') == $value ? 'selected' : '' }}>
                    {{ $value }}
                </option>
                @endforeach
            </select>
        </div>
    </div>

    @if($suppliers->count())

    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped text-center align-middle" id="suppliersTable">

            <thead class="table-danger">
                <tr>
                    <th>#</th>
                    <th>Supplier</th>
                    <th>Address</th>
                    <!-- <th>Total Sales</th>
                    <th>Total Paid</th> -->
                    <th>Outstanding</th>
                    <th>Ledger</th>
                </tr>
            </thead>

            <tbody>
                @foreach($suppliers as $index => $supplier)
                @php
                $isCounter = strtolower(trim($supplier->name)) === 'counter sale';
                @endphp
                <tr class="{{ $isCounter ? 'counter-td' : '' }}">
                    <td>{{ ($suppliers->currentPage() - 1) * $suppliers->perPage() + $loop->iteration }}</td>
                    <td>
                        <strong>
                            {{ $supplier->company_name }}
                            ( {{ $supplier->name }} )
                        </strong>
                    </td>

                    <td>{{ $supplier->address }}</td>

                    <!-- <td class="text-primary fw-bold">
                        Rs {{ number_format($supplier->total_sales, 2) }}
                    </td>

                    <td class="text-success fw-bold">
                        Rs {{ number_format($supplier->total_paid, 2) }}
                    </td> -->

                    <td class="text-danger fw-bold">
                        Rs {{ number_format($supplier->outstanding, 2) }}
                    </td>

                    <td>
                        <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-sm btn-secondary">
                            View Ledger
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>

        </table>

        <div class="d-flex justify-content-center mt-4">
            {!! $suppliers->links('pagination::bootstrap-5') !!}
        </div>

    </div>

    @else
    <div class="alert alert-success text-center">
        🎉 No outstanding suppliers! Everyone has paid.
    </div>
    @endif

</div>

<style>
    /* Counter Sale highlight */
    tr.counter-td td {
        background-color: #d4edda !important;
        /* color: #155724 !important; */
        font-weight: bold;
        font-size: 16px;
    }

    /* tr.counter-td td a {
        background-color: #d4edda !important;
        color: #155724 !important;
    } */
</style>

<script>
    // search
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const value = this.value.toLowerCase();
        document.querySelectorAll('#suppliersTable tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
        });
    });

    // rows per page
    document.getElementById('rowsPerPage').addEventListener('change', function() {
        const selected = this.value;
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', selected);
        window.location.href = url.toString();
    });
</script>

@endsection