@extends('layouts.app')

@section('content')

<div class="p-4  bg-white shadow rounded">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary mb-0">Payment Type Management</h2>
        <div>
            <a href="{{ route('payment-types.create') }}" class="btn btn-secondary">
                <i class="material-icons me-1">&#xE147;</i> Add Payment Type
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    @if($paymentTypes->count())
    <div class="table-responsive">
        <div class="table-wrapper">
            <table class="table table-striped table-hover table-bordered align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Type Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($paymentTypes as $index => $type)
                    <tr>
                        <td>{{ $type->id }}</td>
                        <td>{{ $type->name }}</td>
                        <td class="d-flex justify-content-center">
                            <a href="{{ route('payment-types.edit', $type) }}" class="btn btn-sm btn-success me-1 text-white">Edit</a>
                            <form action="{{ route('payment-types.destroy', $type) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this payment type?')">
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
        No payment types found. <a href="{{ route('payment-types.create') }}" class="text-dark">Create one</a>.
    </div>
    @endif
</div>
@endsection