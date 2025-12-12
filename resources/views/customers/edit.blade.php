@extends('layouts.app')

@section('content')

<div class="table-responsive">
    <div class="table-title">
        <div class="row">
            <div class="col-sm-5">
                <h2>Customer <b>Edit</b></h2>
            </div>
        </div>
    </div>

    <div class="p-3">
        <form method="POST" action="{{ route('customers.update', $customer) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input name="name" id="name" class="form-control" value="{{ old('name', $customer->name) }}" required>
            </div>

            <div class="mb-3">
                <label for=" " class="form-label">Company Name</label>
                <input name="company_name" id="company_name" class="form-control" value="{{ old('name', $customer->company_name) }}" required>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input name="phone" id="phone" class="form-control" value="{{ old('name', $customer->phone) }}" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input name="email" id="email" class="form-control" value="{{ old('name', $customer->email) }}" required>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea name="address" id="address" class="form-control" rows="4">{{ old('address', $customer->address) }}</textarea>
            </div>

            <div class="mb-3">
                <label for="balance" class="form-label">Balance</label>
                <input name="balance" id="balance" type="number" step="0.01" class="form-control" value="{{ old('balance', $customer->balance) }}">
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('customers.index') }}" class="btn btn-dark">Back</a>
            </div>
        </form>
    </div>

</div>

@endsection