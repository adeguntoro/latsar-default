@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <h3 class="mb-4">Dashboard</h3>

    <div class="row">
        @role('Superadmin')
        {{-- User Management (Superadmin Only) --}}
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Buat postingan baru</h5>
                    <p class="card-text">buat Postingan Baru</p>

                    <a href="{{ route('posts.create') }}"
                       class="btn btn-primary w-100 disabled">
                        Create Post
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Daftar posting</h5>
                    <p class="card-text">Lihat Daftar Postingan</p>

                    <a href="/dashboard/posts"
                       class="btn btn-primary w-100 disabled">
                        Lihat Postingan
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-danger">
                <div class="card-body">
                    <h5 class="card-title text-danger">
                        User Management
                    </h5>
                    <p class="card-text">
                        Manage system users
                    </p>

                    <a href="{{ route('users.index', ['role' => request()->route('role')]) }}"
                       class="btn btn-danger w-100">
                        Manage Users
                    </a>
                </div>
            </div>
        </div>
            
        @else
        {{-- Create Post (All Roles) --}}
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Buat postingan baru</h5>
                    <p class="card-text">buat Postingan Baru</p>

                    <a href="{{ route('posts.create') }}"
                       class="btn btn-primary w-100">
                        Create Post
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Daftar posting</h5>
                    <p class="card-text">Lihat Daftar Postingan</p>
                    {{-- <a href="/dashboard/posts"class="btn btn-primary w-100">Lihat Postingan</a> --}}
                    <a href="{{ url('/dashboard/posts') }}"class="btn btn-primary w-100">Lihat Postingan</a>
                </div>
            </div>
        </div>
        
        @endrole

    </div>

    {{-- Role-based Content Example --}}
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Role-Based Content Example</h5>
                </div>
                <div class="card-body">
                    @php
                        $currentRole = request()->route('role');
                    @endphp
                    
                    <p><strong>Current Role:</strong> <span class="badge bg-primary">{{ $currentRole ?? 'Guest' }}</span></p>
                    
                    {{-- EXAMPLE: Show different content based on role --}}
                    @if($currentRole === 'Superadmin')
                        <div class="alert alert-success">
                            <h6><i class="bi bi-shield-check me-2"></i>Superadmin View</h6>
                            <p class="mb-0">This data is only visible to Superadmin. You have full access to all system features including user management, role assignments, and system configuration.</p>
                        </div>
                    @elseif($currentRole === 'Staff')
                        <div class="alert alert-info">
                            <h6><i class="bi bi-person-workspace me-2"></i>Staff View</h6>
                            <p class="mb-0">Staff members can manage their own posts and view their profile. Limited access to system features.</p>
                        </div>
                    @elseif($currentRole === 'Kasubag')
                        <div class="alert alert-warning">
                            <h6><i class="bi bi-person-badge me-2"></i>Kasubag View</h6>
                            <p class="mb-0">Kasubag (Kepala Sub Bagian) can review posts and manage department content.</p>
                        </div>
                    @elseif($currentRole === 'Komisioner')
                        <div class="alert alert-secondary">
                            <h6><i class="bi bi-person-check me-2"></i>Komisioner View</h6>
                            <p class="mb-0">Komisioner can review and approve content before publication.</p>
                        </div>
                    @else
                        <div class="alert alert-light">
                            <h6><i class="bi bi-person me-2"></i>Default View</h6>
                            <p class="mb-0">Welcome! You are viewing the dashboard with basic access privileges.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- new layout base on role --}}
    @role('Superadmin')
        @include('dashboard.Superadmin.dashboard-data')
    @else
        @include('dashboard.staff.staff-data')
    @endrole

    {{-- Chart.js Pie Charts Section --}}
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Postingan berdasarkan Status</h5>
                    <canvas id="postsByStatusChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Postingan berdasarkan Department</h5>
                    <canvas id="postsByDepartmentChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Postingan berdasarkan Tipe</h5>
                    <canvas id="postsByTypeChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

{{-- Move chart initialization to scripts section --}}
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Posts by Status Pie Chart
        const postsByStatusCtx = document.getElementById('postsByStatusChart')?.getContext('2d');
        if (postsByStatusCtx) {
            const postsByStatusData = {
                labels: @json(array_keys($postsByStatus)),
                datasets: [{
                    data: @json(array_values($postsByStatus)),
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                        '#FF9F40'
                    ],
                    hoverOffset: 4
                }]
            };
            new Chart(postsByStatusCtx, {
                type: 'pie',
                data: postsByStatusData,
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        }

        // Posts by Department Pie Chart
        const postsByDepartmentCtx = document.getElementById('postsByDepartmentChart')?.getContext('2d');
        if (postsByDepartmentCtx) {
            const postsByDepartmentData = {
                labels: @json(array_keys($postsByDepartment)),
                datasets: [{
                    data: @json(array_values($postsByDepartment)),
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                        '#FF9F40'
                    ],
                    hoverOffset: 4
                }]
            };
            new Chart(postsByDepartmentCtx, {
                type: 'pie',
                data: postsByDepartmentData,
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        }
        
        // Posts by Type Pie Chart
        const postsByTypeCtx = document.getElementById('postsByTypeChart')?.getContext('2d');
        if (postsByTypeCtx) {
            const postsByTypeData = {
                labels: @json(array_keys($postsByType)),
                datasets: [{
                    data: @json(array_values($postsByType)),
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',                      
                        '#4BC0C0',
                        '#9966FF',
                        '#FF9F40'
                    ],
                    hoverOffset: 4
                }]
            };
            new Chart(postsByTypeCtx, {
                type: 'pie',
                data: postsByTypeData,
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        }
    });
</script>
@endsection