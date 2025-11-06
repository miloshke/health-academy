<?php

namespace Database\Factories;

use App\Models\Gym;
use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Package>
 */
class PackageFactory extends Factory
{
    protected $model = Package::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $unlimitedAccess = fake()->boolean(30);

        return [
            'gym_id' => Gym::factory(),
            'name' => fake()->randomElement([
                'Basic Membership',
                'Premium Membership',
                'VIP Access',
                'Monthly Pass',
                'Annual Membership',
                'Student Package',
                'Family Package',
                'Weekend Warrior',
                'Early Bird Special',
                'Group Training Package',
            ]),
            'description' => fake()->optional()->paragraph(),
            'price' => fake()->randomFloat(2, 29.99, 299.99),
            'duration_days' => fake()->randomElement([30, 60, 90, 180, 365]),
            'benefits' => json_encode([
                'Access to gym equipment',
                fake()->randomElement(['Free parking', 'Locker included', 'Towel service']),
                fake()->randomElement(['1 personal training session', 'Nutrition consultation', 'Body composition analysis']),
            ]),
            'group_access_limit' => $unlimitedAccess ? null : fake()->numberBetween(1, 10),
            'unlimited_access' => $unlimitedAccess,
            'status' => fake()->randomElement(['active', 'inactive']),
        ];
    }

    /**
     * Indicate that the package is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the package is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Indicate that the package has unlimited access.
     */
    public function unlimitedAccess(): static
    {
        return $this->state(fn (array $attributes) => [
            'unlimited_access' => true,
            'group_access_limit' => null,
        ]);
    }

    /**
     * Indicate that the package has limited group access.
     */
    public function limitedAccess(int $limit = 5): static
    {
        return $this->state(fn (array $attributes) => [
            'unlimited_access' => false,
            'group_access_limit' => $limit,
        ]);
    }
}
