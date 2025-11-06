<?php

namespace Database\Seeders;

use App\Models\Gym;
use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gyms = Gym::all();

        if ($gyms->isEmpty()) {
            $this->command->warn('No gyms found. Run GymSeeder first.');
            return;
        }

        // Create 3-5 packages for each gym
        foreach ($gyms as $gym) {
            // Basic monthly package
            Package::factory()->active()->limitedAccess(3)->create([
                'gym_id' => $gym->id,
                'name' => 'Basic Monthly',
                'price' => 49.99,
                'duration_days' => 30,
            ]);

            // Premium monthly with unlimited access
            Package::factory()->active()->unlimitedAccess()->create([
                'gym_id' => $gym->id,
                'name' => 'Premium Monthly',
                'price' => 89.99,
                'duration_days' => 30,
            ]);

            // Annual package
            Package::factory()->active()->unlimitedAccess()->create([
                'gym_id' => $gym->id,
                'name' => 'Annual Membership',
                'price' => 799.99,
                'duration_days' => 365,
            ]);

            // Create 1-2 additional random packages
            Package::factory()->count(rand(1, 2))->create([
                'gym_id' => $gym->id,
            ]);
        }
    }
}
