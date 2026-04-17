<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->is_verified) {
            return response()->json([
                'error' => 'Forbidden',
                'message' => 'Akun kamu belum diverifikasi oleh Admin. Harap tunggu.'
            ], 403);
        }

        return $next($request);
    }
}
