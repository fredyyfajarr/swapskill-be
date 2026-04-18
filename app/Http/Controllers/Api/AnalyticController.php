<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Post;
use App\Models\Bookmark;

class AnalyticController extends Controller
{
    /**
     * Mengambil statistik personal untuk Dashboard Profil
     */
    public function getPersonalStats(Request $request): JsonResponse
    {
        $user = $request->user();

        // 1. Total semua tawaran yang pernah dibuat user ini
        $totalPosts = Post::where('user_id', $user->id)->count();

        // 2. Tawaran yang masih "open" (Aktif)
        $activePosts = Post::where('user_id', $user->id)->where('status', 'open')->count();

        // 3. Berapa kali postingan user ini di-bookmark oleh orang lain
        $totalBookmarkedByOthers = Bookmark::whereHas('post', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();

        return response()->json([
            'message' => 'Berhasil mengambil statistik personal.',
            'data' => [
                'total_posts'  => $totalPosts,
                'active_posts' => $activePosts,
                'saved_count'  => $totalBookmarkedByOthers
            ]
        ]);
    }
}
