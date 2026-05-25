<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Skills\Contracts\SkillRepository;
use App\Models\Skill;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

final class EloquentSkillRepository implements SkillRepository
{
    public function firstOrCreateByName(string $name): Skill
    {
        $skill = Skill::firstOrCreate([
            'name' => trim($name),
        ]);

        Cache::forget('skills.alphabetical');

        return $skill;
    }

    public function listAlphabetically(): Collection
    {
        return Cache::remember('skills.alphabetical', now()->addMinutes(10), function () {
            return Skill::query()
                ->select('id', 'name', 'category')
                ->orderBy('name', 'asc')
                ->get();
        });
    }
}
