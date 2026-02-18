@extends('layouts.app')

@section('content')

<div class="invoice-wrapper p-4 my-5 bg-white shadow rounded">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">Sales Invoice</h2>
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

    <form method="POST" action="{{ route('sales.store') }}">
        @csrf

        <!-- Customer and Date -->
        <div class="row mb-4">
            <div class="col-md-6">
                <label for="customer_id" class="form-label">Customer</label>
                <select name="customer_id" id="customer_id" class="form-select select2" required>
                    <option value="" disabled {{ old('customer_id') ? '' : '' }}>-- Select Customer --</option>

                    {{-- First, show Counter Sale on top --}}
                    @php
                    $counterCustomer = $customers->firstWhere('name', 'Counter Sale');
                    @endphp
                    @if($counterCustomer)
                    <option value="{{ $counterCustomer->id }}"
                        {{ old('customer_id') 
                    ? (old('customer_id') == $counterCustomer->id ? 'selected' : '') 
                    : 'selected' }}>
                        {{ $counterCustomer->company_name }} ( {{ $counterCustomer->name }} )
                    </option>
                    @endif

                    {{-- Then, show the rest of the customers --}}
                    @foreach($customers as $customer)
                    @if($customer->name !== 'Counter Sale')
                    <option value="{{ $customer->id }}"
                        {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                        {{ $customer->company_name }} ( {{ $customer->name }} )
                    </option>
                    @endif
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="date" class="form-label">Date</label>
                <input type="date" name="date" id="date" class="form-control" required value="{{ old('date', date('Y-m-d')) }}">
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
                    @if(old('product_id'))
                    @foreach(old('product_id') as $index => $oldProduct)
                    <tr class="product-row">
                        <td width="50%">
                            <select name="product_id[]" class="form-select select2 product-select" required>
                                <option value="" disabled {{ $oldProduct ? '' : 'selected' }}>Select an option</option>
                                @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ $oldProduct == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} (Stock: {{ $product->quantity }}) (PP: {{ $product->price_per_unit }})
                                </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" name="quantity[]" class="form-control qty" min="1" value="{{ old('quantity')[$index] ?? 1 }}" required>
                        </td>
                        <td>
                            <input type="number" name="price[]" class="form-control price" step="0.01" value="{{ old('price')[$index] ?? 0 }}" required>
                        </td>
                        <td>
                            <input type="number" name="discount[]" class="form-control discount" min="0" step="0.01" value="{{ old('discount')[$index] ?? 0 }}">
                        </td>
                        <td>
                            <select name="tax[]" class="form-select tax">
                                <option value="0" {{ (old('tax')[$index] ?? 0) == 0 ? 'selected' : '' }}>0%</option>
                                <option value="18" {{ (old('tax')[$index] ?? 0) == 18 ? 'selected' : '' }}>18%</option>
                            </select>
                        </td>
                        <td><input type="text" class="form-control subtotal" readonly value="Rs 0.00"></td>
                        <td><button type="button" class="btn btn-danger remove-product">×</button></td>
                    </tr>
                    @endforeach
                    @else
                    <tr class="product-row">
                        <td width="50%">
                            <select name="product_id[]" class="form-select select2 product-select" required>
                                <option value="" disabled selected>Select an option</option>
                                @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} (Stock: {{ $product->quantity }}) (PP: {{ $product->price_per_unit }})</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" name="quantity[]" class="form-control qty" min="1" value="1" required></td>
                        <td><input type="number" name="price[]" class="form-control price" step="0.01" value="0" required></td>
                        <td><input type="number" name="discount[]" class="form-control discount" min="0" step="0.01" value="0"></td>
                        <td>
                            <select name="tax[]" class="form-select tax">
                                <option value="0">0%</option>
                                <option value="18">18%</option>
                            </select>
                        </td>
                        <td><input type="text" class="form-control subtotal" readonly value="Rs 0.00"></td>
                        <td><button type="button" class="btn btn-danger remove-product">×</button></td>
                    </tr>
                    @endif
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
                        <input type="number" step="0.01" min="0" name="advance"
                            id="advance"
                            class="form-control form-control-sm text-end"
                            style="width:140px"
                            value="{{ old('advance',0) }}">
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
            <button type="submit" class="btn btn-primary px-5">Submit</button>
            <a href="{{ route('sales.index') }}" class="btn btn-dark ms-2">Back</a>
        </div>
    </form>
</div>

<!-- KEEP ALL YOUR EXISTING STYLES AND JS -->
<style>
    /* --- KEEP EXISTING STYLING --- */
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

    li {
        color: #11142d;
    }
</style>

<script>
    let productRowTemplate = null;

    $(document).ready(function() {

        /* STORE CLEAN TEMPLATE BEFORE SELECT2 TOUCHES IT */
        productRowTemplate = $('#product-list .product-row:first').prop('outerHTML');

        initSelect2();
        calculateTotals();
    });

    function initSelect2(context = document) {
        $(context).find('.select2').each(function() {

            if ($(this).hasClass("select2-hidden-accessible")) return;

            $(this).select2({
                width: '100%',
                dropdownParent: $('.invoice-wrapper'),
            });
        });
    }

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

    $(document).on('input change', '#advance', function() {
        calculateTotals();
    });

    $('#add-product').on('click', function() {
        let newRow = $(productRowTemplate);

        // RESET VALUES
        newRow.find('input').val('');
        newRow.find('.qty').val(1);
        newRow.find('.price').val(0);
        newRow.find('.discount').val(0);
        newRow.find('.subtotal').val('Rs 0.00');
        newRow.find('.tax').val(0);
        newRow.find('select').val('');

        $('#product-list').append(newRow);

        initSelect2(newRow);

        calculateTotals();
    });

    $(document).on('click', '.remove-product', function() {
        if ($('#product-list .product-row').length > 1) {
            let row = $(this).closest('.product-row');
            row.find('.select2').each(function() {
                if ($(this).hasClass("select2-hidden-accessible")) {
                    $(this).select2('destroy');
                }
            });
            row.remove();
            calculateTotals();
        }
    });

    $(document).on('input change', '.qty, .price, .discount, .tax', function() {
        calculateTotals();
    });

    $('form').on('submit', function(e) {

        let isValid = true;

        $('#product-list .product-row').each(function() {

            let product = $(this).find('.product-select').val();

            if (!product) {
                isValid = false;

                $(this).find('.select2-selection').css({
                    'border': '2px solid red'
                });
            } else {
                $(this).find('.select2-selection').css({
                    'border': '1px solid #ced4da'
                });
            }

        });

        if (!isValid) {
            e.preventDefault();
            alert('Please select product in all rows');
            return false;
        }

        $('.product-select').removeAttr('required');

    });
</script>

@endsection