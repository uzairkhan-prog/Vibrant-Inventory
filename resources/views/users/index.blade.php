@extends('layouts.app')

@section('content')

<div class="p-4 bg-white shadow rounded">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary mb-0">User Management</h2>

        <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="material-icons me-1">&#xE147;</i> Add User
        </button>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    @if($users->count())
    <div class="table-responsive">
        <div class="table-wrapper">

            <table class="table table-striped table-hover table-bordered align-middle text-center" id="usersTable">

                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                    @php
                    // Put admins first, editors after
                    $sortedUsers = $users->sortByDesc(function($user) {
                    return $user->role === 'admin' ? 1 : 0;
                    });
                    @endphp

                    @foreach($sortedUsers as $index => $user)

                    <tr class="{{ $user->role == 'admin' ? 'table-primary fw-bold' : '' }}">

                        <td>{{ $index + 1 }}</td>

                        <td>
                            {{ $user->name }}
                            @if($user->role == 'admin')
                            <span class="badge bg-primary ms-1">Admin</span>
                            @endif
                        </td>

                        <td>{{ $user->email }}</td>

                        <td>
                            @if($user->role == 'admin')
                            <span class="badge bg-success">Admin</span>
                            @else
                            <span class="badge bg-secondary">Editor</span>
                            @endif
                        </td>

                        <td class="d-flex justify-content-center">

                            <button class="btn btn-sm btn-success me-1 text-white"
                                data-bs-toggle="modal"
                                data-bs-target="#editUser{{ $user->id }}">
                                Edit <i class="material-icons">&#xE254;</i>
                            </button>

                            <form action="{{ route('users.destroy',$user->id) }}" method="POST"
                                class="d-inline"
                                onsubmit="return confirm('Delete this user?')">

                                @csrf
                                @method('DELETE')

                                <button class="btn btn-sm btn-danger">
                                    Delete <i class="material-icons">&#xE872;</i>
                                </button>

                            </form>

                        </td>

                    </tr>

                    {{-- EDIT MODAL --}}

                    <div class="modal fade" id="editUser{{ $user->id }}">

                        <div class="modal-dialog">

                            <div class="modal-content">

                                <form action="{{ route('users.update',$user->id) }}" method="POST">

                                    @csrf
                                    @method('PUT')

                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">

                                        <input type="text"
                                            name="name"
                                            class="form-control mb-2"
                                            value="{{ $user->name }}"
                                            placeholder="Name">


                                        <input type="email"
                                            name="email"
                                            class="form-control mb-2"
                                            value="{{ $user->email }}"
                                            placeholder="Email">


                                        <input type="password"
                                            name="password"
                                            class="form-control mb-2"
                                            placeholder="New Password (optional)">


                                        <select name="role" class="form-control">

                                            <option value="admin" {{ $user->role=='admin'?'selected':'' }}>
                                                Admin
                                            </option>

                                            <option value="editor" {{ $user->role=='editor'?'selected':'' }}>
                                                Editor
                                            </option>

                                        </select>

                                    </div>

                                    <div class="modal-footer">

                                        <button class="btn btn-success">
                                            Update
                                        </button>

                                    </div>

                                </form>

                            </div>

                        </div>

                    </div>

                    @endforeach

                </tbody>

            </table>

        </div>
    </div>

    @else

    <div class="alert alert-info text-center mt-4">
        No users found. Click <strong>Add User</strong> to create one.
    </div>

    @endif

</div>

{{-- ADD USER MODAL --}}

<div class="modal fade" id="addUserModal">

    <div class="modal-dialog">

        <div class="modal-content">

            <form action="{{ route('users.store') }}" method="POST">

                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="text"
                        name="name"
                        class="form-control mb-2"
                        placeholder="Name">


                    <input type="email"
                        name="email"
                        class="form-control mb-2"
                        placeholder="Email">


                    <input type="password"
                        name="password"
                        class="form-control mb-2"
                        placeholder="Password">


                    <select name="role" class="form-control">

                        <option value="admin">Admin</option>

                        <option value="editor">Editor</option>

                    </select>

                </div>

                <div class="modal-footer">

                    <button class="btn btn-primary">
                        Save User
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection