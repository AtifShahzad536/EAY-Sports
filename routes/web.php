<?php

use App\Http\Controllers\BuilderController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\DealerLocatorController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SavedDesignController;
use App\Http\Controllers\StoreAuthController;
use App\Http\Controllers\SubscriberController;
use App\Models\PageContent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Storefront Public Pages (Inertia)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'getBannersForFrontend'])->name('home');
Route::get('/about', function () {
    $contents = PageContent::where('page_key', 'about')->get()->pluck('content_data', 'section_key');

    return inertia('About', [
        'hero' => $contents['hero'] ?? null,
        'stats' => $contents['stats'] ?? [],
        'values' => $contents['values'] ?? [],
        'team' => $contents['team'] ?? [],
    ]);
})->name('about');

Route::get('/faq', function () {
    $contents = PageContent::where('page_key', 'faq')->get()->pluck('content_data', 'section_key');

    return inertia('FAQ', [
        'hero' => $contents['hero'] ?? null,
        'faqs' => $contents['faqs'] ?? [],
    ]);
})->name('faq');

Route::get('/privacy-policy', function () {
    $contents = PageContent::where('page_key', 'privacy')->get()->pluck('content_data', 'section_key');

    return inertia('PrivacyPolicy', [
        'hero' => $contents['hero'] ?? null,
        'sections' => $contents['sections'] ?? [],
    ]);
})->name('privacy-policy');

Route::get('/terms-of-service', function () {
    $contents = PageContent::where('page_key', 'terms')->get()->pluck('content_data', 'section_key');

    return inertia('TermsOfService', [
        'hero' => $contents['hero'] ?? null,
        'sections' => $contents['sections'] ?? [],
    ]);
})->name('terms-of-service');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
// Rate limit: 3 submissions per minute — prevents contact form spam
Route::post('/contact', [ContactController::class, 'submit'])->middleware('throttle:3,1')->name('contact.submit');
Route::get('/auth', fn () => inertia('Auth'))->middleware('redirect.if.auth')->name('auth');
Route::get('/forgot-password', [StoreAuthController::class, 'showForgotPassword'])->middleware('redirect.if.auth')->name('password.request');
Route::post('/forgot-password', [StoreAuthController::class, 'sendResetLink'])->middleware(['redirect.if.auth', 'throttle:3,1'])->name('password.email');
Route::get('/reset-password/{token}', [StoreAuthController::class, 'showResetForm'])->middleware('redirect.if.auth')->name('password.reset');
Route::post('/reset-password', [StoreAuthController::class, 'resetPassword'])->middleware(['redirect.if.auth', 'throttle:5,1'])->name('password.update');
Route::get('/banner', [HomeController::class, 'GetBanner'])->name('banner');

/*
|--------------------------------------------------------------------------
| Products & Builder
|--------------------------------------------------------------------------
*/
Route::get('/products', [ProductController::class, 'index'])->name('products');
Route::get('/product-details/{id}', [ProductController::class, 'show'])->name('product-details');
Route::post('/product-details/{product}/reviews', [ProductController::class, 'storeReview'])->name('products.reviews.store');
Route::get('/builder/models', [BuilderController::class, 'subModels'])->name('builder.models');
Route::get('/builder/{id?}', [BuilderController::class, 'index'])->where('id', '.*')->name('builder');
Route::get('/dealer-locator', [DealerLocatorController::class, 'index'])->name('dealer-locator');

/*
|--------------------------------------------------------------------------
| Checkout & Orders
|--------------------------------------------------------------------------
*/
Route::get('/checkout', function () {
    if (! Auth::check()) {
        return redirect()->route('auth')->with('error', 'Please login to checkout.');
    }

    return inertia('Checkout');
})->name('checkout');

Route::get('/checkout/success/{type}/{id}', [OrderController::class, 'successPage'])->name('checkout.success');

/*
|--------------------------------------------------------------------------
| Storefront JSON API Routes
|--------------------------------------------------------------------------
*/
// Products Search (public)
Route::get('/api/products/search', [ProductController::class, 'search'])->name('api.products.search');
Route::get('/api/size-charts', [ProductController::class, 'getSizeCharts'])->name('api.size-charts');

// Authentication — rate-limited to 6 attempts per minute to prevent brute-force
Route::middleware(['throttle:6,1'])->group(function () {
    Route::post('/api/auth/register', [StoreAuthController::class, 'register'])->name('api.auth.register');
    Route::post('/api/auth/login', [StoreAuthController::class, 'login'])->name('api.auth.login');
});

Route::post('/api/auth/logout', [StoreAuthController::class, 'logout'])->name('api.auth.logout');
Route::get('/api/auth/me', [StoreAuthController::class, 'me'])->name('api.auth.me');
Route::post('/api/auth/profile', [StoreAuthController::class, 'updateProfile'])->name('api.auth.profile');
Route::post('/api/auth/profile-image', [StoreAuthController::class, 'updateProfileImage'])->name('api.auth.profile-image');
Route::post('/api/auth/settings', [StoreAuthController::class, 'updateSettings'])->name('api.auth.settings');
Route::post('/api/decal/upload', [OrderController::class, 'uploadDecal'])->name('api.decal.upload');
Route::post('/api/model/upload', [OrderController::class, 'uploadModel'])->name('api.model.upload');

// Orders — requires auth
Route::middleware('auth')->group(function () {
    Route::post('/api/orders/checkout', [OrderController::class, 'store'])->middleware('throttle:5,1')->name('api.orders.checkout');
    Route::get('/api/orders', [OrderController::class, 'index'])->name('api.orders.index');
});

// Coupons — rate limited: 10 attempts per minute prevents brute-force coupon guessing
Route::post('/api/apply-coupon', [CouponController::class, 'apply'])->middleware('throttle:10,1')->name('api.apply-coupon');
Route::get('/api/auto-coupons', [CouponController::class, 'autoCoupons'])->name('api.auto-coupons');

// Saved Designs
Route::get('/api/saved-designs', [SavedDesignController::class, 'index'])->name('api.saved-designs.index');
Route::post('/api/saved-designs', [SavedDesignController::class, 'store'])->name('api.saved-designs.store');
Route::get('/api/saved-designs/{id}', [SavedDesignController::class, 'show'])->name('api.saved-designs.show');
Route::delete('/api/saved-designs/{id}', [SavedDesignController::class, 'destroy'])->name('api.saved-designs.destroy');

// Newsletter — rate limit: 3 per minute prevents email list flooding
Route::post('/api/subscribe', [SubscriberController::class, 'store'])->middleware('throttle:3,1')->name('api.subscribe');

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
*/
require __DIR__.'/admin.php';

/*
|--------------------------------------------------------------------------
| Dealer Portal Routes
|--------------------------------------------------------------------------
*/
require __DIR__.'/dealer.php';
