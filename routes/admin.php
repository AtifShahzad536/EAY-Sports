<?php

use App\Http\Controllers\Admin\AdminAreaController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminBuilderLogoController;
use App\Http\Controllers\Admin\AdminBuilderModelController;
use App\Http\Controllers\Admin\AdminBuilderPatternController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminContactController;
use App\Http\Controllers\Admin\AdminCouponController;
use App\Http\Controllers\Admin\AdminCustomerApplicationController;
use App\Http\Controllers\Admin\AdminDealerController;
use App\Http\Controllers\Admin\AdminHomeCategoryController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminOrderStatusController;
use App\Http\Controllers\Admin\AdminPageContentController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminShowcaseVideoController;
use App\Http\Controllers\Admin\AdminSizeChartController;
use App\Http\Controllers\Admin\DealerApplicationAdminController;
use App\Http\Controllers\Admin\DealerOrderAdminController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Middleware\RedirectAdminIfAuthenticated;
use App\Http\Middleware\RedirectAdminIfNotAuthenticated;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
| Prefix: /admin  |  Name prefix: admin.
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

    // Guest-only admin routes (redirect if already logged in)
    Route::middleware(RedirectAdminIfAuthenticated::class)->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        // Rate limit: 5 attempts per minute — blocks brute-force on admin panel
        Route::post('/login', [AdminAuthController::class, 'login'])->middleware('throttle:5,1')->name('login.submit');
    });

    // Authenticated admin routes
    Route::middleware(RedirectAdminIfNotAuthenticated::class)->group(function () {

        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::post('/toggle-maintenance', [AdminDashboardController::class, 'toggleMaintenance'])->name('toggleMaintenance');
        Route::post('/update-display-config', [AdminDashboardController::class, 'updateDisplayConfig'])->name('updateDisplayConfig');
        Route::get('/footer-settings', [AdminDashboardController::class, 'editFooterSettings'])->name('footer-settings.edit');
        Route::post('/footer-settings', [AdminDashboardController::class, 'updateFooterSettings'])->name('footer-settings.update');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        // Profile & Password
        Route::get('/profile', [AdminAuthController::class, 'showProfileForm'])->name('profile');
        Route::put('/profile', [AdminAuthController::class, 'updateProfile'])->name('profile.update');
        Route::put('/profile/password', [AdminAuthController::class, 'updatePassword'])->name('profile.password');

        // Banners, Showcase Videos, Home Categories
        Route::resource('/banners', HomeController::class);
        Route::resource('/showcase-videos', AdminShowcaseVideoController::class);
        Route::resource('/home-categories', AdminHomeCategoryController::class);

        // Areas & Dealers
        Route::resource('/areas', AdminAreaController::class);
        Route::resource('/dealers', AdminDealerController::class);

        // Categories (with slug check helper)
        Route::get('/categories/check-slug', [AdminCategoryController::class, 'checkSlug'])->name('categories.check-slug');
        Route::resource('/categories', AdminCategoryController::class);

        // Products (with slug check & gallery image delete helpers)
        Route::get('/products/check-slug', [AdminProductController::class, 'checkSlug'])->name('products.check-slug');
        Route::delete('/products/gallery-image/{id}', [AdminProductController::class, 'deleteGalleryImage'])->name('products.delete-gallery-image');
        Route::resource('/products', AdminProductController::class);

        // Builder Models
        Route::resource('/builder-models', AdminBuilderModelController::class);
        Route::resource('/builder-patterns', AdminBuilderPatternController::class);
        Route::resource('/builder-logos', AdminBuilderLogoController::class);

        // Size Charts
        Route::resource('/size-charts', AdminSizeChartController::class);

        // Page CMS Content
        Route::get('/pages/{page}/edit', [AdminPageContentController::class, 'edit'])->name('pages.edit');
        Route::post('/pages/{page}', [AdminPageContentController::class, 'update'])->name('pages.update');

        // Contact Queries (read-only + delete)
        Route::resource('/contact-queries', AdminContactController::class)->only(['index', 'show', 'destroy']);

        // Coupons
        Route::get('/coupons/{coupon}/toggle', [AdminCouponController::class, 'toggleStatus'])->name('coupons.toggle');
        Route::resource('/coupons', AdminCouponController::class);

        // Order Statuses
        Route::resource('/order-statuses', AdminOrderStatusController::class);

        // Customer Orders
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{id}/pdf', [AdminOrderController::class, 'pdf'])->name('orders.pdf');
        Route::post('/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');

        // Dealer Applications
        Route::get('/dealer-applications', [DealerApplicationAdminController::class, 'index'])->name('dealer-applications.index');
        Route::get('/dealer-applications/{id}', [DealerApplicationAdminController::class, 'show'])->name('dealer-applications.show');
        Route::post('/dealer-applications/{id}/approve', [DealerApplicationAdminController::class, 'approve'])->name('dealer-applications.approve');
        // Customer Applications
        Route::get('/customer-applications', [AdminCustomerApplicationController::class, 'index'])->name('customer-applications.index');
        Route::get('/customer-applications/{id}', [AdminCustomerApplicationController::class, 'show'])->name('customer-applications.show');
        Route::post('/customer-applications/{id}/approve', [AdminCustomerApplicationController::class, 'approve'])->name('customer-applications.approve');
        Route::post('/customer-applications/{id}/reject', [AdminCustomerApplicationController::class, 'reject'])->name('customer-applications.reject');

        // Dealer Orders (admin view)
        Route::get('/dealer-orders', [DealerOrderAdminController::class, 'index'])->name('dealer-orders.index');
        Route::get('/dealer-orders/{id}', [DealerOrderAdminController::class, 'show'])->name('dealer-orders.show');
        Route::get('/dealer-orders/{id}/pdf', [DealerOrderAdminController::class, 'pdf'])->name('dealer-orders.pdf');
        Route::post('/dealer-orders/{id}/status', [DealerOrderAdminController::class, 'updateStatus'])->name('dealer-orders.updateStatus');
    });
});
