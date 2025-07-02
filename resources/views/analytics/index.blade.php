@extends('layouts.app')

@section('content')
@if(session('status'))
<div class="alert alert-success">
  {{ session('status') }}
</div>
@endif

<style>
    .dashboard-wrapper {
        background: #f8f9fa;
        border-radius: 20px;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
        padding: 40px;
        transition: all 0.3s ease-in-out;
        border: 1px solid #dee2e6;
    }
    .dashboard-wrapper:hover {
        box-shadow: 0 18px 36px rgba(0, 0, 0, 0.12);
        transform: scale(1.01);
    }

    /* Colorful metric cards with gradients */
    .card-metric {
        border-radius: 16px;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease-in-out;
        color: white;
        font-weight: 700;
        cursor: default;
    }
    .card-metric:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
    }
    /* Individual colors for each card */
    .card-products {
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
        border: none;
    }
    .card-categories {
        background: linear-gradient(135deg, #198754, #146c43);
        border: none;
    }
    .card-suppliers {
        background: linear-gradient(135deg, #fd7e14, #cc6400);
        border: none;
    }
    .card-customers {
        background: linear-gradient(135deg, #dc3545, #a52729);
        border: none;
    }
    .card-purchases {
        background: linear-gradient(135deg, #6f42c1, #4a2788);
        border: none;
    }
    .card-sales {
        background: linear-gradient(135deg, #20c997, #198754);
        border: none;
    }
    .card-expenses {
        background: linear-gradient(135deg, #0dcaf0, #0a8abf);
        border: none;
    }
    .card-assets {
        background: linear-gradient(135deg, #ffc107, #cc9a06);
        border: none;
    }
    .card-ledgers {
        background: linear-gradient(135deg, #6610f2, #440dbf);
        border: none;
    }
    .card-total {
        background: linear-gradient(135deg, #343a40, #212529);
        border: none;
    }

    .metric-label {
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.05rem;
        opacity: 0.8;
        margin-bottom: 4px;
    }
    .metric-value {
        font-size: 32px;
        font-weight: 900;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    }

    /* Colorful heading */
    .dashboard-heading {
        font-size: 36px;
        font-weight: 900;
        color: #0d6efd;
        text-transform: uppercase;
        letter-spacing: 0.15rem;
        text-shadow: 2px 2px 6px rgba(13, 110, 253, 0.4);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin-bottom: 30px;
        user-select: none;
        transition: color 0.3s ease;
    }
    .dashboard-heading:hover {
        color: #6610f2;
        text-shadow: 3px 3px 8px rgba(102, 16, 242, 0.5);
        cursor: default;
    }

    .chart-section {
        background: linear-gradient(135deg, #ffffff, #f8f9fa);
        border-radius: 16px;
        padding: 30px;
        border: 1px solid #dee2e6;
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
    }
    .chart-section:hover {
        box-shadow: 0 14px 36px rgba(0, 0, 0, 0.10);
        transform: translateY(-5px);
    }
    .chart-section:hover .section-title {
        color: #0d6efd;
        text-shadow: 1px 1px 3px rgba(13, 110, 253, 0.1);
    }
    .section-title {
        font-weight: bold;
        font-size: 20px;
        color: #495057;
        text-align: center;
        margin-bottom: 25px;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }
</style>

<div class="container-fluid py-4">
    <div class="dashboard-wrapper">
        <!-- Title -->
        <div class="d-flex align-items-center mb-4">
            <i class="bi bi-graph-up-arrow text-primary fs-2 me-2"></i>
            <h2 class="dashboard-heading mb-0">Shipping & Inventory Status Dashboard</h2>
        </div>

        <!-- Metric Cards -->
        <div class="row g-4 flex-nowrap overflow-auto">
            @foreach ([
                ['title' => 'Products', 'value' => $totalProducts, 'class' => 'card-products'],
                ['title' => 'Categories', 'value' => $totalCategories, 'class' => 'card-categories'],
                ['title' => 'Suppliers', 'value' => $totalSuppliers, 'class' => 'card-suppliers'],
                ['title' => 'Customers', 'value' => $totalCustomers, 'class' => 'card-customers'],
                ['title' => 'Purchases', 'value' => $totalPurchases, 'class' => 'card-purchases'],
                ['title' => 'Sales', 'value' => $totalSales, 'class' => 'card-sales'],
                ['title' => 'Expenses', 'value' => $totalExpenses, 'class' => 'card-expenses'],
                ['title' => 'Assets', 'value' => $totalAssets, 'class' => 'card-assets'],
                ['title' => 'Ledgers', 'value' => $totalLedgers, 'class' => 'card-ledgers'],
            ] as $item)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card card-metric text-center p-3 {{ $item['class'] }}">
                    <div class="card-body">
                        <div class="metric-label mb-1">{{ $item['title'] }}</div>
                        <div class="metric-value">{{ $item['value'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Metric Cards -->
        <div class="row g-4 flex-nowrap overflow-auto pt-4">

            <!-- Totals Cards -->
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card card-metric text-center p-3 card-total">
                    <div class="card-body">
                        <div class="metric-label mb-1">Total Purchases Amount</div>
                        <div class="metric-value">Rs {{ number_format($sumPurchasesAmount, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card card-metric text-center p-3 card-total">
                    <div class="card-body">
                        <div class="metric-label mb-1">Total Sales Amount</div>
                        <div class="metric-value">Rs {{ number_format($sumSalesAmount, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card card-metric text-center p-3 card-total">
                    <div class="card-body">
                        <div class="metric-label mb-1">Total Expenses Amount</div>
                        <div class="metric-value">Rs {{ number_format($sumExpensesAmount, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card card-metric text-center p-3 card-total">
                    <div class="card-body">
                        <div class="metric-label mb-1">Total Assets Amount</div>
                        <div class="metric-value">Rs {{ number_format($sumAssetsAmount, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card card-metric text-center p-3 card-total">
                    <div class="card-body">
                        <div class="metric-label mb-1">Total Ledger Balance</div>
                        <div class="metric-value">Rs {{ number_format($totalLedgerBalance, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row mt-5">
            <div class="col-lg-6 mb-4">
                <div class="chart-section">
                    <h4 class="section-title">📦 Inventory Category Breakdown</h4>
                    <div id="inventory-pie" style="height: 350px;"></div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="chart-section">
                    <h4 class="section-title">📉 Monthly Sales vs Purchases (Zig-Zag)</h4>
                    <div id="financial-bar" style="height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Scripts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const pieSeries = [
        {{ $totalProducts }},
        {{ $totalSuppliers }},
        {{ $totalCustomers }},
        {{ $totalAssets }}
    ];

    new ApexCharts(document.querySelector("#inventory-pie"), {
        chart: { type: 'donut', height: 350 },
        series: pieSeries,
        labels: ['Products', 'Suppliers', 'Customers', 'Assets'],
        colors: ['#0d6efd', '#198754', '#fd7e14', '#dc3545'],
        legend: { position: 'bottom' },
        dataLabels: { enabled: true },
        title: {
            text: 'Current Inventory Composition',
            align: 'center',
            style: { fontSize: '16px', fontWeight: '600' }
        }
    }).render();

    const salesData = [
        @foreach($monthlySales as $sale)
            {{ $sale->total }},
        @endforeach
    ];
    const purchasesData = [
        @foreach($monthlyPurchases as $purchase)
            {{ $purchase->total }},
        @endforeach
    ];
    const months = [
        @foreach($monthlySales as $sale)
            '{{ \Carbon\Carbon::create()->month($sale->month)->format("F") }}',
        @endforeach
    ];

    new ApexCharts(document.querySelector("#financial-bar"), {
        chart: {
            type: 'line',
            height: 350,
            zoom: { enabled: false },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
                animateGradually: { enabled: true, delay: 150 },
                dynamicAnimation: { enabled: true, speed: 350 }
            }
        },
        series: [
            { name: 'Sales', data: salesData },
            { name: 'Purchases', data: purchasesData }
        ],
        xaxis: {
            categories: months,
            title: { text: 'Month', style: { fontWeight: 600, fontSize: '14px' } }
        },
        yaxis: {
            title: { text: 'Amount (Rs)', style: { fontWeight: 600, fontSize: '14px' } }
        },
        stroke: {
            curve: 'stepline',
            width: 3
        },
        colors: ['#6610f2', '#20c997'],
        markers: { size: 6 },
        dataLabels: { enabled: false },
        tooltip: {
            y: {
                formatter: val => 'Rs ' + val.toLocaleString()
            }
        },
        legend: { position: 'top' }
    }).render();
</script>
@endsection
