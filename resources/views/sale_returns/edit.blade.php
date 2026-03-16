@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="table-responsive">
    <div class="table-title">
        <div class="row">
            <div class="col-sm-6">
                <h2>Edit <b>Sale Return</b></h2>
            </div>
            <div class="col-sm-6 text-end"><a href="{{ route('sale-returns.index') }}" class="btn btn-dark">Back to Returns</a></div>
        </div>
    </div>

    <form method="POST" action="{{ route('sale-returns.update', $sale_return->id) }}">
        @csrf
        @method('PUT')

        <!-- Hidden sale_id input -->
        <input type="hidden" name="sale_id" id="saleIdInput" value="{{ $sale_return->sale_id }}">

        <div class="row my-3">

            <!-- DATE -->
            <div class="col-md-4">
                <label>Return Date</label>
                <input type="date" name="return_date" class="form-control"
                    value="{{ old('return_date', date('Y-m-d')) }}" required>
            </div>

            <!-- Customer -->
            <div class="col-md-4">
                <label>Customer</label>
                <select name="customer_id" id="customerSelect" class="form-select select2" required>
                    <option value="">-- Select Customer --</option>
                    @foreach($customers as $c)
                    <option value="{{ $c->id }}" data-balance="{{ $c->balance }}" {{ $c->id == $sale_return->customer_id ? 'selected' : '' }}>
                        {{ $c->name }}
                    </option>
                    @endforeach
                </select>
                <div class="form-text text-primary fw-bold">Current Balance: Rs <span id="customerBalance">0.00</span></div>
            </div>

            <!-- Product -->
            <div class="col-md-4">
                <label>Product / Sale ID</label>
                <select name="product_id" id="productSelect" class="form-select select2" required>
                    <option value="{{ $sale_return->product_id }}"
                        data-qty="{{ $sale_return->product->quantity ?? 0 }}"
                        data-price="{{ $sale_return->amount_deducted / max($sale_return->qty_return,1) }}"
                        data-packing="{{ $sale_return->packing }}"
                        data-sale-id="{{ $sale_return->sale_id }}" selected>
                        {{ $sale_return->product->name ?? '-' }} (Sale ID: {{ $sale_return->sale_id }})
                    </option>
                </select>
                <div class="form-text">
                    <span class="text-success fw-bold">Qty Available:</span> <span id="productQty">0</span> |
                    <span class="text-info fw-bold">Price/Unit:</span> Rs <span id="productPrice">0.00</span> |
                    <span class="text-muted">Packing:</span> <span id="productPack">-</span>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label>Packing</label>
                <input type="text" name="packing" id="packingInput" class="form-control" value="{{ $sale_return->packing }}">
            </div>
            <div class="col-md-4">
                <label>Quantity Returned</label>
                <input type="number" name="qty_return" id="qtyReturn" class="form-control" min="1" value="{{ $sale_return->qty_return }}" required>
            </div>
            <div class="col-md-4">
                <label>Amount Deducted (Rs)</label>
                <input type="number" name="amount_deducted" id="amountDeducted" class="form-control" step="0.01" value="{{ $sale_return->amount_deducted }}" required>
            </div>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary">Update Return</button>
            <a href="{{ route('sale-returns.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Search here...",
            allowClear: true
        });

        // Functions to update UI
        function updateCustomerBalance() {
            $('#customerBalance').text(parseFloat($('#customerSelect').find(':selected').data('balance') || 0).toFixed(2));
        }

        function updateProductDetails() {
            let selected = $('#productSelect').find(':selected');
            $('#productQty').text(selected.data('qty') || 0);
            $('#productPrice').text(parseFloat(selected.data('price') || 0).toFixed(2));
            $('#productPack').text(selected.data('packing') || '-');
            $('#packingInput').val(selected.data('packing') || '');
            $('#saleIdInput').val(selected.data('sale-id') || '');
        }

        function calculateAmountDeducted() {
            let qty = parseFloat($('#qtyReturn').val()) || 0;
            let price = parseFloat($('#productSelect').find(':selected').data('price') || 0);
            $('#amountDeducted').val((qty * price).toFixed(2));
        }

        // On customer change: fetch products
        $('#customerSelect').on('change', function() {
            updateCustomerBalance();
            let customerId = $(this).val();
            if (!customerId) return $('#productSelect').html('<option value="">-- Select Product --</option>').trigger('change');

            $.get('/sale-returns/customer-products/' + customerId, function(data) {
                let options = '<option value="">-- Select Product --</option>';
                data.forEach(function(p) {
                    options += `<option value="${p.id}" 
                    data-qty="${p.quantity}" 
                    data-price="${p.price_per_unit}" 
                    data-packing="${p.packing}" 
                    data-sale-id="${p.sale_id}">
                    ${p.name} (Sale ID: ${p.sale_id})
                </option>`;
                });
                $('#productSelect').html(options).trigger('change');
            });
        });

        // On product change
        $('#productSelect').on('change', function() {
            updateProductDetails();
            calculateAmountDeducted();
        });

        // On quantity change
        $('#qtyReturn').on('input', calculateAmountDeducted);

        // Initial load for edit page
        updateCustomerBalance();
        updateProductDetails();
        calculateAmountDeducted();
    });
</script>
@endsection