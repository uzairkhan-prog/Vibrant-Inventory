@extends('layouts.app')

@section('content')

<div class="invoice-wrapper p-4 my-5 bg-white shadow rounded">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">Purchase Invoice</h2>
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

    <form method="POST" action="{{ route('purchases.store') }}">
        @csrf

        <!-- Supplier and Date -->
        <div class="row mb-4">
            <div class="col-md-6">
                <label for="supplier_id" class="form-label">Supplier</label>
                <select name="supplier_id" id="supplier_id" class="form-select" required>
                    <option value="">Select Supplier</option>
                    @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="date" class="form-label">Date</label>
                <input type="date" name="date" id="date" class="form-control" required>
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
                    <tr class="product-row">
                        <td>
                            <select name="category_id[]" class="form-select category-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select name="product_id[]" class="form-select product-select" required>
                                <option value="">Select Product</option>
                            </select>
                        </td>
                        <td><input type="number" name="quantity[]" class="form-control qty" min="1" required></td>
                        <td><input type="number" name="price[]" class="form-control price" step="0.01" required></td>
                        <td><input type="number" name="discount[]" class="form-control discount" value="0" min="0" step="0.01"></td>
                        <td>
                            <select name="tax[]" class="form-select tax">
                                <option value="0">0%</option>
                                <option value="18">18%</option>
                            </select>
                        </td>
                        <td><input type="text" class="form-control subtotal" readonly></td>
                        <td><button type="button" class="btn btn-danger remove-product">×</button></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mb-3">
            <button type="button" class="btn btn-secondary" id="add-product">+ Add Product</button>
        </div>

        <!-- Total Summary -->
        <div class="row justify-content-end">
            <div class="col-md-4">
                <div class="p-3 border rounded bg-light text-end">
                    <strong class="d-block mb-2 fs-5">Total Amount:</strong>
                    <span id="grand-total" class="fs-4 text-success">Rs 0.00</span>
                </div>
            </div>
        </div>

        <div class="mt-4 text-center">
            <button type="submit" class="btn btn-primary px-5">Submit</button>
            <a href="{{ route('purchases.index') }}" class="btn btn-dark ms-2">Back</a>
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
</style>

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

    // Add new row
    $('#add-product').click(function() {
        const newRow = $('#product-list .product-row:first').clone();
        newRow.find('input').val('');
        newRow.find('.discount').val('0');
        newRow.find('.subtotal').val('');
        newRow.find('.category-select').val('');
        newRow.find('.product-select').html('<option value="">Select Product</option>');
        $('#product-list').append(newRow);
    });

    // Remove row
    $(document).on('click', '.remove-product', function() {
        if ($('#product-list .product-row').length > 1) {
            $(this).closest('.product-row').remove();
            calculateTotals();
        }
    });

    // Calculate totals on change
    $(document).on('input change', '.qty, .price, .discount, .tax', calculateTotals);

    // Fetch products by category
    $(document).on('change', '.category-select', function() {
        let categoryId = $(this).val();
        let productSelect = $(this).closest('tr').find('.product-select');
        productSelect.html('<option>Loading...</option>');
        if (categoryId) {
            $.ajax({
                url: "{{ url('products/by-category') }}/" + categoryId,
                type: 'GET',
                success: function(products) {
                    productSelect.empty().append('<option value="">Select Product</option>');
                    $.each(products, function(index, product) {
                        productSelect.append('<option value="' + product.id + '">' +
                            product.name + '</option>');
                    });
                }
            });
        } else {
            productSelect.html('<option value="">Select Product</option>');
        }
    });

    // ✅ Set today's date automatically if empty
    $(document).ready(function() {
        let today = new Date().toISOString().split('T')[0];
        if (!$('#date').val()) {
            $('#date').val(today);
        }
    });
</script>

@endsection