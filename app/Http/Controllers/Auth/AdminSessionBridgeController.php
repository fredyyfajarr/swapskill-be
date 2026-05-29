<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminSessionBridgeController extends Controller
{
    public function __invoke(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->role === 'admin', 403);

        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        return redirect('/admin');
    }
}
