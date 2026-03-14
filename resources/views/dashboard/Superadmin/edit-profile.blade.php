{{-- Edit Profile Page --}}

@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Profile</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Profile Picture Preview -->
                        <div class="row align-items-center mb-4 pb-3 border-bottom">
                            <div class="col-md-3 text-center">
                                @if($profile && $profile->avatar)
                                    <img src="{{-- $profile->avatar --}}{{ asset('storage/'.$profile->avatar) }}" alt="Current Avatar" class="rounded-circle img-thumbnail" style="width: 120px; height: 120px; object-fit: cover;" id="avatar-preview">
                                @else
                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto" style="width: 120px; height: 120px;">
                                        <i class="bi bi-person-fill text-white" style="font-size: 48px;"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-9">
                                <h4 class="mb-1">{{ $user->name }}</h4>
                                <p class="text-muted mb-2">{{ $user->email }}</p>
                                <div class="mb-2">
                                    @foreach($user->getRoleNames() as $role)
                                        <span class="badge bg-primary">{{ $role }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Edit Fields -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="bio" class="form-label">Bio</label>
                                    <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="5" maxlength="1000">{{ old('bio', $profile->bio ?? '') }}</textarea>
                                    @error('bio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $profile->phone ?? '') }}" maxlength="20">
                                    </div>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3" maxlength="500">{{ old('address', $profile->address ?? '') }}</textarea>
                                    </div>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- OLD CODE: Profile Picture URL field (replaced with file upload) --}}
                                {{--
                                <div class="mb-3">
                                    <label for="avatar" class="form-label">Profile Picture URL</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                                        <input type="url" class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar" value="{{ old('avatar', $profile->avatar ?? '') }}">
                                    </div>
                                    <small class="text-muted">Enter a URL to an image</small>
                                    @error('avatar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                --}}

                                {{-- NEW CODE: Profile Picture File Upload (JPG only) --}}
                                <div class="mb-3">
                                    <label for="avatar_file" class="form-label">Profile Picture (JPG only)</label>
                                    <input type="file" class="form-control @error('avatar_file') is-invalid @enderror" id="avatar_file" name="avatar_file" accept=".jpg,.jpeg,.JPG,.JPEG">
                                    <small class="text-muted">Upload a JPG image (max 2MB)</small>
                                    @error('avatar_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="mt-4 d-flex justify-content-between">
                            <a href="{{ route('profile.view') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Live preview for avatar file upload
    document.getElementById('avatar_file')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('avatar-preview');
        
        if (file && preview) {
            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg'];
            if (!allowedTypes.includes(file.type)) {
                alert('Only JPG images are allowed');
                e.target.value = '';
                return;
            }
            
            // Validate file size (2MB max)
            if (file.size > 2048 * 1024) {
                alert('File size must be less than 2MB');
                e.target.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                
                // If preview doesn't exist, create it
                if (!preview.parentElement.querySelector('img')) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'Avatar Preview';
                    img.className = 'rounded-circle img-thumbnail';
                    img.style = 'width: 120px; height: 120px; object-fit: cover;';
                    img.id = 'avatar-preview';
                    preview.parentElement.innerHTML = '';
                    preview.parentElement.appendChild(img);
                }
            };
            reader.readAsDataURL(file);
        }
    });

    // Old code: Live preview for avatar URL
    // document.getElementById('avatar')?.addEventListener('input', function(e) {
    //     const preview = document.getElementById('avatar-preview');
    //     if (preview) {
    //         if (e.target.value) {
    //             preview.src = e.target.value;
    //             preview.onerror = function() {
    //                 preview.style.display = 'none';
    //             };
    //         } else {
    //             preview.style.display = 'none';
    //         }
    //     }
    // });
</script>
@endsection