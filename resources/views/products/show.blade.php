@extends('layouts.app')

@section('content')

<div class="invoice-wrapper p-4 my-5 bg-white shadow rounded" id="invoiceContent">

    <div class="d-flex justify-content-between align-items-start flex-wrap mb-4 border-bottom pb-3">
        <div>
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/images/logos/logo-export.png'))) }}"
                alt="Vibrant Engineering Logo"
                style="max-height: 60px; border-radius: 10px; margin-bottom: 10px; background: linear-gradient(43deg, #11142d 29%, #0B4168 80%);">
            <p class="mb-1">Head Office: Shop #13, Falak Park View Near <br> Inquiry Office Nazimabad #2, Karachi</p>
            <p class="mb-1">Phone: +92 335 2385773</p>
            <p class="mb-0">Email: info@vibrantengineering.pk</p>
        </div>
        <div class="text-end mt-3 mt-md-0">
            <h2 class="fw-bold text-primary">Product Details</h2>
            <p><strong>Product ID:</strong> {{ $product->id }}</p>
            <p><strong>Name:</strong> {{ $product->name }}</p>
            <p><strong>Category:</strong> {{ $product->category ? $product->category->name : 'Uncategorized' }}</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered invoice-table align-middle text-center">
            <thead class="table-light text-uppercase">
                <tr>
                    <th>#</th>
                    <th class="text-start">Product</th>
                    <th>Packing</th>
                    <th>Qty</th>
                    <th>Unit Price (Rs)</th>
                    <th>Discount (%)</th>
                    <th>Tax (%)</th>
                    <th>Subtotal (Rs)</th>
                </tr>
            </thead>
            <tbody>
                @php
                // Assuming discount and tax for the product are 0 if not available
                $discount = $product->discount ?? 0;
                $tax = $product->tax ?? 0;
                $base = $product->quantity * $product->price_per_unit;
                $discountAmount = $discount * $base / 100;
                $taxable = $base - $discountAmount;
                $taxAmount = $tax * $taxable / 100;
                $subtotal = $taxable + $taxAmount;
                @endphp
                <tr>
                    <td>1</td>
                    <td class="text-start">{{ $product->name }}</td>
                    <td>{{ $product->packing }}</td>
                    <td>{{ $product->quantity }}</td>
                    <td>{{ number_format($product->price_per_unit, 2) }}</td>
                    <td>{{ number_format($discount, 2) }}</td>
                    <td>{{ number_format($tax, 2) }}</td>
                    <td class="fw-bold">{{ number_format($subtotal, 2) }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7" class="text-end fw-bold fs-5">Grand Total:</td>
                    <td class="fw-bold text-success fs-5">Rs {{ number_format($subtotal, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Buttons -->
    <div class="mt-4 text-center d-flex justify-content-center gap-3 flex-wrap no-print" id="exportButtons">
        <a href="{{ route('products.index') }}" class="btn btn-primary px-4 py-2">← Back to Products</a>
        <button onclick="exportToPDF()" class="btn btn-danger px-4 py-2">📄 Export PDF</button>
    </div>

</div>

{{-- PDF Export Script --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function exportToPDF() {
        const element = document.getElementById('invoiceContent');

        const opt = {
            margin: 0.3,
            filename: 'product-details-{{ $product->id }}.pdf',
            image: {
                type: 'jpeg',
                quality: 0.98
            },
            html2canvas: {
                scale: 2,
                ignoreElements: (el) => el.classList.contains('no-print')
            },
            jsPDF: {
                unit: 'in',
                format: 'a4',
                orientation: 'portrait'
            }
        };

        html2pdf().set(opt).from(element).save();
    }
</script>

<style>
    .invoice-wrapper {
        max-width: 1200px;
        margin: auto;
        background: #fff;
    }

    .invoice-table th,
    .invoice-table td {
        vertical-align: middle;
        font-size: 0.95rem;
        padding: 0.75rem;
    }

    .invoice-table thead th {
        background-color: #f1f1f1;
        border-bottom: 2px solid #ccc;
        font-weight: 600;
        letter-spacing: 0.03em;
    }

    .invoice-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .btn-primary,
    .btn-success,
    .btn-danger {
        font-weight: 600;
        font-size: 1rem;
        border-radius: 6px;
        transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        color: #fff;
    }

    .btn-success:hover {
        background-color: #218838;
        color: #fff;
    }

    .btn-danger:hover {
        background-color: #c82333;
        color: #fff;
    }

    @media (max-width: 575px) {

        .invoice-table thead th,
        .invoice-table tbody td {
            font-size: 0.85rem;
            padding: 0.5rem 0.4rem;
        }
    }

    @media print {
        .no-print {
            display: none !important;
        }

        body {
            -webkit-print-color-adjust: exact !important;
        }

        .invoice-wrapper {
            box-shadow: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }
    }
</style>

@endsection