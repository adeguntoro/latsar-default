{{-- 
    OLD CODE: Previous view that used different variable names
    @extends('layouts.app')
    @section('content')
    ... old content ...
    @endsection
--}}

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Profile') }}</div>

                <div class="card-body">
                    <h5>User Information</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small">ID</label>
                                <div class="form-control bg-light">{{ $user->id }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Name</label>
                                <div class="form-control bg-light">{{ $user->name }}</div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Email</label>
                                <div class="form-control bg-light">{{ $user->email }}</div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Roles</label>
                                <div class="form-control bg-light">{{ $user->getRoleNames()->implode(', ') }}</div>
                            </div>
                        </div>
                    </div>

                    <h5 class="mt-4">Profile Information</h5>
                    @if($profile)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted small">Bio</label>
                                    <div class="form-control bg-light">{{ $profile->bio ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted small">Phone</label>
                                    <div class="form-control bg-light">{{ $profile->phone ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label text-muted small">Address</label>
                                    <div class="form-control bg-light">{{ $profile->address ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted small">Avatar URL</label>
                                    <div class="form-control bg-light">{{ $profile->avatar ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted small">Created At</label>
                                    <div class="form-control bg-light">{{ $profile->created_at ? $profile->created_at->format('d M Y H:i') : 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-muted">No profile data available.</p>
                    @endif

                    <div class="mt-3">
                        {{-- Show "Back to Users" only for Superadmin --}}
                        @can('viewAny', App\Models\User::class)
                            <a href="{{ route('users.index', ['role' => 'superadmin']) }}" class="btn btn-primary">Back to Users</a>
                        @else
                            <a href="{{ url('/' . auth()->user()->roles->first()->name ?? 'user') . '/dashboard' }}" class="btn btn-primary">Back to Dashboard</a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
