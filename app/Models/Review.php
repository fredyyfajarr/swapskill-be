<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'reviewer_id',
    'reviewee_id',
    'barter_request_id',
    'rating',
    'comment'
])]
class Review extends Model
{
    use HasFactory;

    // Relasi ke user yang MEMBERIKAN ulasan
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    // Relasi ke user yang MENERIMA ulasan
    public function reviewee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }

    public function barterRequest(): BelongsTo
    {
        return $this->belongsTo(BarterRequest::class);
    }
}
