@extends('layouts.app')

@section('content')

@include('analytics.css')

@if(auth()->user()->role == 'admin')

<div id="reportContent">

    <!-- HEADER -->

    <div class="d-flex justify-content-between align-items-center mb-4 dashboard-header">

        <div>
            <h2 class="mb-0">Analytics Dashboard</h2>
            <small>Profit & Loss Overview</small>
        </div>

        <button id="exportPdfBtn" class="btn btn-danger shadow-sm">
            Export PDF
        </button>

    </div>

    <!-- FILTER -->

    <form method="GET" action="{{ route('analytics.index') }}">

        <div class="card filter-card mb-4">

            <div class="card-body">

                <div class="row g-3 align-items-end">

                    <div class="col-md-4">
                        <label>From Date</label>
                        <input type="date" class="form-control" name="from_date" value="{{ $fromDate }}">
                    </div>

                    <div class="col-md-4">
                        <label>To Date</label>
                        <input type="date" class="form-control" name="to_date" value="{{ $toDate }}">
                    </div>

                    <div class="col-md-4 text-md-end">

                        <button class="btn btn-primary px-4">
                            Search
                        </button>

                        <a href="{{ route('dashboard') }}" class="btn btn-warning px-4">
                            Clear
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </form>

    <!-- KPI CARDS -->

    <div class="row g-4 mb-4">

        <div class="col-xl-3 col-md-6">

            <div class="kpi-card kpi-sales text-center">

                <div class="kpi-title">Total Sales</div>

                <div class="kpi-value counter"
                    data-target="{{ $totalSales }}">
                    {{ number_format($totalSales,2) }}
                </div>

                <div class="kpi-badge">100%</div>

            </div>

        </div>

        <div class="col-xl-3 col-md-6">

            <div class="kpi-card kpi-cogs text-center">

                <div class="kpi-title">COGS</div>

                <div class="kpi-value counter"
                    data-target="{{ $adjustedCOGS }}">
                    {{ number_format($adjustedCOGS,2) }}
                </div>

                <div class="kpi-badge">
                    {{ $adjustedSales > 0 ? number_format(($adjustedCOGS/$adjustedSales)*100,2) : '0' }}%
                </div>

            </div>

        </div>

        <div class="col-xl-3 col-md-6">

            <div class="kpi-card kpi-gross text-center">

                <div class="kpi-title">Gross Profit</div>

                <div class="kpi-value counter"
                    data-target="{{ $grossProfit }}">
                    {{ number_format($grossProfit,2) }}
                </div>

                <div class="kpi-badge">
                    {{ number_format($gpPercent,2) }}%
                </div>

            </div>

        </div>

        <div class="col-xl-3 col-md-6">

            <div class="kpi-card kpi-net text-center">

                <div class="kpi-title">Net Profit</div>

                <div class="kpi-value counter"
                    data-target="{{ $netProfit }}">
                    {{ number_format($netProfit,2) }}
                </div>

                <div class="kpi-badge">
                    {{ number_format($npPercent,2) }}%
                </div>

            </div>

        </div>

    </div>

    <!-- MAIN SECTION -->

    <div class="row g-4">

        <!-- FINANCIAL -->

        <div class="col-lg-6">

            <div class="card analytics-card h-100">

                <div class="card-header">
                    <h5>Financial Breakdown</h5>
                </div>

                <div class="card-body analytics-list">

                    <div class="d-flex justify-content-between">
                        <span>Total Sales</span>
                        <strong>{{ number_format($totalSales,2) }}</strong>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Total Sale Return</span>
                        <strong>{{ number_format($totalSaleReturn,2) }}</strong>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>COGS</span>
                        <strong>{{ number_format($adjustedCOGS,2) }}</strong>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Expenses</span>
                        <strong>{{ number_format($totalExpenses,2) }}</strong>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">

                        <span class="fw-bold">Net Profit</span>

                        <strong class="text-success">
                            {{ number_format($netProfit,2) }}
                        </strong>

                    </div>

                </div>

            </div>

        </div>

        <!-- CHART -->

        <div class="col-lg-6">

            <div class="card analytics-card h-100">

                <div class="card-header text-center">
                    <h5>Profit % Breakdown</h5>
                </div>

                <div class="card-body d-flex justify-content-center align-items-center">

                    <div class="chart-wrapper">
                        <canvas id="donutChart"></canvas>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- SECONDARY STATS -->

    <div class="row g-4 mt-1">

        <div class="col-lg-6">

            <div class="card analytics-card">

                <div class="card-header">
                    <h5>Profit Percentage</h5>
                </div>

                <div class="card-body">

                    <div class="d-flex justify-content-between">
                        <span>Gross Profit %</span>
                        <strong>{{ number_format($gpPercent,2) }}%</strong>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Expenses %</span>
                        <strong>{{ number_format($expensePercent,2) }}%</strong>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">

                        <span class="fw-bold">Net Profit %</span>

                        <strong class="text-success">
                            {{ number_format($npPercent,2) }}%
                        </strong>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-lg-6">

            <div class="card analytics-card">

                <div class="card-header">
                    <h5>Inventory & Purchase Stats</h5>
                </div>

                <div class="card-body">

                    <div class="d-flex justify-content-between">
                        <span>Purchases</span>
                        <strong>{{ number_format($totalPurchases,2) }}</strong>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Purchased Qty</span>
                        <strong>{{ $purchaseQty }}</strong>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Sale Qty</span>
                        <strong>{{ $saleQty }}</strong>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Purchase %</span>
                        <strong>{{ number_format($purchasePercent,2) }}%</strong>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@include('analytics.js')

@endif

@endsection