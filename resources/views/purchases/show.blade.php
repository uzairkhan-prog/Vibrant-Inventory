@extends('layouts.app')

@section('content')

<div class="table-responsive">
    <div class="table-wrapper">
        <div class="table-title">
            <div class="row">
                <div class="col-sm-5">
                    <h2>Purchase <b>Details</b></h2>
                </div>
            </div>
        </div>

        <div class="p-3">
            <p><strong>Supplier:</strong> {{ $purchase->supplier->name }}</p>
            <p><strong>Date:</strong> {{ $purchase->date }}</p>
            <p><strong>Total Amount:</strong> {{ $purchase->total_amount }}</p>

            <h5 class="mt-4">Items:</h5>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->price }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="text-center">
                <a href="{{ route('purchases.index') }}" class="btn btn-dark">Back</a>
            </div>
        </div>

    </div>
</div>

@endsection