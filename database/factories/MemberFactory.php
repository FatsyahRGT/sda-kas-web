<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'group_id' => Group::factory(),
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'is_active' => true,
        ];
    }
}
