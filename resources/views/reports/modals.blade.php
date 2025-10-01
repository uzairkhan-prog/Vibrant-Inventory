<!-- Products Ledger Modal -->
<div class="modal fade" id="productsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Products Ledger Report ({{ $startDate }} - {{ $endDate }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="productsReportContent">
                @forelse($productsLedger as $product)
                <div class="mb-5">
                    <h6 class="fw-bold">
                        {{ $product->name }}
                        <span class="text-muted">(Stock: {{ $product->quantity }})</span>
                        <span class="badge bg-info ms-2">{{ $product->category->name ?? 'Uncategorized' }}</span>
                    </h6>

                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-start p-2">Date</th>
                                <th class="text-start p-2">Type</th>
                                <th class="text-start p-2">Invoice</th>
                                <th class="text-start p-2">Category</th>
                                <th class="text-start p-2">Qty</th>
                                <th class="text-start p-2">Price</th>
                                <th class="text-start p-2">Total</th>
                                <th class="text-end p-2">Running Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $runningStock = 0; $hasRows = false; @endphp

                            {{-- Purchases --}}
                            @foreach($product->purchaseItems as $item)
                            @php
                            $hasRows = true;
                            $runningStock += $item->quantity;
                            $lineTotal = $item->quantity * $item->price;
                            @endphp
                            <tr>
                                <td class="text-start p-2">{{ $item->purchase->created_at->format('Y-m-d') }}</td>
                                <td class="text-start p-2"><span class="badge bg-success">Purchase</span></td>
                                <td class="text-start p-2">#{{ $item->purchase->id }}</td>
                                <td class="text-start p-2">{{ $item->product->category->name ?? 'N/A' }}</td>
                                <td class="text-start p-2">+{{ $item->quantity }}</td>
                                <td class="text-start p-2">{{ number_format($item->price,2) }}</td>
                                <td class="text-start p-2">{{ number_format($lineTotal,2) }}</td>
                                <td class="text-end p-2 fw-bold">{{ $runningStock }}</td>
                            </tr>
                            @endforeach

                            {{-- Sales --}}
                            @foreach($product->saleItems as $item)
                            @php
                            $hasRows = true;
                            $runningStock -= $item->quantity;
                            $lineTotal = $item->quantity * $item->price;
                            @endphp
                            <tr>
                                <td class="text-start p-2">{{ $item->sale->created_at->format('Y-m-d') }}</td>
                                <td class="text-start p-2"><span class="badge bg-danger">Sale</span></td>
                                <td class="text-start p-2">#{{ $item->sale->id }}</td>
                                <td class="text-start p-2">{{ $item->product->category->name ?? 'N/A' }}</td>
                                <td class="text-start p-2">-{{ $item->quantity }}</td>
                                <td class="text-start p-2">{{ number_format($item->price,2) }}</td>
                                <td class="text-start p-2">{{ number_format($lineTotal,2) }}</td>
                                <td class="text-end p-2 fw-bold">{{ $runningStock }}</td>
                            </tr>
                            @endforeach

                            {{-- If no records --}}
                            @unless($hasRows)
                            <tr>
                                <td colspan="8" class="text-center text-muted">No records found for this product.</td>
                            </tr>
                            @endunless
                        </tbody>
                    </table>
                </div>
                @empty
                <div class="alert alert-warning">No products found.</div>
                @endforelse

                {{-- Pagination --}}
                @if ($productsLedger instanceof \Illuminate\Contracts\Pagination\Paginator && $productsLedger->hasPages())
                <div class="d-flex justify-content-center">
                    {!! $productsLedger->appends(request()->all())->links('pagination::bootstrap-5') !!}
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" id="downloadProductsPdf">Export PDF</button>
            </div>
        </div>
    </div>
</div>

<!-- Purchases Modal -->
<div class="modal fade" id="purchasesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Purchases Report ({{ $startDate }} - {{ $endDate }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="purchasesReportContent">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-start p-2">ID</th>
                            <th class="text-start p-2">Supplier</th>
                            <th class="text-start p-2">Product</th>
                            <th class="text-start p-2">Category</th>
                            <th class="text-start p-2">Quantity</th>
                            <th class="text-start p-2">Unit Price</th>
                            <th class="text-start p-2">Discount</th>
                            <th class="text-start p-2">Tax</th>
                            <th class="text-start p-2">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $purchaseRow = 1; $totalPurchases = 0; @endphp
                        @forelse($purchases as $purchase)
                        @foreach($purchase->items as $item)
                        @php
                        $subtotal = ($item->quantity * $item->price) - $item->discount + $item->tax;
                        $totalPurchases += $subtotal;
                        @endphp
                        <tr>
                            <td class="text-start p-2">{{ $purchaseRow++ }}</td>
                            <td class="text-start p-2">{{ $purchase->supplier->name ?? 'N/A' }}</td>
                            <td class="text-start p-2">{{ $item->product->name }}</td>
                            <td class="text-start p-2">{{ $item->product->category->name ?? 'N/A' }}</td>
                            <td class="text-start p-2">{{ $item->quantity }}</td>
                            <td class="text-start p-2">{{ number_format($item->price,2) }}</td>
                            <td class="text-start p-2">{{ number_format($item->discount,2) }}</td>
                            <td class="text-start p-2">{{ number_format($item->tax,2) }}</td>
                            <td class="text-start p-2">{{ number_format($subtotal,2) }}</td>
                        </tr>
                        @endforeach
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">No purchases found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-total fw-bold">
                            <td colspan="8" class="text-end">Total Purchases:</td>
                            <td class="text-start p-2">{{ number_format($totalPurchases,2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" id="downloadPurchasesPdf">Export PDF</button>
            </div>
        </div>
    </div>
</div>

<!-- Sales Modal -->
<div class="modal fade" id="salesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sales Report ({{ $startDate }} - {{ $endDate }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="salesReportContent">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-start p-2">ID</th>
                            <th class="text-start p-2">Customer</th>
                            <th class="text-start p-2">Product</th>
                            <th class="text-start p-2">Category</th>
                            <th class="text-start p-2">Quantity</th>
                            <th class="text-start p-2">Unit Price</th>
                            <th class="text-start p-2">Discount</th>
                            <th class="text-start p-2">Tax</th>
                            <th class="text-start p-2">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $saleRow = 1; $totalSales = 0; @endphp
                        @forelse($sales as $sale)
                        @foreach($sale->items as $item)
                        @php
                        $subtotal = ($item->quantity * $item->price) - $item->discount + $item->tax;
                        $totalSales += $subtotal;
                        @endphp
                        <tr>
                            <td class="text-start p-2">{{ $saleRow++ }}</td>
                            <td class="text-start p-2">{{ $sale->customer->name ?? 'N/A' }}</td>
                            <td class="text-start p-2">{{ $item->product->name }}</td>
                            <td class="text-start p-2">{{ $item->product->category->name ?? 'N/A' }}</td>
                            <td class="text-start p-2">{{ $item->quantity }}</td>
                            <td class="text-start p-2">{{ number_format($item->price,2) }}</td>
                            <td class="text-start p-2">{{ number_format($item->discount,2) }}</td>
                            <td class="text-start p-2">{{ number_format($item->tax,2) }}</td>
                            <td class="text-start p-2">{{ number_format($subtotal,2) }}</td>
                        </tr>
                        @endforeach
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">No sales found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-total fw-bold">
                            <td colspan="8" class="text-end">Total Sales:</td>
                            <td class="text-start p-2">{{ number_format($totalSales,2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" id="downloadSalesPdf">Export PDF</button>
            </div>
        </div>
    </div>
</div>

<!-- Expenses Modal -->
<div class="modal fade" id="expensesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Expenses Report ({{ $startDate }} - {{ $endDate }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="expensesReportContent">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-start p-2">ID</th>
                            <th class="text-start p-2">Date</th>
                            <th class="text-start p-2">Expense Name</th>
                            <th class="text-start p-2">Description</th>
                            <th class="text-start p-2">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $expenseRow = 1; $totalExpenses = 0; @endphp
                        @forelse($expenses as $expense)
                        @php $totalExpenses += $expense->amount; @endphp
                        <tr>
                            <td class="text-start p-2">{{ $expenseRow++ }}</td>
                            <td class="text-start p-2">{{ $expense->created_at->format('Y-m-d') }}</td>
                            <td class="text-start p-2">{{ $expense->expenseName->name ?? 'N/A' }}</td>
                            <td class="text-start p-2">{{ $expense->description }}</td>
                            <td class="text-start p-2">{{ number_format($expense->amount,2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No expenses found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-total fw-bold">
                            <td colspan="4" class="text-end">Total Expenses:</td>
                            <td class="text-start p-2">{{ number_format($totalExpenses,2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" id="downloadExpensesPdf">Export PDF</button>
            </div>
        </div>
    </div>
</div>

<!-- Customers Ledger Modal -->
<div class="modal fade" id="customersModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Customers Ledger ({{ $startDate }} - {{ $endDate }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="customersReportContent">
                @forelse($customersLedger as $customer)
                <div class="mb-3">
                    <h6 class="fw-bold">
                        {{ $customer->name }}
                        <span class="text-muted">(Closing Balance: {{ number_format($customer->balance,2) }})</span>
                    </h6>
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-start p-2">Date</th>
                                <th class="text-start p-2">Type</th>
                                <th class="text-start p-2">Invoice/Receipt</th>
                                <th class="text-start p-2">Product</th>
                                <th class="text-start p-2">Qty</th>
                                <th class="text-start p-2">Price</th>
                                <th class="text-start p-2">Tax</th>
                                <th class="text-start p-2">Discount</th>
                                <th class="text-end">Debit (+)</th>
                                <th class="text-end">Credit (-)</th>
                                <th class="text-end">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $runningBalance=0; $hasRows=false; $totalDebit=0; $totalCredit=0; @endphp

                            {{-- Sales --}}
                            @foreach($customer->sales as $sale)
                            @foreach($sale->items as $item)
                            @php
                            $hasRows=true;
                            $lineTotal = ($item->quantity * $item->price) + $sale->tax - $sale->discount;
                            $runningBalance += $lineTotal;
                            $totalDebit += $lineTotal;
                            @endphp
                            <tr>
                                <td class="text-start p-2">{{ $sale->created_at->format('Y-m-d') }}</td>
                                <td class="text-start p-2"><span class="badge bg-success">Sale</span></td>
                                <td class="text-start p-2">Invoice #{{ $sale->id }}</td>
                                <td class="text-start p-2">{{ $item->product->name }}</td>
                                <td class="text-start p-2">{{ $item->quantity }}</td>
                                <td class="text-start p-2">{{ number_format($item->price,2) }}</td>
                                <td class="text-start p-2">{{ number_format($sale->tax,2) }}</td>
                                <td class="text-start p-2">{{ number_format($sale->discount,2) }}</td>
                                <td class="text-end text-success fw-bold">+{{ number_format($lineTotal,2) }}</td>
                                <td class="text-end">-</td>
                                <td class="text-end fw-bold">{{ number_format($runningBalance,2) }}</td>
                            </tr>
                            @endforeach
                            @endforeach

                            {{-- Payments --}}
                            @foreach($customer->payments as $payment)
                            @php
                            $hasRows=true;
                            $runningBalance -= $payment->amount;
                            $totalCredit += $payment->amount;
                            @endphp
                            <tr>
                                <td class="text-start p-2">{{ $payment->created_at->format('Y-m-d') }}</td>
                                <td class="text-start p-2"><span class="badge bg-primary">Payment</span></td>
                                <td class="text-start p-2">Receipt #{{ $payment->id }}</td>
                                <td class="text-start p-2">-</td>
                                <td class="text-start p-2">-</td>
                                <td class="text-start p-2">-</td>
                                <td class="text-start p-2">-</td>
                                <td class="text-start p-2">-</td>
                                <td class="text-end">-</td>
                                <td class="text-end text-danger fw-bold">-{{ number_format($payment->amount,2) }}</td>
                                <td class="text-end fw-bold">{{ number_format($runningBalance,2) }}</td>
                            </tr>
                            @endforeach

                            @unless($hasRows)
                            <tr>
                                <td colspan="11" class="text-center text-muted">No records found for this customer.</td>
                            </tr>
                            @endunless
                        </tbody>

                        {{-- Totals --}}
                        @if($hasRows)
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="8" class="text-end">Totals:</td>
                                <td class="text-end text-success">+{{ number_format($totalDebit,2) }}</td>
                                <td class="text-end text-danger">-{{ number_format($totalCredit,2) }}</td>
                                <td class="text-end">{{ number_format($runningBalance,2) }}</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
                @empty
                <div class="alert alert-warning">No customers found.</div>
                @endforelse
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" id="downloadCustomersPdf">Export PDF</button>
            </div>
        </div>
    </div>
</div>

<!-- Suppliers Ledger Modal -->
<div class="modal fade" id="suppliersModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Suppliers Ledger ({{ $startDate }} - {{ $endDate }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="suppliersReportContent">
                @forelse($suppliersLedger as $supplier)
                <div class="mb-3">
                    <h6 class="fw-bold">
                        {{ $supplier->name }}
                        <span class="text-muted">(Closing Balance: {{ number_format($supplier->balance,2) }})</span>
                    </h6>
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-start p-2">Date</th>
                                <th class="text-start p-2">Type</th>
                                <th class="text-start p-2">Invoice/Receipt</th>
                                <th class="text-start p-2">Product</th>
                                <th class="text-start p-2">Qty</th>
                                <th class="text-start p-2">Price</th>
                                <th class="text-start p-2">Tax</th>
                                <th class="text-start p-2">Discount</th>
                                <th class="text-end">Debit (+)</th>
                                <th class="text-end">Credit (-)</th>
                                <th class="text-end">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $runningBalance=0; $hasRows=false; $totalDebit=0; $totalCredit=0; @endphp

                            {{-- Purchases from Supplier --}}
                            @foreach($supplier->purchases as $purchase)
                            @foreach($purchase->items as $item)
                            @php
                            $hasRows = true;
                            $lineTotal = ($item->quantity * $item->price) + $purchase->tax - $purchase->discount;
                            $runningBalance += $lineTotal;
                            $totalDebit += $lineTotal;
                            @endphp
                            <tr>
                                <td class="text-start p-2">{{ $purchase->created_at->format('Y-m-d') }}</td>
                                <td class="text-start p-2"><span class="badge bg-success">Purchase</span></td>
                                <td class="text-start p-2">Invoice #{{ $purchase->id }}</td>
                                <td class="text-start p-2">{{ $item->product->name }}</td>
                                <td class="text-start p-2">{{ $item->quantity }}</td>
                                <td class="text-start p-2">{{ number_format($item->price,2) }}</td>
                                <td class="text-start p-2">{{ number_format($purchase->tax,2) }}</td>
                                <td class="text-start p-2">{{ number_format($purchase->discount,2) }}</td>
                                <td class="text-end text-success fw-bold">+{{ number_format($lineTotal,2) }}</td>
                                <td class="text-end">-</td>
                                <td class="text-end fw-bold">{{ number_format($runningBalance,2) }}</td>
                            </tr>
                            @endforeach
                            @endforeach

                            {{-- Payments to Supplier --}}
                            @foreach($supplier->payments as $payment)
                            @php
                            $hasRows = true;
                            $runningBalance -= $payment->amount;
                            $totalCredit += $payment->amount;
                            @endphp
                            <tr>
                                <td class="text-start p-2">{{ $payment->created_at->format('Y-m-d') }}</td>
                                <td class="text-start p-2"><span class="badge bg-primary">Payment</span></td>
                                <td class="text-start p-2">Receipt #{{ $payment->id }}</td>
                                <td class="text-start p-2">-</td>
                                <td class="text-start p-2">-</td>
                                <td class="text-start p-2">-</td>
                                <td class="text-start p-2">-</td>
                                <td class="text-start p-2">-</td>
                                <td class="text-end">-</td>
                                <td class="text-end text-danger fw-bold">-{{ number_format($payment->amount,2) }}</td>
                                <td class="text-end fw-bold">{{ number_format($runningBalance,2) }}</td>
                            </tr>
                            @endforeach

                            @unless($hasRows)
                            <tr>
                                <td colspan="11" class="text-center text-muted">No records found for this supplier.</td>
                            </tr>
                            @endunless
                        </tbody>

                        {{-- Totals --}}
                        @if($hasRows)
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="8" class="text-end">Totals:</td>
                                <td class="text-end text-success">+{{ number_format($totalDebit,2) }}</td>
                                <td class="text-end text-danger">-{{ number_format($totalCredit,2) }}</td>
                                <td class="text-end">{{ number_format($runningBalance,2) }}</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
                @empty
                <div class="alert alert-warning">No suppliers found.</div>
                @endforelse
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" id="downloadSuppliersPdf">Export PDF</button>
            </div>
        </div>
    </div>
</div>

{{-- PDF Export --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    function exportPdf(contentId, fileName, reportTitle) {
        const {
            jsPDF
        } = window.jspdf;

        // Clone content for export
        let content = document.getElementById(contentId).cloneNode(true);
        content.style.display = "block";
        content.style.width = "1000px";
        content.style.padding = "20px";

        let tempDiv = document.createElement("div");
        tempDiv.style.position = "absolute";
        tempDiv.style.left = "-9999px";
        tempDiv.appendChild(content);
        document.body.appendChild(tempDiv);

        html2canvas(content, {
            scale: 2
        }).then(canvas => {
            const imgData = canvas.toDataURL("image/png");
            const pdf = new jsPDF("p", "mm", "a4");

            const pdfWidth = pdf.internal.pageSize.getWidth();
            const pageHeight = pdf.internal.pageSize.getHeight();

            // margins
            const marginLeft = 15;
            const marginRight = 15;
            const usableWidth = pdfWidth - marginLeft - marginRight;

            const pdfHeight = (canvas.height * usableWidth) / canvas.width;
            let heightLeft = pdfHeight;
            let position = 50; // start content after header

            // ===== Draw Header Function =====
            function drawHeader() {
                pdf.addImage(
                    "data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/images/logos/logo-export.png'))) }}",
                    "PNG",
                    marginLeft,
                    10,
                    30,
                    25
                );

                pdf.setFont("helvetica", "bold");
                pdf.setFontSize(14);
                pdf.text(reportTitle, pdfWidth / 2, 20, {
                    align: "center"
                });

                pdf.setFont("helvetica", "normal");
                pdf.setFontSize(9);
                pdf.text("Head Office: Shop #13, Falak Park View Near Inquiry Office Nazimabad #2, Karachi", pdfWidth - marginRight, 12, {
                    align: "right"
                });
                pdf.text("Phone: +92 335 2385773", pdfWidth - marginRight, 18, {
                    align: "right"
                });
                pdf.text("Email: info@vibrantengineering.pk", pdfWidth - marginRight, 24, {
                    align: "right"
                });

                pdf.setLineWidth(0.5);
                pdf.line(marginLeft, 35, pdfWidth - marginRight, 35);
            }

            // ===== First Page =====
            drawHeader();
            pdf.addImage(imgData, "PNG", marginLeft, position, usableWidth, pdfHeight);
            heightLeft -= (pageHeight - position);

            // ===== Handle Multiple Pages =====
            while (heightLeft > 0) {
                pdf.addPage();
                drawHeader();

                let newPosition = 50 - (pdfHeight - heightLeft);
                pdf.addImage(imgData, "PNG", marginLeft, newPosition, usableWidth, pdfHeight);
                heightLeft -= (pageHeight - 50);
            }

            pdf.save(fileName);
            document.body.removeChild(tempDiv);
        });
    }

    // Attach Export Buttons
    document.getElementById("downloadProductsPdf").addEventListener("click", () => {
        exportPdf("productsReportContent", "Products_Ledger.pdf", "Products Ledger Report");
    });
    document.getElementById("downloadSalesPdf").addEventListener("click", () => {
        exportPdf("salesReportContent", "Sales_Report.pdf", "Sales Report");
    });
    document.getElementById("downloadPurchasesPdf").addEventListener("click", () => {
        exportPdf("purchasesReportContent", "Purchases_Report.pdf", "Purchases Report");
    });
    document.getElementById("downloadExpensesPdf").addEventListener("click", () => {
        exportPdf("expensesReportContent", "Expenses_Report.pdf", "Expenses Report");
    });
    document.getElementById("downloadCustomersPdf").addEventListener("click", () => {
        exportPdf("customersReportContent", "Customers_Ledger.pdf", "Customers Ledger");
    });
    document.getElementById("downloadSuppliersPdf").addEventListener("click", () => {
        exportPdf("suppliersReportContent", "Suppliers_Ledger.pdf", "Suppliers Ledger");
    });
</script>