<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\BookmarkController;

use App\Http\Middleware\EnsureUserIsVerified;
use App\Models\Skill;

// --- RUTE PUBLIK (Tanpa Login) ---
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login', [LoginController::class, 'store']);

// Rute untuk mengambil daftar keahlian (Untuk Dropdown Frontend)
Route::get('/skills', function () {
    return response()->json([
        'message' => 'Berhasil mengambil daftar skill',
        'data' => Skill::orderBy('name', 'asc')->get()
    ]);
});

// --- RUTE PRIVATE (Harus Login) ---
Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/logout', [LoginController::class, 'destroy']);

    // --- RUTE PROFIL SENDIRI ---
    Route::get('/profile', [ProfileController::class, 'me']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::post('/profile/skills', [ProfileController::class, 'addSkill']);
    Route::delete('/profile/skills/{skillId}', [ProfileController::class, 'removeSkill']);

    // --- RUTE STATISTIK PERSONAL ---
    Route::get('/profile/stats', [\App\Http\Controllers\Api\AnalyticController::class, 'getPersonalStats']);

    // ---> INI DIA RUTE PROFIL PUBLIKNYA <---
    Route::get('/users/{id}/profile', [ProfileController::class, 'showPublic']);

    // --- RUTE REVIEW / ULASAN ---
    Route::post('/reviews', [ReviewController::class, 'store']); // Kirim ulasan
    Route::get('/users/{id}/reviews', [ReviewController::class, 'index']); // Lihat ulasan milik seseorang

    // --- RUTE BOOKMARK ---
    Route::post('/posts/{post}/bookmark', [BookmarkController::class, 'toggle']);
    Route::get('/bookmarks', [BookmarkController::class, 'index']);

    // --- RUTE NOTIFIKASI ---
    Route::get('/notifications', [\App\Http\Controllers\Api\NotificationController::class, 'index']);
    Route::post('/notifications/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
    Route::delete('/notifications/clear', [\App\Http\Controllers\Api\NotificationController::class, 'clearAll']);
    // --- RUTE POSTINGAN (UMUM) ---
    Route::get('/posts', [PostController::class, 'index']);

    // --- RUTE POSTINGAN (KHUSUS VERIFIED USER) ---
    Route::middleware([EnsureUserIsVerified::class])->group(function () {
        Route::post('/posts', [PostController::class, 'store'])->middleware('throttle:5,1'); // Batasi 5 postingan per menit
        Route::get('/posts/recommendations', [PostController::class, 'recommendations']);
        Route::post('/posts/{post}/whatsapp', [PostController::class, 'generateWhatsAppLink']);
        Route::patch('/posts/{post}/status', [PostController::class, 'updateStatus']);
        Route::delete('/posts/{post}', [PostController::class, 'destroy']);
    });
});
