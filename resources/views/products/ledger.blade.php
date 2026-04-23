@extends('layouts.app')

@section('content')

<div class="p-4 bg-white shadow rounded">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="fw-bold text-primary mb-0">
            Product Ledger
        </h2>

        <div class="d-flex gap-2">
            @if($ledger->count())
            <button class="btn btn-danger" onclick="exportProductLedgerPDF()">
                <i class="material-icons align-middle">picture_as_pdf</i> Export PDF
            </button>
            @endif
            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                Back
            </a>
        </div>

    </div>


    <div class="alert alert-info">

        <strong>Product :</strong> {{ $product->name }} <br>

        <strong>Category :</strong> {{ $product->category->name ?? 'Uncategorized' }}

    </div>


    @if($ledger->count())

    <div class="table-responsive">

        <table class="table table-striped table-bordered text-center align-middle">

            <thead class="table-light">

                <tr>

                    <th>Date</th>
                    <th>Invoice</th>
                    <th>Type</th>
                    <th>Party</th>
                    <th>Qty In</th>
                    <th>Qty Out</th>
                    <th>Price</th>
                    <th>Total</th>

                </tr>

            </thead>


            <tbody>
                @foreach($ledger as $row)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row['date'])->format('d-m-Y') }}</td>

                    <td class="fw-bold"
                        @if($row['type']=='Purchase' ) style="background-color:#cce5ff;"
                        @elseif($row['type']=='Sale' ) style="background-color:#d4edda;"
                        @else style="background-color:#f8d7da;" @endif>
                        {{ $row['invoice_no'] }}
                    </td>

                    <td>
                        @if($row['type']=='Purchase')
                        <span class="badge" style="background-color:#cce5ff; color:#000;">Purchase</span>
                        @elseif($row['type']=='Sale')
                        <span class="badge" style="background-color:#d4edda; color:#000;">Sale</span>
                        @else
                        <span class="badge" style="background-color:#f8d7da; color:#000;">Sale Return</span>
                        @endif
                    </td>

                    <td>{{ $row['party'] }}</td>

                    <td class="text-success fw-bold">
                        {{ $row['qty_in'] ?: '-' }}
                    </td>

                    <td class="text-danger fw-bold">
                        {{ $row['qty_out'] ?: '-' }}
                    </td>

                    <td>{{ number_format($row['price'],2) }}</td>

                    <td>{{ number_format($row['total'],2) }}</td>
                </tr>
                @endforeach
            </tbody>

            @php
                $totalPurchaseQtyIn  = $ledger->where('type','Purchase')->sum('qty_in');
                $totalPurchaseAmount = $ledger->where('type','Purchase')->sum('total');
                $totalSaleQtyOut     = $ledger->where('type','Sale')->sum('qty_out');
                $totalSaleAmount     = $ledger->where('type','Sale')->sum('total');
                $totalReturnQtyIn    = $ledger->where('type','Sale Return')->sum('qty_in');
                $totalReturnAmount   = $ledger->where('type','Sale Return')->sum('total');
            @endphp

            <tfoot class="fw-bold text-center">
                <tr style="background-color:#d4edda;">
                    <td colspan="4" class="text-end fw-bold text-success">Total Purchase:</td>
                    <td class="text-success fw-bold text-success">{{ $totalPurchaseQtyIn ?: '-' }}</td>
                    <td>-</td>
                    <td></td>
                    <td class="fw-bold text-success">{{ number_format($totalPurchaseAmount, 2) }}</td>
                </tr>
                <tr style="background-color:#f8d7da;">
                    <td colspan="4" class="text-end fw-bold text-danger">Total Sale:</td>
                    <td>-</td>
                    <td class="text-danger fw-bold text-danger">{{ $totalSaleQtyOut ?: '-' }}</td>
                    <td></td>
                    <td class="fw-bold text-danger">{{ number_format($totalSaleAmount, 2) }}</td>
                </tr>
                @if($totalReturnQtyIn > 0)
                <tr style="background-color:#f8d7da;">
                    <td colspan="4" class="text-end fw-bold text-danger">Total Sale Return:</td>
                    <td class="text-success fw-bold text-success">{{ $totalReturnQtyIn }}</td>
                    <td>-</td>
                    <td></td>
                    <td class="fw-bold text-danger">{{ number_format($totalReturnAmount, 2) }}</td>
                </tr>
                @endif
            </tfoot>

        </table>

    </div>

    @else

    <div class="alert alert-warning text-center">

        No ledger records found for this product.

    </div>

    @endif

</div>

<!-- JS for Export PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

@if($ledger->count())
@php
$_jsData = [
    'productName'        => addslashes($product->name ?? ''),
    'categoryName'       => addslashes($product->category->name ?? 'Uncategorized'),
    'todayDate'          => now()->format('Y-m-d'),
    'logoUrl'            => asset('assets/images/logos/logo.jpg'),
    'ledger'             => $ledger->values(),
    'totalPurchaseQtyIn' => $totalPurchaseQtyIn,
    'totalPurchaseAmt'   => $totalPurchaseAmount,
    'totalSaleQtyOut'    => $totalSaleQtyOut,
    'totalSaleAmt'       => $totalSaleAmount,
    'totalReturnQtyIn'   => $totalReturnQtyIn,
    'totalReturnAmt'     => $totalReturnAmount,
];
@endphp
<script type="application/json" id="product-ledger-data">@json($_jsData)</script>
<script>
    const productLedgerData = JSON.parse(document.getElementById('product-ledger-data').textContent);

    function exportProductLedgerPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF("l", "mm", "a4");
        const d = productLedgerData;
        const img = new Image();
        img.src = d.logoUrl;

        img.onload = function () {
            const pageW = doc.internal.pageSize.width;

            doc.addImage(img, "JPG", 14, 8, 38, 14);

            doc.setFontSize(16);
            doc.setFont("helvetica", "bold");
            doc.setTextColor(17, 20, 45);
            doc.text("Product Ledger Report", pageW / 2, 16, { align: "center" });

            doc.setFontSize(10);
            doc.setFont("helvetica", "normal");
            doc.setTextColor(0, 0, 0);
            doc.text("Product: " + d.productName, 14, 30);
            doc.text("Category: " + d.categoryName, 14, 37);
            doc.text("Date: " + d.todayDate, pageW - 14, 30, { align: "right" });

            const fmtNum = function(n) {
                return parseFloat(n || 0).toLocaleString('en-US', { minimumFractionDigits: 2 });
            };

            const head = [['Date', 'Invoice', 'Type', 'Party', 'Qty In', 'Qty Out', 'Price', 'Total']];
            const body = [];
            d.ledger.forEach(function(row) {
                body.push([
                    row.date ? row.date.substring(0, 10) : '-',
                    row.invoice_no,
                    row.type,
                    row.party,
                    row.qty_in  ? row.qty_in  : '-',
                    row.qty_out ? row.qty_out : '-',
                    fmtNum(row.price),
                    fmtNum(row.total),
                ]);
            });

            const footRows = [
                [
                    { content: 'Total Purchase:', colSpan: 4, styles: { halign: 'right', fontStyle: 'bold', textColor: [0, 128, 0], fillColor: [212, 237, 218] } },
                    { content: d.totalPurchaseQtyIn || '-', styles: { halign: 'center', fontStyle: 'bold', textColor: [0, 128, 0], fillColor: [212, 237, 218] } },
                    { content: '-',                         styles: { halign: 'center', textColor: [0, 128, 0], fillColor: [212, 237, 218] } },
                    { content: '',                          styles: { fillColor: [212, 237, 218] } },
                    { content: fmtNum(d.totalPurchaseAmt),  styles: { halign: 'center', fontStyle: 'bold', textColor: [0, 128, 0], fillColor: [212, 237, 218] } },
                ],
                [
                    { content: 'Total Sale:', colSpan: 4, styles: { halign: 'right', fontStyle: 'bold', textColor: [220, 0, 0], fillColor: [248, 215, 218] } },
                    { content: '-',                        styles: { halign: 'center', textColor: [220, 0, 0], fillColor: [248, 215, 218] } },
                    { content: d.totalSaleQtyOut || '-',   styles: { halign: 'center', fontStyle: 'bold', textColor: [220, 0, 0], fillColor: [248, 215, 218] } },
                    { content: '',                         styles: { fillColor: [248, 215, 218] } },
                    { content: fmtNum(d.totalSaleAmt),     styles: { halign: 'center', fontStyle: 'bold', textColor: [220, 0, 0], fillColor: [248, 215, 218] } },
                ],
            ];

            if (d.totalReturnQtyIn > 0) {
                footRows.push([
                    { content: 'Total Sale Return:', colSpan: 4, styles: { halign: 'right', fontStyle: 'bold', textColor: [220, 0, 0], fillColor: [248, 215, 218] } },
                    { content: d.totalReturnQtyIn,          styles: { halign: 'center', fontStyle: 'bold', textColor: [0, 128, 0], fillColor: [248, 215, 218] } },
                    { content: '-',                         styles: { halign: 'center', textColor: [220, 0, 0], fillColor: [248, 215, 218] } },
                    { content: '',                          styles: { fillColor: [248, 215, 218] } },
                    { content: fmtNum(d.totalReturnAmt),    styles: { halign: 'center', fontStyle: 'bold', textColor: [220, 0, 0], fillColor: [248, 215, 218] } },
                ]);
            }

            doc.autoTable({
                head: head,
                body: body,
                startY: 44,
                theme: 'grid',
                styles: { fontSize: 8, halign: 'center', valign: 'middle', cellPadding: 2 },
                headStyles: { fillColor: [17, 20, 45], textColor: [255, 255, 255], fontStyle: 'bold', fontSize: 8 },
                columnStyles: {
                    0: { cellWidth: 22 },
                    1: { cellWidth: 50 },
                    2: { cellWidth: 22 },
                    3: { halign: 'left', cellWidth: 55 },
                    4: { cellWidth: 18, textColor: [0, 128, 0], fontStyle: 'bold' },
                    5: { cellWidth: 18, textColor: [220, 0, 0], fontStyle: 'bold' },
                    6: { cellWidth: 28 },
                    7: { cellWidth: 28, fontStyle: 'bold' },
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

            doc.save("product-ledger-" + d.productName + ".pdf");
        };
    }
</script>
@endif

@endsection
