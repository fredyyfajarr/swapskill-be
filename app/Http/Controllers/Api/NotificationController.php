<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    /**
     * Mengambil daftar notifikasi user
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()->userNotifications()
                                         ->latest()
                                         ->take(10) // Ambil 10 notif terbaru saja
                                         ->get();

        $unreadCount = $request->user()->userNotifications()->where('is_read', false)->count();

        return response()->json([
            'message' => 'Berhasil mengambil notifikasi.',
            'data'    => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Menandai semua notifikasi sudah dibaca
     */
    public function markAsRead(Request $request): JsonResponse
    {
        $request->user()->userNotifications()->where('is_read', false)->update(['is_read' => true]);

        return response()->json([
            'message' => 'Semua notifikasi telah ditandai dibaca.'
        ]);
    }

    /**
     * Menghapus semua notifikasi user (Clear All)
     */
    public function clearAll(Request $request): JsonResponse
    {
        // Hapus semua data notifikasi milik user yang sedang login
        $request->user()->userNotifications()->delete();

        return response()->json([
            'message' => 'Semua notifikasi berhasil dibersihkan.'
        ]);
    }
}
