<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    public function store(RegisterUserRequest $request): JsonResponse
    {
        // 1. Simpan foto KTM
        $ktmPath = $request->file('ktm')->store('ktm', 'public');

        // 2. Buat User baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nim' => $request->nim,
            'whatsapp_number' => $request->whatsapp_number,
            'ktm_path' => $ktmPath,
            'role' => 'student',
        ]);

        // 3. Buat token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil. Silakan tunggu verifikasi Admin.',
            'user' => $user,
            'token' => $token
        ], 201);
    }
}
