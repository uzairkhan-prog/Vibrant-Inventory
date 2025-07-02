@extends('layouts.app')

@section('content')

<div class="table-responsive">
    <div class="table-wrapper">
        <div class="table-title">
            <div class="row">
                <div class="col-sm-5">
                    <h2>Category <b>Create</b></h2>
                </div>
            </div>
        </div>

        <div class="p-3">
            <form method="POST" action="{{ route('categories.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="{{ route('categories.index') }}" class="btn btn-dark">Back</a>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection
