<?php

namespace App\Application\Posts\UseCases;

use App\Domain\Posts\Contracts\PostRepository;
use App\Models\User;
use Illuminate\Support\Collection;

final readonly class RecommendPosts
{
    public function __construct(private PostRepository $posts)
    {
    }

    public function __invoke(User $user): Collection
    {
        return $this->posts->matchingRecommendationsFor($user);
    }
}
