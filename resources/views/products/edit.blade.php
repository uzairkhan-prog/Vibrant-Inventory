@extends('layouts.app')

@section('content')

<div class="table-responsive">
    <div class="table-wrapper">
        <div class="table-title">
            <div class="row">
                <div class="col-sm-5">
                    <h2>Product <b>Edit</b></h2>
                </div>
            </div>
        </div>

        <div class="p-3">
            <form method="POST" action="{{ route('products.update', $product) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input name="name" id="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                </div>

                <div class="mb-3">
                    <label for="packing" class="form-label">Packing</label>
                    <input name="packing" id="packing" type="number" class="form-control" value="{{ old('packing', $product->packing) }}" required>
                </div>

                <div class="mb-3">
                    <label for="price_per_unit" class="form-label">Rate (Price per Unit)</label>
                    <input name="price_per_unit" id="price_per_unit" type="number" step="0.01" class="form-control" value="{{ $product->price_per_unit }}" required>
                </div>

                <div class="mb-3">
                    <label for="quantity" class="form-label">Total QTY</label>
                    <input name="quantity" id="quantity" type="number" class="form-control" value="{{ $product->quantity }}" required>
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label">Category</label>
                    <select name="category_id" id="category_id" class="form-select">
                        <option value="">-- None --</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @if($product->category_id == $cat->id) selected @endif>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('products.index') }}" class="btn btn-dark">Back</a>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection