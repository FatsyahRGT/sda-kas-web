<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Period;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'period_id' => Period::factory(),
            'category_id' => ExpenseCategory::factory(),
            'item_name' => fake()->words(3, true),
            'nominal' => fake()->numberBetween(10000, 200000),
            'transaction_date' => now(),
            'note' => fake()->optional()->sentence(),
            'created_by' => User::factory(),
        ];
    }
}
