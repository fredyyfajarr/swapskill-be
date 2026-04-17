<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request; // <-- JANGAN LUPA TAMBAHKAN INI

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // $middleware->api(prepend: [
        //     \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        // ]);

        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // --- TAMBAHKAN KODE INI ---
        // Memaksa semua error di route /api/ menjadi format JSON
        $exceptions->shouldRenderJsonWhen(function (Request $request) {
            return $request->is('api/*');
        });

        // --- TAMBAHKAN KODE INI ---
        // Mengubah pesan error 404 Not Found menjadi bahasa manusia
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Yahh, data yang kamu cari tidak ditemukan nih. Coba cek lagi ya!'
                ], 404);
            }
        });
        // --------------------------

    })->create();
