<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GuestbookController;
use App\Http\Controllers\CategoryService;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profile routes 
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Permission management routes
    Route::get('/guestbook/manageUser', [PermissionController::class, 'index'])->name('guestbook.manageUser');
    Route::post('/users/permissions/update', [PermissionController::class, 'update'])->name('users.permissions.update');

    // Guestbook
    Route::get('/', [GuestbookController::class, 'index'])->name('guestbook.index');
    Route::get('/guestbook/stats', [GuestbookController::class, 'stats'])->name('guestbook.stats');
    Route::put('/users/update', [UserController::class, 'update'])->name('users.update');

    // Create routes → permission  1
    Route::middleware('permission:1')->group(function () {
        Route::get('/guestbook/create', [GuestbookController::class, 'create'])->name('guestbook.create');
        Route::post('/guestbook', [GuestbookController::class, 'store'])->name('guestbook.store');
    });

    // Edit/Update routes → permission  2
    Route::middleware('permission:2')->group(function () {
        Route::get('/guestbook/{id}', [GuestbookController::class, 'edit'])->name('guestbook.edit');
        Route::put('/guestbook/{id}', [GuestbookController::class, 'update'])->name('guestbook.update');
    });

    // Delete routes → permission  3
    Route::middleware('permission:3')->group(function () {
        Route::delete('/guestbook/{id}', [GuestbookController::class, 'SoftDelete'])->name('guestbook.delete');
    });

    // Categories
    Route::get('/categories', [CategoryService::class, 'index'])->name('categories.index');

    // Create → permission 1
    Route::middleware('permission:1')->group(function () {
        Route::get('/categories/create', [CategoryService::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryService::class, 'store'])->name('categories.store');
    });

    // Edit/Update → permission 2
    Route::middleware('permission:2')->group(function () {
        Route::get('/categories/{id}/edit', [CategoryService::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{id}', [CategoryService::class, 'update'])->name('categories.update');
    });

    // Delete → permission 3
    Route::middleware('permission:3')->group(function () {
        Route::delete('/categories/{id}', [CategoryService::class, 'SoftDelete'])->name('categories.destroy');
        Route::get('/categories/{id}/deleteAlert', [CategoryService::class, 'deleteAlert'])->name('categories.deleteAlert');
        Route::put('/categories/{id}/UpdateThenDestroy', [CategoryService::class, 'UpdateThenDestroy'])->name('categories.UpdateThenDestroy');
        Route::any('/categories/{id}/handle-option', [CategoryService::class, 'handleOption'])->name('categories.handleOption');
    });
});
