@extends('layouts.app')

@section('content')
<div class="container py-5">

    <!-- Filters -->
    <div class="card mb-4 shadow">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Summary -->
    <div id="reportContent">
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card shadow-sm text-center" style="cursor:pointer;" data-bs-toggle="modal" data-bs-target="#salesModal">
                    <div class="card-body">
                        <h6 class="text-muted">Total Sales</h6>
                        <h4 class="fw-bold">{{ number_format($totalSales, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm text-center" style="cursor:pointer;" data-bs-toggle="modal" data-bs-target="#purchasesModal">
                    <div class="card-body">
                        <h6 class="text-muted">Total Purchases</h6>
                        <h4 class="fw-bold">{{ number_format($totalPurchases, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm text-center">
                    <div class="card-body">
                        <h6 class="text-muted">Customers</h6>
                        <h4 class="fw-bold">{{ $customerCount }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('reports.modals')
</div>
@endsection