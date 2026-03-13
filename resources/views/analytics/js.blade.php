<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

/* ============================================
CENTER LABEL PLUGIN
============================================ */

const centerLabel = {
    id: 'centerLabel',
    beforeDraw(chart) {

        const {width,height,ctx} = chart;

        ctx.restore();

        const fontSize = (height / 150).toFixed(2);
        ctx.font = fontSize + "em Inter, sans-serif";
        ctx.textBaseline = "middle";

        const text = "{{ number_format($npPercent,2) }}%";
        const textX = (width - ctx.measureText(text).width) / 2;
        const textY = height / 2;

        ctx.fillStyle = "#111827";
        ctx.fillText(text, textX, textY);

        ctx.save();
    }
};



/* ============================================
PROFESSIONAL DOUGHNUT CHART
============================================ */

const ctx = document.getElementById('donutChart');

new Chart(ctx, {

type: 'doughnut',

data: {

labels: [
'Gross Profit',
'Expenses',
'Net Profit',
'Sale Return'
],

datasets: [{

data: [

{{ $gpPercent }},
{{ $expensePercent }},
{{ $npPercent }},
{{ $totalSales > 0 ? number_format(($totalSaleReturn / $totalSales) * 100, 2) : 0 }}

],

backgroundColor: [

'#10b981',   // profit
'#ef4444',   // expenses
'#3b82f6',   // net
'#f59e0b'    // returns

],

borderColor: '#ffffff',
borderWidth: 2,
hoverOffset: 6

}]

},

options: {

responsive: true,
maintainAspectRatio: false,

cutout: '72%',

animation: {

animateRotate: true,
duration: 1000,
easing: 'easeOutCubic'

},

layout: {

padding: 10

},

plugins: {

legend: {

position: 'bottom',

labels: {

boxWidth: 12,
boxHeight: 12,
padding: 14,

font: {
size: 13,
weight: '600'
}

}

},

tooltip: {

backgroundColor: '#111827',

titleFont: {
size: 13
},

bodyFont: {
size: 13
},

padding: 10,
cornerRadius: 6

}

}

},

plugins:[centerLabel]

});

</script>