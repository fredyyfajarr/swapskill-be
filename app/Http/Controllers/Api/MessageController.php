<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Get a list of users the current user has chatted with.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        // Fetch distinct user IDs that the current user has exchanged messages with
        $contacts = User::whereHas('sentMessages', function ($query) use ($userId) {
            $query->where('receiver_id', $userId);
        })->orWhereHas('receivedMessages', function ($query) use ($userId) {
            $query->where('sender_id', $userId);
        })->get(['id', 'name', 'avatar_path']); // Or adjust select fields as needed

        return response()->json([
            'message' => 'Berhasil mengambil daftar kontak obrolan.',
            'data' => $contacts
        ]);
    }

    /**
     * Get messages between the current user and another user.
     */
    public function show(Request $request, int $otherUserId): JsonResponse
    {
        $userId = $request->user()->id;

        $messages = Message::where(function ($query) use ($userId, $otherUserId) {
            $query->where('sender_id', $userId)->where('receiver_id', $otherUserId);
        })->orWhere(function ($query) use ($userId, $otherUserId) {
            $query->where('sender_id', $otherUserId)->where('receiver_id', $userId);
        })
        ->oldest()
        ->get();

        // Mark unread messages as read
        Message::where('sender_id', $otherUserId)
            ->where('receiver_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'message' => 'Berhasil mengambil histori pesan.',
            'data' => $messages
        ]);
    }

    /**
     * Send a new message.
     */
    public function store(Request $request, int $receiverId): JsonResponse
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'post_id' => 'nullable|exists:posts,id',
        ]);

        $sender = $request->user();

        if ($sender->id === $receiverId) {
            return response()->json(['message' => 'Tidak bisa mengirim pesan ke diri sendiri.'], 400);
        }

        $message = Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiverId,
            'post_id' => $request->post_id,
            'content' => $request->content,
        ]);

        // Broadcast the message via Reverb
        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'message' => 'Pesan berhasil dikirim.',
            'data' => $message
        ]);
    }
}
