<?php

use App\Http\Controllers\OrderManagementController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\AccountController;



Route::middleware(['auth', 'role:staff'])->group(function () {
    Route::get('/inventory', [InventoryController::class, 'index']);
    Route::post('/inventory/update', [InventoryController::class, 'update']);
    Route::post('/orders/{order}/accept', [InventoryController::class, 'acceptOrder']);
    Route::post('/orders/{order}/decline', [InventoryController::class, 'declineOrder']);
});


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
    Route::get('/managemenu', [MenuController::class, 'index'])->name('managemenu');
    Route::put('/manage-menu/update/{id}', [MenuController::class, 'update'])->name('menu.update');  // Changed to PUT
    Route::put('/menu/{id}/update-quantity', action: [MenuController::class, 'updateQuantity'])->name('menu.update.quantity');

    Route::get('/accountmanagement', function () {
        return Inertia::render('Admin/AccountManagement');
    })->name('accountmanagement');


    Route::post('/accountmanagement/store', [AccountController::class, 'store'])->name('account.store');
    Route::get('/users/grouped', [AccountController::class, 'getGroupedUsers'])->name('users.grouped');
    Route::put('/accountmanagement/update/{id}', [AccountController::class, 'update'])->name('account.update');
    Route::delete('/account/{id}', [AccountController::class, 'destroy'])->name('account.destroy');

    Route::get('/productmanagement', function () {
        return Inertia::render('Admin/ProductManagement');
    })->name('productmanagement');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
  

    Route::get('/ordermanagement', [OrderManagementController::class, 'index'])->name('ordermanagement');


});


Route::get('/auth/google/redirect', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Dashboard route with role-based redirection
Route::get('/dashboard', function () {
    $role = Auth::user()->role;

    if ($role === 'admin') {
        return Inertia::render('Admin/AdminDashboard');
    } elseif ($role === 'staff') {
        return Inertia::render('Staff/StaffDashboard');
    } elseif ($role === 'customer') {
        return Inertia::render('Customer/CustomerDashboard');
    }
    
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
