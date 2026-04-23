@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <h2 class="fw-bold text-primary-emphasis">
            Supplier Profile: <strong>{{ $supplier->name }}</strong>
        </h2>

        @if($ledger->count())
        <button class="btn btn-danger" onclick="exportSupplierPaymentsPDF()">
            <i class="material-icons align-middle">picture_as_pdf</i> Export PDF
        </button>
        @endif
    </div>

    <!-- Balance Info -->
    <div class="alert alert-info d-flex justify-content-between align-items-center shadow-sm rounded-3 fs-5">
        <span><strong>Current Balance:</strong></span>
        <span class="fw-bold text-success">Rs {{ number_format($currentBalance, 2) }}</span>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="alert alert-success shadow-sm">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger shadow-sm fw-semibold">
        <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
    </div>
    @endif

    <!-- Payment Form -->
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body">
            <h4 class="mb-4 text-dark fw-semibold">💳 Record a Payment</h4>

            <form method="POST" action="{{ route('suppliers.payments.store', $supplier) }}">
                @csrf
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Supplier Name</label>
                        <input type="text" class="form-control bg-light" value="{{ $supplier->name }}" readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="purchase_id" class="form-label">Purchase</label>
                        <select name="purchase_id" id="purchase_id" class="form-select">
                            <option value="">-- Select Purchase --</option>
                            @foreach($purchases as $purchase)
                            @if($purchase->remaining_amount > 0)
                            <option value="{{ $purchase->id }}">
                                Purchase no ( {{ $purchase->id }} ) - Remaining amount ( {{ number_format($purchase->remaining_amount,2) }} )
                            </option>
                            @endif
                            @endforeach
                        </select>
                        @error('purchase_id')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="payment_type" class="form-label">Payment Type</label>
                        <select name="payment_type" class="form-select" required>
                            <option value="">-- Select --</option>
                            <option value="Cash">Cash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Amount (Rs)</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required min="0.01" value="{{ old('amount') }}">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Payment details...">{{ old('description') }}</textarea>
                    </div>

                </div>

                @php
                $isDisabled = $currentBalance <= 0 ? 'disabled' : '' ;
                    @endphp

                    <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('suppliers.index') }}" class="btn btn-outline-dark">
                        ← Back to Suppliers
                    </a>
                    <button type="submit" class="btn btn-primary px-4" {{ $isDisabled }}>
                        <i class="material-icons align-middle">send</i> Submit
                    </button>
        </div>
        </form>
    </div>

    <hr>

    <div class="card-body py-0">
        <button type="button" class="btn btn-info" id="ledgerBtn" onclick="toggleLedger()">
            📊 View Supplier Ledger
        </button>
    </div>

    <div id="ledgerSection" style="display: none;">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">

                <h4 class="mb-4 text-dark fw-semibold">
                    {{ $supplier->company_name ? strtoupper($supplier->company_name) : 'N/A' }}
                    ( {{ $supplier->name ?? 'N/A' }} )
                </h4>

                @if($ledger->count())

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">

                        <thead class="table-dark text-center">
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Reference</th>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Discount</th>
                                <th>Tax</th>
                                <th class="text-success">Debit (+)</th>
                                <th class="text-danger">Credit (-)</th>
                                <th>Balance</th>
                            </tr>
                        </thead>

                        <tbody class="text-center">

                            @php
                            $totalDebit = 0;
                            $totalCredit = 0;
                            @endphp

                            @foreach($ledger as $row)

                            @php
                            $totalDebit += $row['debit'];
                            $totalCredit += $row['credit'];
                            $rowDate = \Carbon\Carbon::parse($row['date']);
                            @endphp

                            <tr>
                                <td>
                                    <span class="d-block fw-bold">{{ $rowDate->format('Y-m-d') }}</span>
                                    <small class="text-danger">{{ $rowDate->format('H:i:s') }}</small>
                                </td>

                                <td>
                                    @if($row['type'] == 'purchase')
                                    <span class="badge bg-primary">Purchase</span>
                                    @else
                                    <span class="badge bg-success">Payment</span>
                                    @endif
                                </td>

                                <td>{{ $row['reference'] }}</td>
                                <td>{{ $row['product'] }}</td>
                                <td>{{ $row['qty'] }}</td>
                                <td>{{ is_numeric($row['price']) ? number_format($row['price'],2) : '-' }}</td>
                                <td>{{ $row['discount'] ? number_format($row['discount'],2) : '-' }}</td>
                                <td>{{ $row['tax'] ? number_format($row['tax'],2) : '-' }}</td>

                                <td class="text-success fw-bold">
                                    {{ $row['debit'] ? number_format($row['debit'],2) : '-' }}
                                </td>

                                <td class="text-danger fw-bold">
                                    {{ $row['credit'] ? number_format($row['credit'],2) : '-' }}
                                </td>

                                <td class="fw-bold">
                                    {{ number_format($row['balance'],2) }}
                                </td>
                            </tr>

                            @endforeach

                        </tbody>

                        <tfoot class="table-light fw-bold text-end">
                            <tr>
                                <td colspan="8">Totals:</td>
                                <td class="text-success">{{ number_format($totalDebit,2) }}</td>
                                <td class="text-danger">{{ number_format($totalCredit,2) }}</td>
                                <td>{{ number_format($currentBalance,2) }}</td>
                            </tr>
                        </tfoot>

                    </table>
                </div>

                @else
                <div class="alert alert-warning text-center">
                    No transactions found for this supplier.
                </div>
                @endif

            </div>
        </div>
    </div>

    <hr>

    <!-- Previous Payments -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">
            <h4 class="mb-4 text-dark fw-semibold">📄 Previous Payments</h4>

            @if($supplier->payments->count())

            <div class="table-responsive">
                <table id="supplierPaymentsTable" class="table table-striped align-middle table-hover">

                    <thead class="table-dark text-center">
                        <tr>
                            <th>Payment no</th>
                            <th>Mode</th>
                            <th>Purchase no</th>
                            <th>Description</th>
                            <th>Amount (Rs)</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody class="text-center">

                        @foreach($supplier->payments as $payment)
                        <tr>
                            <td>{{ $payment->id ?? '-' }}</td>
                            <td><span class="badge bg-secondary">{{ $payment->payment_type }}</span></td>
                            <td>{{ $payment->purchase_id ?? '-' }}</td>
                            <td>{{ $payment->description }}</td>
                            <td class="fw-bold text-success">Rs {{ number_format($payment->amount,2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($payment->created_at)->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('supplier-payments.edit', $payment) }}" class="btn btn-sm btn-success text-white">
                                    Edit <i class="material-icons">edit</i>
                                </a>

                                <form action="{{ route('supplier-payments.destroy', $payment) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this payment?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        Delete <i class="material-icons">delete</i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

            @else
            <div class="text-muted fst-italic">No payments recorded yet.</div>
            @endif

        </div>
    </div>

</div>
</div>

<!-- JS for Export PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<style>
    tfoot.table-light.fw-bold.text-end tr td {
        font-size: 18px !important;
        font-weight: 600 !important;
    }
</style>
<script>
    const supplierLedgerData = {
        supplierName: '{{ addslashes($supplier->name) }}',
        companyName: '{{ addslashes($supplier->company_name ?? '') }}',
        currentBalance: '{{ number_format($currentBalance, 2) }}',
        ledger: @json($ledger->values()),
        totalDebit: {{ $ledger->sum('debit') }},
        totalCredit: {{ $ledger->sum('credit') }},
    };

    function exportSupplierPaymentsPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF("l", "mm", "a4"); // landscape for wide ledger

        const logoUrl = "{{ asset('assets/images/logos/logo.jpg') }}";
        const img = new Image();
        img.src = logoUrl;

        img.onload = function () {
            const d = supplierLedgerData;
            const pageW = doc.internal.pageSize.width;

            // --- Logo ---
            doc.addImage(img, "JPG", 14, 8, 38, 14);

            // --- Title ---
            doc.setFontSize(16);
            doc.setFont("helvetica", "bold");
            doc.setTextColor(17, 20, 45);
            doc.text("Supplier Ledger Report", pageW / 2, 16, { align: "center" });

            // --- Supplier Info ---
            doc.setFontSize(10);
            doc.setFont("helvetica", "normal");
            doc.setTextColor(0, 0, 0);
            const label = d.companyName ? `${d.companyName} ( ${d.supplierName} )` : d.supplierName;
            doc.text(`Supplier: ${label}`, 14, 30);
            doc.text(`Current Balance: Rs ${d.currentBalance}`, 14, 37);
            doc.text(`Date: {{ now()->format('Y-m-d') }}`, pageW - 14, 30, { align: "right" });

            // --- Build table body ---
            const head = [['Date', 'Type', 'Reference', 'Product', 'Qty', 'Price', 'Disc%', 'Tax%', 'Debit (+)', 'Credit (-)', 'Balance']];
            const body = [];

            d.ledger.forEach(row => {
                const dt = row.date ? row.date.substring(0, 10) : '-';
                const type = row.type === 'purchase' ? 'Purchase' : 'Payment';
                const price = (row.price !== null && row.price !== '-' && !isNaN(row.price))
                    ? parseFloat(row.price).toLocaleString('en-US', { minimumFractionDigits: 2 })
                    : '-';
                const disc = row.discount ? parseFloat(row.discount).toFixed(2) : '-';
                const tax  = row.tax  ? parseFloat(row.tax).toFixed(2)  : '-';
                const debit  = row.debit  ? parseFloat(row.debit).toLocaleString('en-US', { minimumFractionDigits: 2 }) : '-';
                const credit = row.credit ? parseFloat(row.credit).toLocaleString('en-US', { minimumFractionDigits: 2 }) : '-';
                const balance = parseFloat(row.balance).toLocaleString('en-US', { minimumFractionDigits: 2 });

                body.push([dt, type, row.reference, row.product ?? '-', row.qty ?? '-', price, disc, tax, debit, credit, balance]);
            });

            const totalDebit  = parseFloat(d.totalDebit).toLocaleString('en-US', { minimumFractionDigits: 2 });
            const totalCredit = parseFloat(d.totalCredit).toLocaleString('en-US', { minimumFractionDigits: 2 });

            doc.autoTable({
                head: head,
                body: body,
                startY: 44,
                theme: 'grid',
                styles: {
                    fontSize: 8,
                    halign: 'center',
                    valign: 'middle',
                    cellPadding: 2,
                },
                headStyles: {
                    fillColor: [17, 20, 45],
                    textColor: [255, 255, 255],
                    fontStyle: 'bold',
                    fontSize: 8,
                },
                columnStyles: {
                    0: { cellWidth: 22 },
                    1: { cellWidth: 20 },
                    2: { cellWidth: 25 },
                    3: { halign: 'left', cellWidth: 52 },
                    4: { cellWidth: 12 },
                    5: { cellWidth: 22 },
                    6: { cellWidth: 14 },
                    7: { cellWidth: 14 },
                    8: { cellWidth: 25, textColor: [0, 128, 0], fontStyle: 'bold' },
                    9: { cellWidth: 25, textColor: [220, 0, 0], fontStyle: 'bold' },
                    10: { cellWidth: 28, fontStyle: 'bold' },
                },
                foot: [[
                    { content: 'Totals:', colSpan: 8, styles: { halign: 'right', fontStyle: 'bold', textColor: "#11142d", fillColor: [240, 240, 240] } },
                    { content: totalDebit,  styles: { halign: 'center', fontStyle: 'bold', textColor: [0, 128, 0], fillColor: [240, 240, 240] } },
                    { content: totalCredit, styles: { halign: 'center', fontStyle: 'bold', textColor: [220, 0, 0], fillColor: [240, 240, 240] } },
                    { content: d.currentBalance, styles: { halign: 'center', fontStyle: 'bold', textColor: "#11142d", fillColor: [240, 240, 240] } },
                ]],
                footStyles: { fillColor: [240, 240, 240], fontStyle: 'bold' },
                didDrawPage: function (data) {
                    const pageHeight = doc.internal.pageSize.height;
                    doc.setFontSize(7);
                    doc.setTextColor(120);
                    doc.text(
                        "Page " + doc.internal.getNumberOfPages(),
                        pageW - 14, pageHeight - 8,
                        { align: "right" }
                    );
                },
            });

            doc.save(`supplier-ledger-${d.supplierName}.pdf`);
        };
    }

    function toggleLedger() {
        const ledger = document.getElementById("ledgerSection");
        const btn = document.getElementById("ledgerBtn");

        if (ledger.style.display === "none" || ledger.style.display === "") {
            ledger.style.display = "block";
            ledger.style.animation = "fadeIn 0.3s ease-in-out";
            btn.innerHTML = "❌ Hide Supplier Ledger";
        } else {
            ledger.style.display = "none";
            btn.innerHTML = "📊 View Supplier Ledger";
        }
    }
</script>

@endsection