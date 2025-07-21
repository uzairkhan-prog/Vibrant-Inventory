@extends('layouts.app')

@section('content')
<div class="table-responsive">
    <div class="table-wrapper">
        <div class="table-title">
            <div class="row">
                <div class="col-md-6">
                    <h2>Expense Name <b>Management</b></h2>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('expense-name.create') }}" class="btn btn-secondary">
                        <i class="material-icons">&#xE147;</i> <span>Add Expense Name</span>
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
        @endif

        @if($expenseNames->count())
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Expense Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($expenseNames as $name)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $name->name }}</td>
                    <td>
                        <a href="{{ route('expense-name.edit', $name) }}" class="btn btn-sm btn-success text-white">Edit</a>
                        <form action="{{ route('expense-name.destroy', $name) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this exoense name?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="alert alert-info text-center">No expense name found. <a href="{{ route('expense-name.create') }}">Create one</a>.</div>
        @endif
    </div>
</div>
@endsection