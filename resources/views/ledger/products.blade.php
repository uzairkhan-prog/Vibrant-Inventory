@extends('layouts.app')

@section('content')

<div class="table-responsive">
    <div class="table-wrapper">

        <!-- Header -->
        <div class="table-title">
            <div class="row">
                <div class="col-md-12">
                    <h2>Product <b>Ledger</b></h2>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-3 align-items-center">
            <div class="col-md-10 d-flex align-items-center">
                <label class="me-2 fw-semibold">Search:</label>
                <input type="text" id="searchInput" class="form-control" placeholder="Search expense name, description...">
            </div>
            <div class="col-md-2 d-flex justify-content-end align-items-center">
                <label class="me-2 fw-semibold">Show</label>
                <select id="rowsPerPage" class="form-select w-auto">
                    @foreach ([5, 10, 50, 100] as $value)
                    <option value="{{ $value }}" {{ request('per_page', $perPage) == $value ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                    @endforeach
                </select>
                <label class="ms-2 fw-semibold">entries</label>
            </div>
        </div>

        @if($ledgerEntries->count())
        <table class="table table-striped table-hover" id="ledgerTable">
            <thead>
                <tr>
                    <th>Invoice No</th>
                    <!-- <th>#</th>
                    <th>Date</th> -->
                    <th>Type</th>
                    <th>Product Name</th>
                    <th>QTY</th>
                    <th>Unit Price</th> <!-- New Column -->
                    <th>Invoice Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ledgerEntries as $index => $entry)
                <tr>
                    <td>#{{ $entry['invoice_no'] }}</td>
                    <!-- <td>{{ ($ledgerEntries->currentPage() - 1) * $ledgerEntries->perPage() + $loop->iteration }}</td>
                    <td>{{ $entry['date'] }}</td> -->
                    <td>{{ $entry['type'] }}</td>
                    <td>{{ $entry['product_name'] }}</td>
                    <td>{{ $entry['qty'] }}</td>
                    <td>Rs {{ number_format($entry['unit_price'], 2) }}</td> <!-- New Column Value -->
                    <td>Rs {{ number_format($entry['invoice_value'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-3">
            {!! $ledgerEntries->appends(['per_page' => request('per_page')])->links('pagination::bootstrap-5') !!}
        </div>

        <!-- Totals Section -->
        <div class="alert alert-info mt-4 p-3 fw-bold fs-5">
            <div class="row mb-2">
                <div class="col-md-6">
                    <strong>Total Units Sold:</strong> {{ $total_sold_qty }}
                </div>
                <div class="col-md-6">
                    <strong>Total Amount Sold:</strong> Rs {{ number_format($total_sold_value, 2) }}
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <strong>Total Units Purchased:</strong> {{ $total_purchase_qty }}
                </div>
                <div class="col-md-6">
                    <strong>Total Amount Purchased:</strong> Rs {{ number_format($total_purchase_value, 2) }}
                </div>
            </div>
        </div>
        @else
        <div class="alert alert-info text-center">No ledger data found.</div>
        @endif

    </div>
</div>

<!-- JS -->
<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const value = this.value.toLowerCase();
        document.querySelectorAll('#ledgerTable tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
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