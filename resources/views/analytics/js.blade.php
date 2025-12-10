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
        labels: ['Gross Profit %', 'Expenses %', 'Net Profit %', 'Sale Return %'],
        datasets: [{
            data: [
                {{ $gpPercent }},
                {{ $expensePercent }},
                {{ $npPercent }},
                {{ $totalSales > 0 ? number_format(($totalSaleReturn / $totalSales) * 100, 2) : 0 }}
            ],
            backgroundColor: ['#4caf50', '#f44336', '#2196f3', '#ff9800'],
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