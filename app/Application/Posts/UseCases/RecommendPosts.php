<?php

namespace App\Application\Posts\UseCases;

use App\Domain\Posts\Contracts\PostRepository;
use App\Models\User;
use Illuminate\Support\Collection;

use Illuminate\Support\Facades\Cache;

final readonly class RecommendPosts
{
    public function __construct(private PostRepository $posts)
    {
    }

    public function __invoke(User $user): Collection
    {
        return Cache::remember('posts.recommendations.' . $user->id, now()->addMinutes(15), function () use ($user) {
            return $this->posts->matchingRecommendationsFor($user);
        });
    }
}
