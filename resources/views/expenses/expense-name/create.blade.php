@extends('layouts.app')

@section('content')
<div class="table-responsive">
    <div class="table-wrapper">
        <div class="table-title">
            <h2>Add New <b>Expense Name Type</b></h2>
        </div>

        <div class="p-3">
            <form method="POST" action="{{ route('expense-name.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Expense Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="{{ route('expense-name.index') }}" class="btn btn-dark">Back</a>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection 