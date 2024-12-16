<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Application;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// User Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Google Authentication Routes
Route::get('/auth/google/redirect', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');

Route::middleware(['auth', 'setDB'])->group(function () {

    Route::get('/dashboard', function () {
        return match (Auth::user()->role_id) {
            1 => Inertia::render('Admin/AdminDashboard'),
            2 => Inertia::render('Staff/StaffDashboard'),
            3 => Inertia::render('Customer/CustomerDashboard'),
            default => abort(403),
        };
    })->name('dashboard');

    Route::get('/user', [AdminController::class, 'users'])->name('user');

    // Admin Controller
    Route::middleware('admin')->group(function () {
        Route::get('Admin/Managemenu', [AdminController::class, 'viewMenu'])->name('managemenu');
        Route::put('Admin/Manage-menu/update/{id}', [AdminController::class, 'updateMenu'])->name('menu.update');
        
        Route::get('Admin/AccountManagement', [AdminController::class, 'viewAccountManagement'])->name('accountmanagement');// Update menu item details
        Route::post('Admin/AccountManagement/Insert', [AdminController::class, 'insertIntoAccountManagement'])->name('account.store');
        Route::get('Admin/AccountManagement/Display', [AdminController::class, 'displayUsers'])->name('users.grouped');
        Route::put('Admin/Account/{id}', [AdminController::class, 'updateUsers'])->name('account.update');
        Route::delete('Admin/Account/{id}', [AdminController::class, 'destroyUsers'])->name('account.destroy');
        
        Route::get('Admin/ProductManagement', [AdminController::class, 'viewProductManagement'])->name('productmanagement');// Update menu item details
        Route::get('Admin/ProductManagement/Display', [AdminController::class, 'displayProducts'])->name('products.index');
        Route::post('Admin/ProductManagement/Insert', [AdminController::class, 'insertProducts'])->name('products.store'); 
        Route::put('Admin/ProductManagement/Update/{id}', [AdminController::class, 'updateProducts'])->name('products.update');
        Route::delete('Admin/ProductManagement/Products/{id}', [AdminController::class, 'deleteProduct'])->name('products.destroy');
    
        Route::get('Admin/OrderManagement', [AdminController::class, 'viewOrder'])->name('ordermanagement');
        Route::get('Admin/Orders', [AdminController::class, 'displayOrder'])->name('orders.index');
        Route::put('Admin/Orders/{id}', [AdminController::class, 'updateOrder'])->name('orders.update');
        Route::get('Admin/Orders/History', [AdminController::class, 'getOrderHistory'])->name('orders.history');
        Route::delete('Admin/Orders/{id}', [AdminController::class, 'deleteOrder'])->name('orders.delete');

    });


    // Staff Controller
    Route::middleware('staff')->group(function (){
        Route::get('Staff/MenuManagement', [StaffController::class,'displayMenu'])->name('staff.menu');
        Route::put('Staff/Menu/{id}/Update-Qty', [StaffController::class, 'updateMenu'])->name('staff.menu.update');
       
        
        Route::get('Staff/OrderManagement', [StaffController::class,'viewOrder'])->name('staff.order');
        Route::post('/staff/orders/{order}/accept', [StaffController::class, 'acceptOrder'])->name('staff.orders.accept');
        Route::put('/staff/orders/{order}', [StaffController::class, 'updateOrder'])->name('staff.orders.update');
        Route::delete('/staff/orders/{order}', [StaffController::class, 'destroyOrder'])->name('staff.orders.destroy');
    });
    
});

require __DIR__ . '/auth.php';
