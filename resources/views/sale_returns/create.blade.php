@extends('layouts.app')

@section('content')

<div class="table-responsive">
    <div class="table-wrapper">
        <div class="table-title">
            <div class="row">
                <div class="col-sm-6">
                    <h2>Sale Return <b>Entry</b></h2>
                </div>
                <div class="col-sm-6 text-end">
                    <a href="{{ route('sale-returns.index') }}" class="btn btn-dark">View All Returns</a>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
        @endif

        <div class="p-3">
            <form method="POST" action="{{ route('sale-returns.store') }}">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Customer</label>
                        <select name="customer_id" id="customerSelect" class="form-select" required>
                            <option value="">-- Select Customer --</option>
                            @foreach($customers as $c)
                            <option value="{{ $c->id }}" data-balance="{{ $c->balance }}">{{ $c->name }}</option>
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
                                data-packing="{{ $p->packing }}">
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
                        <input type="text" name="packing" id="packingInput" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>Quantity Returned</label>
                        <input type="number" name="qty_return" id="qtyReturn" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>Amount Deducted (Rs)</label>
                        <input type="number" name="amount_deducted" id="amountDeducted" step="0.01" class="form-control" required>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Return & Deduct</button>
                    <a href="{{ route('sale-returns.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>

    </div>
</div>

<!-- Script -->
<script>
    document.getElementById('customerSelect').addEventListener('change', function() {
        let balance = this.options[this.selectedIndex].getAttribute('data-balance') || '0.00';
        document.getElementById('customerBalance').innerText = parseFloat(balance).toFixed(2);
    });

    document.getElementById('productSelect').addEventListener('change', function() {
        let qty = this.options[this.selectedIndex].getAttribute('data-qty');
        let price = this.options[this.selectedIndex].getAttribute('data-price');
        let pack = this.options[this.selectedIndex].getAttribute('data-packing');

        document.getElementById('productQty').innerText = qty;
        document.getElementById('productPrice').innerText = parseFloat(price).toFixed(2);
        document.getElementById('productPack').innerText = pack;
        document.getElementById('packingInput').value = pack;
    });

    // Optional: Auto-calculate amount based on quantity and price
    document.getElementById('qtyReturn').addEventListener('input', function() {
        let qty = parseFloat(this.value || 0);
        let price = parseFloat(document.getElementById('productSelect').options[document.getElementById('productSelect').selectedIndex].getAttribute('data-price') || 0);
        document.getElementById('amountDeducted').value = (qty * price).toFixed(2);
    });
</script>

@endsection