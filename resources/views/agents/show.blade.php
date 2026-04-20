@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Agent Details</h2>

    <p><strong>Name:</strong> {{ $agent->name }}</p>
    <p><strong>Email:</strong> {{ $agent->email }}</p>
    <p><strong>Phone:</strong> {{ $agent->phone }}</p>
    <p><strong>Address:</strong> {{ $agent->address }}</p>

    <a href="{{ route('agents.index') }}" class="btn btn-secondary">Back</a>
</div>
@endsection