<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Models\Post;

#[Fillable([
    'name',
    'email',
    'password',
    'nim',
    'whatsapp_number',
    'ktm_path',
    'role',
    'is_verified'
])]
#[Hidden([
    'password',
    'remember_token',
])]
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
        ];
    }

    // Relasi: Satu mahasiswa bisa membuat banyak postingan
    // public function posts(): HasMany
    // {
    //     return $this->hasMany(Post::class);
    // }

    // Relasi untuk melihat ulasan yang DITERIMA oleh user ini
    public function reviewsReceived(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }


    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Ulasan yang DITERIMA oleh user ini
     */
    public function receivedReviews()
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    /**
     * Relasi untuk mengambil riwayat postingan (digunakan di halaman Profil)
     */
    public function historyPosts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Relasi untuk mengambil daftar bookmark (digunakan di halaman Profil)
     */
    public function userNotifications()
    {
        return $this->hasMany(\App\Models\Notification::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(\App\Models\Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(\App\Models\Message::class, 'receiver_id');
    }

    public function badges()
    {
        return $this->belongsToMany(\App\Models\Badge::class);
    }

    public function portfolios()
    {
        return $this->hasMany(\App\Models\Portfolio::class);
    }
}
