@extends('layouts.app')

@section('content')

<div class="table-responsive">
    <div class="table-wrapper">
        <div class="table-title">
            <div class="row">
                <div class="col-sm-5">
                    <h2>Purchase <b>Create</b></h2>
                </div>
            </div>
        </div>

        <div class="p-3">
            <form method="POST" action="{{ route('purchases.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="supplier_id" class="form-label">Supplier</label>
                    <select name="supplier_id" id="supplier_id" class="form-select" required>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" name="date" id="date" class="form-control" required>
                </div>

                <h5 class="mt-4">Products</h5>

                <div id="product-list">
                    <div class="row mb-2 align-items-end product-row">
                        <div class="col-md-3">
                            <label>Product</label>
                            <select name="product_id[]" class="form-select" required>
                                @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} (Stock: {{ $product->quantity }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-1">
                            <label>Qty</label>
                            <input type="number" name="quantity[]" class="form-control qty" min="1" required>
                        </div>

                        <div class="col-md-2">
                            <label>Price</label>
                            <input type="number" name="price[]" class="form-control price" step="0.01" required>
                        </div>

                        <div class="col-md-2">
                            <label>Discount %</label>
                            <input type="number" class="form-control discount" value="0">
                        </div>

                        <div class="col-md-2">
                            <label>Tax %</label>
                            <select class="form-select tax">
                                <option value="0">0%</option>
                                <option value="18">18%</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label>Subtotal</label>
                            <input type="text" class="form-control subtotal" readonly>
                        </div>

                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-danger remove-product">X</button>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-secondary my-3" id="add-product">+ Add Product</button>

                <!-- Total Calculation -->
                <div class="alert alert-info fw-bold fs-6">
                    <div class="d-flex justify-content-between">
                        <span>Total Purchase Amount:</span>
                        <span id="grand-total">Rs 0.00</span>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="{{ route('purchases.index') }}" class="btn btn-dark">Back</a>
                </div>
            </form>
        </div>

    </div>
</div>

<style>
    .table-wrapper {
        background: #fff;
        padding: 30px 35px;
        border-radius: 15px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        margin-top: 30px;
    }

    .table-title {
        background: linear-gradient(to right, #2364d2, #5fa8f5);
        padding: 15px 25px;
        border-radius: 10px;
        color: #fff;
        margin-bottom: 25px;
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.1);
    }

    .table-title h2 {
        font-weight: 600;
        font-size: 22px;
        margin: 0;
    }

    form label {
        font-size: 0.85rem;
        font-weight: 500;
        color: #333;
    }

    .product-row {
        background: #f8f9fb;
        padding: 15px 10px;
        border-radius: 10px;
        margin-bottom: 15px;
        box-shadow: 0 1px 6px rgba(0, 0, 0, 0.05);
    }

    .product-row .form-control,
    .product-row .form-select {
        border-radius: 6px;
        font-size: 0.875rem;
    }

    .product-row .btn-danger {
        padding: 4px 10px;
        font-size: 0.85rem;
        font-weight: bold;
        border-radius: 6px;
        box-shadow: none;
    }

    #add-product {
        font-weight: 500;
        padding: 8px 20px;
        font-size: 0.9rem;
        border-radius: 6px;
    }

    .alert-info {
        background-color: #e7f3ff;
        color: #084298;
        border-left: 4px solid #0d6efd;
        font-size: 1rem;
        padding: 15px 20px;
        border-radius: 8px;
    }

    .btn-primary {
        background: #2364d2;
        border: none;
        font-weight: 600;
        padding: 10px 20px;
        font-size: 0.95rem;
        border-radius: 6px;
        transition: 0.2s ease-in-out;
    }

    .btn-primary:hover {
        background: #1b4db3;
    }
 
    .btn-dark {
        background: #111827;
        border: none;
        font-weight: 600;
        padding: 10px 20px;
        font-size: 0.95rem;
        border-radius: 6px;
        transition: 0.2s ease-in-out;
    }

    .btn-dark:hover {
        background: #000000;
    }

    #grand-total {
        font-size: 1.2rem;
        font-weight: bold;
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

            let base = qty * price;
            let discountAmount = (discount / 100) * base;
            let taxable = base - discountAmount;
            let taxAmount = (tax / 100) * taxable;
            let finalAmount = taxable + taxAmount;

            $(this).find('.subtotal').val('Rs ' + finalAmount.toFixed(2));
            grandTotal += finalAmount;
        });

        $('#grand-total').text('Rs ' + grandTotal.toFixed(2));
    }

    $('#add-product').click(function() {
        const newRow = $('#product-list .product-row:first').clone();
        newRow.find('input, select').val('');
        newRow.find('.subtotal').val('');
        $('#product-list').append(newRow);
    });

    $(document).on('click', '.remove-product', function() {
        if ($('#product-list .product-row').length > 1) {
            $(this).closest('.product-row').remove();
            calculateTotals();
        }
    });

    $(document).on('input change', '.qty, .price, .discount, .tax', calculateTotals);
</script>

@endsection