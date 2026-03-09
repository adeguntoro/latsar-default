{{-- 
    manage user base on their role and permission
    <ul>
        <li>Superadmin: can manage all users, roles, and permissions</li>
        <li>Staff: can manage internal posts</li>
        <li>Kasubag: can manage confidential files</li>
        <li>Komisioner: can manage confidential files</li>
    </ul>
--}}

@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <!-- Create User Form -->
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="bi bi-person-plus"></i> Create New User
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

                    @php
                        $currentUserRole = request()->route('role');
                    @endphp
                    <form action="{{ route('users.store', ['role' => $currentUserRole]) }}" method="POST">
                        @csrf

                        <!-- User Name -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">User Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" placeholder="Enter user name" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" placeholder="Enter email address" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Password</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                   placeholder="Enter password (min 8 characters)" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" 
                                   placeholder="Confirm password" required>
                        </div>

                        <!-- Select Role -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Role</label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="" selected disabled>Select a role</option>
                                @forelse($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @empty
                                    <option value="" disabled>No roles available</option>
                                @endforelse
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Select Permissions -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Permissions</label>
                            <div class="border p-3 rounded" style="max-height: 300px; overflow-y: auto;">
                                @forelse($permissions as $permission)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" 
                                               value="{{ $permission->name }}" id="perm_{{ $permission->id }}">
                                        <label class="form-check-label" for="perm_{{ $permission->id }}">
                                            {{ ucfirst($permission->name) }}
                                        </label>
                                    </div>
                                @empty
                                    <p class="text-muted">No permissions available</p>
                                @endforelse
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-circle"></i> Create User
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Users List -->
        <div class="col-md-7">
            <div class="card shadow">
                <div class="card-header bg-secondary text-white fw-bold">
                    <i class="bi bi-people"></i> Users List
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            {{-- OLD CODE: Display role as badge --}}
                                            {{--
                                            @forelse($user->roles as $role)
                                                <span class="badge bg-info">{{ ucfirst($role->name) }}</span>
                                            @empty
                                                <span class="badge bg-secondary">No Role</span>
                                            @endforelse
                                            --}}
                                            
                                            {{-- NEW CODE: Dropdown select for role change with auto-update --}}
                                            <select class="form-select form-select-sm role-select" 
                                                    style="width: auto; display: inline-block; min-width: 150px;"
                                                    data-user-id="{{ $user->id }}">
                                                <option value="__no_role__" {{ $user->roles->isEmpty() ? 'selected' : '' }}>
                                                    No Role
                                                </option>
                                                @foreach($roles as $role)
                                                    <option value="{{ $role->name }}" 
                                                            {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                                        {{ ucfirst($role->name) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="spinner-border spinner-border-sm text-primary role-spinner-{{ $user->id }}" 
                                                 style="display: none; width: 1rem; height: 1rem;"></div>
                                        </td>
                                        <td>
                                            <a href="{{ route('users.show', ['role' => $currentUserRole, 'user' => $user->id]) }}" class="btn btn-sm btn-warning" data-user-id="{{ $user->id }}">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            {{-- edit new view, instead of delete it directly it will show to new views
                                            <form action="{{ route('users.destroy', ['role' => $currentUserRole, 'user' => $user->id]) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                            --}}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            No users found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Role change auto-update functionality
        const roleSelects = document.querySelectorAll('.role-select');
        
        roleSelects.forEach(select => {
            select.addEventListener('change', function() {
                const userId = this.getAttribute('data-user-id');
                let newRole = this.value;
                const spinner = document.querySelector(`.role-spinner-${userId}`);
                
                if (!userId) return;
                
                // Show loading spinner
                if (spinner) {
                    spinner.style.display = 'inline-block';
                }
                
                // Convert "__no_role__" to empty string for removing all roles
                const rolePayload = newRole === '__no_role__' ? '' : newRole;
                
                // Send AJAX request to update user role
                fetch(`/dashboard/manage-user/${userId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name: '', // We'll only update role, not name
                        email: '',
                        role: rolePayload,
                        permissions: []
                    })
                })
                .then(response => response.json())
                .then(data => {
                    // Hide spinner
                    if (spinner) {
                        spinner.style.display = 'none';
                    }
                    
                    if (data.success || data.message) {
                        // Show success message
                        const roleDisplay = newRole === '__no_role__' ? 'No Role' : newRole;
                        showAlert('success', `User role updated to ${roleDisplay} successfully`);
                    } else if (data.errors) {
                        // Show error and revert selection
                        showAlert('danger', Object.values(data.errors).join(', '));
                        // Reload page to reset to original state
                        setTimeout(() => location.reload(), 1500);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (spinner) {
                        spinner.style.display = 'none';
                    }
                    showAlert('danger', 'Failed to update user role');
                    setTimeout(() => location.reload(), 1500);
                });
            });
        });
        
        // Helper function to show alert
        function showAlert(type, message) {
            // Remove existing alerts
            const existingAlert = document.querySelector('.role-update-alert');
            if (existingAlert) {
                existingAlert.remove();
            }
            
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show role-update-alert`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            // Insert after the card header
            const cardBody = document.querySelector('.card-body');
            if (cardBody) {
                cardBody.insertBefore(alertDiv, cardBody.firstChild);
            }
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
    });
</script>
@endsection