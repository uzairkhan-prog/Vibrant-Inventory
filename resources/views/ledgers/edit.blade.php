@extends('layouts.app')

@section('content')

<div class="table-responsive">
    <div class="table-wrapper">
        <div class="table-title">
            <div class="row">
                <div class="col-sm-5">
                    <h2>Ledger Entry <b>Edit</b></h2>
                </div>
            </div>
        </div>

        <div class="p-3">
            <form method="POST" action="{{ route('ledgers.update', $ledger) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="type" class="form-label">Type</label>
                    <select name="type" id="type" class="form-select" required>
                        <option value="customer" @if($ledger->type == 'customer') selected @endif>Customer</option>
                        <option value="supplier" @if($ledger->type == 'supplier') selected @endif>Supplier</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="reference_id" class="form-label">Reference</label>
                    <select name="reference_id" id="reference_id" class="form-select" required>
                        <optgroup label="Customers">
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" @if($ledger->type == 'customer' && $ledger->reference_id == $customer->id) selected @endif>
                                {{ $customer->name }}
                            </option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Suppliers">
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @if($ledger->type == 'supplier' && $ledger->reference_id == $supplier->id) selected @endif>
                                {{ $supplier->name }}
                            </option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="balance" class="form-label">Balance</label>
                    <input type="number" step="0.01" name="balance" id="balance" class="form-control" value="{{ $ledger->balance }}" required>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('ledgers.index') }}" class="btn btn-dark">Back</a>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection