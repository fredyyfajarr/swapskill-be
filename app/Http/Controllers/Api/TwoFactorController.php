<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    /**
     * Enable 2FA and get the QR Code & Secret.
     */
    public function enable(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();
        
        $user->two_factor_secret = encrypt($secret);
        $user->save();

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        return response()->json([
            'message' => '2FA has been enabled but not confirmed. Scan the QR code or use the secret key.',
            'secret' => $secret,
            'qr_code_url' => $qrCodeUrl,
        ]);
    }

    /**
     * Confirm the 2FA using OTP.
     */
    public function confirm(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = $request->user();

        if (!$user->two_factor_secret) {
            return response()->json(['message' => '2FA is not enabled.'], 400);
        }

        $google2fa = new Google2FA();
        $secret = decrypt($user->two_factor_secret);

        $valid = $google2fa->verifyKey($secret, $request->code);

        if ($valid) {
            $user->two_factor_confirmed_at = now();
            $user->save();

            return response()->json(['message' => '2FA successfully confirmed!']);
        }

        return response()->json(['message' => 'Invalid OTP code.'], 400);
    }
    
    /**
     * Disable 2FA.
     */
    public function disable(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->two_factor_secret = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        return response()->json(['message' => '2FA has been disabled.']);
    }
}
