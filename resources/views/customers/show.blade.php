@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <h2 class="fw-bold text-primary-emphasis">
            Customer Profile: <strong>{{ $customer->name }}</strong>
        </h2>
        <!-- Export PDF Button -->
        @if($customer->payments->count())
        <button class="btn btn-danger" onclick="exportCustomerPaymentsPDF()">
            <i class="material-icons align-middle">picture_as_pdf</i> Export PDF
        </button>
        @endif
    </div>

    <!-- Balance Info -->
    <div class="alert alert-success d-flex justify-content-between align-items-center shadow-sm rounded-3 fs-5">
        <span><strong>Current Balance:</strong></span>
        <!-- <span class="fw-bold text-success">Rs {{ number_format($currentBalance, 2) }}</span> -->
        @if($customer->name === 'Counter Sale')
        <span class="fw-bold text-success">
            Rs {{ number_format($currentBalance, 2) }}
        </span>
        @else
        <span class="fw-bold text-success">
            Rs {{ number_format(($customer->balance ?? 0) - ($customer->payments->sum('amount') ?? 0), 2) }}
        </span>
        @endif
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
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body">
            <h4 class="mb-4 text-dark fw-semibold">💳 Record a Payment</h4>
            <form method="POST" action="{{ route('customers.payments.store', $customer) }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Customer Name</label>
                        <input type="text" class="form-control bg-light" value="{{ $customer->name }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="sale_id" class="form-label">Invoice</label>
                        <select name="sale_id" id="sale_id" class="form-select">
                            <option value="">-- Select Sale --</option>
                            @foreach($sales as $sale)
                            @if($sale->remaining_amount > 0)
                            <option value="{{ $sale->id }}">
                                Invoice no ( {{ $sale->id }} ) - Remaining amount ( {{ number_format($sale->remaining_amount,2) }} )
                            </option>
                            @endif
                            @endforeach
                        </select>
                        @error('sale_id')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
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
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-dark">
                        ← Back to Customers
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
            📊 View Customer Ledger
        </button>
    </div>
    <div id="ledgerSection" style="display: none;">
        <!-- Customer Ledger Report -->
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <h4 class="mb-4 text-dark fw-semibold">{{ $customer->company_name ? strtoupper($customer->company_name) : 'N/A' }} ( {{ $customer->name ?? 'N/A' }} )</h4>

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
                                    @if($row['type'] == 'sale')
                                    <span class="badge bg-success">Sale</span>
                                    @else
                                    <span class="badge bg-primary">Payment</span>
                                    @endif
                                </td>

                                <td>{{ $row['reference'] }}</td>
                                <td>{{ $row['product'] }}</td>
                                <td>{{ $row['qty'] }}</td>
                                <td>
                                    {{ is_numeric($row['price']) ? number_format($row['price'],2) : '-' }}
                                </td>

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

                        <!-- Totals Footer -->
                        <tfoot class="table-light fw-bold text-end">
                            <tr>
                                <td colspan="8">Totals:</td>
                                <td class="text-success">
                                    {{ number_format($totalDebit,2) }}
                                </td>
                                <td class="text-danger">
                                    {{ number_format($totalCredit,2) }}
                                </td>
                                <td>
                                    {{ number_format($currentBalance,2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @else
                <div class="alert alert-warning text-center">
                    No transactions found for this customer.
                </div>
                @endif

            </div>
        </div>
    </div>
    <hr>
    <!-- Previous Payments Table -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">
            <h4 class="mb-4 text-dark fw-semibold">📄 Previous Payments</h4>

            @if($customer->payments->count())
            <div class="table-responsive">
                <table id="customerPaymentsTable" class="table table-striped align-middle table-hover">
                    <thead class="table-dark text-center">
                        <tr>
                            <th scope="col">Receipt no</th>
                            <th scope="col">Mode</th>
                            <th scope="col">Invoice no</th>
                            <th scope="col">Description</th>
                            <th scope="col">Amount (Rs)</th>
                            <th scope="col">Date</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach($customer->payments as $payment)
                        @php
                        // Check if this payment is related to a Sale Return
                        $isSaleReturn = str_contains(strtolower($payment->description), 'reduced rs');
                        $rowClass = $isSaleReturn ? 'sale-return-bg-color' : ''; // Bootstrap red background class
                        @endphp

                        <tr class="{{ $rowClass }}">
                            <td>{{ $payment->id ?? '-' }}</td>
                            <td><span class="badge bg-secondary">{{ $payment->payment_type }}</span></td>
                            <td>{{ $payment->sale_id ?? '-' }}</td>
                            <td>{{ $payment->description }}</td>
                            <td class="fw-bold text-success">Rs {{ number_format($payment->amount, 2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($payment->date)->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('customer-payments.edit', $payment) }}" class="btn btn-sm btn-success text-white" title="Edit">
                                    Edit <i class="material-icons">edit</i>
                                </a>
                                <form action="{{ route('customer-payments.destroy', $payment) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this payment?')">
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

<!-- JS for Export PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<style>
    tfoot.table-light.fw-bold.text-end tr td {
        font-size: 18px !important;
        font-weight: 600 !important;
    }

    .sale-return-bg-color td {
        background-color: #f4433647 !important;
    }
</style>
<script>
    async function exportCustomerPaymentsPDF() {
        const {
            jsPDF
        } = window.jspdf;
        const doc = new jsPDF("p", "mm", "a4");

        // --- Logo ---
        const logoUrl = "{{ asset('assets/images/logos/logo.jpg') }}"; // path to your logo
        const img = new Image();
        img.src = logoUrl;

        img.onload = function() {
            doc.addImage(img, "JPG", 14, 10, 40, 15); // x=14mm, y=10mm, width=40mm, height=15mm

            // --- Header ---
            doc.setFontSize(16);
            doc.setFont("helvetica", "bold");
            doc.setTextColor(17, 20, 45);
            doc.text("Customer Payments Report", 105, 18, {
                align: "center"
            });

            // Customer Info
            doc.setFontSize(12);
            doc.setFont("helvetica", "normal");
            doc.text(`Customer: ${'{{ $customer->name }}'}`, 14, 32);
            doc.text(`Current Balance: Rs {{ number_format($currentBalance, 2) }}`, 14, 39);

            // --- Table Data ---
            let head = [
                ['Mode', 'Description', 'Amount (Rs)', 'Date']
            ];
            let body = [];
            document.querySelectorAll("#customerPaymentsTable tbody tr").forEach(tr => {
                let tds = tr.querySelectorAll("td");
                body.push([
                    tds[0].innerText,
                    tds[1].innerText,
                    tds[2].innerText,
                    tds[3].innerText
                ]);
            });

            doc.autoTable({
                head: head,
                body: body,
                startY: 50, // leave space for logo & header
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
                    let str = "Page " + doc.internal.getNumberOfPages();
                    doc.text(str, 200, pageHeight - 10, {
                        align: "right"
                    });
                }
            });

            // --- Total Amount ---
            let total = 0;
            body.forEach(row => {
                let val = parseFloat(row[2].replace(/[^\d.-]/g, '')) || 0;
                total += val;
            });

            let finalY = doc.lastAutoTable.finalY + 10;
            doc.setFontSize(10);
            doc.setFont("helvetica", "bold");
            doc.setTextColor(17, 20, 45);
            doc.text(`Total Payments: Rs ${total.toFixed(2)}`, 14, finalY);

            // Save PDF
            doc.save('customer-payments.pdf');
        };
    }
</script>
<script>
    function toggleLedger() {
        const ledger = document.getElementById("ledgerSection");
        const btn = document.getElementById("ledgerBtn");

        if (ledger.style.display === "none" || ledger.style.display === "") {
            ledger.style.display = "block";
            ledger.style.animation = "fadeIn 0.3s ease-in-out";
            btn.innerHTML = "❌ Hide Customer Ledger";
        } else {
            ledger.style.display = "none";
            btn.innerHTML = "📊 View Customer Ledger";
        }
    }
</script>
@endsection