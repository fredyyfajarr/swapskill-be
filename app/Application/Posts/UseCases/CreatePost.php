<?php

namespace App\Application\Posts\UseCases;

use App\Application\Posts\DTO\CreatePostInput;
use App\Domain\Posts\Contracts\PostRepository;
use App\Domain\Skills\Contracts\SkillRepository;
use App\Models\Post;
use App\Models\User;

final readonly class CreatePost
{
    public function __construct(
        private PostRepository $posts,
        private SkillRepository $skills,
    ) {
    }

    public function __invoke(User $user, CreatePostInput $input): Post
    {
        $neededSkill = $this->skills->firstOrCreateByName($input->neededSkill);
        $offeredSkill = $this->skills->firstOrCreateByName($input->offeredSkill);

        return $this->posts->createForUser(
            user: $user,
            neededSkill: $neededSkill,
            offeredSkill: $offeredSkill,
            description: strip_tags($input->description),
        );
    }
}
