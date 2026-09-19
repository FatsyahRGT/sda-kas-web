<?php

namespace Database\Factories;

use App\Models\Income;
use App\Models\Member;
use App\Models\Period;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Income>
 */
class IncomeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'period_id' => Period::factory(),
            'member_id' => Member::factory(),
            'nominal' => 50000,
            'transaction_date' => now(),
            'note' => fake()->optional()->sentence(),
            'created_by' => User::factory(),
        ];
    }
}
