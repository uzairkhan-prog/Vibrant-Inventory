@extends('layouts.app')

@section('content')

@if(session('status'))
<div class="alert alert-success">
  {{ session('status') }}
</div>
@endif

<div class="container-fluid py-4">

    <!-- Dashboard Summary Cards -->
    <div class="row mb-4">
        @foreach ([
            ['title' => 'Products', 'value' => $totalProducts, 'color' => 'primary'],
            ['title' => 'Categories', 'value' => $totalCategories, 'color' => 'info'],
            ['title' => 'Suppliers', 'value' => $totalSuppliers, 'color' => 'warning'],
            ['title' => 'Customers', 'value' => $totalCustomers, 'color' => 'success'],
            ['title' => 'Purchases', 'value' => $totalPurchases, 'color' => 'secondary'],
            ['title' => 'Sales', 'value' => $totalSales, 'color' => 'danger'],
            ['title' => 'Expenses', 'value' => $totalExpenses, 'color' => 'info'],
            ['title' => 'Assets', 'value' => $totalAssets, 'color' => 'warning'],
            ['title' => 'Ledgers', 'value' => $totalLedgers, 'color' => 'primary'],
        ] as $item)
        <div class="col-md-4 col-lg-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">{{ $item['title'] }}</h6>
                    <h3 class="fw-bold text-{{ $item['color'] }}">{{ $item['value'] }}</h3>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Monthly Sales & Purchases Chart -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-3">Monthly Sales & Purchases</h4>
                    <div id="sales-purchases-chart" style="height: 400px;"></div>
                </div>
            </div>
        </div>
    </div>

</div> <!-- container -->

<!-- ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    var options = {
        chart: {
            type: 'bar',
            height: 400
        },
        series: [{
            name: 'Sales',
            data: [
                @foreach($monthlySales as $sale)
                    {{ $sale->total }},
                @endforeach
            ]
        }, {
            name: 'Purchases',
            data: [
                @foreach($monthlyPurchases as $purchase)
                    {{ $purchase->total }},
                @endforeach
            ]
        }],
        xaxis: {
            categories: [
                @foreach($monthlySales as $sale)
                    'Month {{ $sale->month }}',
                @endforeach
            ]
        },
        colors: ['#00c292', '#03a9f3'],
        dataLabels: {
            enabled: true
        }
    };

    var chart = new ApexCharts(document.querySelector("#sales-purchases-chart"), options);
    chart.render();
</script>

@endsection