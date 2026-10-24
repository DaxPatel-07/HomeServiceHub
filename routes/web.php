<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboard;
use App\Http\Controllers\Customer\BookingController as CustomerBooking;
use App\Http\Controllers\Customer\SearchController;
use App\Http\Controllers\Customer\ReviewController;
use App\Http\Controllers\Customer\ReminderController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfile;
use App\Http\Controllers\Customer\ComplaintController as CustomerComplaint;
use App\Http\Controllers\Provider\DashboardController as ProviderDashboard;
use App\Http\Controllers\Provider\BookingController as ProviderBooking;
use App\Http\Controllers\Provider\ProfileController as ProviderProfile;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\ProviderController as AdminProvider;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ComplaintController;
use App\Http\Controllers\Admin\BookingController as AdminBooking;
use App\Http\Controllers\Admin\CustomerController as AdminCustomer;
use App\Http\Controllers\MasterAdmin\DashboardController as MasterDashboard;
use App\Http\Controllers\MasterAdmin\AdminController;
use App\Http\Controllers\MasterAdmin\ReportController;
use App\Http\Controllers\MasterAdmin\SettingsController;
use App\Http\Controllers\AI\ChatController;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [SearchController::class, 'index'])->name('search');

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login')->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:register')->name('register.post');
    Route::get('/become-a-provider', [AuthController::class, 'showBecomeProvider'])->name('become-provider');
    Route::post('/become-a-provider', [AuthController::class, 'register'])->middleware('throttle:register')->name('become-provider.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// AI Assistant
Route::get('/ai-assistant', [ChatController::class, 'index'])->name('ai.assistant');
Route::post('/ai/chat', [ChatController::class, 'chat'])->middleware('throttle:ai-chat')->name('ai.chat');
Route::post('/ai/suggest-service', [ChatController::class, 'suggestService'])->middleware('throttle:ai-chat')->name('ai.suggest');

// Customer routes
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerDashboard::class, 'index'])->name('dashboard');
    Route::post('/location', [CustomerDashboard::class, 'updateLocation'])->name('location.update');
    Route::get('/book/{provider}', [CustomerBooking::class, 'showBooking'])->name('book');
    Route::post('/book', [CustomerBooking::class, 'store'])->name('book.store');
    Route::get('/split-booking', [CustomerBooking::class, 'showSplitBooking'])->name('split.booking');
    Route::post('/split-booking', [CustomerBooking::class, 'storeSplitBooking'])->name('split.booking.store');
    Route::get('/bookings', [CustomerBooking::class, 'index'])->name('bookings');
    Route::get('/bookings/{booking}', [CustomerBooking::class, 'show'])->name('booking.show');
    Route::post('/bookings/{booking}/cancel', [CustomerBooking::class, 'cancel'])->name('booking.cancel');
    Route::get('/review/{booking}', [ReviewController::class, 'create'])->name('review.create');
    Route::post('/review/{booking}', [ReviewController::class, 'store'])->name('review.store');
    Route::get('/reminders', [ReminderController::class, 'index'])->name('reminders');
    Route::post('/reminders', [ReminderController::class, 'store'])->name('reminders.store');
    Route::delete('/reminders/{reminder}', [ReminderController::class, 'destroy'])->name('reminders.destroy');
    // Profile
    Route::get('/profile', [CustomerProfile::class, 'index'])->name('profile');
    Route::put('/profile', [CustomerProfile::class, 'update'])->name('profile.update');
    Route::put('/password', [CustomerProfile::class, 'updatePassword'])->middleware('throttle:6,1')->name('password.update');
    // Complaints
    Route::get('/complaint/{booking}', [CustomerComplaint::class, 'create'])->name('complaint.create');
    Route::post('/complaint/{booking}', [CustomerComplaint::class, 'store'])->name('complaint.store');
});

// Provider routes
Route::middleware(['auth', 'role:provider'])->prefix('provider')->name('provider.')->group(function () {
    Route::get('/dashboard', [ProviderDashboard::class, 'index'])->name('dashboard');
    Route::get('/bookings', [ProviderBooking::class, 'index'])->name('bookings');
    Route::post('/bookings/{booking}/accept', [ProviderBooking::class, 'accept'])->name('booking.accept');
    Route::post('/bookings/{booking}/reject', [ProviderBooking::class, 'reject'])->name('booking.reject');
    Route::post('/bookings/{booking}/complete', [ProviderBooking::class, 'complete'])->name('booking.complete');
    Route::get('/profile', [ProviderProfile::class, 'edit'])->name('profile');
    Route::put('/profile', [ProviderProfile::class, 'update'])->name('profile.update');
    Route::post('/availability', [ProviderProfile::class, 'toggleAvailability'])->name('availability');
});

// Provider profile (requires authentication for full details) - MUST be after the authenticated provider group to avoid wildcard conflict
Route::get('/provider/{provider}', [SearchController::class, 'providerProfile'])->name('provider.show');
Route::get('/api/providers/{provider}', [SearchController::class, 'apiProviderDetails'])->name('api.provider.show');

// Admin routes
Route::middleware(['auth', 'role:admin,master_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::get('/providers', [AdminProvider::class, 'index'])->name('providers');
    Route::post('/providers/{provider}/verify', [AdminProvider::class, 'verify'])->name('providers.verify');
    Route::post('/providers/{provider}/reject', [AdminProvider::class, 'reject'])->name('providers.reject');
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/complaints', [ComplaintController::class, 'index'])->name('complaints');
    Route::post('/complaints/{complaint}/resolve', [ComplaintController::class, 'resolve'])->name('complaints.resolve');
    Route::get('/bookings', [AdminBooking::class, 'index'])->name('bookings');
    Route::get('/customers', [AdminCustomer::class, 'index'])->name('customers');
});

// Master Admin routes
Route::middleware(['auth', 'role:master_admin'])->prefix('master-admin')->name('master.')->group(function () {
    Route::get('/dashboard', [MasterDashboard::class, 'index'])->name('dashboard');
    Route::get('/admins', [AdminController::class, 'index'])->name('admins');
    Route::post('/admins', [AdminController::class, 'store'])->name('admins.store');
    Route::delete('/admins/{user}', [AdminController::class, 'destroy'])->name('admins.destroy');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
});
