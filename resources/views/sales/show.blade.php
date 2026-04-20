@extends('layouts.app')

@section('content')

<div class="invoice-wrapper p-4 my-5 bg-white shadow rounded" id="invoiceContent" style="font-family: 'Arial', sans-serif;">

    {{-- HEADER --}}
    <div class="pdf-header d-flex justify-content-between align-items-start flex-wrap pb-3">

        <div class="company-info">
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/images/logos/logo-export.png'))) }}"
                alt="Vibrant Engineering Logo" style="max-height: 60px; border-radius: 10px; margin-bottom: 10px; background: linear-gradient(43deg, #11142d 29%, #0B4168 80%);">

            <p class="mt-2 mb-1"><strong>Head Office:</strong> Shop #13, Falak Park View Near <br> Inquiry Office Nazimabad #2, Karachi</p>
            <p class="mb-1"><strong>Phone:</strong> +92 335 2385773</p>
            <p class="mb-0"><strong>Email:</strong> info@vibrantengineering.pk</p>
        </div>

        <div class="text-end mt-md-0 pt-4">
            <h2 class="fw-bold text-primary" style="font-size: 32px;">Sale Invoice</h2>

            <p><strong>Invoice #:</strong> {{ $sale->id }}</p>
            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($sale->date)->format('d M, Y') }}</p>
            <p><strong>Payment Terms:</strong> Due on receipt</p>
             @if($sale->agent)
                <p><strong>Agent:</strong> {{ $sale->agent->name }}</p>
            @endif
        </div>
    </div>

    {{-- CUSTOMER DETAILS --}}
    <div class="pdf-section p-3 border rounded" style="background: #f7f9fc;">
        <p class="mb-1"><strong>Customer:</strong> {{ $sale->customer->name }}</p>
        <p class="mb-1"><strong>Company:</strong> {{ $sale->customer->company_name }}</p>
        <p class="mb-0"><strong>Phone:</strong> {{ $sale->customer->phone }}</p>
    </div>

    {{-- TABLE --}}
    <div class="table-responsive">
        <table class="table table-bordered invoice-table align-middle text-center">
            <thead class="pdf-thead">
                <tr>
                    <th>#</th>
                    <th class="text-start">Product</th>
                    <th>Qty</th>
                    <th>Unit Price (Rs)</th>
                    <th>Discount (%)</th>
                    <th>Tax (%)</th>
                    <th>Subtotal (Rs)</th>
                </tr>
            </thead>
            <tbody>
                @php $calculatedTotal = 0; @endphp
                @foreach($sale->items as $index => $item)
                @php
                    $base = $item->quantity * $item->price;
                    $discountAmount = ($item->discount ?? 0) * $base / 100;
                    $taxable = $base - $discountAmount;
                    $taxAmount = ($item->tax ?? 0) * $taxable / 100;
                    $subtotal = $taxable + $taxAmount;
                    $calculatedTotal += $subtotal;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-start">{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->price, 2) }}</td>
                    <td>{{ number_format($item->discount ?? 0, 2) }}</td>
                    <td>{{ number_format($item->tax ?? 0, 2) }}</td>
                    <td class="fw-bold">{{ number_format($subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>

            @php
                $advance = $sale->customer->payments->sum('amount');
                $balance = $calculatedTotal - $advance;
            @endphp

            <tfoot class="pdf-tfoot">
                <tr>
                    <td colspan="6" class="text-end fw-bold fs-5 pe-3">Advance:</td>
                    <td class="fw-bold text-success fs-5">{{ number_format($advance, 2) }}</td>
                </tr>

                <tr>
                    <td colspan="6" class="text-end fw-bold fs-5 pe-3">Balance:</td>
                    <td class="fw-bold text-danger fs-5">{{ number_format($balance, 2) }}</td>
                </tr>

                <tr>
                    <td colspan="6" class="text-end fw-bold fs-5 pe-3">Total:</td>
                    <td class="fw-bold text-primary fs-5">{{ number_format($calculatedTotal, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- SIGNATURE BLOCK --}}
    <div class="pdf-sign mt-3 pt-4">

        <div class="d-flex justify-content-between flex-wrap">
            <div class="text-start">
                <p class="mb-5" style="border-top: 2px solid #000; width: 220px;"> </p>
                <strong>Customer Signature</strong>
            </div>

            <div class="text-end">
                <img src="{{ public_path('assets/images/signature.png') }}"
                     onerror="this.style.display='none'"
                     style="max-width: 180px; margin-bottom: -10px;">
                <p class="mb-5" style="border-top: 2px solid #000; width: 220px;"> </p>
                <strong>CEO Signature</strong><br>
                <span style="font-size: 13px;">Vibrant Engineering</span>
            </div>
        </div>

    </div>

    {{-- Buttons --}}
    <div class="mt-4 d-flex justify-content-center gap-3 flex-wrap no-print" id="exportButtons">
        <a href="{{ route('sales.index') }}" class="btn btn-primary px-4 py-2">← Back to Sales</a>
        <a href="{{ route('sales.exportCSV', $sale->id) }}" class="btn btn-success px-4 py-2">📁 Export CSV</a>
        <a onclick="exportToPDF()" class="btn btn-danger px-4 py-2">📄 Export PDF</a>
    </div>

</div>

{{-- PDF SCRIPT --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
function exportToPDF() {
    const element = document.getElementById('invoiceContent');

    const opt = {
        margin:       0.4,
        filename:     'sales-invoice-{{ $sale->id }}.pdf',
        image:        { type: 'jpeg', quality: 0.99 },
        html2canvas:  { scale: 2, ignoreElements: el => el.classList.contains('no-print') },
        jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
    };

    html2pdf().set(opt).from(element).save();
}
</script>

{{-- PDF-ONLY STYLING --}}
<style>
    .invoice-wrapper {
        max-width: 1200px;
        margin: auto;
        background: #fff;
    }
    
    .invoice-wrapper strong,
    .invoice-wrapper p {
        color: #000;
    }

    .pdf-header h2 {
        color: #0b3d91 !important;
    }

    .invoice-table th {
        background: #0B4168 !important;
        color: #fff !important;
        font-size: 0.9rem;
        letter-spacing: .5px;
    }

    .invoice-table td {
        background: #ffffff !important;
        font-size: 0.9rem;
    }

    .pdf-tfoot td {
        background: #f1f5f9 !important;
        border-top: 2px solid #ccc !important;
    }

    /* Signature Lines */
    .pdf-sign p {
        margin-top: 40px;
    }

    /* Print Mode */
    @media print {
        .no-print { display: none !important; }
        body { -webkit-print-color-adjust: exact !important; }
    }
</style>

@endsection
