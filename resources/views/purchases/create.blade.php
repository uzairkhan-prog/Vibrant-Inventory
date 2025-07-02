@extends('layouts.app')

@section('content')

<div class="table-responsive">
    <div class="table-wrapper">
        <div class="table-title">
            <div class="row">
                <div class="col-sm-5">
                    <h2>Purchase <b>Create</b></h2>
                </div>
            </div>
        </div>

        <div class="p-3">
            <form method="POST" action="{{ route('purchases.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="supplier_id" class="form-label">Supplier</label>
                    <select name="supplier_id" id="supplier_id" class="form-select" required>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" name="date" id="date" class="form-control" required>
                </div>

                <h5 class="mt-4">Products</h5>

                <div id="product-list">
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <select name="product_id[]" class="form-select" required>
                                @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} (Stock: {{ $product->quantity }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="quantity[]" class="form-control" placeholder="Qty" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" step="0.01" name="price[]" class="form-control" placeholder="Price" required>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger remove-product">Remove</button>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-secondary mb-3" id="add-product">Add Another Product</button>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="{{ route('purchases.index') }}" class="btn btn-dark">Back</a>
                </div>
            </form>
        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#add-product').click(function() {
        var html = $('#product-list .row:first').clone();
        html.find('input').val('');
        $('#product-list').append(html);
    });

    $(document).on('click', '.remove-product', function() {
        if ($('#product-list .row').length > 1) {
            $(this).closest('.row').remove();
        }
    });
</script>

@endsection