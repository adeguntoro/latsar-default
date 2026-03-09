@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Profile') }}</div>

                <div class="card-body">
                    <h5>User Information</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>ID:</strong> {{ $user->id }}</li>
                        <li class="list-group-item"><strong>Name:</strong> {{ $user->name }}</li>
                        <li class="list-group-item"><strong>Email:</strong> {{ $user->email }}</li>
                        <li class="list-group-item"><strong>Roles:</strong> {{ $user->getRoleNames()->implode(', ') }}</li>
                    </ul>

                    <h5 class="mt-4">Profile Information</h5>
                    @if($profile)
                        <ul class="list-group list-group-flush">
                            @foreach($profile->toArray() as $key => $value)
                                <li class="list-group-item"><strong>{{ ucfirst($key) }}:</strong> 
                                    @if(is_array($value))
                                        <pre>{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                    @else
                                        {{ $value }}
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">No profile data available.</p>
                    @endif

                    <div class="mt-3">
                        <a href="{{ route('users.index', ['role' => 'superadmin']) }}" class="btn btn-primary">Back to Users</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection