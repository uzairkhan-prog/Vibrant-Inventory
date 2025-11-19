@extends('layouts.app')

@section('content')
<div class="my-5" id="reportContent">

    <!-- ===== Header Filter Section ===== -->
    <form method="GET" action="{{ route('analytics.index') }}">
        <div class="filter-section mb-4 p-4 bg-white shadow rounded-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-4 col-sm-6">
                    <label for="from_date" class="form-label fw-semibold">From Date</label>
                    <input type="date" id="from_date" name="from_date" class="form-control shadow-sm"
                           value="{{ request('from_date') }}">
                </div>
                <div class="col-md-4 col-sm-6">
                    <label for="to_date" class="form-label fw-semibold">To Date</label>
                    <input type="date" id="to_date" name="to_date" class="form-control shadow-sm"
                           value="{{ request('to_date') }}">
                </div>                
                <div class="col-md-4 text-md-end text-center mt-3 mt-md-0">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <a href="{{ route('analytics.index') }}" class="btn btn-warning me-2">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                    <button type="button" class="btn btn-danger" id="exportPdfBtn">
                        <i class="bi bi-file-earmark-pdf"></i> Export PDF
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- ===== Info Cards Section (Visible on Page) ===== -->
    <div class="row g-4">
        @foreach ([
            ['label'=>'Total Sales','amount'=>$sumSalesAmount,'bg'=>'bg-blue','icon'=>'bi-cart4'],
            ['label'=>'Cost of Goods Sold','amount'=>$sumPurchasesAmount,'bg'=>'bg-orange','icon'=>'bi-box-seam'],
            ['label'=>'Gross Profit','amount'=>$sumSalesAmount-$sumPurchasesAmount,'bg'=>'bg-green','icon'=>'bi-graph-up'],
            ['label'=>'Expenses','amount'=>$sumExpensesAmount,'bg'=>'bg-purple','icon'=>'bi-wallet2'],
            ['label'=>'Net Profit','amount'=>$sumSalesAmount-$sumPurchasesAmount-$sumExpensesAmount,'bg'=>'bg-dark','icon'=>'bi-cash-coin'],
            ['label'=>'Total Purchases','amount'=>$sumPurchasesAmount,'bg'=>'bg-cyan','icon'=>'bi-basket2-fill']
        ] as $card)
        <div class="col-md-4">
            <div class="card-box {{ $card['bg'] }}">
                <div>
                    <h5>{{ $card['label'] }}</h5>
                    <p>PKR <span>{{ number_format($card['amount'], 2) }}</span></p>
                </div>
                <i class="bi {{ $card['icon'] }} icon"></i>
            </div>
        </div>
        @endforeach
    </div>

    <!-- ===== Charts Section (Page Only) ===== -->
    <div class="row mt-2 g-4">
        <div class="col-lg-12 col-md-6">
            <div class="chart-section">
                <h4><i class="bi bi-bar-chart-line"></i> Profit & Loss Overview</h4>
                <canvas id="profitChart"></canvas>
            </div>
        </div>
        <div class="col-lg-8 col-md-6">
            <div class="chart-section">
                <h4><i class="bi bi-graph-up-arrow"></i> Sales vs Purchases Trend</h4>
                <canvas id="trendChart"></canvas>
            </div>
        </div>
        <div class="col-lg-4 col-md-12">
            <div class="chart-section">
                <h4><i class="bi bi-circle"></i> Summary Distribution</h4>
                <canvas id="summaryChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- ===== PDF Export Section (Centered Table) ===== -->
<div id="pdfContent" style="width:210mm; padding:40px; font-family: Arial, sans-serif; background:#fff; text-align:center; display: none;">

    <!-- Company Header (Centered) -->
    <div style="text-align:center; margin-bottom:30px;">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/images/logos/logo-export.png'))) }}"
            alt="Vibrant Engineering Logo" style="max-height: 60px;border-radius: 10px;margin-bottom: 10px;background: #11142d;padding: 5px;">
        <p class="mb-1">Head Office: Shop #13, Falak Park View Near Inquiry Office Nazimabad #2, Karachi</p>
        <p class="mb-1">Phone: +92 335 2385773</p>
        <p class="mb-0">Email: info@vibrantengineering.pk</p>
        <h2 style="margin-top:20px; color:#1a73e8;">Profit & Loss Report</h2>
        <p><strong>Date:</strong> {{ now()->format('d M, Y') }}</p>
        <p><strong>Payment Terms:</strong> Due on receipt</p>
    </div>

    <!-- Table for Totals (Centered) -->
    <table style="margin: 0 auto; width:80%; border-collapse: collapse; margin-top:20px;">
        <thead>
            <tr style="background:#f2f2f2;">
                <th style="border:1px solid #ccc; padding:10px; text-align:left;">Description</th>
                <th style="border:1px solid #ccc; padding:10px; text-align:right;">Amount (PKR)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border:1px solid #ccc; padding:10px; text-align:left;">Total Sales</td>
                <td style="border:1px solid #ccc; padding:10px; text-align:right;">{{ number_format($sumSalesAmount, 2) }}</td>
            </tr>
            <tr>
                <td style="border:1px solid #ccc; padding:10px; text-align:left;">Cost of Goods Sold</td>
                <td style="border:1px solid #ccc; padding:10px; text-align:right;">{{ number_format($sumPurchasesAmount, 2) }}</td>
            </tr>
            <tr>
                <td style="border:1px solid #ccc; padding:10px; text-align:left;">Gross Profit</td>
                <td style="border:1px solid #ccc; padding:10px; text-align:right;">{{ number_format($sumSalesAmount-$sumPurchasesAmount, 2) }}</td>
            </tr>
            <tr>
                <td style="border:1px solid #ccc; padding:10px; text-align:left;">Expenses</td>
                <td style="border:1px solid #ccc; padding:10px; text-align:right;">{{ number_format($sumExpensesAmount, 2) }}</td>
            </tr>
            <tr>
                <td style="border:1px solid #ccc; padding:10px; text-align:left;">Net Profit</td>
                <td style="border:1px solid #ccc; padding:10px; text-align:right;">{{ number_format($sumSalesAmount-$sumPurchasesAmount-$sumExpensesAmount, 2) }}</td>
            </tr>
            <tr>
                <td style="border:1px solid #ccc; padding:10px; text-align:left;">Total Purchases</td>
                <td style="border:1px solid #ccc; padding:10px; text-align:right;">{{ number_format($sumPurchasesAmount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Sign of COD (Centered) -->
    <div style="margin-top:80px; text-align:center; font-weight:bold; font-size:16px;">
        Sign of COD: ______________________
    </div>
</div>

<!-- ===== Styles ===== -->
<style>
.card-box {
    border-radius: 16px;
    padding: 25px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: #fff;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
}
.card-box:hover { transform: translateY(-6px); }
.icon { font-size: 2.2rem; opacity: 0.9; }
.bg-blue { background: linear-gradient(135deg, #36d1dc, #5b86e5); }
.bg-cyan { background: linear-gradient(135deg, #43cea2, #185a9d); }
.bg-orange { background: linear-gradient(135deg, #f7971e, #ffd200); }
.bg-green { background: linear-gradient(135deg, #56ab2f, #a8e063); }
.bg-purple { background: linear-gradient(135deg, #8e2de2, #4a00e0); }
.bg-dark { background: linear-gradient(135deg, #232526, #414345); }
.card-box h5 { color: #fff; font-size: 1.1rem; font-weight: 600; }
.card-box p { font-size: 28px; margin-bottom: 0; font-weight: 700; }
.chart-section { background: #fff; border-radius: 15px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
.chart-section h4 { font-weight: 600; margin-bottom: 20px; color: #333; }
@media (max-width: 992px) { .chart-section { margin-bottom: 20px; } }
</style>

<!-- ===== Libraries ===== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
// ===== Charts Data =====
const totalSales = {{ $sumSalesAmount }};
const totalPurchases = {{ $sumPurchasesAmount }};
const totalExpenses = {{ $sumExpensesAmount }};
const grossProfit = totalSales - totalPurchases;
const netProfit = grossProfit - totalExpenses;

const salesData = @json($monthlySales->pluck('total'));
const purchaseData = @json($monthlyPurchases->pluck('total'));
const months = @json($monthlySales->pluck('month')->map(fn($m) => \Carbon\Carbon::create()->month($m)->format('M')));

// ===== Charts =====
new Chart(document.getElementById("profitChart"), {
    type: "bar",
    data: {
        labels: ["Sales", "COGS", "Gross Profit", "Expenses", "Net Profit", "Purchases"],
        datasets: [{
            label: "Amount (PKR)",
            data: [totalSales, totalPurchases, grossProfit, totalExpenses, netProfit, totalPurchases],
            backgroundColor: ["#36d1dc","#f7971e","#56ab2f","#8e2de2","#232526","#43cea2"],
            borderRadius: 8
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

// Trend Chart
const trendCtx = document.getElementById("trendChart").getContext("2d");
const gradientSales = trendCtx.createLinearGradient(0, 0, 0, 400);
gradientSales.addColorStop(0, "rgba(54, 209, 220, 0.6)");
gradientSales.addColorStop(1, "rgba(54, 209, 220, 0.1)");
const gradientPurchases = trendCtx.createLinearGradient(0, 0, 0, 400);
gradientPurchases.addColorStop(0, "rgba(67, 206, 162, 0.6)");
gradientPurchases.addColorStop(1, "rgba(67, 206, 162, 0.1)");

new Chart(trendCtx, {
    type: "line",
    data: {
        labels: months,
        datasets: [
            { label: "Sales", data: salesData, borderColor: "#36d1dc", backgroundColor: gradientSales, fill: true, tension: 0.4, pointRadius: 5, pointBackgroundColor: "#36d1dc", pointHoverRadius: 7, borderWidth: 3 },
            { label: "Purchases", data: purchaseData, borderColor: "#43cea2", backgroundColor: gradientPurchases, fill: true, tension: 0.4, pointRadius: 5, pointBackgroundColor: "#43cea2", pointHoverRadius: 7, borderWidth: 3 }
        ]
    },
    options: { responsive: true }
});

// Doughnut Chart
new Chart(document.getElementById("summaryChart"), {
    type: "doughnut",
    data: {
        labels: ["Total Sales","COGS","Gross Profit","Expenses","Net Profit","Total Purchases"],
        datasets: [{
            data: [totalSales,totalPurchases,grossProfit,totalExpenses,netProfit,totalPurchases],
            backgroundColor: ["#36d1dc","#f7971e","#56ab2f","#8e2de2","#232526","#43cea2"],
            borderColor: "#fff",
            borderWidth: 2
        }]
    },
    options: { responsive: true }
});

// ===== PDF Export Function =====
document.getElementById('exportPdfBtn').addEventListener('click', function() {
    const element = document.getElementById('pdfContent');
    element.style.display = 'block';

    html2pdf().set({
        margin: 0.5,
        filename: 'Profit_Loss_Report.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
    }).from(element).save().then(() => {
        element.style.display = 'none';
    });
});
</script>
@endsection
