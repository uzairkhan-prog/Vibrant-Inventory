@extends('layouts.app')

@section('content')

<div class="table-responsive">
    <div class="table-wrapper">
        <div class="table-title">
            <h2>Edit <b>Expense</b></h2>
        </div>

        <div class="p-3">
            <form method="POST" action="{{ route('expenses.update', $expense) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Expense Name</label>
                    <input type="text" name="expense_name" class="form-control" value="{{ $expense->expense_name }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control">{{ $expense->description }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Payment Type</label>
                    <input type="text" name="payment_type" class="form-control" value="{{ $expense->payment_type }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Amount</label>
                    <input type="number" name="amount" step="0.01" class="form-control" value="{{ $expense->amount }}" required>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('expenses.index') }}" class="btn btn-dark">Back</a>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection