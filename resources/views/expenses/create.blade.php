@extends('layouts.app')

@section('content')

<div class="table-responsive">
    <div class="table-wrapper">
        <div class="table-title">
            <h2>Add New <b>Expense</b></h2>
        </div>

        <div class="p-3">
            <form method="POST" action="{{ route('expenses.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Expense Name</label>
                    <input type="text" name="expense_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Payment Type</label>
                    <input type="text" name="payment_type" class="form-control" placeholder="Cash or Account Name" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Amount</label>
                    <input type="number" name="amount" step="0.01" class="form-control" required>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="{{ route('expenses.index') }}" class="btn btn-dark">Back</a>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection