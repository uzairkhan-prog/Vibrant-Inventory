@extends('layouts.app')

@section('content')
<div class="table-responsive">
    <div class="table-wrapper">
        <div class="table-title">
            <div class="row">
                <div class="col-md-6">
                    <h2>Payment Type <b>Management</b></h2>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('payment-types.create') }}" class="btn btn-secondary">
                        <i class="material-icons">&#xE147;</i> <span>Add Payment Type</span>
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
        @endif

        @if($paymentTypes->count())
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Type Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($paymentTypes as $type)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $type->name }}</td>
                    <td>
                        <a href="{{ route('payment-types.edit', $type) }}" class="btn btn-sm btn-success text-white">Edit</a>
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
        @else
        <div class="alert alert-info text-center">No payment types found. <a href="{{ route('payment-types.create') }}">Create one</a>.</div>
        @endif
    </div>
</div>
@endsection