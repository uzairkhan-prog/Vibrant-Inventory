@extends('layouts.app')

@section('content')

<div class="table-responsive">
    <div class="table-title">
        <div class="row">
            <div class="col-sm-5">
                <h2>Agent <b>Edit</b></h2>
            </div>
        </div>
    </div>

    <div class="p-3">
        <form method="POST" action="{{ route('agents.update', $agent->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input name="name" id="name" class="form-control" value="{{ old('name', $agent->name) }}" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input name="email" id="email" type="email" class="form-control" value="{{ old('email', $agent->email) }}" required>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input name="phone" id="phone" class="form-control" value="{{ old('phone', $agent->phone) }}">
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input name="address" id="address" class="form-control" value="{{ old('address', $agent->address) }}">
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('agents.index') }}" class="btn btn-dark">Back</a>
            </div>
        </form>
    </div>

</div>

@endsection