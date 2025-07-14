@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h2 class="fw-bold text-primary-emphasis">Customer Profile: <strong>{{ $customer->name }}</strong></h2>
    </div>

    <!-- Balance Info -->
    <div class="alert alert-info d-flex justify-content-between align-items-center shadow-sm rounded-3 fs-5">
        <span><strong>Current Balance:</strong></span>
        <span class="fw-bold text-success">Rs {{ number_format($customer->balance, 2) }}</span>
    </div>

    <!-- Flash message -->
    @if(session('success'))
    <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
    @endif

    <!-- Payment Form -->
    <div class="card shadow-lg border-0 rounded-4 mb-5">
        <div class="card-body">
            <h4 class="mb-4 text-dark fw-semibold">💳 Record a Payment</h4>
            <form method="POST" action="{{ route('customers.payments.store', $customer) }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Payment Mode</label>
                        <select name="payment_mode" class="form-select" required>
                            <option value="">-- Select --</option>
                            <option value="Cash">Cash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control" placeholder="Payment details...">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Amount</label>
                        <input type="number" name="amount" step="0.01" class="form-control" required>
                    </div> v
                </div>

                <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-dark">
                        ← Back to Customers
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

            @if($customer->payments->count())
            <div class="table-responsive">
                <table class="table table-striped align-middle table-hover">
                    <thead class="table-dark text-center">
                        <tr>
                            <th scope="col">Mode</th>
                            <th scope="col">Description</th>
                            <th scope="col">Amount (Rs)</th>
                            <th scope="col">Date</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach($customer->payments as $payment)
                        <tr>
                            <td>
                                <span class="badge bg-secondary">{{ $payment->payment_mode }}</span>
                            </td>
                            <td>{{ $payment->description }}</td>
                            <td class="fw-bold text-success">Rs {{ number_format($payment->amount, 2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($payment->date)->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('customer-payments.edit', $payment) }}" class="btn btn-sm btn-success text-white" title="Edit">
                                    Edit
                                    <i class="material-icons">edit</i>
                                </a>
                                <form action="{{ route('customer-payments.destroy', $payment) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this payment?')">
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
            <div class="text-muted fst-italic">No payments found.</div>
            @endif
        </div>
    </div>
</div>
@endsection