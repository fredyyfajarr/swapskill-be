<?php

namespace App\Application\Posts\UseCases;

use App\Domain\Posts\Contracts\PostRepository;
use App\Models\Post;

final readonly class DeletePost
{
    public function __construct(private PostRepository $posts)
    {
    }

    public function __invoke(Post $post): void
    {
        $this->posts->delete($post);
    }
}
