<div class="row mt-4">
    
    <div class="col-md-6 mb-3">
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

    <div class="col-md-6 mb-3">
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

</div>