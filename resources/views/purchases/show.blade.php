@extends('layouts.app')

@section('content')

<div class="invoice-wrapper p-4 my-5 bg-white shadow rounded">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">Purchase Invoice</h2>
        <span class="text-muted">Invoice Date: {{ \Carbon\Carbon::parse($purchase->date)->format('d M, Y') }}</span>
    </div>

    <!-- Supplier Info -->
    <div class="mb-4">
        <p><strong>Supplier:</strong> {{ $purchase->supplier->name }}</p>
        <p><strong>Total Amount:</strong>
            <span class="text-success fw-bold">Rs {{ number_format($purchase->total_amount, 2) }}</span>
        </p>
    </div>

    <!-- Invoice Items Table -->
    <div class="table-responsive">
        <table class="table table-bordered invoice-table align-middle text-center">
            <thead class="table-light text-uppercase">
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price (Rs)</th>
                    <th>Discount (%)</th>
                    <th>Tax (%)</th>
                    <th>Subtotal (Rs)</th>
                </tr>
            </thead>
            <tbody>
                @php $calculatedTotal = 0; @endphp
                @foreach($purchase->items as $index => $item)
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
            <tfoot>
                <tr>
                    <td colspan="6" class="text-end fw-bold fs-5">Grand Total:</td>
                    <td class="fw-bold text-success fs-5">Rs {{ number_format($calculatedTotal, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="mt-4 text-center">
        <a href="{{ route('purchases.index') }}" class="btn btn-primary px-4 py-2">← Back to Purchases</a>
    </div>
</div>

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
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        letter-spacing: 0.05em;
    }

    .invoice-table tbody tr:hover {
        background-color: #f1f3f5;
    }

    .btn-dark {
        font-weight: 600;
        font-size: 1rem;
        border-radius: 6px;
        transition: background-color 0.3s ease;
    }

    .btn-dark:hover {
        background-color: #000;
        color: #fff;
    }

    /* Responsive */
    @media (max-width: 575px) {

        .invoice-table thead th,
        .invoice-table tbody td {
            font-size: 0.85rem;
            padding: 0.5rem 0.4rem;
        }
    }
</style>

@endsection