<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BarterRequest;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    /**
     * Menyimpan ulasan baru
     */
    public function store(Request $request): JsonResponse
    {
        // 1. Validasi input
        $request->validate([
            'reviewee_id' => 'required|exists:users,id',
            'barter_request_id' => 'required|exists:barter_requests,id',
            'rating'      => 'required|integer|min:1|max:5',
            'comment'     => 'required|string|max:500'
        ]);

        $reviewer = $request->user();
        $barterRequest = BarterRequest::with('post')->findOrFail($request->barter_request_id);

        // 2. Cegah user memberi bintang ke dirinya sendiri
        if ($reviewer->id == $request->reviewee_id) {
            return response()->json([
                'message' => 'Tidak bisa memberikan ulasan ke diri sendiri.'
            ], 400);
        }

        if ($barterRequest->status !== 'completed') {
            return response()->json([
                'message' => 'Ulasan hanya bisa diberikan setelah barter selesai.'
            ], 422);
        }

        $postOwnerId = $barterRequest->post->user_id;
        $requesterId = $barterRequest->requester_id;
        $isParticipant = in_array($reviewer->id, [$postOwnerId, $requesterId], true);
        $isReviewingPartner = (
            ($reviewer->id === $postOwnerId && (int) $request->reviewee_id === $requesterId) ||
            ($reviewer->id === $requesterId && (int) $request->reviewee_id === $postOwnerId)
        );

        if (!$isParticipant || !$isReviewingPartner) {
            return response()->json([
                'message' => 'Kamu hanya bisa memberi ulasan ke partner barter ini.'
            ], 403);
        }

        // 3. Cegah user melakukan spam ulasan ke orang yang sama berkali-kali
        $existingReview = Review::where('reviewer_id', $reviewer->id)
                                ->where('reviewee_id', $request->reviewee_id)
                                ->first();

        if ($existingReview) {
            return response()->json([
                'message' => 'Kamu sudah pernah memberikan ulasan untuk teman ini.'
            ], 400);
        }

        // 4. Simpan ulasannya ke database
        Review::create([
            'reviewer_id' => $reviewer->id,
            'reviewee_id' => $request->reviewee_id,
            'barter_request_id' => $barterRequest->id,
            'rating'      => $request->rating,
            'comment'     => strip_tags($request->comment)
        ]);

        // --- TAMBAHKAN LOGIKA NOTIFIKASI DI SINI ---
        \App\Jobs\SendNotificationJob::dispatch(
            $request->reviewee_id,
            'review',
            [
                'sender_name' => $reviewer->name, // Nama yang memberikan review
                'rating'      => $request->rating,
                'message'     => 'memberikan ulasan ⭐' . $request->rating . ' untukmu!'
            ]
        );
        // -------------------------------------------

        return response()->json([
            'message' => 'Ulasan berhasil dikirim! Terima kasih. ⭐'
        ]);
    }

    /**
     * Mengambil daftar ulasan milik seseorang
     */
    public function index($userId): JsonResponse
    {
        // Tarik data ulasan sekaligus nama orang yang memberikan ulasan
        $reviews = Review::with('reviewer:id,name')
                         ->where('reviewee_id', $userId)
                         ->latest()
                         ->get();

        return response()->json([
            'message' => 'Berhasil mengambil data ulasan.',
            'data'    => $reviews
        ]);
    }
}
