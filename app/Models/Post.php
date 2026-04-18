<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'needed_skill_id',
        'offered_skill_id',
        'description',
        'status'
    ];

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
}
