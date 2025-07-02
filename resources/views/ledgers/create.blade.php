@extends('layouts.app')

@section('content')

<div class="table-responsive">
    <div class="table-wrapper">
        <div class="table-title">
            <div class="row">
                <div class="col-sm-5">
                    <h2>Ledger Entry <b>Create</b></h2>
                </div>
            </div>
        </div>

        <div class="p-3">
            <form method="POST" action="{{ route('ledgers.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="type" class="form-label">Type</label>
                    <select name="type" id="type" class="form-select" required>
                        <option value="customer">Customer</option>
                        <option value="supplier">Supplier</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="reference_id" class="form-label">Reference</label>
                    <select name="reference_id" id="reference_id" class="form-select" required>
                        <optgroup label="Customers">
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Suppliers">
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="balance" class="form-label">Balance</label>
                    <input type="number" step="0.01" name="balance" id="balance" class="form-control" required>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="{{ route('ledgers.index') }}" class="btn btn-dark">Back</a>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection