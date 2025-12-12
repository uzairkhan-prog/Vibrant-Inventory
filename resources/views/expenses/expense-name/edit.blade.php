@extends('layouts.app')

@section('content')
<div class="table-responsive">
    <div class="table-title">
        <h2>Edit <b>Expense Name</b></h2>
    </div>

    <div class="p-3">
        <form method="POST" action="{{ route('expense-name.update', $expenseName) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Expense Name</label>
                <input type="text" name="name" class="form-control" value="{{ $expenseName->name }}" required>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('expense-name.index') }}" class="btn btn-dark">Back</a>
            </div>
        </form>
    </div>

</div>
@endsection