@extends('layouts.app')

@section('content')
<style>
    /* GENERAL SUMMARY STYLE */
    .summary-box strong {
        font-weight: 700;
        color: #11142d;
    }

    .summary-box {
        font-size: 18px;
        line-height: 40px;
        /* increased line spacing for readability */
    }

    /* SECTION HEADINGS */
    .section-title {
        text-align: center;
        font-weight: 800;
        font-size: 26px;
        margin-bottom: 25px;
        color: #007bff;
        /* professional blue */
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
        /* bold red to stand out */
    }

    /* FILTER BUTTONS */
    .btn-primary,
    .btn-warning,
    .btn-danger {
        min-width: 100px;
    }

    /* SUMMARY BOX CONTAINER */
    .summary-box .col-md-6 {
        padding: 15px;
    }

    .border-end {
        border-right: 2px solid #eee;
    }

    hr {
        border-top: 2px solid #11142d5c;
    }
</style>

<div class="container my-5" id="reportContent">

    <!-- FILTER SECTION -->
    <form method="GET" action="{{ route('analytics.index') }}">
        <div class="p-4 bg-white shadow rounded-4 mb-5">
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
                    <a href="{{ route('analytics.index') }}" class="btn btn-warning">Clear</a>
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
                <strong>Total Sales:</strong> <span class="label-line"></span> {{ number_format($totalSales, 2) }} <br>
                <strong>COGS:</strong> <span class="label-line"></span> {{ number_format($totalPurchases, 2) }} <br>
                <strong>Gross Profit:</strong> <span class="label-line"></span> {{ number_format($grossProfit, 2) }}
                <span class="percent-value">({{ number_format($gpPercent, 2) }}%)</span><br>
                <strong>Expenses:</strong> <span class="label-line"></span> {{ number_format($totalExpenses, 2) }}
                <span class="percent-value">({{ number_format($expensePercent, 2) }}%)</span><br>
                <hr>
                <strong>Net Profit:</strong> <span class="label-line"></span> {{ number_format($netProfit, 2) }}
                <span class="percent-value">({{ number_format($npPercent, 2) }}%)</span><br>
            </div>

            <!-- RIGHT SUMMARY -->
            <div class="col-md-6">
                <strong>Total Purchases:</strong> <span class="label-line"></span> {{ number_format($totalPurchases, 2) }} <br>
                <strong>Purchased Qty:</strong> <span class="label-line"></span> {{ $purchaseQty }} <br>
                <strong>Purchase %:</strong> <span class="label-line"></span>
                <span class="percent-value">{{ number_format($purchasePercent, 2) }}%</span><br>
            </div>
        </div>

        <hr>

        <h3 class="section-title">Percent Summary</h3>

        <div class="summary-box">
            <strong>Gross Profit %:</strong> <span class="label-line"></span>
            <span class="percent-value">{{ number_format($gpPercent, 2) }}%</span><br>
            <strong>Expenses %:</strong> <span class="label-line"></span>
            <span class="percent-value">{{ number_format($expensePercent, 2) }}%</span><br>
            <hr>
            <strong>Net Profit %:</strong> <span class="label-line"></span>
            <span class="percent-value">{{ number_format($npPercent, 2) }}%</span><br>
            <!-- <hr>
            <strong>Overall %:</strong> <span class="label-line"></span>
            <span class="percent-value">{{ number_format($overallPercent, 2) }}%</span><br> -->
        </div>
    </div>
</div>

<!-- ========================= PDF CONTENT ========================= -->
<div id="pdfContent" style="width:210mm; padding:40px; background:#fff; display:none; font-family:Arial;">
    <h2 style="text-align:center; font-size:30px; font-weight:800; margin-bottom:15px; color:#007bff;">
        Profit & Loss Report
    </h2>

    <p style="text-align:center; margin-bottom:25px; font-size:18px;">
        Date: {{ now()->format('d M, Y') }}
    </p>

    <div style="font-size:18px; line-height:40px;">
        <strong>Total Sales:</strong> <span class="label-line"></span> {{ number_format($totalSales, 2) }}<br>
        <strong>COGS:</strong> <span class="label-line"></span> {{ number_format($totalPurchases, 2) }}<br>
        <strong>Expenses:</strong> <span class="label-line"></span> {{ number_format($totalExpenses, 2) }}<br>
        <strong>Net Profit:</strong> <span class="label-line"></span> {{ number_format($netProfit, 2) }}<br>
    </div>

    <hr style="margin:25px 0;">

    <h3 style="font-size:24px; font-weight:800; color:#007bff;">Summary:</h3>

    <div style="font-size:18px; line-height:40px;">
        <strong>Gross Profit %:</strong> <span class="label-line"></span> <span class="percent-value">{{ number_format($gpPercent, 2) }}%</span><br>
        <strong>Expenses %:</strong> <span class="label-line"></span> <span class="percent-value">{{ number_format($expensePercent, 2) }}%</span><br>
        <strong>Net Profit %:</strong> <span class="label-line"></span> <span class="percent-value">{{ number_format($npPercent, 2) }}%</span><br>
        <hr>
        <strong>Overall %:</strong> <span class="label-line"></span> <span class="percent-value">{{ number_format($overallPercent, 2) }}%</span><br>
    </div>

    <div style="margin-top:80px; font-size:22px; text-align:center; font-weight:bold;">
        Sign of COD: ______________________
    </div>
</div>

<!-- PDF GENERATOR -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    document.getElementById('exportPdfBtn').addEventListener('click', function() {
        const element = document.getElementById('pdfContent');
        element.style.display = 'block';

        html2pdf()
            .set({
                margin: 10,
                filename: 'Profit_Loss_Report.pdf',
                html2canvas: {
                    scale: 2
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'portrait'
                }
            })
            .from(element)
            .save()
            .then(() => element.style.display = 'none');
    });
</script>
@endsection