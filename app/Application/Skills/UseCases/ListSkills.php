<?php

namespace App\Application\Skills\UseCases;

use App\Domain\Skills\Contracts\SkillRepository;
use Illuminate\Support\Collection;

use Illuminate\Support\Facades\Cache;

final readonly class ListSkills
{
    public function __construct(private SkillRepository $skills)
    {
    }

    public function __invoke(): Collection
    {
        return Cache::remember('skills.all', now()->addHours(24), function () {
            return $this->skills->listAlphabetically();
        });
    }
}
