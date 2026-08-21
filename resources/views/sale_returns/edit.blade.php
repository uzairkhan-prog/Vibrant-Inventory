@extends('layouts.app')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="py-2">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold text-primary-emphasis mb-1">Edit <span class="text-dark">Sale Return</span></h2>
            <p class="text-muted mb-0">Update the returned quantity or deduction amount for this return.</p>
        </div>
        <a href="{{ route('sale-returns.index') }}" class="btn btn-outline-dark">
            <i class="material-icons align-middle">list_alt</i> Back to Returns
        </a>
    </div>

    <!-- Validation Errors -->
    @if ($errors->any())
    <div class="alert alert-danger shadow-sm rounded-3">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('sale-returns.update', $sale_return->id) }}">
        @csrf
        @method('PUT')

        <!-- Hidden sale_id input -->
        <input type="hidden" name="sale_id" id="saleIdInput" value="{{ $sale_return->sale_id }}">

        <!-- Return Information -->
        <div class="card shadow-lg border-0 rounded-4 mb-4">
            <div class="card-body">
                <h5 class="fw-semibold text-dark mb-4">
                    <i class="material-icons align-middle text-primary">assignment</i> Return Information
                </h5>

                <div class="row g-3">

                    <!-- DATE -->
                    <div class="col-md-4">
                        <label class="form-label">Return Date</label>
                        <input type="date" name="return_date" class="form-control"
                            value="{{ old('return_date', date('Y-m-d')) }}" required>
                    </div>

                    <!-- Customer -->
                    <div class="col-md-4">
                        <label class="form-label">Customer</label>
                        <select name="customer_id" id="customerSelect" class="form-select select2" required>
                            <option value="">-- Select Customer --</option>
                            @foreach($customers as $c)
                            <option value="{{ $c->id }}" data-balance="{{ $c->balance }}" {{ $c->id == $sale_return->customer_id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                            @endforeach
                        </select>
                        <div class="form-text text-primary fw-semibold">Current Balance: Rs <span id="customerBalance">0.00</span></div>
                    </div>

                    <!-- Product -->
                    <div class="col-md-4">
                        <label class="form-label">Product / Sale Invoice</label>
                        <select name="product_id" id="productSelect" class="form-select select2" required>
                            <option value="{{ $sale_return->product_id }}"
                                data-qty="{{ $sale_return->product->quantity ?? 0 }}"
                                data-price="{{ ($sale_return->amount_deducted + $sale_return->cut_amount) / max($sale_return->qty_return,1) }}"
                                data-packing="{{ $sale_return->packing }}"
                                data-sale-id="{{ $sale_return->sale_id }}" selected>
                                {{ $sale_return->product->name ?? '-' }} (Sale ID: {{ $sale_return->sale_id }})
                            </option>
                        </select>
                        <div class="form-text">
                            <span class="text-success fw-semibold">Available:</span> <span id="productQty">0</span> |
                            <span class="text-info fw-semibold">Price/Unit:</span> Rs <span id="productPrice">0.00</span> |
                            <span class="text-muted">Packing:</span> <span id="productPack">-</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Return Calculation -->
        <div class="card shadow-lg border-0 rounded-4 mb-4">
            <div class="card-body">
                <h5 class="fw-semibold text-dark mb-4">
                    <i class="material-icons align-middle text-primary">calculate</i> Return Calculation
                </h5>

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Packing</label>
                        <input type="text" name="packing" id="packingInput" class="form-control" value="{{ $sale_return->packing }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Quantity Returned</label>
                        <input type="number" name="qty_return" id="qtyReturn" class="form-control" min="1" value="{{ $sale_return->qty_return }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Deduction Amount (Rs)</label>
                        <input type="number" name="cut_amount" id="cutAmount" class="form-control" step="0.01" min="0" value="{{ $sale_return->cut_amount ?? 0 }}">
                        <div class="form-text">Retained by the business, not refunded.</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Net Refund to Customer (Rs)</label>
                        <input type="number" name="amount_deducted" id="amountDeducted" class="form-control fw-bold bg-light" step="0.01" value="{{ $sale_return->amount_deducted }}" readonly required>
                    </div>
                </div>

                <!-- Summary Strip -->
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 bg-light border h-100">
                            <div class="text-muted small fw-semibold text-uppercase">Total Return Value</div>
                            <div class="fs-5 fw-bold text-dark">Rs <span id="totalReturnValue">0.00</span></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 bg-warning-subtle border h-100">
                            <div class="text-muted small fw-semibold text-uppercase">Deduction Retained</div>
                            <div class="fs-5 fw-bold text-warning-emphasis">Rs <span id="summaryCutAmount">0.00</span></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 bg-success-subtle border h-100">
                            <div class="text-muted small fw-semibold text-uppercase">Net Refund to Customer</div>
                            <div class="fs-5 fw-bold text-success-emphasis">Rs <span id="summaryNetRefund">0.00</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mb-4">
            <button type="submit" class="btn btn-primary px-4">
                <i class="material-icons align-middle">check_circle</i> Update Return
            </button>
            <a href="{{ route('sale-returns.index') }}" class="btn btn-secondary px-4">Cancel</a>
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
            let totalValue = qty * price;
            let cut = parseFloat($('#cutAmount').val()) || 0;

            if (cut > totalValue) {
                cut = totalValue;
                $('#cutAmount').val(cut.toFixed(2));
            }

            let netRefund = totalValue - cut;

            $('#totalReturnValue').text(totalValue.toFixed(2));
            $('#summaryCutAmount').text(cut.toFixed(2));
            $('#summaryNetRefund').text(netRefund.toFixed(2));
            $('#amountDeducted').val(netRefund.toFixed(2));
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

        // On quantity / deduction amount change
        $('#qtyReturn').on('input', calculateAmountDeducted);
        $('#cutAmount').on('input', calculateAmountDeducted);

        // Initial load for edit page
        updateCustomerBalance();
        updateProductDetails();
        calculateAmountDeducted();
    });
</script>
@endsection
