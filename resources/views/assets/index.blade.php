@extends('layouts.app')

@section('content')

<div class="table-responsive">
    <div class="table-wrapper">
        <div class="table-title">
            <div class="row">
                <div class="col-sm-5">
                    <h2>Assets <b>Management</b></h2>
                </div>
                <div class="col-sm-7 text-end">
                    <a href="{{ route('assets-inventory.create') }}" class="btn btn-secondary">
                        <i class="material-icons">&#xE147;</i> <span>Add Asset</span>
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
        @endif

        @if($assets->count())
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Title</th>
                    <th>Value</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($assets as $asset)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $asset->date }}</td>
                    <td>{{ $asset->title }}</td>
                    <td>{{ $asset->value }}</td>
                    <td>
                        <a href="{{ route('assets-inventory.edit', $asset) }}" class="btn btn-sm btn-warning text-white" title="Edit">
                            <i class="material-icons">&#xE254;</i>
                        </a>
                        <form action="{{ route('assets-inventory.destroy', $asset) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete asset?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                <i class="material-icons">&#xE872;</i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="alert alert-info text-center">No assets found. <a href="{{ route('assets-inventory.create') }}">Create one</a>.</div>
        @endif
    </div>
</div>

@endsection