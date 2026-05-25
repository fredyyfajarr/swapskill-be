<?php

use App\Http\Controllers\Auth\AdminSessionBridgeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::get('/admin/session-bridge/{user}', AdminSessionBridgeController::class)
    ->middleware('signed')
    ->name('admin.session-bridge');

require __DIR__.'/auth.php';
