<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Gym;
use App\Models\Location;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
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

        // Create 3-6 groups for each gym
        foreach ($gyms as $gym) {
            $groupCount = rand(3, 6);
            $locations = Location::where('gym_id', $gym->id)->get();

            for ($i = 0; $i < $groupCount; $i++) {
                $group = Group::factory()->create([
                    'gym_id' => $gym->id,
                ]);

                // Attach random locations to the group
                if ($locations->isNotEmpty()) {
                    $randomLocations = $locations->random(min(rand(1, 3), $locations->count()));
                    $group->locations()->attach($randomLocations->pluck('id'));
                }
            }
        }
    }
}
