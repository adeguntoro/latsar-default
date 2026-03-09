<div class="row mt-4">
    
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">Jumlah Postingan</h6>
                        <h2 class="mb-0">{{ $totalPosts }}</h2>
                    </div>
                    <i class="bi bi-file-earmark-text" style="font-size: 48px; opacity: 0.8;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card shadow-sm bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">Jumlah User</h6>
                        <h2 class="mb-0">{{ $totalUsers }}</h2>
                    </div>
                    <i class="bi bi-people" style="font-size: 48px; opacity: 0.8;"></i>
                </div>
            </div>
        </div>
    </div>


    <div class="col-md-4 mb-3">
        <div class="card shadow-sm bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">User Roles</h6>
                        <h2 class="mb-0">{{ count($usersByRole) }}</h2>
                    </div>
                    <i class="bi bi-person-badge" style="font-size: 48px; opacity: 0.8;"></i>
                </div>
            </div>
        </div>
    </div>

</div>


<div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-people-fill me-2"></i>Users by Role</h5>
            </div>
            <div class="card-body">
                @if(count($usersByRole) > 0)
                    <div class="row">
                        @foreach($usersByRole as $role => $count)
                            <div class="col-md-3 mb-3">
                                <div class="card shadow-sm border-primary">
                                    <div class="card-body text-center">
                                        <h6 class="card-title text-uppercase text-muted mb-2">{{ $role }}</h6>
                                        <h2 class="display-4 text-primary mb-0">{{ $count }}</h2>
                                        <p class="text-muted mb-0">
                                            {{ number_format(($count / $totalUsers) * 100, 1) }}% of total users
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-2"></i>No user role data available.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>