<?php

namespace Database\Seeders;

use App\Models\Gym;
use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
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

        // Create 2-4 locations for each gym
        foreach ($gyms as $gym) {
            $locationCount = rand(2, 4);

            Location::factory()->count($locationCount)->create([
                'gym_id' => $gym->id,
            ]);
        }
    }
}
