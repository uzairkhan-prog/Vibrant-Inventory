@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Payment</h3>

    <form method="POST" action="{{ route('supplier-payments.update', $payment) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Supplier</label>
            <input type="text" class="form-control" value="{{ $payment->supplier->name }}" disabled>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Payment Description</label>
            <textarea name="description" id="description" class="form-control">{{ $payment->description }}</textarea>
        </div>

        <div class="mb-3">
            <label for="payment_type" class="form-label">Payment Type</label>
            <select name="payment_type" id="payment_type" class="form-select" required>
                <option value="Cash" {{ $payment->payment_type == 'Cash' ? 'selected' : '' }}>Cash</option>
                <option value="Bank Transfer" {{ $payment->payment_type == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                <option value="Cheque" {{ $payment->payment_type == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                <option value="Other" {{ $payment->payment_type == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">Amount (Rs)</label>
            <input type="number" step="0.01" name="amount" id="amount" class="form-control" value="{{ $payment->amount }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Update Payment</button>
        <a href="{{ route('suppliers.show', $payment->supplier_id) }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection