@extends('layouts.app')

@section('content')

<div class="p-4 bg-white shadow rounded">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary mb-0">Assets Management</h2>
        <div>
            <a href="{{ route('assets-inventory.create') }}" class="btn btn-secondary">
                <i class="material-icons me-1">&#xE147;</i> Add Asset
            </a>
        </div>
    </div>

    @php
    $subtotal = $assets->sum('value');
    @endphp
    <div class="alert alert-success shadow-sm rounded-3 fs-6 fw-bold mb-4">
        <div class="d-flex justify-content-between">
            <span>Total Asset Value:</span>
            <span>Rs {{ number_format($subtotal, 2) }}</span>
        </div>
    </div>

    <!-- Filters Row -->
    <div class="row mb-3 align-items-center">
        <div class="col-md-10 d-flex align-items-center">
            <label class="me-2 fw-semibold">Search:</label>
            <input type="text" id="searchInput" class="form-control w-100" placeholder="Search by title, value or date">
        </div>
        <div class="col-md-2 d-flex justify-content-end align-items-center">
            <label class="me-2 fw-semibold">Show</label>
            <select id="rowsPerPage" class="form-select w-auto">
                @foreach ([20, 50, 100] as $value)
                <option value="{{ $value }}" {{ request('per_page') == $value ? 'selected' : '' }}>{{ $value }}</option>
                @endforeach
            </select>
            <span class="ms-2 fw-semibold">entries</span>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    @if($assets->count())
    <div class="table-responsive">
        <div class="table-wrapper">
            <table class="table table-striped table-hover table-bordered align-middle text-center" id="assetsTable">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Value</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($assets as $index => $asset)
                    <tr>
                        <td>{{ $asset->id }}</td>
                        <td>{{ $asset->title }}</td>
                        <td>Rs {{ number_format($asset->value, 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($asset->date)->format('Y-m-d') }}</td>
                        <td class="d-flex justify-content-center">
                            <a href="{{ route('assets-inventory.edit', $asset) }}" class="btn btn-sm btn-success me-1 text-white" title="Edit">
                                Edit <i class="material-icons">&#xE254;</i>
                            </a>
                            <form action="{{ route('assets-inventory.destroy', $asset) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete asset?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                    Delete <i class="material-icons">&#xE872;</i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="alert alert-info text-center mt-4">
        No assets found. <a href="{{ route('assets-inventory.create') }}" class="text-dark">Create one</a>.
    </div>
    @endif
</div>

<script>
    // Search input
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const value = this.value.toLowerCase();
        document.querySelectorAll('#assetsTable tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
        });
    });

    // Rows per page
    document.getElementById('rowsPerPage').addEventListener('change', function() {
        const selected = this.value;
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', selected);
        window.location.href = url.toString();
    });
</script>

@endsection