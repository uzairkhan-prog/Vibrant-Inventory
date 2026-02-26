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
                <select name="supplier_id" id="supplier_id" class="form-select select2" required>
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
                            <select name="category_id[]" class="form-select category-select select2" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $cat->id == $categoryId ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                                @endforeach
                            </select>
                        </td>

                        <!-- Product -->
                        <td width="30%">
                            <select name="product_id[]" class="form-select product-select select2" required>
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

        <!-- Total Summary -->
        <div class="row justify-content-end">
            <div class="col-md-4">
                <div class="p-3 border rounded bg-light">
                    <div class="d-flex justify-content-between mb-2">
                        <strong>Sub Total:</strong>
                        <span id="sub-total">Rs 0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 align-items-center">
                        <strong>Advance:</strong>
                        <input type="number" step="0.01" min="0" name="advance" id="advance"
                            class="form-control form-control-sm text-end" style="width:140px"
                            value="{{ old('advance', $advancePayment ?? 0) }}">
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <strong>Balance:</strong>
                        <span id="balance" class="text-danger">Rs 0.00</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong class="fs-5">Total Amount:</strong>
                        <span id="grand-total" class="fs-4 text-success">Rs 0.00</span>
                    </div>
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
    let productRowTemplate = null;

    /* =========================================
        INIT
    ========================================= */
    $(document).ready(function() {

        // store CLEAN HTML BEFORE select2 touches it
        productRowTemplate = $('#product-list .product-row:first').prop('outerHTML');

        initSelect2();
        calculateTotals();

        // load products for existing items
        loadExistingRows();
    });

    /* =========================================
        SELECT2
    ========================================= */
    function initSelect2(context = document) {
        $(context).find('.select2').each(function() {

            if ($(this).hasClass('select2-hidden-accessible')) return;

            $(this).select2({
                width: '100%',
                dropdownParent: $('.invoice-wrapper'),
            });
        });
    }

    // ---------- CALCULATE TOTALS ----------
    function calculateTotals() {
        let subtotal = 0;

        $('#product-list .product-row').each(function() {
            const qty = parseFloat($(this).find('.qty').val()) || 0;
            const price = parseFloat($(this).find('.price').val()) || 0;
            const discount = parseFloat($(this).find('.discount').val()) || 0;
            const tax = parseFloat($(this).find('.tax').val()) || 0;

            let base = qty * price;
            let discountAmount = (discount / 100) * base;
            let taxable = base - discountAmount;
            let taxAmount = (tax / 100) * taxable;
            let finalAmount = taxable + taxAmount;

            $(this).find('.subtotal').val('Rs ' + finalAmount.toFixed(2));

            subtotal += finalAmount;
        });

        let advance = parseFloat($('#advance').val()) || 0;
        let balance = subtotal - advance;
        if (balance < 0) balance = 0;

        $('#sub-total').text('Rs ' + subtotal.toFixed(2));
        $('#grand-total').text('Rs ' + subtotal.toFixed(2));
        $('#balance').text('Rs ' + balance.toFixed(2));
    }

    $(document).ready(function() {
        // Initial calculation
        calculateTotals();

        // Live update on any relevant input change
        $(document).on('input change', '.qty, .price, .discount, .tax, #advance', function() {
            calculateTotals();
        });
    });

    /* =========================================
        LOAD PRODUCTS (AJAX)
    ========================================= */
    function loadProducts(categorySelect, selectedProductId = null) {
        const row = categorySelect.closest('tr');
        const productSelect = row.find('.product-select');

        productSelect.html('<option>Loading...</option>');

        $.ajax({
            url: "{{ url('products/by-category') }}/" + categorySelect.val(),
            type: "GET",
            success: function(products) {

                productSelect.empty().append('<option value="">Select Product</option>');

                $.each(products, function(index, product) {

                    let selected = (selectedProductId == product.id) ? 'selected' : '';

                    productSelect.append(
                        `<option value="${product.id}" ${selected}>
                        ${product.name} (Stock: ${product.quantity})
                    </option>`
                    );
                });

                // IMPORTANT: refresh select2
                productSelect.trigger('change.select2');
            }
        });
    }

    /* =========================================
        LOAD EXISTING ROWS (EDIT MODE FIX)
    ========================================= */
    function loadExistingRows() {
        $('#product-list .product-row').each(function() {
            let row = $(this);
            let categorySelect = row.find('.category-select');
            let productSelect = row.find('.product-select');
            let selectedProductId = row.find('.current-product-id').val();

            if (categorySelect.val()) {
                // Load products via AJAX
                $.ajax({
                    url: "{{ url('products/by-category') }}/" + categorySelect.val(),
                    type: "GET",
                    success: function(products) {
                        productSelect.empty().append('<option value="">Select Product</option>');
                        $.each(products, function(index, product) {
                            let selected = (selectedProductId == product.id) ? 'selected' : '';
                            productSelect.append(`<option value="${product.id}" ${selected}>${product.name} (Stock: ${product.quantity})</option>`);
                        });
                        // Refresh select2
                        productSelect.trigger('change.select2');
                    }
                });
            }
        });
    }

    /* =========================================
        ADD ROW  (FIXED)
    ========================================= */
    $('#add-product').on('click', function() {

        let newRow = $(productRowTemplate);

        // reset
        newRow.find('input').val('');
        newRow.find('.qty').val(1);
        newRow.find('.price').val(0);
        newRow.find('.discount').val(0);
        newRow.find('.subtotal').val('Rs 0.00');

        newRow.find('.category-select').val('');
        newRow.find('.product-select').html('<option value="">Select Product</option>');
        newRow.find('.current-product-id').val('');

        $('#product-list').append(newRow);

        initSelect2(newRow);
    });

    /* =========================================
        REMOVE ROW
    ========================================= */
    $(document).on('click', '.remove-product', function() {

        if ($('#product-list .product-row').length > 1) {

            let row = $(this).closest('.product-row');

            row.find('.select2').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('destroy');
                }
            });

            row.remove();
            calculateTotals();
        }
    });

    /* =========================================
        EVENTS
    ========================================= */
    $(document).on('input change', '.qty, .price, .discount, .tax', calculateTotals);

    $(document).on('change', '.category-select', function() {
        loadProducts($(this));
    });
</script>
@endsection