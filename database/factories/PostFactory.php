<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Skill;

class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::all()->random()->id,
            'needed_skill_id' => Skill::all()->random()->id,
            'offered_skill_id' => Skill::all()->random()->id,
            'description' => $this->faker->sentence(15),
            'status' => 'open',
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
