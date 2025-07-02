@extends('layouts.app')

@section('content')

<div class="table-responsive">
    <div class="table-wrapper">
        <div class="table-title">
            <div class="row">
                <div class="col-sm-5">
                    <h2>Sale <b>Details</b></h2>
                </div>
            </div>
        </div>

        <div class="p-3">
            <p><strong>Customer:</strong> {{ $sale->customer->name }}</p>
            <p><strong>Date:</strong> {{ $sale->date }}</p>
            <p><strong>Total Amount:</strong> {{ $sale->total_amount }}</p>

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
                    @foreach($sale->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->price }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="text-center">
                <a href="{{ route('sales.index') }}" class="btn btn-dark">Back</a>
            </div>
        </div>

    </div>
</div>

@endsection