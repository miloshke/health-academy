<?php

namespace Tests\Feature\Modules\Gym;

use App\Models\Gym;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GymControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['email_verified_at' => now()]);
    }

    public function test_can_list_gyms(): void
    {
        Gym::factory()->count(3)->create();

        $response = $this->actingAs($this->user)
            ->getJson('/api/gyms');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'description',
                        'email',
                        'phone',
                        'website',
                        'status',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_gym(): void
    {
        $gymData = [
            'name' => 'Test Gym',
            'slug' => 'test-gym',
            'description' => 'A test gym description',
            'email' => 'test@gym.com',
            'phone' => '+1-555-0100',
            'website' => 'https://testgym.com',
            'status' => 'active',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/gyms', $gymData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'Test Gym',
                'slug' => 'test-gym',
                'email' => 'test@gym.com',
            ]);

        $this->assertDatabaseHas('gyms', [
            'name' => 'Test Gym',
            'slug' => 'test-gym',
        ]);
    }

    public function test_can_show_gym(): void
    {
        $gym = Gym::factory()->create();

        $response = $this->actingAs($this->user)
            ->getJson("/api/gyms/{$gym->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $gym->id,
                'name' => $gym->name,
                'slug' => $gym->slug,
            ]);
    }

    public function test_can_update_gym(): void
    {
        $gym = Gym::factory()->create();

        $updateData = [
            'name' => 'Updated Gym Name',
            'status' => 'inactive',
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/gyms/{$gym->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Updated Gym Name',
                'status' => 'inactive',
            ]);

        $this->assertDatabaseHas('gyms', [
            'id' => $gym->id,
            'name' => 'Updated Gym Name',
            'status' => 'inactive',
        ]);
    }

    public function test_can_delete_gym(): void
    {
        $gym = Gym::factory()->create();

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/gyms/{$gym->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'success' => true,
                'message' => 'Gym deleted successfully',
            ]);

        $this->assertDatabaseMissing('gyms', [
            'id' => $gym->id,
        ]);
    }

    public function test_cannot_create_gym_with_duplicate_slug(): void
    {
        $gym = Gym::factory()->create(['slug' => 'unique-gym']);

        $gymData = [
            'name' => 'Another Gym',
            'slug' => 'unique-gym',
            'status' => 'active',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/gyms', $gymData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['slug']);
    }

    public function test_cannot_create_gym_without_required_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/gyms', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'slug', 'status']);
    }

    public function test_unauthenticated_user_cannot_access_gyms(): void
    {
        $response = $this->getJson('/api/gyms');

        $response->assertStatus(401);
    }
}
