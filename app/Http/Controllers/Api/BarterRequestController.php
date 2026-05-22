<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BarterRequest;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BarterRequestController extends Controller
{
    /**
     * List all barter requests for the current user (as requester or post owner).
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $barters = BarterRequest::with([
            'post.user:id,name',
            'post.neededSkill:id,name',
            'post.offeredSkill:id,name',
            'requester:id,name',
        ])
            ->where(function ($q) use ($userId) {
                $q->where('requester_id', $userId)
                  ->orWhereHas('post', fn($pq) => $pq->where('user_id', $userId));
            })
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Berhasil mengambil daftar barter.',
            'data' => $barters,
        ]);
    }

    /**
     * Create a new barter request.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'message' => 'nullable|string|max:500',
        ]);

        $post = Post::findOrFail($validated['post_id']);
        $user = $request->user();

        // Can't request own post
        if ($post->user_id === $user->id) {
            return response()->json(['message' => 'Tidak bisa mengajukan barter ke postingan sendiri.'], 422);
        }

        // Post must be open
        if ($post->status !== 'open') {
            return response()->json(['message' => 'Tawaran ini sudah tidak tersedia.'], 422);
        }

        // Can't request if already have a pending/accepted request
        $existing = BarterRequest::where('post_id', $post->id)
            ->where('requester_id', $user->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Kamu sudah mengajukan barter untuk tawaran ini.'], 422);
        }

        $barter = BarterRequest::create([
            'post_id' => $post->id,
            'requester_id' => $user->id,
            'message' => $validated['message'] ?? null,
            'status' => 'pending',
        ]);

        // Send notification to post owner
        if (class_exists(\App\Jobs\SendNotificationJob::class)) {
            \App\Jobs\SendNotificationJob::dispatch(
                $post->user_id,
                'barter_request',
                [
                    'sender_name' => $user->name,
                    'post_title' => $post->neededSkill?->name . ' ↔ ' . $post->offeredSkill?->name,
                    'message' => 'mengajukan barter denganmu!',
                ]
            );
        }

        return response()->json([
            'message' => 'Pengajuan barter berhasil dikirim!',
            'data' => $barter->load(['post.user:id,name', 'post.neededSkill', 'post.offeredSkill', 'requester:id,name']),
        ], 201);
    }

    /**
     * Accept a barter request (only post owner).
     */
    public function accept(Request $request, BarterRequest $barterRequest): JsonResponse
    {
        $user = $request->user();

        if ($barterRequest->post->user_id !== $user->id) {
            return response()->json(['message' => 'Hanya pemilik tawaran yang bisa menerima.'], 403);
        }

        if ($barterRequest->status !== 'pending') {
            return response()->json(['message' => 'Pengajuan ini sudah tidak bisa diterima.'], 422);
        }

        // Accept this request
        $barterRequest->update(['status' => 'accepted']);

        // Update post status
        $barterRequest->post->update(['status' => 'in_progress']);

        // Reject all other pending requests for the same post
        BarterRequest::where('post_id', $barterRequest->post_id)
            ->where('id', '!=', $barterRequest->id)
            ->where('status', 'pending')
            ->update(['status' => 'rejected']);

        // Notify requester
        if (class_exists(\App\Jobs\SendNotificationJob::class)) {
            \App\Jobs\SendNotificationJob::dispatch(
                $barterRequest->requester_id,
                'barter_accepted',
                [
                    'sender_name' => $user->name,
                    'message' => 'menerima pengajuan bartermu! Mulai chat sekarang.',
                ]
            );
        }

        return response()->json([
            'message' => 'Barter diterima! Kalian bisa mulai chat.',
            'data' => $barterRequest->fresh(['post.user:id,name', 'requester:id,name']),
        ]);
    }

    /**
     * Reject a barter request (only post owner).
     */
    public function reject(Request $request, BarterRequest $barterRequest): JsonResponse
    {
        if ($barterRequest->post->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Hanya pemilik tawaran yang bisa menolak.'], 403);
        }

        if ($barterRequest->status !== 'pending') {
            return response()->json(['message' => 'Pengajuan ini sudah tidak bisa ditolak.'], 422);
        }

        $barterRequest->update(['status' => 'rejected']);

        return response()->json(['message' => 'Pengajuan barter ditolak.']);
    }

    /**
     * Confirm completion (both parties must confirm).
     */
    public function complete(Request $request, BarterRequest $barterRequest): JsonResponse
    {
        $user = $request->user();
        $isRequester = $barterRequest->requester_id === $user->id;
        $isOwner = $barterRequest->post->user_id === $user->id;

        if (!$isRequester && !$isOwner) {
            return response()->json(['message' => 'Kamu bukan bagian dari barter ini.'], 403);
        }

        if ($barterRequest->status !== 'accepted') {
            return response()->json(['message' => 'Barter ini belum diterima atau sudah selesai.'], 422);
        }

        // Set the appropriate confirmation flag
        if ($isRequester) {
            $barterRequest->update(['requester_confirmed_complete' => true]);
        }
        if ($isOwner) {
            $barterRequest->update(['owner_confirmed_complete' => true]);
        }

        $barterRequest->refresh();

        // If both confirmed, mark as completed
        if ($barterRequest->requester_confirmed_complete && $barterRequest->owner_confirmed_complete) {
            $barterRequest->update(['status' => 'completed']);
            $barterRequest->post->update(['status' => 'completed']);

            return response()->json([
                'message' => 'Barter selesai! Kalian bisa saling memberi ulasan sekarang. 🎉',
                'data' => $barterRequest->fresh(),
                'both_confirmed' => true,
            ]);
        }

        return response()->json([
            'message' => 'Konfirmasi berhasil. Menunggu konfirmasi dari pihak lain.',
            'data' => $barterRequest->fresh(),
            'both_confirmed' => false,
        ]);
    }

    /**
     * Cancel a barter request (only requester, only if pending).
     */
    public function cancel(Request $request, BarterRequest $barterRequest): JsonResponse
    {
        if ($barterRequest->requester_id !== $request->user()->id) {
            return response()->json(['message' => 'Hanya pengaju yang bisa membatalkan.'], 403);
        }

        if ($barterRequest->status !== 'pending') {
            return response()->json(['message' => 'Pengajuan ini sudah tidak bisa dibatalkan.'], 422);
        }

        $barterRequest->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Pengajuan barter dibatalkan.']);
    }
}
