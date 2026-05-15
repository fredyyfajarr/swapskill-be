<?php

namespace App\Application\Posts\UseCases;

use App\Domain\Posts\Contracts\PostRepository;
use App\Models\Post;

final readonly class UpdatePostStatus
{
    public function __construct(private PostRepository $posts)
    {
    }

    public function __invoke(Post $post, string $status): Post
    {
        return $this->posts->updateStatus($post, $status);
    }
}
