<?php

namespace Database\Seeders;

use App\Models\Gym;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create super admin user
        User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'superadmin@example.com',
            'mobile' => '+1-555-0100',
            'phone' => '+1-555-0101',
            'status' => 'active',
            'birthdate' => '1985-01-15',
            'gender' => 'male',
            'role' => User::ROLE_SUPER_ADMIN,
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $gyms = Gym::all();

        if ($gyms->isEmpty()) {
            $this->command->warn('No gyms found. Skipping gym-related user creation.');
            return;
        }

        // Create users for each gym
        foreach ($gyms as $gym) {
            $locations = Location::where('gym_id', $gym->id)->get();
            $primaryLocation = $locations->first();

            // Create gym admin
            $gymAdmin = User::create([
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'email' => 'admin@' . $gym->slug . '.com',
                'mobile' => fake()->phoneNumber(),
                'status' => 'active',
                'birthdate' => fake()->date('Y-m-d', '-30 years'),
                'gender' => fake()->randomElement(['male', 'female']),
                'role' => User::ROLE_GYM_ADMIN,
                'gym_id' => $gym->id,
                'primary_location_id' => $primaryLocation?->id,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);

            // Attach admin to all locations
            if ($locations->isNotEmpty()) {
                $gymAdmin->locations()->attach($locations->pluck('id'));
            }

            // Create 2-3 trainers per gym
            $trainerCount = rand(2, 3);
            for ($i = 0; $i < $trainerCount; $i++) {
                $trainer = User::factory()->trainer()->create([
                    'gym_id' => $gym->id,
                    'primary_location_id' => $primaryLocation?->id,
                    'status' => 'active',
                ]);

                // Attach trainer to 1-2 random locations
                if ($locations->isNotEmpty()) {
                    $trainerLocations = $locations->random(min(rand(1, 2), $locations->count()));
                    $trainer->locations()->attach($trainerLocations->pluck('id'));
                }
            }

            // Create 5-10 trainees per gym
            $traineeCount = rand(5, 10);
            for ($i = 0; $i < $traineeCount; $i++) {
                $randomLocation = $locations->isNotEmpty() ? $locations->random() : null;

                $trainee = User::factory()->trainee()->create([
                    'gym_id' => $gym->id,
                    'primary_location_id' => $randomLocation?->id,
                    'status' => fake()->randomElement(['active', 'active', 'active', 'inactive']),
                ]);

                // Attach trainee to 1 location
                if ($randomLocation) {
                    $trainee->locations()->attach($randomLocation->id);
                }
            }
        }
    }
}
