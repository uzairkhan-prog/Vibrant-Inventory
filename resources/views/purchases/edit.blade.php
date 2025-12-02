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
                <input type="date" name="date" id="date" class="form-control"
                    value="{{ \Carbon\Carbon::parse($purchase->date)->format('Y-m-d') }}" required>
            </div>
        </div>

        <!-- Products Table -->
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center invoice-table mb-4">
                <thead class="table-light">
                    <tr>
                        <th>Category</th>
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
                    $categoryId = $item->product->category_id ?? null;
                    @endphp
                    <tr class="product-row">
                        <!-- Category -->
                        <td>
                            <select name="category_id[]" class="form-select category-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $cat->id == $categoryId ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                                @endforeach
                            </select>
                        </td>

                        <!-- Product -->
                        <td>
                            <select name="product_id[]" class="form-select product-select" required>
                                <option value="">Select Product</option>
                                <!-- Will be loaded by AJAX -->
                            </select>
                            <input type="hidden" class="current-product-id" value="{{ $item->product_id }}">
                        </td>

                        <!-- Quantity -->
                        <td><input type="number" name="quantity[]" class="form-control qty"
                                value="{{ $item->quantity }}" min="1" required></td>

                        <!-- Price -->
                        <td><input type="number" name="price[]" class="form-control price"
                                step="0.01" value="{{ $item->price }}" required></td>

                        <!-- Discount -->
                        <td><input type="number" class="form-control discount" name="discount[]"
                                value="{{ $item->discount ?? 0 }}"></td>

                        <!-- Tax -->
                        <td>
                            <select class="form-select tax" name="tax[]">
                                <option value="0" {{ $item->tax == 0 ? 'selected' : '' }}>0%</option>
                                <option value="18" {{ $item->tax == 18 ? 'selected' : '' }}>18%</option>
                            </select>
                        </td>

                        <!-- Subtotal -->
                        <td><input type="text" class="form-control subtotal"
                                value="Rs {{ number_format($subtotal, 2) }}" readonly></td>

                        <!-- Remove -->
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
        background: #fff;
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

    // Load products for a category (with preselected product if exists)
    function loadProducts(categorySelect, selectedProductId = null) {
        const categoryId = categorySelect.val();
        const productSelect = categorySelect.closest('tr').find('.product-select');
        productSelect.html('<option>Loading...</option>');

        if (categoryId) {
            $.getJSON("{{ url('products/by-category') }}/" + categoryId, function(products) {
                productSelect.empty().append('<option value="">Select Product</option>');
                $.each(products, function(index, product) {
                    const isSelected = selectedProductId == product.id ? 'selected' : '';
                    productSelect.append('<option value="' + product.id + '" ' + isSelected + '>' +
                        product.name + ' (Stock: ' + product.quantity + ')</option>');
                });
            });
        } else {
            productSelect.html('<option value="">Select Product</option>');
        }
    }

    $(document).ready(function() {
        calculateTotals();

        // Init existing rows with correct products
        $('#product-list .product-row').each(function() {
            const categorySelect = $(this).find('.category-select');
            const selectedProductId = $(this).find('.current-product-id').val();
            loadProducts(categorySelect, selectedProductId);
        });

        // Change events
        $(document).on('input change', '.qty, .price, .discount, .tax', calculateTotals);

        // Category change
        $(document).on('change', '.category-select', function() {
            loadProducts($(this));
        });

        // Add new row
        $('#add-product').click(function() {
            const newRow = $('#product-list .product-row:first').clone();
            newRow.find('input').val('');
            newRow.find('.discount').val('0');
            newRow.find('.subtotal').val('');
            newRow.find('.category-select').val('');
            newRow.find('.product-select').html('<option value="">Select Product</option>');
            newRow.find('.current-product-id').val('');
            $('#product-list').append(newRow);
        });

        // Remove row
        $(document).on('click', '.remove-product', function() {
            if ($('#product-list .product-row').length > 1) {
                $(this).closest('.product-row').remove();
                calculateTotals();
            }
        });
    });
</script>

@endsection