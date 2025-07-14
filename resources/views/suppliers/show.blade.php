@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h2 class="fw-bold text-primary-emphasis">Supplier Profile: <strong>{{ $supplier->name }}</strong></h2>
    </div>

    <!-- Balance Info -->
    <div class="alert alert-info d-flex justify-content-between align-items-center shadow-sm rounded-3 fs-5">
        <span><strong>Previous Balance:</strong></span>
        <span class="fw-bold text-success">Rs {{ number_format($currentBalance, 2) }}</span>
    </div>

    <!-- Flash message -->
    @if(session('success'))
    <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
    @endif

    <!-- Payment Form -->
    <div class="card shadow-lg border-0 rounded-4 mb-5">
        <div class="card-body">
            <h4 class="mb-4 text-dark fw-semibold">💳 Record a Payment</h4>
            <form method="POST" action="{{ route('suppliers.payments.store', $supplier) }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Supplier Name</label>
                        <input type="text" class="form-control bg-light" value="{{ $supplier->name }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label for="payment_type" class="form-label">Payment Type</label>
                        <select name="payment_type" id="payment_type" class="form-select" required>
                            <option value="">-- Select --</option>
                            <option value="Cash">Cash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label for="description" class="form-label">Payment Description</label>
                        <textarea name="description" id="description" class="form-control" rows="2" placeholder="Payment details..."></textarea>
                    </div>

                    <div class="col-md-6">
                        <label for="amount" class="form-label">Amount (Rs)</label>
                        <input type="number" step="0.01" name="amount" id="amount" class="form-control" required min="0.01">
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('suppliers.index') }}" class="btn btn-outline-dark">
                        ← Back to Suppliers
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="material-icons align-middle">send</i> Submit
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Previous Payments Table -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">
            <h4 class="mb-4 text-dark fw-semibold">📄 Previous Payments</h4>

            @if($supplier->payments->count())
            <div class="table-responsive">
                <table class="table table-striped align-middle table-hover">
                    <thead class="table-dark text-center">
                        <tr>
                            <th scope="col">Type</th>
                            <th scope="col">Description</th>
                            <th scope="col">Amount (Rs)</th>
                            <th scope="col">Date</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach($supplier->payments as $payment)
                        <tr>
                            <td>
                                <span class="badge bg-secondary">{{ $payment->payment_type }}</span>
                            </td>
                            <td>{{ $payment->description }}</td>
                            <td class="fw-bold text-success">Rs {{ number_format($payment->amount, 2) }}</td>
                            <td>{{ $payment->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('supplier-payments.edit', $payment) }}" class="btn btn-sm btn-success text-white" title="Edit">
                                    Edit
                                    <i class="material-icons">edit</i>
                                </a>
                                <form action="{{ route('supplier-payments.destroy', $payment) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this payment?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        Delete
                                        <i class="material-icons">delete</i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-muted fst-italic">No payments recorded yet.</div>
            @endif
        </div>
    </div>
</div>
@endsection