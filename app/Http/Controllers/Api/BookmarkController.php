<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BookmarkController extends Controller
{
    /**
     * Menyimpan atau Menghapus Simpanan (Toggle)
     */
    public function toggle(Request $request, Post $post): JsonResponse
    {
        $user = $request->user();

        // 1. Cek apakah user ini sudah menyimpan postingan ini sebelumnya
        $bookmark = Bookmark::where('user_id', $user->id)
                            ->where('post_id', $post->id)
                            ->first();

        if ($bookmark) {
            // 2a. Jika SUDAH ADA, hapus (Un-bookmark)
            $bookmark->delete();

            // Catatan: Biasanya kita TIDAK mengirim notifikasi saat orang meng-UN-bookmark

            return response()->json([
                'message' => 'Tawaran dihapus dari daftar simpanan.',
                'is_bookmarked' => false
            ]);
        } else {
            // 2b. Jika BELUM ADA, simpan (Bookmark)
            Bookmark::create([
                'user_id' => $user->id,
                'post_id' => $post->id
            ]);

            // --- TAMBAHKAN LOGIKA NOTIFIKASI DI SINI ---
            // Kita harus pastikan user TIDAK mengirim notifikasi ke dirinya sendiri
            // (kalau dia iseng mem-bookmark postingannya sendiri)
            if ($post->user_id !== $user->id) {
                // Pastikan relasi neededSkill sudah ter-load untuk mengambil nama skillnya
                $post->load('neededSkill', 'offeredSkill');

                \App\Jobs\SendNotificationJob::dispatch(
                    $post->user_id,
                    'bookmark',
                    [
                        'sender_name' => $user->name, // Nama yang melakukan bookmark
                        'post_title'  => $post->neededSkill->name . ' ↔ ' . $post->offeredSkill->name, // Ringkasan tawaran
                        'message'     => 'baru saja menyimpan tawaranmu!'
                    ]
                );
            }
            // -------------------------------------------

            return response()->json([
                'message' => 'Tawaran berhasil disimpan! 📌',
                'is_bookmarked' => true
            ]);
        }
    }

    /**
     * Mengambil daftar tawaran yang disimpan oleh User
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $bookmarks = Bookmark::with([
            'post.user:id,name,whatsapp_number',
            'post.neededSkill',
            'post.offeredSkill'
        ])
        ->where('user_id', $user->id)
        ->latest()
        ->get();

        return response()->json([
            'message' => 'Berhasil mengambil daftar simpanan.',
            'data' => $bookmarks
        ]);
    }
}
