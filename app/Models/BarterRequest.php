<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarterRequest extends Model
{
    protected $fillable = [
        'post_id',
        'requester_id',
        'status',
        'message',
        'requester_confirmed_complete',
        'owner_confirmed_complete',
    ];

    protected $casts = [
        'requester_confirmed_complete' => 'boolean',
        'owner_confirmed_complete' => 'boolean',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    /**
     * Get the post owner (shortcut via post relationship).
     */
    public function getOwnerAttribute(): ?User
    {
        return $this->post?->user;
    }
}
