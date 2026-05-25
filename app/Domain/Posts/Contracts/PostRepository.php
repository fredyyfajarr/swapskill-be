<?php

namespace App\Domain\Posts\Contracts;

use App\Models\Post;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface PostRepository
{
    public function paginateOpenForUser(
        User $user,
        ?string $search,
        ?int $skillId,
        string $sortBy,
        bool $bookmarkedOnly
    ): LengthAwarePaginator;

    public function createForUser(User $user, Skill $neededSkill, Skill $offeredSkill, string $description): Post;

    public function update(Post $post, Skill $neededSkill, Skill $offeredSkill, string $description): Post;

    public function updateStatus(Post $post, string $status): Post;

    public function delete(Post $post): void;

    public function matchingRecommendationsFor(User $user, int $limit = 5): Collection;
}
