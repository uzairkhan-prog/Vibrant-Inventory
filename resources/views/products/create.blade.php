@extends('layouts.app')

@section('content')

<div class="table-responsive">
    <div class="table-wrapper">
        <div class="table-title">
            <div class="row">
                <div class="col-sm-5">
                    <h2>Product <b>Create</b></h2>
                </div>
            </div>
        </div>

        <div class="p-3">
            <form method="POST" action="{{ route('products.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <div class="mb-3">
                    <label for="packing" class="form-label">Packing</label>
                    <input name="packing" id="packing" type="number" class="form-control" value="{{ old('packing', 1) }}" required>
                </div>

                <div class="mb-3">
                    <label for="price_per_unit" class="form-label">Rate (Price per Unit)</label>
                    <input name="price_per_unit" id="price_per_unit" type="number" step="0.01" class="form-control" value="{{ old('price_per_unit') }}" required>
                </div>

                <div class="mb-3">
                    <label for="quantity" class="form-label">Total QTY</label>
                    <input name="quantity" id="quantity" type="number" class="form-control" value="{{ old('quantity') }}" required>
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label">Category</label>
                    <select name="category_id" id="category_id" class="form-select">
                        <option value="">-- None --</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="{{ route('products.index') }}" class="btn btn-dark">Back</a>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection