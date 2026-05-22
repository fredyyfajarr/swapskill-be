<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Message $message)
    {
    }

    public function broadcastOn(): array
    {
        // Broadcast ke channel privat antara sender dan receiver
        $chatId = min($this->message->sender_id, $this->message->receiver_id) . '-' . max($this->message->sender_id, $this->message->receiver_id);
        
        return [
            new PrivateChannel('chat.' . $chatId),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'sender_id' => $this->message->sender_id,
            'receiver_id' => $this->message->receiver_id,
            'post_id' => $this->message->post_id,
            'content' => $this->message->content,
            'created_at' => $this->message->created_at->toIso8601String(),
        ];
    }
}
