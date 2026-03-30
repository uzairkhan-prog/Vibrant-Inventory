@extends('layouts.app')

@section('content')

<div class="p-3 bg-white shadow rounded">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary mb-0">Sales Invoice Management</h2>
        <div>
            <a href="{{ route('sales.create') }}" class="btn btn-secondary">
                <i class="material-icons me-1">&#xE147;</i> Add Sale
            </a>
        </div>
    </div>

    <!-- @php $subtotal = $sales->sum('total_amount'); @endphp
    <div class="alert alert-success shadow-sm rounded-3 fs-6 fw-bold mb-4">
        <div class="d-flex justify-content-between">
            <span>Total Sales Value:</span>
            <span>Rs {{ number_format($subtotal, 2) }}</span>
        </div>
    </div> -->

    <div class="row mb-4 align-items-center">

        <!-- GRAND TOTAL (LEFT CARD) -->
        <div class="col-md-6">
            <div class="card border-0 rounded-4"
                style="box-shadow:0 4px 18px rgba(0,0,0,0.08); border: 1px solid #e6edf5 !important;">

                <div class="card-body py-3">

                    <div class="d-flex justify-content-between align-items-center">

                        <!-- All Time Total -->
                        <!-- <div>
                            <div style="color:#7b8a9a;font-size:13px;font-weight:600;">
                                Total Sale (All Time)
                            </div>

                            <div style="font-size:24px;font-weight:700;color:#20b26b;">
                                Rs {{ number_format($allTimeTotal, 2) }}
                            </div>
                        </div> -->

                        <!-- Divider -->
                        <!-- <div style="height:45px;width:1px;background:#e2e8f0;"></div> -->

                        <!-- Selected Period -->
                        <div>
                            <div style="color:#7b8a9a;font-size:13px;font-weight:600;">
                                @if($monthYear == 'all')
                                All Records
                                @elseif($monthYear == 'custom')
                                {{ $fromDate }} to {{ $toDate }}
                                @else
                                {{ \Carbon\Carbon::createFromFormat('Y-m', $monthYear)->format('F Y') }}
                                @endif
                            </div>

                            <div style="font-size:24px;font-weight:700;color:#20b26b;">
                                Rs {{ number_format($monthTotal, 2) }}
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <!-- MONTH FILTER (RIGHT SIDE PILL) -->
        <div class="col-md-6 text-md-end mt-3 mt-md-0">

            <form method="GET" action="{{ route('sales.index') }}"
                class="d-inline-flex align-items-center"
                style="
                background:#eef3fb;
                padding:10px 14px;
                border-radius:14px;
                box-shadow:0 4px 14px rgba(0,0,0,0.06);
                border:1px solid #dbe5f1;
                gap:10px;
            ">

                <label style="font-weight:600;color:#2c6ed5;margin-bottom:0;">
                    Month:
                </label>

                <select name="month_year"
                    onchange="this.form.submit()"
                    style="
                    border:2px solid #2c6ed5;
                    border-radius:10px;
                    padding:6px 12px;
                    outline:none;
                    font-weight:600;
                    color:#1e293b;
                    background:white;
                ">

                    {{-- All Records Option --}}
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

                <!-- <button type="submit"
                    style="
                    background:#2c6ed5;
                    color:white;
                    border:none;
                    padding:7px 18px;
                    border-radius:10px;
                    font-weight:600;
                    box-shadow:0 3px 10px rgba(44,110,213,0.35);
                    transition:0.2s;
                "
                    onmouseover="this.style.background='#1f5cc1'"
                    onmouseout="this.style.background='#2c6ed5'">
                    Filter
                </button> -->

                <a href="{{ route('sales.index') }}" class="btn btn-secondary">Reset</a>

            </form>

        </div>

    </div>

    <!-- Filters Row -->
    <div class="row mb-3 align-items-center gap-2">
        <!-- Existing Search -->
        <div class="col-xl-12 col-md-4 d-flex align-items-center">
            <label class="me-2 fw-semibold">Search:</label>
            <form method="GET" action="{{ route('sales.index') }}" class="d-flex w-100">
                <input type="hidden" name="month_year" value="{{ $monthYear }}">
                <input type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="Search invoice, customer or amount">
                <button class="btn btn-primary ms-2">Search</button>
            </form>
        </div>

        <!-- Date Filters -->
        <div class="col-xl-12 col-md-4 d-flex align-items-center justify-content-center">
            <form method="GET" action="{{ route('sales.index') }}" class="d-flex align-items-center">
                <label class="me-2 fw-semibold">From:</label>
                <input type="date" name="from_date" class="form-control me-2" value="{{ $fromDate ?? '' }}">
                <label class="me-2 fw-semibold">To:</label>
                <input type="date" name="to_date" class="form-control me-2" value="{{ $toDate ?? '' }}">
                <br>
                <button type="submit" class="btn btn-primary me-2">Search</button>
                <a href="{{ route('sales.index') }}" class="btn btn-secondary">Reset</a>
                <label class="ms-3 me-2 fw-semibold">Show</label>
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

    @if($sales->count())
    <div class="table-responsive">
        <div class="table-wrapper">
            <table class="table table-striped table-hover table-bordered align-middle text-center" id="salesTable">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Invoice no</th>
                        <th>Customer</th>
                        <th>Total Amount</th>
                        <th>View Invoice</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sales as $sale)
                    <tr>
                        <td @if($sale->customer->name === 'Counter Sale') style="background-color: #d4edda; color: #155724; font-weight: bold;" @endif>{{ \Carbon\Carbon::parse($sale->date)->format('Y-m-d') }}</td>
                        <td @if($sale->customer->name === 'Counter Sale') style="background-color: #d4edda; color: #155724; font-weight: bold;" @endif>{{ $sale->id }}</td>
                        <td @if($sale->customer->name === 'Counter Sale') style="background-color: #d4edda; color: #155724; font-weight: bold;" @endif>{{ $sale->customer->company_name }} ( {{ $sale->customer->name }} )</td>
                        <td @if($sale->customer->name === 'Counter Sale') style="background-color: #d4edda; color: #155724; font-weight: bold;" @endif>Rs {{ number_format($sale->total_amount ?? 0, 2) }}</td>
                        <td @if($sale->customer->name === 'Counter Sale') style="background-color: #d4edda; color: #155724; font-weight: bold;" @endif>
                            <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-info me-1">
                                Invoice <i class="material-icons">&#xE8F4;</i>
                            </a>
                        </td>
                        <td class="d-flex justify-content-center" @if($sale->customer->name === 'Counter Sale') style="background-color: #d4edda; color: #155724; font-weight: bold;" @endif>
                            <a href="{{ route('sales.edit', $sale) }}" class="btn btn-sm btn-warning me-1">
                                Edit <i class="material-icons">&#xE3C9;</i>
                            </a>
                            <form action="{{ route('sales.destroy', $sale) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete sale?')">
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

    <div class="d-flex justify-content-center mt-4">
        {!! $sales->appends(request()->all())->links('pagination::bootstrap-5') !!}
    </div>

    @else
    <div class="alert alert-info text-center mt-4">No sales found. <a href="{{ route('sales.create') }}" class="text-dark">Create one</a>.</div>
    @endif
</div>

<script>
    // Search input (existing)
    // document.getElementById('searchInput').addEventListener('keyup', function() {
    //     const searchVal = this.value.toLowerCase();
    //     const rows = document.querySelectorAll('#salesTable tbody tr');
    //     rows.forEach(row => {
    //         const rowText = row.innerText.toLowerCase();
    //         row.style.display = rowText.includes(searchVal) ? '' : 'none';
    //     });
    // });

    // Rows per page select
    document.getElementById('rowsPerPage').addEventListener('change', function() {
        const selected = this.value;
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', selected);
        window.location.href = url.toString();
    });
</script>

@endsection