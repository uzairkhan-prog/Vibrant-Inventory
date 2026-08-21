@extends('layouts.app')

@section('content')

<div class="p-3 bg-white shadow rounded">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary mb-0">Sale Returns Management</h2>
        <div>
            <a href="{{ route('sale-returns.create') }}" class="btn btn-secondary">
                <i class="material-icons me-1">&#xE147;</i> Add Sale Return
            </a>
        </div>
    </div>

    <!-- @php $subtotal = $returns->sum('amount_deducted'); @endphp
    <div class="alert alert-success shadow-sm rounded-3 fs-6 fw-bold mb-4">
        <div class="d-flex justify-content-between">
            <span>Total Returned Value:</span>
            <span>Rs {{ number_format($subtotal, 2) }}</span>
        </div>
    </div> -->

    <div class="row mb-4 align-items-center">

        <!-- TOTAL CARD -->
        <div class="col-md-6">
            <div class="card border-0 rounded-4"
                style="box-shadow:0 4px 18px rgba(0,0,0,0.08); border:1px solid #e6edf5;">

                <div class="card-body py-3">
                    <div style="color:#7b8a9a;font-size:13px;font-weight:600;">
                        @if($monthYear == 'all')
                        All Records
                        @elseif($monthYear == 'custom')
                        {{ $fromDate }} to {{ $toDate }}
                        @else
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $monthYear)->format('F Y') }}
                        @endif
                    </div>

                    <div style="font-size:24px;font-weight:700;color:#dc3545;">
                        Rs {{ number_format($monthTotal, 2) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- MONTH FILTER -->
        <div class="col-md-6 text-md-end mt-3 mt-md-0">

            <form method="GET" action="{{ route('sale-returns.index') }}"
                class="d-inline-flex align-items-center"
                style="background:#eef3fb;padding:10px 14px;border-radius:14px;gap:10px;">

                <label style="font-weight:600;color:#2c6ed5;">Month:</label>

                <select name="month_year" onchange="this.form.submit()"
                    style="border:2px solid #2c6ed5;border-radius:10px;padding:6px 12px;">

                    <option value="all" {{ $monthYear == 'all' ? 'selected' : '' }}>
                        All Records
                    </option>

                    @foreach($months as $m)
                    <option value="{{ $m }}" {{ $monthYear == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $m)->format('F Y') }}
                    </option>
                    @endforeach

                </select>

                <!-- <button class="btn btn-primary">Filter</button> -->

                <a href="{{ route('sale-returns.index') }}" class="btn btn-secondary">Reset</a>

            </form>

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
            <form method="GET" action="{{ route('sale-returns.index') }}" class="d-flex align-items-center">
                <label class="me-2 fw-semibold">From:</label>
                <input type="date" name="from_date" class="form-control me-2" value="{{ $fromDate ?? '' }}">
                <label class="me-2 fw-semibold">To:</label>
                <input type="date" name="to_date" class="form-control me-2" value="{{ $toDate ?? '' }}">
                <br>
                <button type="submit" class="btn btn-primary me-2">Search</button>
                <a href="{{ route('sale-returns.index') }}" class="btn btn-secondary">Reset</a>
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

    @if($returns->count())
    <div class="table-responsive">
        <div class="table-wrapper">
            <table class="table table-striped table-hover table-bordered align-middle text-center" id="returnsTable">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Invoice</th>
                        <th>Product</th>
                        <th>Packing</th>
                        <th>Qty</th>
                        <th>Deduction</th>
                        <th>Net Refund</th>
                        <!-- <th>Total After</th> -->
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($returns as $index => $r)
                    <tr>
                        <td>{{ ($returns->currentPage() - 1) * $returns->perPage() + $loop->iteration }}</td>
                        <td>{{ $r->created_at->format('Y-m-d') }}</td>
                        <td>{{ $r->customer->name }}</td>
                        <td>{{ $r->sale_id }}</td>
                        <td>{{ $r->product->name }}</td>
                        <td>{{ $r->packing }}</td>
                        <td>{{ $r->qty_return }}</td>
                        <td>Rs {{ number_format($r->cut_amount, 2) }}</td>
                        <td>Rs {{ number_format($r->amount_deducted, 2) }}</td>
                        <!-- <td>Rs {{ number_format($r->total_after_return, 2) }}</td> -->
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