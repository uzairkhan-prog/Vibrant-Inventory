@extends('layouts.app') <!-- Use your main layout -->

@section('title', 'Profile') <!-- Optional page title -->

@section('content')
<div class="container py-12">

    <div class="space-y-6">

        <!-- Update Profile Information -->
        <div class="p-4 bg-white shadow rounded-lg">
            <div class="max-w-xl mx-auto">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Update Password -->
        <div class="p-4 bg-white shadow rounded-lg">
            <div class="max-w-xl mx-auto">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Delete User Account -->
        <div class="p-4 bg-white shadow rounded-lg">
            <div class="max-w-xl mx-auto">
                @include('profile.partials.delete-user-form')
            </div>
        </div>

    </div>
</div>
@endsection