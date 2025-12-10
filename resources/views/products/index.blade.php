@extends('layouts.app')

@section('content')

<div class="p-4 bg-white shadow rounded">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary mb-0">Stock Management</h2>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('products.create') }}" class="btn btn-secondary">
                <i class="material-icons me-1">&#xE147;</i> Add Product
            </a>
            <input type="file" id="csvFileInput" accept=".csv" style="display:none;">
            <button class="btn btn-success" id="importCSVBtn">
                <i class="material-icons">upload_file</i> Import CSV
            </button>
            <button class="btn btn-primary" onclick="exportTableToCSV('products.csv')">
                <i class="material-icons">file_download</i> Export CSV
            </button>
            <button class="btn btn-danger" onclick="exportTableToPDF()">
                <i class="material-icons">picture_as_pdf</i> Export PDF
            </button>
        </div>
    </div>

    <!-- Total Stock Value -->
    @php
    $subtotal = $products->sum('price_per_unit');
    @endphp
    <div class="alert alert-success shadow-sm rounded-3 fs-6 fw-bold">
        <div class="d-flex justify-content-between">
            <span>Total Stock Value:</span>
            <span id="totalValue">Rs {{ number_format($subtotal, 2) }}</span>
        </div>
    </div>

    <!-- Filters Row -->
    <div class="row mb-3 align-items-center">
        <div class="col-md-6 d-flex align-items-center">
            <label class="me-2 fw-semibold">Search:</label>
            <input type="text" id="searchInput" class="form-control w-100" placeholder="Search product...">
        </div>
        <div class="col-md-4">
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
                <option value="{{ $value }}" {{ request('per_page',20) == $value ? 'selected' : '' }}>
                    {{ $value }}
                </option>
                @endforeach
            </select>
            <span class="ms-2 fw-semibold">entries</span>
        </div>
    </div>

    <!-- Flash Success -->
    @if(session('success'))
    <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    <!-- Table -->
    @if($products->count())
    <div class="table-responsive">
        <div class="table-wrapper">
            <table class="table table-striped table-hover table-bordered align-middle text-center" id="productTable">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Packing</th>
                        <th>Value</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $index => $product)
                    <tr>
                        <td>{{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category->name ?? 'Uncategorized' }}</td>
                        <td>{{ $product->packing }}</td>
                        <td>{{ number_format($product->price_per_unit, 2) }}</td>
                        <td class="d-flex justify-content-center gap-1">
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

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {!! $products->appends(request()->all())->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-info text-center">No products found.</div>
    @endif

</div>

<!-- JS -->
<script>
    // Search filter
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const val = this.value.toLowerCase();
        document.querySelectorAll('#productTable tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(val) ? '' : 'none';
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
        if (categoryId) url.searchParams.set('category_id', categoryId);
        else url.searchParams.delete('category_id');
        window.location.href = url.toString();
    });

    // CSV Export
    function downloadCSV(csv, filename) {
        const csvFile = new Blob([csv], {
            type: "text/csv;charset=utf-8;"
        });
        const link = document.createElement("a");
        link.download = filename;
        link.href = window.URL.createObjectURL(csvFile);
        link.style.display = "none";
        document.body.appendChild(link);
        link.click();
    }

    function exportTableToCSV(filename) {
        const csv = [];
        const rows = document.querySelectorAll("#productTable tr");
        rows.forEach(row => {
            const cols = row.querySelectorAll("td, th");
            const rowData = Array.from(cols).slice(0, -1).map(c => '"' + c.innerText.replace(/"/g, '""') + '"');
            csv.push(rowData.join(","));
        });
        const totalValue = document.getElementById("totalValue").innerText.replace(/[^\d.]/g, '');
        csv.push(`"","","","","Total Value",${totalValue}`);
        downloadCSV(csv.join("\n"), filename);
    }

    // Import CSV
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
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                location.reload();
            })
            .catch(err => console.error(err));
    });

    // PDF Export
    async function exportTableToPDF() {
        const {
            jsPDF
        } = window.jspdf;
        const doc = new jsPDF("p", "mm", "a4");
        doc.text("Stock Management Report", 14, 20);
        const head = [
            ['#', 'Name', 'Category', 'Packing', 'Value']
        ];
        const body = [];
        document.querySelectorAll("#productTable tbody tr").forEach(r => {
            const cols = r.querySelectorAll("td");
            body.push([cols[0].innerText, cols[1].innerText, cols[2].innerText, cols[3].innerText, cols[4].innerText]);
        });
        doc.autoTable({
            head,
            body,
            startY: 30,
            theme: 'grid',
            styles: {
                fontSize: 8
            }
        });
        const totalValue = document.getElementById("totalValue").innerText;
        doc.text(`Total Stock Value: ${totalValue}`, 14, doc.lastAutoTable.finalY + 10);
        doc.save('products.pdf');
    }
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>

@endsection