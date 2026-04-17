<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    /**
     * Proses Login pengguna dan berikan Token baru
     */
    public function store(Request $request): JsonResponse
    {
        // 1. Validasi input
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        // 3. Cek apakah user ada dan passwordnya cocok
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Kredensial yang diberikan salah atau tidak terdaftar.'],
            ]);
        }

        // 4. (Opsional tapi disarankan) Hapus token lama agar rapi (1 device per sesi)
        $user->tokens()->delete();

        // 5. Buat token baru
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * Proses Logout (Hapus Token)
     */
    public function destroy(Request $request): JsonResponse
    {
        // Hapus token yang sedang digunakan untuk request ini
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil. Token telah dihapus.'
        ]);
    }
}
