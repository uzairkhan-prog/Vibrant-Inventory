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
                    <a href="{{ route('products.create') }}" class="btn btn-secondary" style="float: none;">
                        <i class="material-icons">&#xE147;</i> <span>Add Product</span>
                    </a>
                    <input type="file" id="csvFileInput" accept=".csv" style="display:none;">
                    <button class="btn btn-success export-CSV" id="importCSVBtn">
                        <i class="material-icons">upload_file</i> <span>Import CSV</span>
                    </button>
                    <button class="btn btn-primary export-CSV" onclick="exportTableToCSV('products.csv')">
                        <i class="material-icons">file_download</i> <span>Export CSV</span>
                    </button>
                    <button class="btn btn-danger export-PDF" onclick="exportTableToPDF()">
                        <i class="material-icons">picture_as_pdf</i> <span>Export PDF</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Subtotal Calculation -->
        <div class="alert alert-success shadow-sm rounded-3 fs-6 fw-bold mt-4">
            <div class="d-flex justify-content-between">
                <span>Total Stock Value (table):</span>
                @php
                $subtotal = 0;
                foreach ($products as $product) {
                $subtotal += $product->price_per_unit;
                }
                @endphp
                <span id="totalValue">Rs {{ number_format($subtotal, 2) }}</span>
            </div>
        </div>

        <!-- Filter & Table -->
        <div class="row mb-3 align-items-center">
            <div class="col-md-6 d-flex align-items-center">
                <label class="me-2 fw-semibold">Search:</label>
                <input type="text" id="searchInput" class="form-control" placeholder="Search product...">
            </div>
            <div class="col-md-4">
                <!-- <label class="fw-semibold">Category:</label> -->
                <select id="categoryFilter" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ ($categoryId == $category->id) ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex justify-content-end align-items-center">
                <label class="me-2 fw-semibold">Show</label>
                <select id="rowsPerPage" class="form-select w-auto">
                    @foreach ([20, 50, 100] as $value)
                    <option value="{{ $value }}" {{ request('per_page',20) == $value ? 'selected' : '' }}>{{ $value }}</option>
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
                    <th>Category</th>
                    <th>Packing</th>
                    <th>Value</th>
                    <!-- <th>Total QTY</th> -->
                    <!-- <th>Value</th> -->
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                <tr>
                    <td>{{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category->name ?? 'Uncategorized' }}</td>
                    <td>{{ $product->packing }}</td>
                    <td>{{ number_format($product->price_per_unit, 2) }}</td>
                    <!-- <td>{{ $product->quantity }}</td> -->
                    <!-- <td>{{ number_format($product->price_per_unit * $product->quantity, 2) }}</td> -->
                    <td>
                        <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-info text-white">Show</a>
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-success text-white">Edit</a>
                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this product?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
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

<!-- JS for search, pagination, and category filter -->
<script>
    // Search filter
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchVal = this.value.toLowerCase();
        const rows = document.querySelectorAll('#productTable tbody tr');
        rows.forEach(row => {
            const rowText = row.innerText.toLowerCase();
            row.style.display = rowText.includes(searchVal) ? '' : 'none';
        });
    });

    // Rows per page
    document.getElementById('rowsPerPage').addEventListener('change', function() {
        const selected = this.value;
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', selected);
        window.location.href = url.toString();
    });

    // Category filter
    document.getElementById('categoryFilter').addEventListener('change', function() {
        const categoryId = this.value;
        const url = new URL(window.location.href);
        if (categoryId) {
            url.searchParams.set('category_id', categoryId);
        } else {
            url.searchParams.delete('category_id');
        }
        window.location.href = url.toString();
    });
</script>

<!-- CSV & PDF export scripts -->
<script>
    function downloadCSV(csv, filename) {
        let csvFile = new Blob([csv], {
            type: "text/csv;charset=utf-8;"
        });
        let downloadLink = document.createElement("a");
        downloadLink.download = filename;
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = "none";
        document.body.appendChild(downloadLink);
        downloadLink.click();
    }

    function exportTableToCSV(filename) {
        let csv = [];
        let rows = document.querySelectorAll("#productTable tr");
        for (let i = 0; i < rows.length; i++) {
            let row = [],
                cols = rows[i].querySelectorAll("td, th");
            for (let j = 0; j < cols.length - 1; j++) { // skip last col "Actions"
                let cellText = cols[j].innerText.replace(/,/g, '');
                row.push('"' + cellText.replace(/"/g, '""') + '"');
            }
            csv.push(row.join(","));
        }
        let totalValue = document.getElementById("totalValue").innerText.replace(/[^\d.]/g, '');
        csv.push(`"","","","","Total Value",${totalValue}`);
        downloadCSV(csv.join("\n"), filename);
    }

    document.getElementById('importCSVBtn').addEventListener('click', () => {
        document.getElementById('csvFileInput').click();
    });

    document.getElementById('csvFileInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const formData = new FormData();
        formData.append('csv_file', file);
        fetch("{{ route('products.import') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        }).then(async res => {
            const text = await res.text();
            try {
                const data = JSON.parse(text);
                alert(data.message);
                location.reload();
            } catch (e) {
                console.error("Not JSON:", text);
            }
        }).catch(err => console.error(err));
    });
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>
<script>
    async function exportTableToPDF() {
        const {
            jsPDF
        } = window.jspdf;
        const doc = new jsPDF("p", "mm", "a4");
        const logoUrl = "{{ asset('assets/images/logos/logo.jpg') }}";
        const img = new Image();
        img.src = logoUrl;
        img.onload = function() {
            doc.addImage(img, "JPG", 14, 8, 70, 20);
            doc.setFontSize(16);
            doc.setTextColor(17, 20, 45);
            doc.setFont("helvetica", "bold");
            doc.text("Stock Management Report", doc.internal.pageSize.getWidth() - 14, 20, {
                align: "right"
            });
            let head = [
                ['#', 'Name', 'Packing', 'Rate', 'Value']
            ];
            let body = [];
            let rows = document.querySelectorAll("#productTable tbody tr");
            rows.forEach(r => {
                let cols = r.querySelectorAll("td");
                body.push([cols[0].innerText, cols[1].innerText, cols[2].innerText, cols[3].innerText, cols[4].innerText, cols[5].innerText]);
            });
            doc.autoTable({
                head,
                body,
                startY: 35,
                theme: 'grid',
                styles: {
                    fontSize: 8,
                    halign: 'center',
                    valign: 'middle',
                    cellPadding: 3,
                    lineColor: [220, 220, 220]
                },
                headStyles: {
                    fillColor: [17, 20, 45],
                    textColor: [255, 255, 255],
                    fontStyle: 'bold'
                },
                alternateRowStyles: {
                    fillColor: [245, 245, 245]
                },
                columnStyles: {
                    1: {
                        halign: 'left',
                        cellWidth: 60
                    }
                },
                didDrawPage: function(data) {
                    let pageHeight = doc.internal.pageSize.height;
                    doc.setFontSize(8);
                    doc.setTextColor(100);
                    let str = "Page " + doc.internal.getNumberOfPages();
                    doc.text("Generated on: " + new Date().toLocaleString(), 14, pageHeight - 10);
                    doc.text(str, 200, pageHeight - 10, {
                        align: "right"
                    });
                }
            });
            let totalValue = document.getElementById("totalValue").innerText;
            let finalY = doc.lastAutoTable.finalY + 10;
            doc.setDrawColor(17, 20, 45);
            doc.setLineWidth(0.5);
            doc.line(14, finalY - 5, 195, finalY - 5);
            doc.setFontSize(10);
            doc.setTextColor(17, 20, 45);
            doc.setFont("helvetica", "bold");
            doc.text(`Total Stock Value: ${totalValue}`, 14, finalY);
            doc.save('products.pdf');
        };
    }
</script>
@endsection