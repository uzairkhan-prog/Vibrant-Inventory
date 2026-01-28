@extends('layouts.app')

@section('content')
<div class="table-responsive">
    <div class="table-title">
        <h2>Add Expense</h2>
    </div>

    <div class="p-3">
        <form method="POST" action="{{ route('expenses.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Expense Name</label>
                <select name="expense_name_id" class="form-select select2" required>
                    <option value="">-- Select Expense Name --</option>
                    @foreach ($expenseNames as $name)
                    <option value="{{ $name->id }}">{{ $name->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Payment Type</label>
                <select name="payment_type_id" class="form-select select2" required>
                    <option value="">-- Select Payment Type --</option>
                    @foreach ($paymentTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Amount</label>
                <input type="number" name="amount" step="0.01" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control"></textarea>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="{{ route('expenses.index') }}" class="btn btn-dark">Back</a>
            </div>
        </form>
    </div>
</div>
@endsection