<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Gym;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Group>
 */
class GroupFactory extends Factory
{
    protected $model = Group::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('now', '+30 days');
        $endDate = fake()->optional()->dateTimeBetween($startDate, '+90 days');

        return [
            'gym_id' => Gym::factory(),
            'name' => fake()->randomElement([
                'Morning Yoga',
                'CrossFit Basics',
                'HIIT Training',
                'Pilates for Beginners',
                'Advanced Weightlifting',
                'Zumba Dance',
                'Boxing Fundamentals',
                'Spin Class',
                'Core Strength',
                'Flexibility & Mobility',
            ]),
            'description' => fake()->optional()->paragraph(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'max_participants' => fake()->optional()->numberBetween(5, 30),
            'status' => fake()->randomElement(['active', 'inactive', 'cancelled', 'completed']),
        ];
    }

    /**
     * Indicate that the group is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the group is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Indicate that the group is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    /**
     * Indicate that the group is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'end_date' => fake()->dateTimeBetween('-30 days', 'now'),
        ]);
    }
}
