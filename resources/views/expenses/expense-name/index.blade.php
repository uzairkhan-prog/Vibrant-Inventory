@extends('layouts.app')

@section('content')

<div class="p-4 bg-white shadow rounded">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary mb-0">Expense Name Management</h2>
        <div>
            <a href="{{ route('expense-name.create') }}" class="btn btn-secondary">
                <i class="material-icons me-1">&#xE147;</i> Add Expense Name
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    @if($expenseNames->count())
    <div class="table-responsive">
        <div class="table-wrapper">
            <table class="table table-striped table-hover table-bordered align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Expense Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($expenseNames as $index => $name)
                    <tr>
                        <td>{{ $name->id }}</td>
                        <td>{{ $name->name }}</td>
                        <td class="d-flex justify-content-center">
                            <a href="{{ route('expense-name.edit', $name) }}" class="btn btn-sm btn-success me-1 text-white">Edit</a>
                            <form action="{{ route('expense-name.destroy', $name) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this expense name?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="alert alert-info text-center mt-4">
        No expense name found. <a href="{{ route('expense-name.create') }}" class="text-dark">Create one</a>.
    </div>
    @endif
</div>
@endsection