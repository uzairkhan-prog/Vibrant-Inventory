@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <h2 class="fw-bold text-primary-emphasis">Supplier Profile: <strong>{{ $supplier->name }}</strong></h2>
        @if($supplier->payments->count())
        <button class="btn btn-danger" onclick="exportSupplierPaymentsPDF()">
            <i class="material-icons align-middle">picture_as_pdf</i> Export PDF
        </button>
        @endif
    </div>

    <!-- Balance Info -->
    <div class="alert alert-info d-flex justify-content-between align-items-center shadow-sm rounded-3 fs-5">
        <span><strong>Previous Balance:</strong></span>
        <span class="fw-bold text-success">Rs {{ number_format($currentBalance, 2) }}</span>
    </div>

    <!-- Flash messages -->
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
    <div class="card shadow-lg border-0 rounded-4 mb-5">
        <div class="card-body">
            <h4 class="mb-4 text-dark fw-semibold">💳 Record a Payment</h4>
            <form method="POST" action="{{ route('suppliers.payments.store', $supplier) }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Supplier Name</label>
                        <input type="text" class="form-control bg-light" value="{{ $supplier->name }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="payment_type" class="form-label">Payment Type</label>
                        <select name="payment_type" id="payment_type" class="form-select" required>
                            <option value="">-- Select --</option>
                            <option value="Cash">Cash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label for="description" class="form-label">Payment Description</label>
                        <textarea name="description" id="description" class="form-control" rows="2" placeholder="Payment details...">{{ old('description') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="amount" class="form-label">Amount (Rs)</label>
                        <input type="number" step="0.01" name="amount" id="amount" class="form-control" required min="0.01" value="{{ old('amount') }}">
                    </div>
                </div>
                @php
                $isDisabled = $currentBalance <= 0 ? 'disabled' : '' ;
                    @endphp
                    <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('suppliers.index') }}" class="btn btn-outline-dark">← Back to Suppliers</a>
                    <button type="submit" class="btn btn-primary px-4" {{ $isDisabled }}>
                        <i class="material-icons align-middle">send</i> Submit
                    </button>
        </div>
        </form>
    </div>
</div>

<!-- Previous Payments Table -->
<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
        <h4 class="mb-4 text-dark fw-semibold">📄 Previous Payments</h4>

        @if($supplier->payments->count())
        <div class="table-responsive">
            <table class="table table-striped align-middle table-hover" id="supplierPaymentsTable">
                <thead class="table-dark text-center">
                    <tr>
                        <th scope="col">Type</th>
                        <th scope="col">Description</th>
                        <th scope="col">Amount (Rs)</th>
                        <th scope="col">Date</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach($supplier->payments as $payment)
                    <tr>
                        <td><span class="badge bg-secondary">{{ $payment->payment_type }}</span></td>
                        <td>{{ $payment->description }}</td>
                        <td class="fw-bold text-success">Rs {{ number_format($payment->amount, 2) }}</td>
                        <td>{{ $payment->created_at->format('Y-m-d') }}</td>
                        <td>
                            <a href="{{ route('supplier-payments.edit', $payment) }}" class="btn btn-sm btn-success text-white" title="Edit">
                                Edit <i class="material-icons">edit</i>
                            </a>
                            <form action="{{ route('supplier-payments.destroy', $payment) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this payment?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
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

<!-- Include jsPDF and AutoTable -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>

<script>
    window.exportSupplierPaymentsPDF = function() {
        // Use jsPDF from window.jspdf
        const doc = new window.jspdf.jsPDF("p", "mm", "a4");

        // --- Logo ---
        const logoUrl = "{{ asset('assets/images/logos/logo.jpg') }}";
        const img = new Image();
        img.src = logoUrl;

        img.onload = function() {
            doc.addImage(img, "JPG", 14, 10, 40, 15);

            // --- Header ---
            doc.setFontSize(16);
            doc.setFont("helvetica", "bold");
            doc.setTextColor(17, 20, 45);
            doc.text("Supplier Payments Report", 105, 18, {
                align: "center"
            });

            // Supplier Info
            doc.setFontSize(12);
            doc.setFont("helvetica", "normal");
            doc.text(`Supplier: {{ $supplier->name }}`, 14, 32);
            doc.text(`Current Balance: Rs {{ number_format($currentBalance, 2) }}`, 14, 39);

            // --- Table Data ---
            let head = [
                ['Mode', 'Description', 'Amount (Rs)', 'Date']
            ];
            let body = [];
            document.querySelectorAll("#supplierPaymentsTable tbody tr").forEach(tr => {
                let tds = tr.querySelectorAll("td");
                body.push([tds[0].innerText, tds[1].innerText, tds[2].innerText, tds[3].innerText]);
            });

            doc.autoTable({
                head: head,
                body: body,
                startY: 50,
                theme: 'grid',
                styles: {
                    fontSize: 9,
                    halign: 'center',
                    valign: 'middle',
                    cellPadding: 3
                },
                headStyles: {
                    fillColor: [17, 20, 45],
                    textColor: [255, 255, 255],
                    fontStyle: 'bold'
                },
                columnStyles: {
                    1: {
                        halign: 'left',
                        cellWidth: 80
                    }
                },
                didDrawPage: function(data) {
                    let pageHeight = doc.internal.pageSize.height;
                    doc.setFontSize(8);
                    doc.setTextColor(100);
                    doc.text("Page " + doc.internal.getNumberOfPages(), 200, pageHeight - 10, {
                        align: "right"
                    });
                }
            });

            // --- Total Amount ---
            let total = 0;
            body.forEach(row => {
                total += parseFloat(row[2].replace(/[^\d.-]/g, '')) || 0;
            });
            let finalY = doc.lastAutoTable.finalY + 10;
            doc.setFontSize(10);
            doc.setFont("helvetica", "bold");
            doc.setTextColor(17, 20, 45);
            doc.text(`Total Payments: Rs ${total.toFixed(2)}`, 14, finalY);

            // Save PDF
            doc.save('supplier-payments.pdf');
        };
    }
</script>
@endsection