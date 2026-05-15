<?php

namespace App\Application\Posts\UseCases;

use App\Application\Posts\DTO\ListPostsInput;
use App\Domain\Posts\Contracts\PostRepository;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class ListOpenPosts
{
    public function __construct(private PostRepository $posts)
    {
    }

    public function __invoke(User $user, ListPostsInput $input): LengthAwarePaginator
    {
        return $this->posts->paginateOpenForUser(
            user: $user,
            search: $input->search,
            skillId: $input->skillId,
            sortBy: $input->sortBy,
            bookmarkedOnly: $input->bookmarkedOnly,
        );
    }
}
