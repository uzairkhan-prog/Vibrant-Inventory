@extends('layouts.app')

@section('content')

<div class="table-responsive">
    <div class="table-wrapper">
        <div class="table-title">
            <div class="row">
                <div class="col-md-6">
                    <h2>Stock <b>Management</b></h2>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('products.create') }}" class="btn btn-secondary">
                        <i class="material-icons">&#xE147;</i> <span>Add Product</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="mb-3 d-flex justify-content-end gap-2 pt-3">
            <button onclick="exportTableToCSV('all_products.csv')" class="btn btn-sm btn-outline-primary">Export All CSV</button>
            <button onclick="exportTableToPDF()" class="btn btn-sm btn-outline-danger">Export All PDF</button>
        </div>

        <!-- Subtotal Calculation -->
        <div class="alert alert-success shadow-sm rounded-3 fs-6 fw-bold mt-4">
            <div class="d-flex justify-content-between">
                <span>Total Stock Value (table):</span>
                @php
                $subtotal = 0;
                foreach ($products as $product) {
                $subtotal += $product->price_per_unit * $product->quantity;
                }
                @endphp
                <span>Rs {{ number_format($subtotal, 2) }}</span>
            </div>
        </div>

        <!-- Filter & Table -->
        <div class="row mb-3 align-items-center">
            <div class="col-md-10 d-flex align-items-center">
                <label class="me-2 fw-semibold">Search:</label>
                <input type="text" id="searchInput" class="form-control" placeholder="Search product...">
            </div>
            <div class="col-md-2 d-flex justify-content-end align-items-center">
                <label class="me-2 fw-semibold">Show</label>
                <select id="rowsPerPage" class="form-select w-auto">
                    @foreach ([20, 50, 100] as $value)
                    <option value="{{ $value }}" {{ request('per_page') == $value ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
                <label class="ms-2 fw-semibold">entries</label>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
        @endif

        @if($products->count())
        <table class="table table-striped table-hover" id="productTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Packing</th>
                    <th>Rate</th>
                    <th>Total QTY</th>
                    <th>Value</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                <tr>
                    <td>{{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->packing }}</td>
                    <td>{{ number_format($product->price_per_unit, 2) }}</td>
                    <td>{{ $product->quantity }}</td>
                    <td>{{ number_format($product->price_per_unit * $product->quantity, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($product->date)->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-success text-white">Edit</a>
                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this product?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                        <button onclick="exportRowToCSV(this)" class="btn btn-sm btn-outline-primary mt-1">CSV</button>
                        <button onclick="exportRowToPDF(this)" class="btn btn-sm btn-outline-danger mt-1">PDF</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center mt-3">
            {!! $products->links('pagination::bootstrap-5') !!}
        </div>
        @else
        <div class="alert alert-info text-center">No products found.</div>
        @endif
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    // CSV Export (unchanged)
    function exportTableToCSV(filename) {
        let csv = [];
        const rows = document.querySelectorAll("#productTable tr");
        for (let row of rows) {
            let cols = row.querySelectorAll("td, th");
            let data = Array.from(cols).map(col => `"${col.innerText.trim()}"`);
            csv.push(data.join(","));
        }
        const blob = new Blob([csv.join("\n")], {
            type: "text/csv"
        });
        const link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        link.click();
    }

    function exportRowToCSV(button) {
        const row = button.closest("tr");
        const cols = row.querySelectorAll("td");
        const data = Array.from(cols).slice(0, 7).map(col => `"${col.innerText.trim()}"`);
        const csv = ["#,Name,Packing,Rate,Total QTY,Value,Date", data.join(",")];
        const blob = new Blob([csv.join("\n")], {
            type: "text/csv"
        });
        const link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = "single_product.csv";
        link.click();
    }
</script>

<script>
    function applyPdfTableStyles(table) {
        table.style.borderCollapse = "collapse";
        table.style.width = "100%";
        table.style.fontSize = "13px";
        table.style.fontFamily = "'Inter', 'Helvetica Neue', Helvetica, Arial, sans-serif";
        table.style.color = "#212529";
        table.style.backgroundColor = "#fff";

        const headers = table.querySelectorAll("th");
        headers.forEach(th => {
            th.style.backgroundColor = "#0a2540";
            th.style.color = "#ffffff";
            th.style.border = "1px solid #dee2e6";
            th.style.padding = "10px 12px";
            th.style.textAlign = "center";
            th.style.fontWeight = "700";
            th.style.fontSize = "13px";
            th.style.textTransform = "uppercase";
            th.style.letterSpacing = "0.02em";
        });

        const cells = table.querySelectorAll("td");
        cells.forEach(td => {
            td.style.border = "1px solid #dee2e6";
            td.style.padding = "8px 12px";
            td.style.textAlign = "center";
            td.style.verticalAlign = "middle";
            td.style.fontSize = "13px";
            td.style.color = "#212529";
        });

        const rows = table.querySelectorAll("tbody tr");
        rows.forEach((tr, index) => {
            tr.style.backgroundColor = index % 2 === 0 ? "#f9f9f9" : "#ffffff";
        });

        // Add Grand Total Row (styled)
        const subtotalCells = table.querySelectorAll("tbody td:last-child");
        let grandTotal = 0;
        subtotalCells.forEach(cell => {
            grandTotal += parseFloat(cell.textContent.replace(/,/g, '')) || 0;
        });

        const totalRow = document.createElement("tr");
        totalRow.innerHTML = `
            <td colspan="${table.rows[0].cells.length - 1}" style="text-align: right; font-weight: bold; font-size: 13px; padding: 10px 12px; border: 1px solid #dee2e6;">Grand Total:</td>
            <td style="text-align: center; font-weight: bold; font-size: 13px; padding: 10px 12px; border: 1px solid #dee2e6; color: #00b050;">Rs ${grandTotal.toLocaleString("en-PK", { minimumFractionDigits: 2 })}</td>
        `;
        table.querySelector("tbody").appendChild(totalRow);
    }

    async function exportTableToPDF() {
        const {
            jsPDF
        } = window.jspdf;
        const doc = new jsPDF('p', 'pt', 'a4');
        const pageWidth = doc.internal.pageSize.getWidth();

        const logoUrl = "https://vibrantengineering.pk/logo.png";

        const loadImage = (url) => new Promise((resolve, reject) => {
            const img = new Image();
            img.crossOrigin = "anonymous";
            img.onload = () => resolve(img);
            img.onerror = () => reject(new Error("Logo load failed"));
            img.src = url;
        });

        let logoHeight = 0;
        try {
            const img = await loadImage(logoUrl);
            const props = doc.getImageProperties(img);
            const imgWidth = 70;
            logoHeight = (props.height * imgWidth) / props.width;
            doc.addImage(img, 'PNG', 40, 30, imgWidth, logoHeight);
            doc.setFont("helvetica", "bold");
            doc.setFontSize(16);
            doc.setTextColor("#0a2540");
            doc.text("Vibrant Engineering", 120, 50);
        } catch (e) {}

        doc.setFont("helvetica", "bold");
        doc.setFontSize(22);
        doc.setTextColor("#0a2540");
        doc.text("Purchase Invoice", pageWidth - 180, 50);

        doc.setFontSize(10);
        doc.setTextColor("#000");
        doc.setFont("helvetica", "normal");
        doc.text("Invoice #: 9", pageWidth - 180, 70);
        doc.text("Date: 06 Aug, 2025", pageWidth - 180, 85);
        doc.text("Payment Terms: Due on receipt", pageWidth - 180, 100);

        doc.setFontSize(10);
        doc.text("Head Office: Shop #13, Falak Park View Near", 120, 110);
        doc.text("Inquiry Office Nazimabad #2, Karachi", 120, 125);
        doc.text("Phone: +92 335 2385773", 120, 140);
        doc.text("Email: info@vibrantengineering.pk", 120, 155);

        doc.setDrawColor("#0d6efd");
        doc.setLineWidth(1.2);
        doc.line(40, 170, pageWidth - 40, 170);

        const table = document.getElementById("productTable");
        const clonedTable = table.cloneNode(true);
        clonedTable.querySelectorAll("td:nth-child(8), th:nth-child(8)").forEach(td => td.remove());

        const wrapper = document.createElement("div");
        wrapper.style.padding = "10px";
        wrapper.style.backgroundColor = "#ffffff";
        wrapper.appendChild(clonedTable);
        document.body.appendChild(wrapper);

        applyPdfTableStyles(clonedTable);

        await html2canvas(wrapper, {
            scale: 2
        }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const imgProps = doc.getImageProperties(imgData);
            const margin = 40;
            const pdfWidth = pageWidth - margin * 2;
            const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
            doc.addImage(imgData, 'PNG', margin, 180, pdfWidth, pdfHeight);
        });

        wrapper.remove();
        doc.save("all_products.pdf");
    }
</script>

@endsection