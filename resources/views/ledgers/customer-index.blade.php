@extends('layouts.app')

@section('content')

<div class="table-responsive">
    <div class="table-wrapper">

        <div class="table-title">
            <div class="row">
                <div class="col-md-6">
                    <h2>Customer Ledger <b>Management</b></h2>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('customer-ledgers.create') }}" class="btn btn-secondary">
                        <i class="material-icons">&#xE147;</i> <span>Add Entry</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="alert alert-success shadow-sm rounded-3 fs-6 fw-bold">
            <div class="d-flex justify-content-between">
                <span>Total Balance:</span>
                <span>Rs {{ number_format($total_balance, 2) }}</span>
            </div>
        </div>

        @include('ledgers._search-pagination')

        @if($ledgers->count())
        <table class="table table-striped table-hover" id="ledgerTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Balance</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ledgers as $ledger)
                <tr>
                    <td>{{ ($ledgers->currentPage() - 1) * $ledgers->perPage() + $loop->iteration }}</td>
                    <td>{{ \App\Models\Customer::find($ledger->reference_id)?->name }}</td>
                    <td>{{ number_format($ledger->balance, 2) }}</td>
                    <td>{{ $ledger->updated_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <a href="{{ route('customer-ledgers.edit', $ledger) }}" class="btn btn-sm btn-success text-white">Edit</a>
                        <form action="{{ route('customer-ledgers.destroy', $ledger) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete ledger entry?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center mt-3">
            {!! $ledgers->links('pagination::bootstrap-5') !!}
        </div>
        @else
        <div class="alert alert-info text-center">No customer ledger entries found.</div>
        @endif

    </div>
</div>

@include('ledgers._scripts')
@endsection