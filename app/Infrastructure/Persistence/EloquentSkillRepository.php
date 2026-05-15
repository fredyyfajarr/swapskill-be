<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Skills\Contracts\SkillRepository;
use App\Models\Skill;
use Illuminate\Support\Collection;

final class EloquentSkillRepository implements SkillRepository
{
    public function firstOrCreateByName(string $name): Skill
    {
        return Skill::firstOrCreate([
            'name' => trim($name),
        ]);
    }

    public function listAlphabetically(): Collection
    {
        return Skill::orderBy('name', 'asc')->get();
    }
}
