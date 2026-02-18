@extends('layouts.app')

@section('content')

<div class="invoice-wrapper p-4 my-5 bg-white shadow rounded">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">Edit Sale Invoice</h2>
        <span class="text-muted">{{ \Carbon\Carbon::now()->format('d M, Y') }}</span>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('sales.update', $sale->id) }}">
        @csrf
        @method('PUT')

        <!-- Customer and Date -->
        <div class="row mb-4">
            <div class="col-md-6">
                <label for="customer_id" class="form-label">Customer</label>
                <select name="customer_id" id="customer_id" class="form-select select2" required>
                    @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" {{ old('customer_id', $sale->customer_id) == $customer->id ? 'selected' : '' }}>
                        {{ $customer->company_name }} ( {{ $customer->name }} )
                    </option>
                    @endforeach
                </select>
                @error('customer_id')
                <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="date" class="form-label">Date</label>
                <input type="date" name="date" id="date" class="form-control"
                    value="{{ old('date', \Carbon\Carbon::parse($sale->date)->format('Y-m-d')) }}" required>
                @error('date')
                <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Products Table -->
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
                    @php $oldInputs = old('product_id') ? count(old('product_id')) : $sale->items->count(); @endphp
                    @for ($i = 0; $i < $oldInputs; $i++)
                        @php
                        $item=$sale->items[$i] ?? null;
                        $productId = old('product_id.' . $i, $item->product_id ?? '');
                        $quantity = old('quantity.' . $i, $item->quantity ?? '');
                        $price = old('price.' . $i, $item->price ?? '');
                        $discount = old('discount.' . $i, $item->discount ?? 0);
                        $tax = old('tax.' . $i, $item->tax ?? 0);
                        $base = $quantity * $price;
                        $discountAmt = ($discount / 100) * $base;
                        $taxable = $base - $discountAmt;
                        $taxAmt = ($tax / 100) * $taxable;
                        $subtotal = $taxable + $taxAmt;
                        @endphp
                        <tr class="product-row">
                            <td width="50%">
                                <select name="product_id[]" class="form-select select2" required>
                                    <option value="">Select an option</option>
                                    @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ $productId == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} (Stock: {{ $product->quantity }}) (PP: {{ $product->price_per_unit }})
                                    </option>
                                    @endforeach
                                </select>
                                @error("product_id.$i")
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </td>
                            <td>
                                <input type="number" name="quantity[]" class="form-control qty" value="{{ $quantity }}" min="1" required>
                                @error("quantity.$i")
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </td>
                            <td>
                                <input type="number" name="price[]" class="form-control price" step="0.01" value="{{ $price }}" required>
                                @error("price.$i")
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </td>
                            <td>
                                <input type="number" name="discount[]" class="form-control discount" value="{{ $discount }}">
                                @error("discount.$i")
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </td>
                            <td>
                                <select name="tax[]" class="form-select tax">
                                    <option value="0" {{ $tax == 0 ? 'selected' : '' }}>0%</option>
                                    <option value="18" {{ $tax == 18 ? 'selected' : '' }}>18%</option>
                                </select>
                                @error("tax.$i")
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </td>
                            <td><input type="text" class="form-control subtotal" value="Rs {{ number_format($subtotal, 2) }}" readonly></td>
                            <td><button type="button" class="btn btn-danger remove-product">×</button></td>
                        </tr>
                        @endfor
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
                            class="form-control form-control-sm text-end" style="width:140px" value="{{ old('advance', $advancePayment ?? 0) }}">
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
            <a href="{{ route('sales.index') }}" class="btn btn-dark ms-2">Back</a>
        </div>
    </form>
</div>

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

    li {
        color: #11142d;
    }
</style>

<script>
    // ---------- SELECT2 INIT ----------
    function initSelect2(context = document) {
        $(context).find('.select2').each(function() {
            if ($(this).hasClass("select2-hidden-accessible")) {
                $(this).select2('destroy');
            }

            $(this).select2({
                width: '100%',
                dropdownParent: $('.invoice-wrapper')
            });
        });
    }

    // ---------- CALCULATE TOTAL ----------
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

        // Live update
        $(document).on('input change', '.qty, .price, .discount, .tax, #advance', function() {
            calculateTotals();
        });
    });

    // ---------- DOCUMENT READY ----------
    $(document).ready(function() {

        // first initialize select2 for edit items
        initSelect2();

        // initial totals
        calculateTotals();

        // live calculation
        $(document).on('input change', '.qty, .price, .discount, .tax', function() {
            calculateTotals();
        });

        // ---------- ADD PRODUCT ----------
        $('#add-product').click(function() {

            let firstRow = $('#product-list .product-row:first');

            // STEP 1: clone row
            let newRow = firstRow.clone(false, false);

            // STEP 2: remove select2 generated DOM
            newRow.find('.select2-container').remove();

            // STEP 3: reset select fields completely
            newRow.find('select').each(function() {
                $(this)
                    .removeAttr('data-select2-id')
                    .removeClass('select2-hidden-accessible')
                    .off('select2:select');

                this.selectedIndex = 0;
            });

            // STEP 4: clear inputs
            newRow.find('input').val('');
            newRow.find('.subtotal').val('Rs 0.00');

            // STEP 5: append
            $('#product-list').append(newRow);

            // STEP 6: reinitialize select2 ONLY on new row
            initSelect2(newRow);

            calculateTotals();
        });

        // ---------- REMOVE PRODUCT ----------
        $(document).on('click', '.remove-product', function() {

            if ($('#product-list .product-row').length > 1) {

                let row = $(this).closest('.product-row');

                // destroy select2 before remove (IMPORTANT)
                row.find('.select2').each(function() {
                    if ($(this).hasClass("select2-hidden-accessible")) {
                        $(this).select2('destroy');
                    }
                });

                row.remove();
                calculateTotals();
            }
        });

    });
</script>

@endsection