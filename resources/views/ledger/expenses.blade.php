@extends('layouts.app')

@section('content')

<div class="p-4 bg-white shadow rounded">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary mb-0">Expense Ledger</h2>
        @if($ledger->count())
        <button class="btn btn-danger" onclick="exportExpenseLedgerPDF()">
            <i class="material-icons align-middle">picture_as_pdf</i> Export PDF
        </button>
        @endif
    </div>

    <form method="GET" action="{{ route('ledger.expenses') }}" class="row g-3 mb-4">

        <div class="col-md-4">
            <label class="form-label">Expense Name</label>
            <select name="expense_name_id" class="form-control">
                <option value="">-- All Expense Names --</option>
                @foreach($expenseNames as $en)
                <option value="{{ $en->id }}" {{ (string) $expenseNameId === (string) $en->id ? 'selected' : '' }}>
                    {{ $en->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">From Date</label>
            <input type="date" name="from_date" value="{{ $fromDate }}" class="form-control">
        </div>

        <div class="col-md-3">
            <label class="form-label">To Date</label>
            <input type="date" name="to_date" value="{{ $toDate }}" class="form-control">
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-primary w-100">Search</button>
            <a href="{{ route('ledger.expenses') }}" class="btn btn-secondary w-100 ms-1">Reset</a>
        </div>

    </form>

    <div class="alert alert-success shadow-sm rounded-3 fs-6 fw-bold mb-4">
        <div class="d-flex justify-content-between">
            <span>Total Expense:</span>
            <span>Rs {{ number_format($grandTotal, 2) }}</span>
        </div>
    </div>

    {{-- TABLE --}}
    @if($ledger->count())

    <div class="table-responsive">

        <table class="table table-striped table-bordered text-center align-middle">

            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Expense Name</th>
                    <th>Description</th>
                    <th>Payment Type</th>
                    <th>Amount</th>
                    <th>Running Total</th>
                </tr>
            </thead>

            <tbody>
                @foreach($ledger as $row)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row['date'])->format('d-m-Y') }}</td>
                    <td class="fw-bold">{{ $row['expense_name'] }}</td>
                    <td>{{ $row['description'] ?: '-' }}</td>
                    <td>{{ $row['payment_type'] }}</td>
                    <td class="text-danger fw-bold">{{ number_format($row['amount'], 2) }}</td>
                    <td class="fw-bold text-primary">{{ number_format($row['balance'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>

            <tfoot class="fw-bold text-center">
                <tr style="background-color:#f8d7da;">
                    <td colspan="4" class="text-end fw-bold text-danger">Grand Total:</td>
                    <td class="fw-bold text-danger">{{ number_format($grandTotal, 2) }}</td>
                    <td class="fw-bold text-primary">{{ number_format($grandTotal, 2) }}</td>
                </tr>
            </tfoot>

        </table>

    </div>

    @else

    <div class="alert alert-warning text-center">
        No ledger records found for selected filters.
    </div>

    @endif

</div>

<!-- JS for Export PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

@if($ledger->count())
@php
$_jsData = [
    'expenseName' => addslashes(optional($expenseNames->firstWhere('id', (int) $expenseNameId))->name ?? 'All Expense Names'),
    'fromDate'    => $fromDate ?? '',
    'toDate'      => $toDate ?? '',
    'todayDate'   => now()->format('Y-m-d'),
    'logoUrl'     => asset('assets/images/logos/logo.jpg'),
    'ledger'      => $ledger->values(),
    'grandTotal'  => $grandTotal,
];
@endphp
<script type="application/json" id="expense-ledger-data">@json($_jsData)</script>
<script>
    const expenseLedgerData = JSON.parse(document.getElementById('expense-ledger-data').textContent);

    function exportExpenseLedgerPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF("l", "mm", "a4");
        const d = expenseLedgerData;
        const img = new Image();
        img.src = d.logoUrl;

        img.onload = function () {
            const pageW = doc.internal.pageSize.width;

            doc.addImage(img, "JPG", 14, 8, 38, 14);

            doc.setFontSize(16);
            doc.setFont("helvetica", "bold");
            doc.setTextColor(17, 20, 45);
            doc.text("Expense Ledger Report", pageW / 2, 16, { align: "center" });

            doc.setFontSize(10);
            doc.setFont("helvetica", "normal");
            doc.setTextColor(0, 0, 0);
            doc.text("Expense: " + d.expenseName, 14, 30);

            let dateLabel = d.todayDate;
            if (d.fromDate && d.toDate) {
                dateLabel = d.fromDate + " to " + d.toDate;
            }
            doc.text("Date: " + dateLabel, pageW - 14, 30, { align: "right" });

            const fmtNum = n => parseFloat(n || 0).toLocaleString('en-US', { minimumFractionDigits: 2 });

            const head = [['Date', 'Expense Name', 'Description', 'Payment Type', 'Amount', 'Running Total']];
            const body = [];
            d.ledger.forEach(function(row) {
                body.push([
                    row.date ? row.date.substring(0, 10) : '-',
                    row.expense_name,
                    row.description || '-',
                    row.payment_type,
                    fmtNum(row.amount),
                    fmtNum(row.balance),
                ]);
            });

            const footRows = [
                [
                    { content: 'Grand Total:', colSpan: 4, styles: { halign: 'right', fontStyle: 'bold', textColor: [220, 0, 0], fillColor: [248, 215, 218] } },
                    { content: fmtNum(d.grandTotal), styles: { halign: 'center', fontStyle: 'bold', textColor: [220, 0, 0], fillColor: [248, 215, 218] } },
                    { content: fmtNum(d.grandTotal), styles: { halign: 'center', fontStyle: 'bold', textColor: [17, 20, 45], fillColor: [248, 215, 218] } },
                ],
            ];

            doc.autoTable({
                head: head,
                body: body,
                startY: 40,
                theme: 'grid',
                styles: { fontSize: 8, halign: 'center', valign: 'middle', cellPadding: 2 },
                headStyles: { fillColor: [17, 20, 45], textColor: [255, 255, 255], fontStyle: 'bold', fontSize: 8 },
                columnStyles: {
                    0: { cellWidth: 22 },
                    1: { cellWidth: 45 },
                    2: { halign: 'left', cellWidth: 70 },
                    3: { cellWidth: 32 },
                    4: { cellWidth: 28, textColor: [220, 0, 0], fontStyle: 'bold' },
                    5: { cellWidth: 28, fontStyle: 'bold' },
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

            doc.save("expense-ledger-" + d.expenseName + ".pdf");
        };
    }
</script>
@endif

@endsection
