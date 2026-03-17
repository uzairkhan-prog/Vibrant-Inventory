@extends('layouts.app')

@section('content')

<div class="p-4 bg-white shadow rounded">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="fw-bold text-primary mb-0">
            Product Ledger
        </h2>

        <a href="{{ route('products.index') }}" class="btn btn-secondary">
            Back
        </a>

    </div>


    <div class="alert alert-info">

        <strong>Product :</strong> {{ $product->name }} <br>

        <strong>Category :</strong> {{ $product->category->name ?? 'Uncategorized' }}

    </div>


    @if($ledger->count())

    <div class="table-responsive">

        <table class="table table-striped table-bordered text-center align-middle">

            <thead class="table-light">

                <tr>

                    <th>Date</th>
                    <th>Invoice</th>
                    <th>Type</th>
                    <th>Party</th>
                    <th>Qty In</th>
                    <th>Qty Out</th>
                    <th>Price</th>
                    <th>Total</th>
                    <!-- <th>Balance</th> -->

                </tr>

            </thead>


            <tbody>
                @foreach($ledger as $row)
                <tr>
                    <!-- Date -->
                    <td>{{ \Carbon\Carbon::parse($row['date'])->format('d-m-Y') }}</td>

                    <!-- Invoice with background color -->
                    <td class="fw-bold"
                        @if($row['type']=='Purchase' ) style="background-color:#cce5ff;"
                        @elseif($row['type']=='Sale' ) style="background-color:#d4edda;"
                        @else style="background-color:#f8d7da;" @endif>
                        {{ $row['invoice_no'] }}
                    </td>

                    <!-- Type Badge with background color -->
                    <td>
                        @if($row['type']=='Purchase')
                        <span class="badge" style="background-color:#cce5ff; color:#000;">Purchase</span>

                        @elseif($row['type']=='Sale')
                        <span class="badge" style="background-color:#d4edda; color:#000;">Sale</span>

                        @else
                        <span class="badge" style="background-color:#f8d7da; color:#000;">Sale Return</span>
                        @endif
                    </td>

                    <!-- Party -->
                    <td>{{ $row['party'] }}</td>

                    <!-- Qty In -->
                    <td class="text-success fw-bold">
                        {{ $row['qty_in'] ?: '-' }}
                    </td>

                    <!-- Qty Out -->
                    <td class="text-danger fw-bold">
                        {{ $row['qty_out'] ?: '-' }}
                    </td>

                    <!-- Price -->
                    <td>{{ number_format($row['price'],2) }}</td>

                    <!-- Total -->
                    <td>{{ number_format($row['total'],2) }}</td>

                    <!-- Balance -->
                    <!-- <td class="fw-bold text-primary">
                        {{ $row['balance'] }}
                    </td> -->
                </tr>
                @endforeach
            </tbody>

        </table>

    </div>

    @else

    <div class="alert alert-warning text-center">

        No ledger records found for this product.

    </div>

    @endif

</div>

@endsection