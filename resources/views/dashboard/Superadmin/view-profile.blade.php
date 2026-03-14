{{-- 
    OLD CODE: Previous view that used different variable names
--}}

@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <!-- Profile Header -->
                    <div class="row align-items-center mb-4 pb-3 border-bottom">
                        <div class="col-md-3 text-center">
                            @if($profile && $profile->avatar)
                                <img src="{{ asset('storage/'.$profile->avatar) }} {{-- $profile->avatar --}}" alt="Profile Picture" class="rounded-circle img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto" style="width: 150px; height: 150px;">
                                    <i class="bi bi-person-fill text-white" style="font-size: 64px;"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <h3 class="mb-1">{{ $user->name }}</h3>
                            <p class="text-muted mb-2">{{ $user->email }}</p>
                            <div class="mb-2">
                                @foreach($user->getRoleNames() as $role)
                                    <span class="badge bg-primary">{{ $role }}</span>
                                @endforeach
                            </div>
                            <p class="text-muted mb-0">
                                <i class="bi bi-calendar3 me-2"></i>Joined {{ $user->created_at ? $user->created_at->format('F d, Y') : 'N/A' }}
                            </p>
                        </div>
                    </div>

                    <!-- Profile Details -->
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="mb-3">Profile Information</h5>
                        </div>
                        
                        @if($profile)
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold">Bio</label>
                                    <div class="form-control bg-light" style="min-height: 120px; white-space: pre-wrap;">{{ $profile->bio ?? 'No bio available' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold">Phone</label>
                                    <div class="form-control bg-light">
                                        <i class="bi bi-telephone me-2"></i>{{ $profile->phone ?? 'N/A' }}
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold">Address</label>
                                    <div class="form-control bg-light">
                                        <i class="bi bi-geo-alt me-2"></i>{{ $profile->address ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="col-md-12">
                                <div class="alert alert-info mb-0">
                                    <i class="bi bi-info-circle me-2"></i>No profile information available.
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-4 d-flex justify-content-between align-items-center">
                        <div>
                            @can('viewAny', App\Models\User::class)
                                <a href="{{ route('users.index', ['role' => 'superadmin']) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Back to Users
                                </a>
                            @else
                                <a href="{{ url('/' . auth()->user()->roles->first()->name ?? 'user') . '/dashboard' }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                                </a>
                            @endcan
                        </div>
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                            <i class="bi bi-pencil me-2"></i>Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection