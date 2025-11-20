@extends('layouts.app')

@section('content')
<style>
    /* GENERAL SUMMARY STYLE */
    .col-md-6 {
        color: #11142d;
        padding: 30px 20px;
    }

    .summary-box strong {
        font-weight: 700;
        color: #11142d;
    }

    .summary-box {
        font-size: 18px;
        line-height: 40px;
    }

    /* SECTION HEADINGS */
    .section-title {
        font-weight: 800;
        font-size: 26px;
        color: #4d75e3;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* LABEL LINES */
    .label-line {
        display: inline-block;
        width: 220px;
        border-bottom: 2px dotted #999;
        margin: 0 10px;
    }

    /* HIGHLIGHT % VALUES */
    .percent-value {
        font-weight: 800;
        color: #d32f2f;
    }

    /* FILTER BUTTONS */
    .btn-primary,
    .btn-warning,
    .btn-danger {
        min-width: 100px;
    }

    .border-end {
        border-right: 2px solid #eee;
    }

    hr {
        border-top: 2px solid #11142d5c;
    }

    /* CHART CARD */
    .chart-card {
        position: absolute;
    }
</style>

<div class="container my-5" id="reportContent">

    <!-- FILTER SECTION -->
    <form method="GET" action="{{ route('analytics.index') }}">
        <div class="p-4 bg-white shadow rounded-4 mb-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">From Date</label>
                    <input type="date" class="form-control" name="from_date" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">To Date</label>
                    <input type="date" class="form-control" name="to_date" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <button class="btn btn-primary">Filter</button>
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
                {{ number_format($totalSales, 2) }} -
                <span class="percent-value">(100%)</span> <br>

                <strong>COGS:</strong> <span class="label-line"></span>
                {{ number_format($totalPurchases, 2) }} -
                <span class="percent-value">(
                    {{ $totalSales > 0 ? number_format(($totalPurchases/$totalSales)*100, 2) : '0' }}%
                )</span> <br>

                <strong>Gross Profit:</strong> <span class="label-line"></span>
                {{ number_format($grossProfit, 2) }} -
                <span class="percent-value">({{ number_format($gpPercent, 2) }}%)</span><br>

                <strong>Expenses:</strong> <span class="label-line"></span>
                {{ number_format($totalExpenses, 2) }} -
                <span class="percent-value">({{ number_format($expensePercent, 2) }}%)</span><br>

                <hr>

                <strong>Net Profit:</strong> <span class="label-line"></span>
                {{ number_format($netProfit, 2) }} -
                <span class="percent-value">({{ number_format($npPercent, 2) }}%)</span><br>
            </div>

            <!-- PERCENT CIRCLE CHART -->
            <div class="col-md-6 d-flex justify-content-center align-items-center">
                <div class="chart-card">
                    <h5 class="fw-bold text-center">Profit % Breakdown</h5>
                    <canvas id="donutChart" height="260"></canvas>
                </div>
            </div>
                <hr>
        </div>

        <h3 class="section-title">Percent Summary</h3>

        <div class="row">
            <div class="col-md-6 summary-box border-end">
                <strong>Gross Profit %:</strong> <span class="label-line"></span>
                <span class="percent-value">{{ number_format($gpPercent, 2) }}%</span><br>

                <strong>Expenses %:</strong> <span class="label-line"></span>
                <span class="percent-value">{{ number_format($expensePercent, 2) }}%</span><br>

                <hr>

                <strong>Net Profit %:</strong> <span class="label-line"></span>
                <span class="percent-value">{{ number_format($npPercent, 2) }}%</span><br>
            </div>

            <!-- Purchases SUMMARY -->
            <div class="col-md-6 summary-box">
                <strong>Total Purchases:</strong> <span class="label-line"></span>
                {{ number_format($totalPurchases, 2) }} -
                <span class="percent-value">(
                    {{ $totalSales > 0 ? number_format(($totalPurchases/$totalSales)*100, 2) : '0' }}%
                )</span> <br>

                <strong>Purchased Qty:</strong> <span class="label-line"></span> {{ $purchaseQty }} <br>

                <strong>Purchase %:</strong> <span class="label-line"></span>
                <span class="percent-value">{{ number_format($purchasePercent, 2) }}%</span><br>
            </div>
        </div>

    </div>
</div>

<!-- PDF CONTENT ----------------------------------->
<div id="pdfContent" style="display:none; width:210mm; padding:40px; background:#fff; font-family:Arial;">

    <!-- ================= PDF HEADER ADDED HERE ================= -->
    <div style="margin-bottom:20px;">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/images/logos/logo-export.png'))) }}"
             alt="Vibrant Engineering Logo"
             style="max-height: 60px; border-radius: 10px; margin-bottom: 10px; background: linear-gradient(43deg, #11142d 29%, #0B4168 80%);">

        <p class="mb-1">Head Office: Shop #13, Falak Park View Near <br> Inquiry Office Nazimabad #2, Karachi</p>
        <p class="mb-1">Phone: +92 335 2385773</p>
        <p class="mb-0">Email: info@vibrantengineering.pk</p>
    </div>
    <!-- ========================================================= -->

    <h2 style="font-size:30px; font-weight:800; margin-bottom:15px; color:#007bff;">
        Profit & Loss Report
    </h2>

    <p style="margin-bottom:25px; font-size:18px;">
        Date: {{ now()->format('d M, Y') }}
    </p>

    <div style="font-size:18px; line-height:40px;">
        <strong>Total Sales:</strong> <span class="label-line"></span>
        {{ number_format($totalSales, 2) }} (100%)<br>

        <strong>COGS:</strong> <span class="label-line"></span>
        {{ number_format($totalPurchases, 2) }} ({{ $totalSales > 0 ? number_format(($totalPurchases/$totalSales)*100, 2) : '0' }}%)<br>

        <strong>Expenses:</strong> <span class="label-line"></span>
        {{ number_format($totalExpenses, 2) }} ({{ $totalSales > 0 ? number_format(($totalExpenses/$totalSales)*100, 2) : '0' }}%)

        <hr>

        <strong>Net Profit:</strong> <span class="label-line"></span>
        {{ number_format($netProfit, 2) }} ({{ $totalSales > 0 ? number_format(($netProfit/$totalSales)*100, 2) : '0' }}%)<br>
    </div>

    <hr>

    <h3 style="font-size:24px; font-weight:800; color:#007bff;">Summary:</h3>

    <div style="font-size:18px; line-height:40px;">
        <strong>Gross Profit %:</strong> <span class="label-line"></span>
        {{ number_format($gpPercent, 2) }}%<br>

        <strong>Expenses %:</strong> <span class="label-line"></span>
        {{ number_format($expensePercent, 2) }}%

        <hr>

        <strong>Net Profit %:</strong> <span class="label-line"></span>
        {{ number_format($npPercent, 2) }}%
    </div>

    <div style="margin-top:80px; font-size:22px; font-weight:bold;">
        Sign of COD: ______________________
    </div>
</div>


<!-- PDF JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
document.getElementById('exportPdfBtn').addEventListener('click', function() {
    const element = document.getElementById('pdfContent');
    element.style.display = 'block';

    html2pdf()
        .set({
            margin: 10,
            filename: 'Profit_Loss_Report.pdf',
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        })
        .from(element)
        .save()
        .then(() => element.style.display = 'none');
});
</script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
new Chart(document.getElementById('donutChart'), {
    type: 'doughnut',
    data: {
        labels: ['Gross Profit %', 'Expenses %', 'Net Profit %'],
        datasets: [{
            data: [
                {{ $gpPercent }},
                {{ $expensePercent }},
                {{ $npPercent }}
            ],
            backgroundColor: ['#4caf50', '#f44336', '#2196f3'],
            borderColor: '#fff',
            borderWidth: 3,
            hoverOffset: 10
        }]
    },
    options: {
        plugins: { legend: { position: 'bottom' } },
        layout: { padding: 20 }
    }
});
</script>

@endsection
