<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\BookmarkController;
use App\Http\Controllers\Api\PortfolioController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\Api\TwoFactorController;
use App\Http\Controllers\Api\BarterRequestController;

use App\Http\Middleware\EnsureUserIsVerified;

// --- RUTE PUBLIK (Tanpa Login) ---
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1');

// Rute untuk mengambil daftar keahlian (Untuk Dropdown Frontend)
Route::get('/skills', [SkillController::class, 'index']);

// --- RUTE PRIVATE (Harus Login) ---
Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/logout', [LoginController::class, 'destroy']);

    // --- RUTE 2FA ---
    Route::post('/2fa/enable', [TwoFactorController::class, 'enable']);
    Route::post('/2fa/confirm', [TwoFactorController::class, 'confirm'])->middleware('throttle:3,1');
    Route::post('/2fa/disable', [TwoFactorController::class, 'disable']);

    // --- RUTE PROFIL SENDIRI ---
    Route::get('/profile', [ProfileController::class, 'me']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->middleware('throttle:5,1');
    Route::post('/profile/skills', [ProfileController::class, 'addSkill']);
    Route::delete('/profile/skills/{skillId}', [ProfileController::class, 'removeSkill']);

    // --- RUTE STATISTIK PERSONAL ---
    Route::get('/profile/stats', [\App\Http\Controllers\Api\AnalyticController::class, 'getPersonalStats']);

    // --- RUTE PROFIL PUBLIK ---
    Route::get('/users/{id}/profile', [ProfileController::class, 'showPublic']);

    // --- RUTE REVIEW / ULASAN ---
    Route::post('/reviews', [ReviewController::class, 'store'])->middleware('throttle:5,1');
    Route::get('/users/{id}/reviews', [ReviewController::class, 'index']);

    // --- RUTE BOOKMARK ---
    Route::post('/posts/{post}/bookmark', [BookmarkController::class, 'toggle']);
    Route::get('/bookmarks', [BookmarkController::class, 'index']);

    // --- RUTE PORTFOLIO ---
    Route::get('/portfolios', [PortfolioController::class, 'index']);
    Route::post('/portfolios', [PortfolioController::class, 'store']);

    // --- RUTE NOTIFIKASI ---
    Route::get('/notifications', [\App\Http\Controllers\Api\NotificationController::class, 'index']);
    Route::post('/notifications/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
    Route::delete('/notifications/clear', [\App\Http\Controllers\Api\NotificationController::class, 'clearAll']);

    // --- RUTE POSTINGAN (UMUM) ---
    Route::get('/posts', [PostController::class, 'index']);

    // --- RUTE PESAN (CHAT IN-APP) ---
    Route::get('/messages', [MessageController::class, 'index']);
    Route::get('/messages/{userId}', [MessageController::class, 'show']);
    Route::post('/messages/{userId}', [MessageController::class, 'store'])->middleware('throttle:30,1');

    // --- RUTE BARTER REQUEST ---
    Route::get('/barter-requests', [BarterRequestController::class, 'index']);
    Route::post('/barter-requests', [BarterRequestController::class, 'store']);
    Route::patch('/barter-requests/{barterRequest}/accept', [BarterRequestController::class, 'accept']);
    Route::patch('/barter-requests/{barterRequest}/reject', [BarterRequestController::class, 'reject']);
    Route::patch('/barter-requests/{barterRequest}/complete', [BarterRequestController::class, 'complete']);
    Route::delete('/barter-requests/{barterRequest}/cancel', [BarterRequestController::class, 'cancel']);

    // --- RUTE POSTINGAN (KHUSUS VERIFIED USER) ---
    Route::middleware([EnsureUserIsVerified::class])->group(function () {
        Route::post('/posts', [PostController::class, 'store'])->middleware('throttle:5,1');
        Route::get('/posts/recommendations', [PostController::class, 'recommendations']);
        Route::put('/posts/{post}', [PostController::class, 'update']);
        Route::patch('/posts/{post}/status', [PostController::class, 'updateStatus']);
        Route::delete('/posts/{post}', [PostController::class, 'destroy']);
    });
});
