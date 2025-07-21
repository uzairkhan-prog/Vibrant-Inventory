@extends('layouts.app')

@section('content')

<div class="table-responsive">
    <div class="table-wrapper">
        <div class="table-title">
            <div class="row">
                <div class="col-sm-5">
                    <h2>Supplier Ledger <b>Create</b></h2>
                </div>
            </div>
        </div>

        <div class="p-3">
            <form method="POST" action="{{ route('supplier-ledgers.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="reference_id" class="form-label">Supplier</label>
                    <select name="reference_id" id="reference_id" class="form-select" required>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="balance" class="form-label">Balance</label>
                    <input type="number" step="0.01" name="balance" id="balance" class="form-control" required>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="{{ route('supplier-ledgers.index') }}" class="btn btn-dark">Back</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection