<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InventoryController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function () {
    // Simple login - create a test user
    $user = \App\Models\User::firstOrCreate(
        ['email' => 'test@example.com'],
        ['name' => 'Test User', 'password' => bcrypt('password')]
    );
    auth()->login($user);
    return redirect()->route('dashboard');
})->name('login.post');

Route::get('/logout', function () {
    auth()->logout();
    return redirect('/');
})->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('inventory', InventoryController::class)->only(['index', 'show', 'edit', 'update']);
    
    Route::get('/inventory/{inventory}/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');
    Route::post('/inventory/{inventory}/adjust', [InventoryController::class, 'processAdjustment'])->name('inventory.process-adjustment');
});


