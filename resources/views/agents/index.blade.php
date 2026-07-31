@extends('layouts.app')

@section('content')

<div class="p-4 bg-white shadow rounded">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary mb-0">Agent Management</h2>
        <div>
            <a href="{{ route('agents.create') }}" class="btn btn-secondary">
                <i class="material-icons me-1">&#xE147;</i> Add Agent
            </a>
        </div>
    </div>

    <!-- Filters Row -->
    <div class="row mb-3 align-items-center">
        <div class="col-md-10 d-flex align-items-center">
            <label class="me-2 fw-semibold">Search:</label>
            <input type="text" id="searchInput" class="form-control w-100" placeholder="Search by name, email or phone">
        </div>
        <div class="col-md-2 d-flex justify-content-end align-items-center">
            <label class="me-2 fw-semibold">Show</label>
            <select id="rowsPerPage" class="form-select w-auto">
                @foreach ([20, 50, 100] as $value)
                <option value="{{ $value }}">{{ $value }}</option>
                @endforeach
            </select>
            <span class="ms-2 fw-semibold">entries</span>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    @if($agents->count())
    <div class="table-responsive">
        <div class="table-wrapper">
            <table class="table table-striped table-hover table-bordered align-middle text-center" id="agentsTable">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Total Sales</th>
                        <th width="240px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($agents as $index => $agent)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $agent->name }}</td>
                        <td>{{ $agent->email }}</td>
                        <td>{{ $agent->phone }}</td>
                        <td class="fw-bold">{{ number_format($agent->sales_sum_total_amount ?? 0, 2) }}</td>
                        <td class="d-flex justify-content-center">
                            <!-- <a href="{{ route('agents.show',$agent->id) }}" class="btn btn-sm btn-info me-1 text-white">
                                Show
                            </a> -->
                            <a href="{{ route('agents.ledger',$agent->id) }}" class="btn btn-sm btn-dark me-1 text-white">
                                Ledger
                            </a>
                            <a href="{{ route('agents.edit',$agent->id) }}" class="btn btn-sm btn-success me-1 text-white">
                                Edit <i class="material-icons">&#xE254;</i>
                            </a>

                            <form action="{{ route('agents.destroy',$agent->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this agent?')">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-sm btn-danger">
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
        No agents found. <a href="{{ route('agents.create') }}" class="text-dark">Create one</a>.
    </div>
    @endif
</div>

<script>
    // Search filter
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const value = this.value.toLowerCase();
        document.querySelectorAll('#agentsTable tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
        });
    });

    // Rows per page (UI only)
    document.getElementById('rowsPerPage').addEventListener('change', function() {
        const selected = this.value;
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', selected);
        window.location.href = url.toString();
    });
</script>

@endsection