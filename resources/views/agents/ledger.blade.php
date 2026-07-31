@extends('layouts.app')

@section('content')

<div class="p-4 bg-white shadow rounded">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="fw-bold text-primary mb-0">
            Agent Ledger
        </h2>

        <div class="d-flex gap-2">
            @if($ledger->count())
            <button class="btn btn-danger" onclick="exportAgentLedgerPDF()">
                <i class="material-icons align-middle">picture_as_pdf</i> Export PDF
            </button>
            @endif
            <a href="{{ route('agents.index') }}" class="btn btn-secondary">
                Back
            </a>
        </div>

    </div>

    <div class="alert alert-info">
        <strong>Agent :</strong> {{ $agent->name }} <br>
        <strong>Email :</strong> {{ $agent->email ?? '-' }} <br>
        <strong>Phone :</strong> {{ $agent->phone ?? '-' }}
    </div>

    @if($ledger->count())

    <div class="table-responsive">

        <table class="table table-striped table-bordered text-center align-middle">

            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Invoice</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Running Total</th>
                </tr>
            </thead>

            <tbody>
                @foreach($ledger as $row)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row['date'])->format('d-m-Y') }}</td>
                    <td class="fw-bold" style="background-color:#d4edda;">{{ $row['invoice_no'] }}</td>
                    <td>{{ $row['customer'] }}</td>
                    <td>{{ number_format($row['amount'], 2) }}</td>
                    <td class="fw-bold">{{ number_format($row['balance'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>

            <tfoot class="fw-bold text-center">
                <tr style="background-color:#d4edda;">
                    <td colspan="3" class="text-end fw-bold text-success">Total Sales:</td>
                    <td class="fw-bold text-success">{{ number_format($totalSales, 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>

        </table>

    </div>

    @else

    <div class="alert alert-warning text-center">
        No sales found for this agent.
    </div>

    @endif

</div>

<!-- JS for Export PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

@if($ledger->count())
@php
$_jsData = [
    'agentName'  => addslashes($agent->name ?? ''),
    'agentEmail' => addslashes($agent->email ?? '-'),
    'todayDate'  => now()->format('Y-m-d'),
    'logoUrl'    => asset('assets/images/logos/logo.jpg'),
    'ledger'     => $ledger->values(),
    'totalSales' => $totalSales,
];
@endphp
<script type="application/json" id="agent-ledger-data">@json($_jsData)</script>
<script>
    const agentLedgerData = JSON.parse(document.getElementById('agent-ledger-data').textContent);

    function exportAgentLedgerPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF("l", "mm", "a4");
        const d = agentLedgerData;
        const img = new Image();
        img.src = d.logoUrl;

        img.onload = function () {
            const pageW = doc.internal.pageSize.width;

            doc.addImage(img, "JPG", 14, 8, 38, 14);

            doc.setFontSize(16);
            doc.setFont("helvetica", "bold");
            doc.setTextColor(17, 20, 45);
            doc.text("Agent Ledger Report", pageW / 2, 16, { align: "center" });

            doc.setFontSize(10);
            doc.setFont("helvetica", "normal");
            doc.setTextColor(0, 0, 0);
            doc.text("Agent: " + d.agentName, 14, 30);
            doc.text("Email: " + d.agentEmail, 14, 37);
            doc.text("Date: " + d.todayDate, pageW - 14, 30, { align: "right" });

            const fmtNum = function(n) {
                return parseFloat(n || 0).toLocaleString('en-US', { minimumFractionDigits: 2 });
            };

            const head = [['Date', 'Invoice', 'Customer', 'Amount', 'Running Total']];
            const body = [];
            d.ledger.forEach(function(row) {
                body.push([
                    row.date ? row.date.substring(0, 10) : '-',
                    row.invoice_no,
                    row.customer,
                    fmtNum(row.amount),
                    fmtNum(row.balance),
                ]);
            });

            const footRows = [
                [
                    { content: 'Total Sales:', colSpan: 3, styles: { halign: 'right', fontStyle: 'bold', textColor: [0, 128, 0], fillColor: [212, 237, 218] } },
                    { content: fmtNum(d.totalSales), styles: { halign: 'center', fontStyle: 'bold', textColor: [0, 128, 0], fillColor: [212, 237, 218] } },
                    { content: '', styles: { fillColor: [212, 237, 218] } },
                ],
            ];

            doc.autoTable({
                head: head,
                body: body,
                startY: 44,
                theme: 'grid',
                styles: { fontSize: 8, halign: 'center', valign: 'middle', cellPadding: 2 },
                headStyles: { fillColor: [17, 20, 45], textColor: [255, 255, 255], fontStyle: 'bold', fontSize: 8 },
                columnStyles: {
                    0: { cellWidth: 25 },
                    1: { cellWidth: 65 },
                    2: { halign: 'left', cellWidth: 70 },
                    3: { cellWidth: 35 },
                    4: { cellWidth: 35, fontStyle: 'bold' },
                },
                foot: footRows,
                footStyles: { fontStyle: 'bold' },
                didDrawPage: function (data) {
                    const pageHeight = doc.internal.pageSize.height;
                    doc.setFontSize(7);
                    doc.setTextColor(120);
                    doc.text("Page " + doc.internal.getNumberOfPages(), pageW - 14, pageHeight - 8, { align: "right" });
                },
            });

            doc.save("agent-ledger-" + d.agentName + ".pdf");
        };
    }
</script>
@endif

@endsection
