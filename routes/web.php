<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// --- Import all necessary controllers ---
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AuthController; // Assuming this is your login controller
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\TerminalController;
use App\Http\Controllers\NearbyStopsController;
use App\Http\Controllers\RoutePlannerController; 

//Nearby Stops Controller
Route::get('/nearby-stops', [NearbyStopsController::class, 'index'])->name('stops.nearby');

// --- Import Admin Controllers with Aliases where needed ---
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\BusController as AdminBusController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\RouteController as AdminRouteController;
use App\Http\Controllers\Admin\DriverController as AdminDriverController;
use App\Http\Controllers\Admin\FeedbackController as AdminFeedbackController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Admin\BusStopController;

// --- Import Driver Controllers with Aliases where needed ---
use App\Http\Controllers\Auth\DriverLoginController;
use App\Http\Controllers\Driver\DashboardController as DriverDashboardController;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
| These routes are accessible to everyone.
*/

// The very first page of your website
Route::get('/', function () {
    return view('welcome'); // Or redirect to login: return redirect()->route('login');
});

// The page to view details of a single bus
Route::post('/bus/{bus}/review', [RatingController::class, 'storeReview'])->middleware('auth');
Route::get('/bus/{bus}', [BusController::class, 'show'])->name('bus.show');

// Public Feedback Form
Route::get('/feedback', [FeedbackController::class, 'create'])->name('feedback.create');
Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');

// Public FAQ Page
Route::get('/faq', [PageController::class, 'faq'])->name('faq');

// Public Terminal TV Display
Route::get('/terminal-view', [TerminalController::class, 'index'])->name('terminal.view');


/*
|--------------------------------------------------------------------------
| User Authentication Routes
|--------------------------------------------------------------------------
| Routes for passenger login, registration, and email verification.
*/

Route::middleware('guest')->group(function () {
    // We assume you have an AuthController for login logic similar to the RegisterController
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Email Verification Routes
Route::get('/email/verify', function () { return view('auth.verify-email'); })->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) { $request->fulfill(); return redirect('/home'); })->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('/email/verification-notification', function (Request $request) { $request->user()->sendEmailVerificationNotification(); return back()->with('message', 'Verification link sent!'); })->middleware(['auth', 'throttle:6,1'])->name('verification.send');


/*
|--------------------------------------------------------------------------
| Authenticated Passenger Routes
|--------------------------------------------------------------------------
| Routes that require a passenger to be logged in and verified.
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Profile Management
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [ProfileController::class, 'editPassword'])->name('password.edit');
    Route::patch('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/settings', [PageController::class, 'settings'])->name('settings');
});


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| All routes in this group are prefixed with /admin and require admin access.
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('buses', AdminBusController::class);
    Route::resource('users', AdminUserController::class)->only(['index', 'destroy']); // Only index and destroy
    Route::patch('/users/{user}/verify', [AdminUserController::class, 'verify'])->name('users.verify');
    Route::resource('routes', AdminRouteController::class);
    Route::resource('drivers', AdminDriverController::class);
    Route::get('/feedback', [AdminFeedbackController::class, 'index'])->name('feedback.index');
    Route::patch('/feedback/{feedback}/read', [AdminFeedbackController::class, 'markAsRead'])->name('feedback.read');
    Route::delete('/feedback/{feedback}', [AdminFeedbackController::class, 'destroy'])->name('feedback.destroy');
     Route::resource('routes.stops', BusStopController::class);
});


/*
|--------------------------------------------------------------------------
| Driver Routes
|--------------------------------------------------------------------------
| Routes for the driver panel.
*/

// Driver Authentication
Route::prefix('driver')->name('driver.')->group(function () {
    Route::get('/login', [DriverLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [DriverLoginController::class, 'login']);
    Route::post('/logout', [DriverLoginController::class, 'logout'])->name('logout');
});

// Driver Dashboard (requires driver to be logged in)
Route::middleware('auth:drivers')->prefix('driver')->name('driver.')->group(function () {
    Route::get('/dashboard', [DriverDashboardController::class, 'index'])->name('dashboard');
    Route::post('/status', [DriverDashboardController::class, 'updateStatus'])->name('status.update');
});

Route::get('/stops-test', function() {
    return view('stops_prototype');
});

// Route Planner page
Route::get('/route-planner', [RoutePlannerController::class, 'index'])->name('route.planner');