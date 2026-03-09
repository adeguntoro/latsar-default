{{-- 
    Edit and Delete User View
    Only accessible by Superadmin role
--}}

@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('users.index', ['role' => $role]) }}">User Management</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Edit User</li>
                </ol>
            </nav>

            <!-- Edit User Form -->
            <div class="card shadow">
                <div class="card-header bg-warning text-dark fw-bold">
                    <i class="bi bi-pencil-square"></i> Edit User
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('users.update', ['role' => $role, 'user' => $userData->id]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- User Name -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">User Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $userData->name) }}" placeholder="Enter user name" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $userData->email) }}" placeholder="Enter email address" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- New Password (Optional) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">New Password <span class="text-muted">(leave blank to keep current)</span></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                   placeholder="Enter new password (min 8 characters)">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm New Password -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control" 
                                   placeholder="Confirm new password">
                        </div>

                        <!-- Current Role (Display) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Current Role</label>
                            <div class="form-control-plaintext">
                                @forelse($userData->roles as $roleItem)
                                    <span class="badge bg-info">{{ ucfirst($roleItem->name) }}</span>
                                @empty
                                    <span class="badge bg-secondary">No Role</span>
                                @endforelse
                            </div>
                        </div>

                        <!-- Select New Role -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Change Role</label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="" selected disabled>Select a role</option>
                                @forelse($roles as $roleItem)
                                    <option value="{{ $roleItem->name }}" 
                                            {{ $userData->hasRole($roleItem->name) ? 'selected' : '' }}>
                                        {{ ucfirst($roleItem->name) }}
                                    </option>
                                @empty
                                    <option value="" disabled>No roles available</option>
                                @endforelse
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Current Permissions (Display) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Current Permissions</label>
                            <div class="form-control-plaintext">
                                @forelse($userData->permissions as $permission)
                                    <span class="badge bg-secondary">{{ ucfirst($permission->name) }}</span>
                                @empty
                                    <span class="text-muted">No specific permissions</span>
                                @endforelse
                            </div>
                        </div>

                        <!-- Select Permissions -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Update Permissions</label>
                            <div class="border p-3 rounded" style="max-height: 300px; overflow-y: auto;">
                                @forelse($permissions as $permission)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" 
                                               value="{{ $permission->name }}" 
                                               id="perm_{{ $permission->id }}"
                                               {{ $userData->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="perm_{{ $permission->id }}">
                                            {{ ucfirst($permission->name) }}
                                        </label>
                                    </div>
                                @empty
                                    <p class="text-muted">No permissions available</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- User Info (Read-only) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">User ID</label>
                            <input type="text" class="form-control disablw" value="{{ $userData->id }}" disabled readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Created At</label>
                            <input type="text" class="form-control" value="{{ $userData->created_at->format('Y-m-d H:i:s') }}" disabled readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Last Updated</label>
                            <input type="text" class="form-control" value="{{ $userData->updated_at->format('Y-m-d H:i:s') }}" disabled readonly>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Update User
                                </button>
                                <a href="{{ route('users.index', ['role' => $role]) }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Cancel
                                </a>
                            </div>
                            
                            <!-- Delete Button -->
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="bi bi-trash"></i> Delete User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle"></i> Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this user?</p>
                <div class="alert alert-warning">
                    <strong>User:</strong> {{ $userData->name }}<br>
                    <strong>Email:</strong> {{ $userData->email }}
                </div>
                <p class="text-danger fw-bold">This action cannot be undone!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>
                <form action="{{ route('users.destroy', ['role' => $role, 'user' => $userData->id]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Delete User
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
