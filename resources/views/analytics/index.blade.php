@extends('layouts.app')

@section('content')

@include('analytics.css')

<div id="reportContent">

    <!-- FILTER SECTION -->
    <form method="GET" action="{{ route('analytics.index') }}">
        <div class="p-4 bg-white shadow rounded-4 mb-3">
            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label fw-semibold">From Date</label>
                    <input type="date" class="form-control" name="from_date" value="{{ request('from_date') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">To Date</label>
                    <input type="date" class="form-control" name="to_date" value="{{ request('to_date') }}">
                </div>

                <div class="col-md-4 filter-btn-wrapper text-md-end">
                    <button class="btn btn-primary">Search</button>
                    <a href="{{ route('dashboard') }}" class="btn btn-warning">Clear</a>
                    <button type="button" id="exportPdfBtn" class="btn btn-danger">Export PDF</button>
                </div>

            </div>
        </div>
    </form>

    <!-- SUMMARY SECTION -->
    <div class="p-4 bg-white shadow rounded-4">

        <h3 class="section-title">Profit & Loss Summary</h3>

        <div class="row summary-box">

            <!-- LEFT SUMMARY -->
            <div class="col-md-6 border-end">

                <strong>Total Sales:</strong> <span class="label-line"></span>
                {{ number_format($totalSales, 2) }} <span class="percent-value">(100%)</span><br>

                <strong>Total Sale Return:</strong> <span class="label-line"></span>
                {{ number_format($totalSaleReturn, 2) }}<br>

                <strong>COGS:</strong> <span class="label-line"></span>
                {{ number_format($adjustedCOGS, 2) }}
                <span class="percent-value">(
                    {{ $adjustedSales > 0 ? number_format(($adjustedCOGS/$adjustedSales)*100, 2) : '0' }}%
                    )</span><br>

                <strong>Gross Profit:</strong> <span class="label-line"></span>
                {{ number_format($grossProfit, 2) }}
                <span class="percent-value">({{ number_format($gpPercent, 2) }}%)</span><br>

                <strong>Expenses:</strong> <span class="label-line"></span>
                {{ number_format($totalExpenses, 2) }}
                <span class="percent-value">({{ number_format($expensePercent, 2) }}%)</span><br>

                <hr>

                <strong>Net Profit:</strong> <span class="label-line"></span>
                {{ number_format($netProfit, 2) }}
                <span class="percent-value">({{ number_format($npPercent, 2) }}%)</span><br>

            </div>

            <!-- RIGHT CHART -->
            <div class="col-md-6 d-flex align-items-center justify-content-center">
                <div class="chart-container">
                    <div class="chart-card">
                        <h5 class="fw-bold text-center">Profit % Breakdown</h5>
                        <canvas id="donutChart"></canvas>
                    </div>
                </div>
            </div>

        </div>

        <hr>

        <h3 class="section-title">Percent Summary</h3>

        <div class="row">
            <div class="col-md-6 summary-box border-end">
                <strong>Gross Profit %:</strong> <span class="label-line"></span>
                <span class="percent-value">{{ number_format($gpPercent, 2) }}%</span><br>

                <strong>Expenses %:</strong> <span class="label-line"></span>
                <span class="percent-value">{{ number_format($expensePercent, 2) }}%</span><br>

                <hr>

                <strong>Net Profit %:</strong> <span class="label-line"></span>
                <span class="percent-value">{{ number_format($npPercent, 2) }}%</span>
            </div>

            <div class="col-md-6 summary-box">
                <strong>Purchases:</strong> <span class="label-line"></span>
                {{ number_format($totalPurchases, 2) }}
                <span class="percent-value">(
                    {{ $adjustedSales > 0 ? number_format(($totalPurchases/$adjustedSales)*100, 2) : '0' }}%
                    )</span><br>

                <strong>Purchased Qty:</strong> <span class="label-line"></span>
                0<br>

                <strong>Sale Qty:</strong> <span class="label-line"></span>
                {{ $purchaseQty }}<br>

                <strong>Purchase %:</strong> <span class="label-line"></span>
                <span class="percent-value">{{ number_format($purchasePercent, 2) }}%</span>
            </div>
        </div>

    </div>
</div>

<!-- PDF CONTENT ----------------------------------->
<div id="pdfContent" style="display: none; width:210mm; padding:40px; background:#fff; font-family:Arial;">

    <!-- PDF HEADER -->
    <div style="margin-bottom:20px;">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/images/logos/logo-export.png'))) }}"
            alt="Vibrant Engineering Logo"
            style="max-height: 60px; border-radius: 10px; margin-bottom: 10px; background: linear-gradient(43deg, #11142d 29%, #0B4168 80%);">

        <p class="mb-1">Head Office: Shop #13, Falak Park View Near <br> Inquiry Office Nazimabad #2, Karachi</p>
        <p class="mb-1">Phone: +92 335 2385773</p>
        <p class="mb-0">Email: info@vibrantengineering.pk</p>
    </div>

    <h2 style="font-size:30px; margin-bottom:15px; color:#4d71e1;">
        Profit & Loss Report
    </h2>

    <p style="margin-bottom:25px; font-size:18px;">
        Date: {{ now()->format('d M, Y') }}
    </p>

    <div style="font-size:18px; line-height:30px;">
        <strong>Total Sales:</strong> {{ number_format($totalSales, 2) }} (100%)<br>

        <strong>COGS:</strong> {{ number_format($adjustedCOGS, 2) }} ({{ $adjustedSales > 0 ? number_format(($adjustedCOGS/$adjustedSales)*100, 2) : '0' }}%)<br>

        <strong>Expenses:</strong> {{ number_format($totalExpenses, 2) }} ({{ $adjustedSales > 0 ? number_format(($totalExpenses/$adjustedSales)*100, 2) : '0' }}%)

        <hr>

        <strong>Net Profit:</strong> {{ number_format($netProfit, 2) }} ({{ $adjustedSales > 0 ? number_format(($netProfit/$adjustedSales)*100, 2) : '0' }}%)<br>
    </div>

    <hr>

    <h3 style="font-size:24px; color:#4d71e1;">Summary:</h3>

    <div style="font-size:18px; line-height:30px;">
        <strong>Gross Profit %:</strong> {{ number_format($gpPercent, 2) }}%<br>

        <strong>Expenses %:</strong> {{ number_format($expensePercent, 2) }}%

        <hr>

        <strong>Net Profit %:</strong> {{ number_format($npPercent, 2) }}%
    </div>

    <div style="margin-top:60px; font-size:22px; font-weight:bold;">
        Sign of COD: ______________________
    </div>
</div>

@include('analytics.js')
@endsection