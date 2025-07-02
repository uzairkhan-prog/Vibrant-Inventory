@extends('layouts.app')

@section('content')

<div class="table-responsive">
    <div class="table-wrapper">
        <div class="table-title">
            <div class="row">
                <div class="col-sm-5">
                    <h2>Asset <b>Create</b></h2>
                </div>
            </div>
        </div>

        <div class="p-3">
            <form method="POST" action="{{ route('assets-inventory.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input name="title" id="title" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="value" class="form-label">Value</label>
                    <input type="number" step="0.01" name="value" id="value" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" name="date" id="date" class="form-control" required>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="{{ route('assets-inventory.index') }}" class="btn btn-dark">Back</a>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection