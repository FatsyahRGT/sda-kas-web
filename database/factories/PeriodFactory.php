<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Period;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Period>
 */
class PeriodFactory extends Factory
{
    public function definition(): array
    {
        return [
            'group_id' => Group::factory(),
            'month' => fake()->numberBetween(1, 12),
            'year' => 2026,
            'due_amount' => null,
            'status' => 'open',
            'created_by' => User::factory(),
        ];
    }
}
