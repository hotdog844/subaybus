<?php

use Illuminate\Support\Facades\Route; 
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\DB;

// --- 0. Import Models (Safe Aliasing) ---
use App\Models\Route as BusRoute; 
use App\Models\Bus;
use App\Models\Stop; 
use App\Models\GpsLog;
use App\Models\Alert; // Added for Alerts

// --- 1. Import General Controllers ---
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\TerminalController;
use App\Http\Controllers\ProfileController;

// --- 2. Import Admin Controllers ---
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\BusController as AdminBusController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\RouteController as AdminRouteController;
use App\Http\Controllers\Admin\DriverController as AdminDriverController;
use App\Http\Controllers\Admin\FeedbackController as AdminFeedbackController;
use App\Http\Controllers\Admin\FareController as AdminFareController;
use App\Http\Controllers\Admin\StopController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\AlertController; // Added
use App\Http\Controllers\Admin\CardManagerController;

// --- 3. Import Mobile/Passenger Controllers ---
use App\Http\Controllers\MobileController;
use App\Http\Controllers\MobileDriverController;
use App\Http\Controllers\Api\GpsController; 
use App\Http\Controllers\Api\HailController; // Added

/*
|--------------------------------------------------------------------------
| 1. PUBLIC ROUTES (Landing, Auth, Terminal)
|--------------------------------------------------------------------------
*/

// Landing Page
Route::get('/', function () {
    return auth()->check() ? redirect()->route('mobile.dashboard') : view('welcome');
});

// Authentication
// --- Public Driver Registration ---
Route::get('/driver/register', [AdminDriverController::class, 'create'])->name('public.driver.register');
Route::post('/driver/register', [AdminDriverController::class, 'store'])->name('public.driver.store');
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Public Screens
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/terminal-view', [TerminalController::class, 'index'])->name('terminal.view');

// Hardware GPS Endpoint (SinoTrack) - No Auth Required
Route::get('/api/gps/data', [GpsController::class, 'receive']);


/*
|--------------------------------------------------------------------------
| 2. ADMIN ROUTES (The Command Center)
|--------------------------------------------------------------------------
*/
// Note: 'prefix' adds /admin to URL. 'name' adds admin. to route names.
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // ✅ This restores your Pro Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Fleet Management
    Route::resource('buses', AdminBusController::class);
    Route::resource('routes', AdminRouteController::class);
    Route::resource('drivers', AdminDriverController::class);
    Route::resource('users', AdminUserController::class)->only(['index', 'destroy']);
    Route::patch('/users/{user}/verify', [AdminUserController::class, 'verify'])->name('users.verify');
    
    // Admin Alerts (Broadcasts)
    Route::resource('alerts', AlertController::class)->only(['index', 'store', 'destroy']);

    // Feedback
    Route::get('/feedback', [AdminFeedbackController::class, 'index'])->name('feedback.index');
    Route::patch('/feedback/{feedback}/read', [AdminFeedbackController::class, 'markAsRead'])->name('feedback.read');
    Route::delete('/feedback/{feedback}', [AdminFeedbackController::class, 'destroy'])->name('feedback.destroy');

    // Fare Matrix
    Route::get('/fares', [AdminFareController::class, 'index'])->name('fares.index');
    Route::put('/fares/{id}', [AdminFareController::class, 'update'])->name('fares.update');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // --- STOP MANAGEMENT ---
    Route::get('routes/{route_id}/stops', [StopController::class, 'index'])->name('routes.stops.index');
    Route::post('routes/{route_id}/stops', [StopController::class, 'store'])->name('routes.stops.store');
    Route::delete('stops/{stop_id}', [StopController::class, 'destroy'])->name('routes.stops.destroy');
    // ADD THIS LINE BELOW:
    Route::patch('/drivers/{driver}/verify', [AdminDriverController::class, 'verify'])->name('drivers.verify');

    Route::prefix('cards')->name('cards.')->group(function () {
        Route::get('/', [CardManagerController::class, 'index'])->name('index');       // Becomes 'admin.cards.index'
        Route::post('/{id}/topup', [CardManagerController::class, 'topUp'])->name('topup');
        Route::post('/{id}/assign', [CardManagerController::class, 'assignCard'])->name('assign');
    });
});



/*
|--------------------------------------------------------------------------
| 3. PASSENGER MOBILE APP (The Main User Interface)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    
    // Main Dashboard
    Route::get('/home', function() { return redirect()->route('mobile.dashboard'); })->name('home');
    Route::get('/mobile/dashboard', function () { return view('mobile_dashboard'); })->name('mobile.dashboard');

    // Live Tracking & Maps
    Route::get('/mobile/track/{id}', function ($id) { return view('mobile.tracking', ['busId' => $id]); });
    
    // Nearby Stops
    Route::get('/mobile/nearby', function () { 
        $stops = DB::table('stops')->get(); 
        return view('nearby_stops', ['stops' => $stops]); 
    })->name('mobile.nearby');

    Route::get('/mobile/planner', function () { return view('mobile_route_planner'); })->name('mobile.planner');

    // Profile & Settings
    Route::get('/mobile/profile', function () { return view('mobile_profile'); })->name('mobile.profile');
    Route::get('/mobile/profile/edit', function () { return view('mobile_edit_profile'); })->name('mobile.edit_profile');
    
    // Profile Update Logic
    Route::post('/mobile/profile/update', function (Request $request) {
        $request->validate(['name' => 'required|string|max:255', 'email' => 'required|email']);
        $user = auth()->user();
        $user->name = $request->name;
        $user->email = $request->email;
        if($request->has('phone')) $user->phone = $request->phone;
        $user->save();
        return redirect()->route('mobile.profile');
    })->name('mobile.updateProfile');

    // Feedback Submission
    Route::post('/api/feedback', function(Request $request) {
        DB::table('feedback')->insert([
            'user_id' => auth()->id(),
            'bus_id' => $request->bus_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'status' => 'new',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        return response()->json(['status' => 'success']);
    });
});


/*
|--------------------------------------------------------------------------
| 4. DRIVER APP ROUTES (Mobile Driver Portal)
|--------------------------------------------------------------------------
*/
// Driver Login
Route::get('/driver', [MobileDriverController::class, 'login'])->name('driver.login');
Route::post('/driver/login', [MobileDriverController::class, 'authenticate'])->name('driver.authenticate');

// Driver Dashboard & Status Updates
Route::get('/driver/dashboard/{id}', [MobileDriverController::class, 'dashboard'])->name('driver.dashboard');
Route::post('/driver/update/{id}', [MobileDriverController::class, 'updateStatus'])->name('driver.update');

// [NEW] Driver Incident Reporting (API)
Route::post('/api/driver/report-incident', function (Request $request) {
    // 1. Get the Bus Name
    $bus = Bus::find($request->bus_id);
    $busName = $bus ? $bus->bus_number : 'A Bus';

    // 2. Create the Alert automatically
    Alert::create([
        'title' => $request->title, // e.g. "Heavy Traffic"
        'message' => "Reported by Bus {$busName} just now. Expect delays in this area.",
        'type' => $request->type
    ]);

    return response()->json(['status' => 'success']);
});

// Add this new route for ending the shift
Route::post('/driver/end-shift/{id}', [App\Http\Controllers\MobileDriverController::class, 'endShift'])->name('driver.end_shift');

// Driver Hub (Landing Page)
Route::get('/driver/menu', [App\Http\Controllers\MobileDriverController::class, 'index'])
    ->middleware(['auth']) // Ensure they are logged in
    ->name('driver.menu');
    
/*
|--------------------------------------------------------------------------
| 5. DATA API (Used by Maps & Javascript)
|--------------------------------------------------------------------------
*/
Route::get('/api/bus-locations', function () {
    return DB::table('buses')
        ->leftJoin('routes', 'buses.route_id', '=', 'routes.id')
        ->leftJoin('users', 'buses.driver_id', '=', 'users.id') 
        ->whereNotNull('buses.lat')
        ->whereNotNull('buses.lng')
        ->select(
            'buses.id', 
            'buses.bus_number', 
            'buses.plate_number', 
            'buses.lat', 
            'buses.lng', 
            'buses.passenger_count', 
            'buses.capacity',
            'buses.status', 
            'buses.updated_at',
            'routes.name as route_name',
            'users.name as driver_name'
        )
        ->get();
});

Route::get('/api/stops', function () {
    return DB::table('stops')->orderBy('sequence', 'asc')->get();
});

// --- PASSENGER HAILING ---
Route::middleware('auth')->group(function () {
    Route::post('/api/hail', [HailController::class, 'store']);
});

// --- DRIVER API ---
Route::get('/api/driver/hails', [HailController::class, 'index']);

// --- USER NOTIFICATIONS ---
Route::get('/api/alerts', function() {
    return Alert::latest()->take(5)->get();
});

// --- ROUTE SHAPES API (For drawing lines on the map) ---
Route::get('/api/routes/shapes', function () {
    // This is where you define the paths for your routes.
    // Ideally, this comes from a database, but we can hardcode the "Color" logic here.
    
    $routes = \App\Models\Route::all();
    
    $data = $routes->map(function($route) {
        // Assign Colors based on Name
        $color = '#636e72'; // Default Gray
        if (str_contains($route->name, 'Red')) $color = '#e74c3c';
        if (str_contains($route->name, 'Blue')) $color = '#0984e3';
        if (str_contains($route->name, 'Green')) $color = '#00b894';
        if (str_contains($route->name, 'UV')) $color = '#6c5ce7';

        return [
            'id' => $route->id,
            'name' => $route->name,
            'color' => $color,
            // Assuming your 'routes' table has a 'path_data' column with JSON coordinates
            // If not, we will handle empty paths in the frontend
            'path_data' => $route->path_data ?? null 
        ];
    });

    return response()->json($data);
});

/*
|--------------------------------------------------------------------------
| 6. EMAIL VERIFICATION
|--------------------------------------------------------------------------
*/
Route::get('/email/verify', function () { return view('auth.verify-email'); })->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) { $request->fulfill(); return redirect('/home'); })->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('/email/verification-notification', function (Request $request) { $request->user()->sendEmailVerificationNotification(); return back()->with('message', 'Link sent!'); })->middleware(['auth', 'throttle:6,1'])->name('verification.send');


/*
|--------------------------------------------------------------------------
| 7. DEBUGGING TOOLS
|--------------------------------------------------------------------------
*/
Route::get('/debug-bus', function () {
    $bus = Bus::first(); 
    if(!$bus) return "No buses found.";
    $busRaw = DB::table('buses')->where('id', $bus->id)->first();
    $route = BusRoute::find($busRaw->route_id);
    return [
        'BUS_INFO' => ['Plate Number' => $bus->plate_number, 'Saved Route ID' => $busRaw->route_id],
        'ROUTE_CONNECTION' => ['Connected?' => $route ? 'YES' : 'NO', 'Name' => $route ? $route->name : 'NULL'],
        'ALL_ROUTES' => BusRoute::all(['id', 'name'])->toArray()
    ];
});

Route::get('/check-gps', function () {
    $log = GpsLog::where('device_id', '9176466392')->latest()->first();
    return $log ? "✅ Lat: {$log->latitude}, Lng: {$log->longitude}" : "❌ No data yet.";
});

// --- FIX: SEPARATION SCRIPT (Ilipat ang SIM buses sa RCITT, iwan ang GPS bus) ---
// Route::get('/fix-database-now', function () {
//     // 1. Target Coordinates (RCITT Terminal)
//     $terminalLat = 11.559536845932017;
//     $terminalLng = 122.75077876568271;

//     // 2. UPDATE ONLY THE 'SIM' BUSES (at iba pa)
//     // Exclude natin si '9176466392' para hindi magulo ang GPS data niya
//     // Ibig sabihin: "Update mo lahat ng bus na HINDI 9176466392"
//     $affected = \Illuminate\Support\Facades\DB::table('buses')
//         ->where('plate_number', '!=', '9176466392') 
//         ->update([
//             'lat' => $terminalLat,
//             'lng' => $terminalLng,
//             'status' => 'active',
//             'updated_at' => now()
//         ]);

//     return "✅ SEPARATION COMPLETE: Inilipat ang $affected na buses sa RCITT Terminal ($terminalLat, $terminalLng). Ang GPS Unit (917...) ay naiwan sa pwesto niya.";
// });

// --- FIX FOR BROKEN PROFILE LINKS ---
// This redirects any old "profile.edit" links to your new Mobile Profile page
Route::get('/profile', function() { 
    return redirect()->route('mobile.edit_profile'); 
})->name('profile.edit');

/*
|--------------------------------------------------------------------------
| 8. SAFETY NET (Global Redirect)
|--------------------------------------------------------------------------
| This catches any broken link looking for 'dashboard' and sends it to Admin.
*/
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->name('dashboard');

Route::get('/force-logout', function () {
    Auth::logout();
    Session::flush();
    return redirect('/login');
});

Route::get('/mobile/nearby', [App\Http\Controllers\MobileController::class, 'nearby'])->name('mobile.nearby');

// In routes/web.php
Route::get('/api/stops/{id}/route', function($id) {
    $stop = \App\Models\BusStop::with('route')->find($id);
    return response()->json($stop->route);
});

Route::get('/test-ocr', function () {
    return view('ocr_test');
});