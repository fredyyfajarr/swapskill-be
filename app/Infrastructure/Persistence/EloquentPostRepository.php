<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Posts\Contracts\PostRepository;
use App\Models\Post;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final class EloquentPostRepository implements PostRepository
{
    public function paginateOpenForUser(
        User $user,
        ?string $search,
        ?int $skillId,
        string $sortBy,
        bool $bookmarkedOnly
    ): LengthAwarePaginator {
        $query = Post::with([
            'user:id,name,whatsapp_number',
            'neededSkill:id,name',
            'offeredSkill:id,name',
        ])
            ->withExists(['bookmarks as is_bookmarked' => function ($q) use ($user) {
                $q->where('user_id', $user->id);
            }])
            ->where('status', 'open');

        $query->when($search, function ($q, string $search) {
            $q->where(function ($subQ) use ($search) {
                $subQ->whereHas('neededSkill', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                })->orWhereHas('offeredSkill', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                });
            });
        });

        $query->when($skillId, function ($q, int $skillId) {
            $q->where('needed_skill_id', $skillId);
        });

        $query->when($bookmarkedOnly, function ($q) use ($user) {
            $q->whereHas('bookmarks', function ($bookmarkQuery) use ($user) {
                $bookmarkQuery->where('user_id', $user->id);
            });
        });

        $sortBy === 'oldest' ? $query->oldest() : $query->latest();

        return $query->paginate(5);
    }

    public function createForUser(User $user, Skill $neededSkill, Skill $offeredSkill, string $description): Post
    {
        return $user->posts()->create([
            'needed_skill_id' => $neededSkill->id,
            'offered_skill_id' => $offeredSkill->id,
            'description' => $description,
            'status' => 'open',
        ]);
    }

    public function updateStatus(Post $post, string $status): Post
    {
        $post->update(['status' => $status]);

        return $post;
    }

    public function delete(Post $post): void
    {
        $post->delete();
    }

    public function matchingRecommendationsFor(User $user, int $limit = 5): Collection
    {
        $mySkills = $user->skills()->pluck('skills.id')->toArray();
        $myNeeds = $user->posts()
            ->where('status', 'open')
            ->pluck('needed_skill_id')
            ->toArray();

        return Post::with(['user:id,name,whatsapp_number', 'neededSkill', 'offeredSkill'])
            ->where('user_id', '!=', $user->id)
            ->where('status', 'open')
            ->whereIn('offered_skill_id', $myNeeds)
            ->whereIn('needed_skill_id', $mySkills)
            ->latest()
            ->take($limit)
            ->get();
    }
}
