<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\Skill;

class ProfileController extends Controller
{
    public function current(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'message' => 'Berhasil mengambil data user.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'nim' => $user->nim,
                'role' => $user->role,
                'is_verified' => $user->is_verified,
                'whatsapp_number' => $user->whatsapp_number,
            ],
        ]);
    }

    /**
     * Memuat Profil Sendiri (Tab: Tawaran Saya & Portfolio)
     */
    public function me(Request $request): JsonResponse
    {
        // Tarik data user login + skill + history posts + kalkulasi ulasan
        $user = $request->user()->load([
            'skills:id,name,category',
            'historyPosts' => function ($query) {
                $query
                    ->select('id', 'user_id', 'needed_skill_id', 'offered_skill_id', 'description', 'status', 'created_at')
                    ->latest();
            },
            'historyPosts.neededSkill:id,name',
            'historyPosts.offeredSkill:id,name',
        ])
        ->loadCount('receivedReviews')
        ->loadAvg('receivedReviews', 'rating');

        return response()->json([
            'message' => 'Berhasil mengambil data profil.',
            'data' => $user
        ]);
    }

    /**
     * Memuat Profil Publik (Saat mengunjungi profil orang lain)
     */
    public function showPublic($id): JsonResponse
    {
        $user = User::query()
        ->select('id', 'name', 'nim', 'whatsapp_number', 'created_at')
        ->with([
            'skills:id,name,category',
            'historyPosts' => function ($query) {
                $query
                    ->select('id', 'user_id', 'needed_skill_id', 'offered_skill_id', 'description', 'status', 'created_at')
                    ->where('status', 'open')
                    ->latest();
            },
            'historyPosts.neededSkill:id,name',
            'historyPosts.offeredSkill:id,name',
        ])
        ->withCount('receivedReviews')
        ->withAvg('receivedReviews', 'rating')
        ->findOrFail($id);

        return response()->json([
            'message' => 'Berhasil mengambil profil publik.',
            'data' => $user
        ]);
    }

    /**
     * Menambahkan Skill ke Portofolio
     */
    public function addSkill(Request $request): JsonResponse
    {
        $request->validate(['skill' => 'required|string|max:255']);
        $user = $request->user();

        // Cari skill di database, kalau belum ada, buatkan baru
        $skill = Skill::firstOrCreate(['name' => strtolower(trim($request->skill))]);
        Cache::forget('skills.alphabetical');

        // Masukkan ke portofolio user tanpa menghapus yang lama
        $user->skills()->syncWithoutDetaching([$skill->id]);

        return response()->json([
            'message' => 'Skill berhasil ditambahkan.',
            'data' => $skill
        ]);
    }

    /**
     * Menghapus Skill dari Portofolio
     */
    public function removeSkill(Request $request, $skillId): JsonResponse
    {
        $request->user()->skills()->detach($skillId);

        return response()->json([
            'message' => 'Skill berhasil dihapus.'
        ]);
    }

    /**
     * Update Data Dasar Profil (opsional untuk masa depan)
     */
   public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'nim'  => 'required|string|max:20|unique:users,nim,' . $user->id,
            'whatsapp_number' => 'required|string|max:15',
        ]);

        $user->update([
            'name' => $request->name,
            'nim'  => $request->nim,
            'whatsapp_number' => $request->whatsapp_number,
        ]);

        return response()->json([
            'message' => 'Profil berhasil diperbarui!',
            'data' => $user
        ]);
    }

/**
 * Memperbarui Password User
 */
    public  function updatePassword(Request $request): JsonResponse
    {
        // Validasi input
        $request->validate([
            'current_password' => ['required', 'current_password'], // Memeriksa apakah password lama benar
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // Update password di database
        $request->user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'message' => 'Password berhasil diperbarui!',
        ]);
    }
}
