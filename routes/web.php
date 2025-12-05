<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// --- Import Controllers ---
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\TerminalController;
use App\Http\Controllers\NearbyStopsController;
use App\Http\Controllers\RoutePlannerController;
use App\Http\Controllers\Api\RatingController;

// --- Import Admin Controllers ---
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\BusController as AdminBusController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\RouteController as AdminRouteController;
use App\Http\Controllers\Admin\DriverController as AdminDriverController;
use App\Http\Controllers\Admin\FeedbackController as AdminFeedbackController;
use App\Http\Controllers\Admin\BusStopController;

// --- Import Driver Controllers ---
use App\Http\Controllers\Auth\DriverLoginController;
use App\Http\Controllers\Driver\DashboardController as DriverDashboardController;


/*
|--------------------------------------------------------------------------
| 1. Landing Page & Guest Routes
|--------------------------------------------------------------------------
*/

// 🌐 Landing Page (Welcome/Onboarding)
Route::get('/', function () {
    // If user is already logged in, send them straight to the dashboard
    if (auth()->check()) {
        return redirect()->route('home');
    }
    return view('welcome');
});

// Public FAQ Page
Route::get('/faq', [PageController::class, 'faq'])->name('faq');

// Public Terminal TV Display
Route::get('/terminal-view', [TerminalController::class, 'index'])->name('terminal.view');

// Authentication (Login/Register)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');


/*
|--------------------------------------------------------------------------
| 2. Email Verification Routes
|--------------------------------------------------------------------------
*/
Route::get('/email/verify', function () { return view('auth.verify-email'); })->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) { $request->fulfill(); return redirect('/home'); })->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('/email/verification-notification', function (Request $request) { $request->user()->sendEmailVerificationNotification(); return back()->with('message', 'Verification link sent!'); })->middleware(['auth', 'throttle:6,1'])->name('verification.send');


/*
|--------------------------------------------------------------------------
| 3. Authenticated Passenger Routes (The Main App)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard / Homepage
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Features
    Route::get('/bus/{bus}', [BusController::class, 'show'])->name('bus.show');
    Route::post('/bus/{bus}/review', [RatingController::class, 'storeReview'])->name('bus.review.store');
    Route::get('/nearby-stops', [NearbyStopsController::class, 'index'])->name('stops.nearby');
    Route::get('/route-planner', [RoutePlannerController::class, 'index'])->name('route.planner');

    // Profile & Settings
    Route::get('/mobile/profile', function () {
    return view('mobile_profile');
})->name('mobile.profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [ProfileController::class, 'editPassword'])->name('password.edit');
    Route::patch('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::get('/settings', [PageController::class, 'settings'])->name('settings');

    // Feedback
    Route::get('/feedback', [FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
});


/*
|--------------------------------------------------------------------------
| 4. Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Core Management
    Route::resource('users', AdminUserController::class)->only(['index', 'destroy']); 
    Route::patch('/users/{user}/verify', [AdminUserController::class, 'verify'])->name('users.verify');
    
    Route::resource('buses', AdminBusController::class);
    Route::resource('routes', AdminRouteController::class);
    Route::resource('drivers', AdminDriverController::class);
    
    // Bus Stops (Nested inside Routes)
    Route::resource('routes.stops', BusStopController::class);

    // Feedback
    Route::get('/feedback', [AdminFeedbackController::class, 'index'])->name('feedback.index');
    Route::patch('/feedback/{feedback}/read', [AdminFeedbackController::class, 'markAsRead'])->name('feedback.read');
    Route::delete('/feedback/{feedback}', [AdminFeedbackController::class, 'destroy'])->name('feedback.destroy');
});


/*
|--------------------------------------------------------------------------
| 5. Driver Routes
|--------------------------------------------------------------------------
*/
// Driver Authentication
Route::prefix('driver')->name('driver.')->group(function () {
    Route::get('/login', [DriverLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [DriverLoginController::class, 'login']);
    Route::post('/logout', [DriverLoginController::class, 'logout'])->name('logout');
});

// Driver Dashboard
Route::middleware('auth:drivers')->prefix('driver')->name('driver.')->group(function () {
    Route::get('/dashboard', [DriverDashboardController::class, 'index'])->name('dashboard');
    Route::post('/status', [DriverDashboardController::class, 'updateStatus'])->name('status.update');
});


// Temporary Test Route (Optional)
Route::get('/stops-test', function() {
    return view('stops_prototype');
});

Route::get('/mobile/dashboard', function () {
    return view('mobile_dashboard');
})->middleware('auth')->name('mobile.dashboard');

// 6. Mobile Edit Profile Routes
Route::get('/mobile/profile/edit', function () {
    return view('mobile_edit_profile');
})->middleware('auth')->name('mobile.edit_profile');

Route::post('/mobile/profile/update', function (Illuminate\Http\Request $request) {
    // Basic validation
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email',
    ]);

    // Update the User
    $user = auth()->user();
    $user->name = $request->name;
    $user->email = $request->email;
    
    // Check if your database has these columns before saving!
    if($request->has('phone')) $user->phone = $request->phone;
    if($request->has('passenger_type')) $user->passenger_type = $request->passenger_type;

    $user->save();

    return redirect()->route('mobile.profile');
})->middleware('auth')->name('mobile.updateProfile');

// 7. Nearby Stops Page
Route::get('/mobile/nearby', function () {
    // Pass the stops to the view so Javascript can use them
    $stops = \Illuminate\Support\Facades\DB::table('bus_stops')->get();
    return view('nearby_stops', ['stops' => $stops]);
})->middleware('auth')->name('mobile.nearby');

Route::get('/mobile/planner', function () {
    return view('mobile_route_planner');
})->middleware('auth')->name('mobile.planner');