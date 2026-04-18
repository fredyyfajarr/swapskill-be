<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
  public function index(Request $request): JsonResponse
    {
        $searchQuery = $request->query('search');
        $skillId = $request->query('skill_id'); // Tangkap filter Dropdown Skill
        $sortBy = $request->query('sort', 'latest'); // Tangkap filter Urutan

        $user = $request->user();

        $query = Post::with([
            'user:id,name,whatsapp_number',
            'neededSkill:id,name',
            'offeredSkill:id,name'
        ])
        ->withExists(['bookmarks as is_bookmarked' => function ($q) use ($user) {
            $q->where('user_id', $user->id);
        }])
        ->where('status', 'open');

        // 1. Logika Pencarian Teks (Search)
        $query->when($searchQuery, function ($q, $searchQuery) {
            $q->where(function ($subQ) use ($searchQuery) {
                $subQ->whereHas('neededSkill', function ($sq) use ($searchQuery) {
                    $sq->where('name', 'like', "%{$searchQuery}%");
                })
                ->orWhereHas('offeredSkill', function ($sq) use ($searchQuery) {
                    $sq->where('name', 'like', "%{$searchQuery}%");
                });
            });
        });

        // 2. Logika Filter Dropdown Skill
        $query->when($skillId, function ($q, $skillId) {
            $q->where('needed_skill_id', $skillId);
        });

        // 3. Logika Pengurutan (Sort)
        if ($sortBy === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $posts = $query->paginate(5);

        // --- TAMBAHAN LOGIKA PESAN ---
        if ($posts->isEmpty()) {
            $message = "Belum ada tawaran yang pas dengan filtermu.";
        } else {
            $message = "Berhasil mengambil data Skill Board";
        }

        return response()->json([
            'message' => $message,
            'data' => $posts->items(),
            'current_page' => $posts->currentPage(),
            'has_more' => $posts->hasMorePages()
        ]);
    }
    public function store(Request $request): JsonResponse
    {
        // 1. Validasi sekarang menerima string teks, bukan lagi ID
        $validated = $request->validate([
            'needed_skill' => ['required', 'string', 'max:100'],
            'offered_skill' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:1000'],
        ]);

        // 2. LOGIKA AJAIB (Dynamic Tagging):
        // firstOrCreate akan mencari keahlian berdasarkan nama.
        // Jika sudah ada (misal: "React"), ambil ID-nya.
        // Jika belum ada (misal: "SvelteKit"), buatkan data baru secara otomatis!
        $neededSkill = \App\Models\Skill::firstOrCreate([
            'name' => trim($validated['needed_skill'])
        ]);

        $offeredSkill = \App\Models\Skill::firstOrCreate([
            'name' => trim($validated['offered_skill'])
        ]);

        // 3. Simpan Postingan menggunakan ID yang didapat dari langkah ke-2
        $post = $request->user()->posts()->create([
            'needed_skill_id' => $neededSkill->id,
            'offered_skill_id' => $offeredSkill->id,
            'description' => $validated['description'],
            'status' => 'open'
        ]);

        return response()->json([
            'message' => 'Postingan berhasil dibuat!',
            'data' => $post
        ], 201);
    }

public function updateStatus(Request $request, Post $post): JsonResponse
    {
        // 1. Panggil Satpam (Otomatis melempar 403 jika bukan pemiliknya)
        Gate::authorize('update', $post);

        $validated = $request->validate([
            'status' => ['required', 'in:open,in_progress,completed'],
        ]);

        $post->update(['status' => $validated['status']]);

        return response()->json([
            'message' => "Status postingan diubah menjadi '{$validated['status']}'",
            'data' => $post
        ]);
    }

    public function destroy(Request $request, Post $post): JsonResponse
    {
        // 1. Panggil Satpam (Otomatis melempar 403 jika bukan pemiliknya)
        Gate::authorize('delete', $post);

        // 2. Eksekusi Hapus dari database
        $post->delete();

        return response()->json([
            'message' => 'Postingan barter berhasil dihapus permanen.'
        ]);
    }

    public function generateWhatsAppLink(Post $post, Request $request): JsonResponse
    {
        $owner = $post->user;
        $bidder = $request->user();

        if ($owner->id === $bidder->id) {
            return response()->json(['message' => 'Tidak bisa menawar postingan sendiri.'], 403);
        }

        $text = "Halo {$owner->name}, saya {$bidder->name} dari SwapSkill. Saya lihat kamu butuh bantuan *{$post->neededSkill->name}* dan menawarkan *{$post->offeredSkill->name}*. Saya tertarik untuk barter!";
        $phone = preg_replace('/^0/', '62', $owner->whatsapp_number);

        return response()->json([
            'message' => 'Link WhatsApp berhasil dibuat.',
            'whatsapp_url' => "https://wa.me/{$phone}?text=" . urlencode($text)
        ]);
    }

    /**
     * Menghapus postingan barter (Hanya bisa dilakukan oleh pemilik postingan)
     */


    public function recommendations(Request $request): JsonResponse
    {
        $user = $request->user();

        // 1. Ambil ID Skill yang dikuasai user (dari portofolio)
        $mySkills = $user->skills()->pluck('skills.id')->toArray();

        // 2. Ambil ID Skill yang dibutuhkan user (dari postingannya yang status 'open')
        $myNeeds = $user->posts()
            ->where('status', 'open')
            ->pluck('needed_skill_id')
            ->toArray();

        // 3. Cari postingan orang lain yang cocok (Perfect Match)
        // Syarat: (Dia menawarkan apa yang saya butuh) DAN (Dia butuh apa yang saya punya)
        $perfectMatches = Post::with(['user:id,name,whatsapp_number', 'neededSkill', 'offeredSkill'])
            ->where('user_id', '!=', $user->id)
            ->where('status', 'open')
            ->whereIn('offered_skill_id', $myNeeds) // Dia menawarkan yang saya butuh
            ->whereIn('needed_skill_id', $mySkills) // Dia butuh yang saya punya
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'message' => 'Rekomendasi jodoh barter ditemukan!',
            'data' => $perfectMatches
        ]);
    }
}
