<?php

use App\Http\Controllers\Dealer\DealerApplicationController;
use App\Http\Controllers\Dealer\DealerAuthController;
use App\Http\Controllers\Dealer\DealerDashboardController;
use App\Http\Controllers\Dealer\DealerDesignController;
use App\Http\Controllers\Dealer\DealerOrderController;
use App\Http\Controllers\Dealer\DealerProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dealer Portal Routes
| Prefix: /dealer  |  Name prefix: dealer.
|--------------------------------------------------------------------------
*/
Route::prefix('dealer')->name('dealer.')->group(function () {

    // Guest-only Dealer portal routes
    Route::middleware('redirect.if.auth')->group(function () {
        Route::get('/apply', [DealerApplicationController::class, 'showForm'])->name('apply');
        Route::get('/login', [DealerAuthController::class, 'showLogin'])->name('login');
        Route::get('/forgot-password', [DealerAuthController::class, 'showForgotPassword'])->name('forgot-password');
        Route::get('/reset-password/{token}', [DealerAuthController::class, 'showResetForm'])->name('reset-password');
    });

    // POST submissions & non-GET routes
    Route::post('/apply', [DealerApplicationController::class, 'submit'])->middleware('throttle:3,1')->name('apply.submit');
    Route::post('/login', [DealerAuthController::class, 'login'])->middleware('throttle:6,1')->name('login.submit');
    Route::post('/logout', [DealerAuthController::class, 'logout'])->name('logout');
    Route::post('/forgot-password', [DealerAuthController::class, 'sendResetLink'])->middleware('throttle:3,1')->name('forgot-password.submit');
    Route::post('/reset-password', [DealerAuthController::class, 'resetPassword'])->middleware('throttle:5,1')->name('reset-password.submit');

    // Protected B2B Dealer Routes
    Route::middleware('dealer.auth')->group(function () {
        Route::get('/dashboard', [DealerDashboardController::class, 'index'])->name('dashboard');

        // Dealer Orders
        Route::get('/orders', [DealerOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/create', [DealerOrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [DealerOrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{id}', [DealerOrderController::class, 'show'])->name('orders.show');

        // Dealer Profile
        Route::get('/profile', [DealerProfileController::class, 'show'])->name('profile');
        Route::put('/profile', [DealerProfileController::class, 'update'])->name('profile.update');

        // Dealer Saved Designs
        Route::get('/designs', [DealerDesignController::class, 'index'])->name('designs.index');
        Route::delete('/designs/{id}', [DealerDesignController::class, 'destroy'])->name('designs.destroy');
    });
});
