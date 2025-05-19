<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\HealthcareProviderController as AdminProviderController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserRoleController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\TranslationController;
use App\Http\Controllers\Admin\ResourceController as AdminResourceController;
use App\Http\Controllers\Admin\ResourceCategoryController as AdminResourceCategoryController;
use App\Http\Controllers\PlansController;
use Illuminate\Support\Facades\Route;

// Home Route
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/refresh-statistics', [HomeController::class, 'refreshStatistics'])->name('refresh-statistics');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Provider Routes
Route::get('/providers', [ProviderController::class, 'index'])->name('providers.index');
Route::get('/providers/{id}', [ProviderController::class, 'show'])->name('providers.show');

// News Routes
Route::get('/news', [NewsController::class, 'index'])->name('news');
Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');

// FAQ Routes
Route::get('/faqs', [FaqController::class, 'index'])->name('faqs.index');
Route::get('/faq', [FaqController::class, 'index'])->name('faq');

// Search Routes
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/search/results', [SearchController::class, 'search'])->name('search.results');

// Contact Routes
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// About Route
Route::get('/about', function () {
    return view('pages.about');
})->name('about');

// Plans Route
Route::get('/plans', [PlansController::class, 'index'])->name('plans');

// Resource Routes
Route::get('/resources', [ResourceController::class, 'index'])->name('resources.index');
Route::get('/resources/category/{slug}', [ResourceController::class, 'category'])->name('resources.category');
Route::get('/resources/{slug}', [ResourceController::class, 'show'])->name('resources.show');
Route::get('/resources/{slug}/download', [ResourceController::class, 'download'])->name('resources.download');

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'role:admin,super-admin,editor,viewer'])->name('admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // News Management
    Route::resource('news', AdminNewsController::class);
    Route::get('news/activity/logs', [AdminNewsController::class, 'activity'])->name('news.activity');
    Route::post('news/bulk-action', [AdminNewsController::class, 'bulkAction'])->name('news.bulk-action');
    
    // Healthcare Providers Management
    Route::resource('providers', AdminProviderController::class);
    Route::get('providers/activity/logs', [AdminProviderController::class, 'activity'])->name('providers.activity');
    Route::post('providers/bulk-action', [AdminProviderController::class, 'bulkAction'])->name('providers.bulk-action');
    
    // FAQ Management
    Route::resource('faqs', AdminFaqController::class);
    Route::get('faqs/activity/logs', [AdminFaqController::class, 'activity'])->name('faqs.activity');
    Route::post('faqs/bulk-action', [AdminFaqController::class, 'bulkAction'])->name('faqs.bulk-action');
    
    // Role Management - restrict to super admin
    Route::resource('roles', RoleController::class)->middleware('role:super-admin');
    Route::get('roles/activity/logs', [RoleController::class, 'activity'])->name('roles.activity')->middleware('role:super-admin');
    
    // User Role Management - restrict to super admin and admin
    Route::middleware('role:super-admin,admin')->group(function() {
        Route::get('users', [UserRoleController::class, 'index'])->name('users.index');
        Route::get('users/{user}/roles', [UserRoleController::class, 'edit'])->name('users.roles.edit');
        Route::put('users/{user}/roles', [UserRoleController::class, 'update'])->name('users.roles.update');
        Route::get('users/activity/logs', [UserRoleController::class, 'activity'])->name('users.activity');
    });
    
    // Activity Log
    Route::get('activity', [ActivityLogController::class, 'index'])->name('activity.index');
    Route::get('activity/{activityLog}', [ActivityLogController::class, 'show'])->name('activity.show');

    // Contact Message Management - restricted to super admin
    Route::middleware('role:super-admin')->group(function() {
        Route::get('messages', [ContactMessageController::class, 'index'])->name('messages.index');
        Route::get('messages/{message}', [ContactMessageController::class, 'show'])->name('messages.show');
        Route::put('messages/{message}/status', [ContactMessageController::class, 'updateStatus'])->name('messages.status.update');
        Route::put('messages/{message}/respond', [ContactMessageController::class, 'markResponded'])->name('messages.respond');
        Route::put('messages/{message}/archive', [ContactMessageController::class, 'archive'])->name('messages.archive');
        Route::get('messages/activity/logs', [ContactMessageController::class, 'activity'])->name('messages.activity');
    });
    
    // Analytics Dashboard - restricted to super admin and admin, not using permission middleware
    Route::middleware('role:super-admin,admin')->group(function() {
        Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics')->withoutMiddleware('CheckPermission');
        Route::get('analytics/reports', [AnalyticsController::class, 'showReportForm'])->name('analytics.reports')->withoutMiddleware('CheckPermission');
        Route::match(['get', 'post'], 'analytics/generate-report', [AnalyticsController::class, 'generateReport'])->name('analytics.generate-report')->withoutMiddleware('CheckPermission');
    });
    
    // Translation Management - restricted to super admin 
    Route::middleware('role:super-admin')->group(function() {
        Route::resource('translations', TranslationController::class)->except(['show']);
        Route::post('translations/import', [TranslationController::class, 'import'])->name('translations.import');
        Route::post('translations/export', [TranslationController::class, 'export'])->name('translations.export');
    });

    // Resource Management
    Route::resource('resources', AdminResourceController::class);
    Route::get('resources/activity/logs', [AdminResourceController::class, 'activity'])->name('resources.activity');
    Route::post('resources/bulk-action', [AdminResourceController::class, 'bulkAction'])->name('resources.bulk-action');
    Route::get('resources/stats/downloads', [AdminResourceController::class, 'downloadStats'])->name('resources.stats.downloads');
    
    // Resource Category Management
    Route::resource('resource-categories', AdminResourceCategoryController::class);
    Route::get('resource-categories/activity/logs', [AdminResourceCategoryController::class, 'activity'])->name('resource-categories.activity');
    Route::post('resource-categories/bulk-action', [AdminResourceCategoryController::class, 'bulkAction'])->name('resource-categories.bulk-action');
});

require __DIR__.'/auth.php';
