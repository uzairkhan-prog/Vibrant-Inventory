@extends('layouts.app')

@section('content')

<div class="table-responsive">
    <div class="table-wrapper">
        <div class="table-title">
            <div class="row">
                <div class="col-sm-6">
                    <h2>Edit <b>Sale Return</b></h2>
                </div>
                <div class="col-sm-6 text-end">
                    <a href="{{ route('sale-returns.index') }}" class="btn btn-dark">Back to Returns</a>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
        @endif

        <div class="p-3">
            <form method="POST" action="{{ route('sale-returns.update', $saleReturn->id) }}">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Customer</label>
                        <select name="customer_id" id="customerSelect" class="form-select" required>
                            <option value="">-- Select Customer --</option>
                            @foreach($customers as $c)
                            <option value="{{ $c->id }}"
                                data-balance="{{ $c->balance }}"
                                {{ $c->id == $saleReturn->customer_id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text text-primary fw-bold">Current Balance: Rs <span id="customerBalance">0.00</span></div>
                    </div>

                    <div class="col-md-6">
                        <label>Product</label>
                        <select name="product_id" id="productSelect" class="form-select" required>
                            <option value="">-- Select Product --</option>
                            @foreach($products as $p)
                            <option value="{{ $p->id }}"
                                data-qty="{{ $p->quantity }}"
                                data-price="{{ $p->price_per_unit }}"
                                data-packing="{{ $p->packing }}"
                                {{ $p->id == $saleReturn->product_id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                            @endforeach
                        </select>
                        <div class="form-text">
                            <span class="text-success fw-bold">Qty Available:</span> <span id="productQty">-</span> |
                            <span class="text-info fw-bold">Price/Unit:</span> Rs <span id="productPrice">-</span> |
                            <span class="text-muted">Packing:</span> <span id="productPack">-</span>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Packing</label>
                        <input type="text" name="packing" id="packingInput" class="form-control" value="{{ $saleReturn->packing }}">
                    </div>
                    <div class="col-md-4">
                        <label>Quantity Returned</label>
                        <input type="number" name="qty_return" id="qtyReturn" class="form-control" value="{{ $saleReturn->qty_return }}" required>
                    </div>
                    <div class="col-md-4">
                        <label>Amount Deducted (Rs)</label>
                        <input type="number" name="amount_deducted" id="amountDeducted" step="0.01" class="form-control" value="{{ $saleReturn->amount_deducted }}" required>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Update Return</button>
                    <a href="{{ route('sale-returns.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script -->
<script>
    function updateCustomerBalance() {
        const selected = document.getElementById('customerSelect');
        const balance = selected.options[selected.selectedIndex].getAttribute('data-balance') || '0.00';
        document.getElementById('customerBalance').innerText = parseFloat(balance).toFixed(2);
    }

    function updateProductDetails() {
        const selected = document.getElementById('productSelect');
        const qty = selected.options[selected.selectedIndex].getAttribute('data-qty') || '-';
        const price = selected.options[selected.selectedIndex].getAttribute('data-price') || '0.00';
        const pack = selected.options[selected.selectedIndex].getAttribute('data-packing') || '-';

        document.getElementById('productQty').innerText = qty;
        document.getElementById('productPrice').innerText = parseFloat(price).toFixed(2);
        document.getElementById('productPack').innerText = pack;
        document.getElementById('packingInput').value = pack;
    }

    function calculateDeductedAmount() {
        const qty = parseFloat(document.getElementById('qtyReturn').value || 0);
        const selected = document.getElementById('productSelect');
        const price = parseFloat(selected.options[selected.selectedIndex].getAttribute('data-price') || 0);
        document.getElementById('amountDeducted').value = (qty * price).toFixed(2);
    }

    document.getElementById('customerSelect').addEventListener('change', updateCustomerBalance);
    document.getElementById('productSelect').addEventListener('change', () => {
        updateProductDetails();
        calculateDeductedAmount();
    });
    document.getElementById('qtyReturn').addEventListener('input', calculateDeductedAmount);

    // Trigger updates on page load
    window.addEventListener('DOMContentLoaded', () => {
        updateCustomerBalance();
        updateProductDetails();
    });
</script>

@endsection