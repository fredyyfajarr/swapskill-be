<?php

namespace App\Application\Posts\UseCases;

use App\Application\Posts\DTO\CreatePostInput;
use App\Domain\Posts\Contracts\PostRepository;
use App\Domain\Skills\Contracts\SkillRepository;
use App\Models\Post;

final readonly class UpdatePost
{
    public function __construct(
        private PostRepository $posts,
        private SkillRepository $skills,
    ) {
    }

    public function __invoke(Post $post, CreatePostInput $input): Post
    {
        $neededSkill = $this->skills->firstOrCreateByName($input->neededSkill);
        $offeredSkill = $this->skills->firstOrCreateByName($input->offeredSkill);

        return $this->posts->update(
            post: $post,
            neededSkill: $neededSkill,
            offeredSkill: $offeredSkill,
            description: strip_tags($input->description),
        );
    }
}
