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
                        @php
                        $purchaseRow = 1;
                        $totalPurchases = 0;
                        @endphp
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
                            <td class="text-start p-2">{{ number_format($item->price, 2) }}</td>
                            <td class="text-start p-2">{{ number_format($item->discount, 2) }}</td>
                            <td class="text-start p-2">{{ number_format($item->tax, 2) }}</td>
                            <td class="text-start p-2">{{ number_format($subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">No purchases found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-total">
                            <td colspan="8" class="text-end">Total Purchases:</td>
                            <td class="text-start">{{ number_format($totalPurchases, 2) }}</td>
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
                        @php
                        $saleRow = 1;
                        $totalSales = 0;
                        @endphp
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
                            <td class="text-start p-2">{{ number_format($item->price, 2) }}</td>
                            <td class="text-start p-2">{{ number_format($item->discount, 2) }}</td>
                            <td class="text-start p-2">{{ number_format($item->tax, 2) }}</td>
                            <td class="text-start p-2">{{ number_format($subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">No sales found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-total">
                            <td colspan="8" class="text-end">Total Sales:</td>
                            <td class="text-start">{{ number_format($totalSales, 2) }}</td>
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
                            <th>ID</th>
                            <th>Date</th>
                            <th>Expense Name</th>
                            <th>Description</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $expenseRow = 1;
                        $totalExpenses = 0;
                        @endphp
                        @forelse($expenses as $expense)
                        @php $totalExpenses += $expense->amount; @endphp
                        <tr>
                            <td>{{ $expenseRow++ }}</td>
                            <td>{{ $expense->created_at->format('Y-m-d') }}</td>
                            <td>{{ $expense->expenseName->name ?? 'N/A' }}</td>
                            <td>{{ $expense->description }}</td>
                            <td>{{ number_format($expense->amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No expenses found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-total">
                            <td colspan="4" class="text-end">Total Expenses:</td>
                            <td class="text-start">{{ number_format($totalExpenses, 2) }}</td>
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

{{-- PDF Export --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    function exportPdf(contentId, fileName) {
        const {
            jsPDF
        } = window.jspdf;
        let content = document.getElementById(contentId).cloneNode(true);
        content.style.display = "block";
        content.style.width = "1000px";

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
            const pdfHeight = (canvas.height * pdfWidth) / canvas.width;

            let heightLeft = pdfHeight;
            let position = 0;

            pdf.addImage(imgData, "PNG", 0, position, pdfWidth, pdfHeight);
            heightLeft -= pdf.internal.pageSize.getHeight();

            while (heightLeft > 0) {
                position = heightLeft - pdfHeight;
                pdf.addPage();
                pdf.addImage(imgData, "PNG", 0, position, pdfWidth, pdfHeight);
                heightLeft -= pdf.internal.pageSize.getHeight();
            }

            pdf.save(fileName);
            document.body.removeChild(tempDiv);
        });
    }

    document.getElementById("downloadSalesPdf").addEventListener("click", () => {
        exportPdf("salesReportContent", "Sales_Report.pdf");
    });
    document.getElementById("downloadPurchasesPdf").addEventListener("click", () => {
        exportPdf("purchasesReportContent", "Purchases_Report.pdf");
    });
    document.getElementById("downloadExpensesPdf").addEventListener("click", () => {
        exportPdf("expensesReportContent", "Expenses_Report.pdf");
    });
</script>