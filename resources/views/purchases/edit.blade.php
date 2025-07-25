@extends('layouts.app')

@section('content')

<div class="invoice-wrapper p-4 my-5 bg-white shadow rounded">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">Edit Purchase Invoice</h2>
        <span class="text-muted">{{ \Carbon\Carbon::now()->format('d M, Y') }}</span>
    </div>

    <form method="POST" action="{{ route('purchases.update', $purchase->id) }}">
        @csrf
        @method('PUT')

        <!-- Supplier and Date -->
        <div class="row mb-4">
            <div class="col-md-6">
                <label for="supplier_id" class="form-label">Supplier</label>
                <select name="supplier_id" id="supplier_id" class="form-select" required>
                    @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" {{ $supplier->id == $purchase->supplier_id ? 'selected' : '' }}>
                        {{ $supplier->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="date" class="form-label">Date</label>
                <input type="date" name="date" id="date" class="form-control" value="{{ \Carbon\Carbon::parse($purchase->date)->format('Y-m-d') }}" required>
            </div>
        </div>

        <!-- Product Table -->
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center invoice-table mb-4">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Discount %</th>
                        <th>Tax %</th>
                        <th>Subtotal</th>
                        <th>Remove</th>
                    </tr>
                </thead>
                <tbody id="product-list">
                    @foreach($purchase->items as $item)
                    @php
                    $base = $item->quantity * $item->price;
                    $discountAmount = ($item->discount ?? 0) * $base / 100;
                    $taxable = $base - $discountAmount;
                    $taxAmount = ($item->tax ?? 0) * $taxable / 100;
                    $subtotal = $taxable + $taxAmount;
                    @endphp
                    <tr class="product-row">
                        <td>
                            <select name="product_id[]" class="form-select" required>
                                <option value="">Select a Product</option>
                                @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} (Stock: {{ $product->quantity }})
                                </option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" name="quantity[]" class="form-control qty" value="{{ $item->quantity }}" min="1" required></td>
                        <td><input type="number" name="price[]" class="form-control price" step="0.01" value="{{ $item->price }}" required></td>
                        <td><input type="number" class="form-control discount" name="discount[]" value="{{ $item->discount ?? 0 }}"></td>
                        <td>
                            <select class="form-select tax" name="tax[]">
                                <option value="0" {{ $item->tax == 0 ? 'selected' : '' }}>0%</option>
                                <option value="18" {{ $item->tax == 18 ? 'selected' : '' }}>18%</option>
                            </select>
                        </td>
                        <td><input type="text" class="form-control subtotal" value="Rs {{ number_format($subtotal, 2) }}" readonly></td>
                        <td><button type="button" class="btn btn-danger remove-product">×</button></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mb-3">
            <button type="button" class="btn btn-secondary" id="add-product">+ Add Product</button>
        </div>

        <!-- Total -->
        <div class="row justify-content-end">
            <div class="col-md-4">
                <div class="p-3 border rounded bg-light text-end">
                    <strong class="d-block mb-2 fs-5">Total Amount:</strong>
                    <span id="grand-total" class="fs-4 text-success">Rs 0.00</span>
                </div>
            </div>
        </div>

        <div class="mt-4 text-center">
            <button type="submit" class="btn btn-primary px-5">Update</button>
            <a href="{{ route('purchases.index') }}" class="btn btn-dark ms-2">Back</a>
        </div>

    </form>
</div>

<!-- Styles -->
<style>
    .invoice-wrapper {
        max-width: 1200px;
        margin: auto;
        background: #ffffff;
    }

    .invoice-table th,
    .invoice-table td {
        vertical-align: middle;
        font-size: 0.9rem;
    }

    .btn-primary {
        background-color: #2364d2;
        font-weight: 600;
    }

    .btn-dark {
        font-weight: 600;
    }

    .btn-danger {
        font-size: 0.9rem;
        padding: 4px 10px;
    }

    .form-control,
    .form-select {
        font-size: 0.85rem;
    }
</style>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function calculateTotals() {
        let grandTotal = 0;

        $('#product-list .product-row').each(function() {
            const qty = parseFloat($(this).find('.qty').val()) || 0;
            const price = parseFloat($(this).find('.price').val()) || 0;
            const discount = parseFloat($(this).find('.discount').val()) || 0;
            const tax = parseFloat($(this).find('.tax').val()) || 0;

            const base = qty * price;
            const discountAmount = (discount / 100) * base;
            const taxable = base - discountAmount;
            const taxAmount = (tax / 100) * taxable;
            const subtotal = taxable + taxAmount;

            $(this).find('.subtotal').val('Rs ' + subtotal.toFixed(2));
            grandTotal += subtotal;
        });

        $('#grand-total').text('Rs ' + grandTotal.toFixed(2));
    }

    $(document).ready(function() {
        calculateTotals();

        $(document).on('input change', '.qty, .price, .discount, .tax', function() {
            calculateTotals();
        });

        $('#add-product').click(function() {
            const firstRow = $('#product-list .product-row:first');
            const newRow = firstRow.clone();

            newRow.find('input').val('');
            newRow.find('.subtotal').val('Rs 0.00');
            newRow.find('select').each(function() {
                $(this).val($(this).find('option:first').val());
            });

            $('#product-list').append(newRow);
            calculateTotals();
        });

        $(document).on('click', '.remove-product', function() {
            if ($('#product-list .product-row').length > 1) {
                $(this).closest('.product-row').remove();
                calculateTotals();
            }
        });
    });
</script>

@endsection