<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable([
    'user_id',
    'needed_skill_id',
    'offered_skill_id',
    'description',
    'status'
])]
class Post extends Model
{
    use HasFactory;

    // Relasi ke pembuat postingan
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke keahlian yang dicari
    public function neededSkill(): BelongsTo
    {
        return $this->belongsTo(Skill::class, 'needed_skill_id');
    }

    // Relasi ke keahlian yang ditawarkan
    public function offeredSkill(): BelongsTo
    {
        return $this->belongsTo(Skill::class, 'offered_skill_id');
    }

    /**
     * Relasi ke tabel bookmarks
     */
    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function barterRequests()
    {
        return $this->hasMany(BarterRequest::class);
    }

    public function acceptedBarter()
    {
        return $this->hasOne(BarterRequest::class)->where('status', 'accepted')->orWhere('status', 'completed');
    }
}
