@extends('layouts.app')

@section('content')

<div class="table-responsive">
    <div class="table-wrapper">
        <div class="table-title">
            <div class="row">
                <div class="col-sm-5">
                    <h2>Customer Ledger <b>Edit</b></h2>
                </div>
            </div>
        </div>

        <div class="p-3">
            <form method="POST" action="{{ route('customer-ledgers.update', $ledger) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="reference_id" class="form-label">Customer</label>
                    <select name="reference_id" id="reference_id" class="form-select" required>
                        @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" @if($ledger->reference_id == $customer->id) selected @endif>
                            {{ $customer->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="balance" class="form-label">Balance</label>
                    <input type="number" step="0.01" name="balance" id="balance" class="form-control" value="{{ $ledger->balance }}" required>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('customer-ledgers.index') }}" class="btn btn-dark">Back</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection