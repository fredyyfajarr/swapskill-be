<?php

namespace App\Domain\Skills\Contracts;

use App\Models\Skill;
use Illuminate\Support\Collection;

interface SkillRepository
{
    public function firstOrCreateByName(string $name): Skill;

    public function listAlphabetically(): Collection;
}
