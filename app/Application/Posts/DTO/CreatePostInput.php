<?php

namespace App\Application\Posts\DTO;

final readonly class CreatePostInput
{
    public function __construct(
        public string $neededSkill,
        public string $offeredSkill,
        public string $description,
    ) {
    }
}
