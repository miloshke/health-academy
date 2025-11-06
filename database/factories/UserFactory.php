<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'mobile' => fake()->optional()->phoneNumber(),
            'phone' => fake()->optional()->phoneNumber(),
            'status' => fake()->randomElement(['active', 'inactive', 'suspended']),
            'birthdate' => fake()->optional()->date('Y-m-d', '-18 years'),
            'gender' => fake()->optional()->randomElement(['male', 'female', 'other']),
            'role' => 'trainee',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is a super admin.
     */
    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'super_admin',
        ]);
    }

    /**
     * Indicate that the user is a gym admin.
     */
    public function gymAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'gym_admin',
        ]);
    }

    /**
     * Indicate that the user is a trainer.
     */
    public function trainer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'trainer',
        ]);
    }

    /**
     * Indicate that the user is a trainee.
     */
    public function trainee(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'trainee',
        ]);
    }
}
