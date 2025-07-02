@extends('layouts.app')

@section('content')

<div class="table-responsive">
    <div class="table-wrapper">
        <div class="table-title">
            <div class="row">
                <div class="col-sm-5">
                    <h2>Supplier <b>Create</b></h2>
                </div>
            </div>
        </div>

        <div class="p-3">
            <form method="POST" action="{{ route('suppliers.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input name="name" id="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea name="address" id="address" class="form-control"></textarea>
                </div>

                <div class="mb-3">
                    <label for="balance" class="form-label">Balance</label>
                    <input type="number" step="0.01" name="balance" id="balance" class="form-control">
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="{{ route('suppliers.index') }}" class="btn btn-dark">Back</a>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection