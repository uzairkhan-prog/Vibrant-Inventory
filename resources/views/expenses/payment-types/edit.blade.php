@extends('layouts.app')

@section('content')
<div class="table-responsive">
    <div class="table-wrapper">
        <div class="table-title">
            <h2>Edit <b>Payment Type</b></h2>
        </div>

        <div class="p-3">
            <form method="POST" action="{{ route('payment-types.update', $paymentType) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Payment Type Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $paymentType->name }}" required>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('payment-types.index') }}" class="btn btn-dark">Back</a>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection