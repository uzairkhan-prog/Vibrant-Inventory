@extends('layouts.app')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="py-2">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold text-primary-emphasis mb-1">Purchase Return <span class="text-dark">Entry</span></h2>
            <p class="text-muted mb-0">Send purchased stock back to the supplier and reduce the amount you owe.</p>
        </div>
        <a href="{{ route('purchase-returns.index') }}" class="btn btn-outline-dark">
            <i class="material-icons align-middle">list_alt</i> View All Returns
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

    <form method="POST" action="{{ route('purchase-returns.store') }}">
        @csrf

        <!-- Hidden inputs for the selected purchase line -->
        <input type="hidden" name="purchase_id" id="purchaseIdInput" value="{{ old('purchase_id') }}">
        <input type="hidden" name="product_id" id="productIdInput" value="{{ old('product_id') }}">

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

                    <!-- SUPPLIER -->
                    <div class="col-md-4">
                        <label class="form-label">Supplier</label>
                        <select name="supplier_id" id="supplierSelect" class="form-select select2" style="width:100%;" required>
                            <option value="">-- Select Supplier --</option>
                            @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" data-balance="{{ $s->balance }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->company_name ? $s->company_name . ' ( ' . $s->name . ' )' : $s->name }}
                            </option>
                            @endforeach
                        </select>
                        <div class="form-text text-primary fw-semibold">
                            Payable Balance: Rs <span id="supplierBalance">0.00</span>
                        </div>
                    </div>

                    <!-- PRODUCT -->
                    <div class="col-md-4">
                        <label class="form-label">Product / Purchase Invoice</label>
                        <select id="productSelect" class="form-select select2" style="width:100%;" required>
                            <option value="">-- Select Product --</option>
                        </select>
                        <div class="form-text">
                            <span class="text-success fw-semibold">Returnable:</span> <span id="productReturnable">0</span> |
                            <span class="text-secondary fw-semibold">In Stock:</span> <span id="productStock">0</span> |
                            <span class="text-info fw-semibold">Cost/Unit:</span> Rs <span id="productPrice">0.00</span> |
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
                        <input type="text" name="packing" id="packingInput" class="form-control" value="{{ old('packing') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Quantity Returned</label>
                        <input type="number" name="qty_return" id="qtyReturn" class="form-control" min="1" value="{{ old('qty_return') }}" required>
                        <div class="form-text text-danger d-none" id="qtyWarning"></div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Deduction Amount (Rs)</label>
                        <input type="number" name="cut_amount" id="cutAmount" class="form-control" step="0.01" min="0" value="{{ old('cut_amount', 0) }}">
                        <div class="form-text">Kept by the supplier, not credited back.</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Net Credit from Supplier (Rs)</label>
                        <input type="number" id="amountDeducted" class="form-control fw-bold bg-light" step="0.01" readonly>
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
                            <div class="text-muted small fw-semibold text-uppercase">Deduction by Supplier</div>
                            <div class="fs-5 fw-bold text-warning-emphasis">Rs <span id="summaryCutAmount">0.00</span></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 bg-success-subtle border h-100">
                            <div class="text-muted small fw-semibold text-uppercase">Net Credit from Supplier</div>
                            <div class="fs-5 fw-bold text-success-emphasis">Rs <span id="summaryNetCredit">0.00</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mb-4">
            <button type="submit" class="btn btn-primary px-4">
                <i class="material-icons align-middle">check_circle</i> Return &amp; Deduct
            </button>
            <a href="{{ route('purchase-returns.index') }}" class="btn btn-secondary px-4">Cancel</a>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

@include('purchase_returns.script', [
    'excludeId' => null,
    'selectedKey' => old('purchase_id') && old('product_id') ? old('purchase_id') . ':' . old('product_id') : null,
])

@endsection
