<?php

namespace App\Application\Skills\UseCases;

use App\Domain\Skills\Contracts\SkillRepository;
use Illuminate\Support\Collection;

final readonly class ListSkills
{
    public function __construct(private SkillRepository $skills)
    {
    }

    public function __invoke(): Collection
    {
        return $this->skills->listAlphabetically();
    }
}
