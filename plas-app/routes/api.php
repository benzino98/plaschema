<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\NewsApiController;
use App\Http\Controllers\API\HealthcareProviderApiController;
use App\Http\Controllers\API\FaqApiController;
use App\Http\Controllers\API\ContactMessageApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// News endpoints
Route::get('/news', [NewsApiController::class, 'index']);
Route::get('/news/{news}', [NewsApiController::class, 'show']);

// Healthcare Provider endpoints
Route::get('/providers', [HealthcareProviderApiController::class, 'index']);
Route::get('/providers/{provider}', [HealthcareProviderApiController::class, 'show']);

// FAQ endpoints
Route::get('/faqs', [FaqApiController::class, 'index']);
Route::get('/faqs/{faq}', [FaqApiController::class, 'show']);

// Contact endpoints
Route::post('/contact', [ContactMessageApiController::class, 'store']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Add logout route
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // News management
    Route::post('/news', [NewsApiController::class, 'store']);
    Route::put('/news/{news}', [NewsApiController::class, 'update']);
    Route::delete('/news/{news}', [NewsApiController::class, 'destroy']);
    
    // Healthcare Provider management
    Route::post('/providers', [HealthcareProviderApiController::class, 'store']);
    Route::put('/providers/{provider}', [HealthcareProviderApiController::class, 'update']);
    Route::delete('/providers/{provider}', [HealthcareProviderApiController::class, 'destroy']);
    
    // FAQ management
    Route::post('/faqs', [FaqApiController::class, 'store']);
    Route::put('/faqs/{faq}', [FaqApiController::class, 'update']);
    Route::delete('/faqs/{faq}', [FaqApiController::class, 'destroy']);
    
    // Contact message management (admin only)
    Route::get('/contact-messages', [ContactMessageApiController::class, 'index']);
    Route::get('/contact-messages/{message}', [ContactMessageApiController::class, 'show']);
    Route::put('/contact-messages/{message}/status', [ContactMessageApiController::class, 'updateStatus']);
}); 