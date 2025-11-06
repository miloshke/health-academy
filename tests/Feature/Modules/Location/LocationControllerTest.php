<?php

namespace Tests\Feature\Modules\Location;

use App\Models\Gym;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Gym $gym;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['email_verified_at' => now()]);
        $this->gym = Gym::factory()->create();
    }

    public function test_can_list_locations(): void
    {
        Location::factory()->count(3)->create(['gym_id' => $this->gym->id]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/locations');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'gym_id',
                        'name',
                        'address',
                        'city',
                        'state',
                        'zip',
                        'country',
                        'phone',
                        'email',
                        'status',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_can_filter_locations_by_gym(): void
    {
        $anotherGym = Gym::factory()->create();

        Location::factory()->count(2)->create(['gym_id' => $this->gym->id]);
        Location::factory()->count(3)->create(['gym_id' => $anotherGym->id]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/locations?gym_id=' . $this->gym->id);

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_create_location(): void
    {
        $locationData = [
            'gym_id' => $this->gym->id,
            'name' => 'Downtown Location',
            'address' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'zip' => '10001',
            'country' => 'USA',
            'phone' => '+1-555-0100',
            'email' => 'downtown@gym.com',
            'status' => 'active',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/locations', $locationData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'Downtown Location',
                'city' => 'New York',
            ]);

        $this->assertDatabaseHas('locations', [
            'name' => 'Downtown Location',
            'gym_id' => $this->gym->id,
        ]);
    }

    public function test_can_show_location(): void
    {
        $location = Location::factory()->create(['gym_id' => $this->gym->id]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/locations/{$location->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $location->id,
                'name' => $location->name,
            ]);
    }

    public function test_can_update_location(): void
    {
        $location = Location::factory()->create(['gym_id' => $this->gym->id]);

        $updateData = [
            'name' => 'Updated Location Name',
            'status' => 'inactive',
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/locations/{$location->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Updated Location Name',
                'status' => 'inactive',
            ]);

        $this->assertDatabaseHas('locations', [
            'id' => $location->id,
            'name' => 'Updated Location Name',
        ]);
    }

    public function test_can_delete_location(): void
    {
        $location = Location::factory()->create(['gym_id' => $this->gym->id]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/locations/{$location->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'success' => true,
                'message' => 'Location deleted successfully',
            ]);

        $this->assertDatabaseMissing('locations', [
            'id' => $location->id,
        ]);
    }

    public function test_cannot_create_location_with_invalid_gym_id(): void
    {
        $locationData = [
            'gym_id' => 99999,
            'name' => 'Test Location',
            'status' => 'active',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/locations', $locationData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['gym_id']);
    }

    public function test_cannot_create_location_without_required_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/locations', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['gym_id', 'name', 'status']);
    }
}
