<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\Skill;

class ProfileController extends Controller
{
    /**
     * Memuat Profil Sendiri (Tab: Tawaran Saya & Portfolio)
     */
    public function me(Request $request): JsonResponse
    {
        // Tarik data user login + skill + history posts + kalkulasi ulasan
        $user = $request->user()->load([
            'skills',
            'historyPosts.neededSkill',
            'historyPosts.offeredSkill'
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
        $user = User::with([
            'skills',
            // Hanya tampilkan tawaran yang masih 'open' di profil publik
            'historyPosts' => function($query) {
                $query->where('status', 'open')->latest();
            },
            'historyPosts.neededSkill',
            'historyPosts.offeredSkill'
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
}
