<?php

namespace Database\Seeders;

use App\Models\Gym;
use Illuminate\Database\Seeder;

class GymSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 5 gyms with different statuses
        Gym::factory()->active()->create([
            'name' => 'FitZone Gym',
            'slug' => 'fitzone-gym',
            'description' => 'Your premier fitness destination with state-of-the-art equipment.',
            'email' => 'info@fitzone.com',
            'phone' => '+1-555-0100',
            'website' => 'https://fitzone.com',
        ]);

        Gym::factory()->active()->create([
            'name' => 'PowerHouse Athletics',
            'slug' => 'powerhouse-athletics',
            'description' => 'Elite training facility for serious athletes.',
            'email' => 'contact@powerhouse.com',
            'phone' => '+1-555-0200',
        ]);

        Gym::factory()->active()->create([
            'name' => 'Wellness Center Pro',
            'slug' => 'wellness-center-pro',
            'description' => 'Holistic approach to fitness and wellness.',
        ]);

        // Create 2 more random gyms
        Gym::factory()->count(2)->create();
    }
}
