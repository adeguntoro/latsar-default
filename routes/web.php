<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\UserManageController;
use Illuminate\Support\Facades\Route;

Route::get('/welcome', function () {
    return view('welcome');
});



// Route::get('/', function () {
//     return view('search', ['posts' => collect([]), 'query' => '']);
// });

// Route::get('/search', [PostController::class, 'search'])->name('search');


Auth::routes(
    ['register' => false, 'reset' => false, 'verify' => false]
);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/table', function () {
    return view('tabel');
});



Route::resource('/dashboard/posts', PostController::class);
Route::get('posts/{post}/download', [PostController::class, 'download'])->name('posts.download')->middleware('signed');;

// Document routes
Route::resource('/dashboard/documents', DocumentController::class);
Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');

//search
Route::get('/', [App\Http\Controllers\SearchController::class, 'index'])->name('search');
Route::get('/search/results', [App\Http\Controllers\SearchController::class, 'results'])->name('search.results');


//route for login

// // Change Password Routes (outside middleware for access from navbar)
// Route::middleware(['auth'])->group(function () {
//     Route::get('/change-password', function () {
//         return view('profile.change-password');
//     })->name('password.form');
//     
//     Route::post('/change-password', [UserManageController::class, 'changePassword'])->name('password.change');
// });

// My Profile routes (accessible to any authenticated user)
Route::middleware(['auth', 'ensure.single.session'])->group(function () {
    Route::get('/my-profile', [UserManageController::class, 'myProfile'])->name('profile.view');
    Route::get('/my-profile/edit', [UserManageController::class, 'editProfile'])->name('profile.edit');
    Route::put('/my-profile', [UserManageController::class, 'updateProfile'])->name('profile.update');
});

Route::middleware(['auth', 'ensure.single.session'])
    ->prefix('{role}')
    ->group(function () {

        // OLD CODE: Using closure - replaced with DashboardController@index for chart data
        // Route::get('/dashboard', function ($role) {
        //     abort_unless(auth()->user()->hasRole($role), 403);
        //     
        //     return view('dashboard.index');
        //     
        //     // return response()->json([
        //     //     'status' => 'success',
        //     //     'user' => auth()->user()->name,
        //     //     'role' => $role,
        //     //     'message' => 'Access granted to ' . $role . ' dashboard'
        //     // ]);
        // 
        // });

        Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index']);

        // Change Password Form
        Route::get('/dashboard/change-password', function ($role) {
            abort_unless(auth()->user()->hasRole($role), 403);
            return view('dashboard.change-password');
        })->name('password.form');

        // Change Password Submit
        Route::post('/dashboard/change-password',
            [UserManageController::class, 'changePassword']
        )->name('password.update');

        // User Management (Superadmin only) - Resource routes
        Route::resource('dashboard/manage-user', UserManageController::class)
            ->parameters(['manage-user' => 'user'])
            ->only(['index', 'store', 'destroy'])
            ->names([
                'index' => 'users.index',
                'store' => 'users.store',
                'destroy' => 'users.destroy',
            ]);
        
        // Show user route (explicit)
        Route::get('/dashboard/manage-user/{user}', [UserManageController::class, 'show'])
            ->where('user', '[0-9]+')
            ->name('users.show');

        // View user profile route (Superadmin can view any, users can view own via myProfile)
        Route::get('/dashboard/manage-user/{user}/profile', [UserManageController::class, 'viewProfile'])
            ->where('user', '[0-9]+')
            ->name('users.profile.view');

        // Update user route (explicit)
        Route::put('/dashboard/manage-user/{user}', [UserManageController::class, 'update'])
            ->where('user', '[0-9]+')
            ->name('users.update');

        // Delete user route (explicit - overriding resource)
        Route::delete('/dashboard/manage-user/{user}', [UserManageController::class, 'destroy'])
            ->where('user', '[0-9]+')
            ->name('users.destroy');

        Route::get('/dashboard/request-file', [PostController::class, 'requestFile'])->name('posts.request-file');
        
        /*
        Route::get('/dashboard/request-file', function () {
            //return '1'; //view('posts.request-file');
            return view('posts.request-file');
        });
        */

    });//->middleware(['role:Superadmin']);

    
// Post by slug route (must be at the end)
Route::get('/{slug}', [PostController::class, 'showBySlug'])->name('posts.slug');