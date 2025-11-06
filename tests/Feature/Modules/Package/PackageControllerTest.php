<?php

namespace Tests\Feature\Modules\Package;

use App\Models\Gym;
use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackageControllerTest extends TestCase
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

    public function test_can_list_packages(): void
    {
        Package::factory()->count(3)->create(['gym_id' => $this->gym->id]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/packages');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'gym_id',
                        'name',
                        'description',
                        'price',
                        'duration_days',
                        'benefits',
                        'group_access_limit',
                        'unlimited_access',
                        'status',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_can_filter_packages_by_gym(): void
    {
        $anotherGym = Gym::factory()->create();

        Package::factory()->count(2)->create(['gym_id' => $this->gym->id]);
        Package::factory()->count(3)->create(['gym_id' => $anotherGym->id]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/packages?gym_id=' . $this->gym->id);

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_create_package(): void
    {
        $packageData = [
            'gym_id' => $this->gym->id,
            'name' => 'Premium Membership',
            'description' => 'Full access to all gym facilities',
            'price' => 99.99,
            'duration_days' => 30,
            'benefits' => json_encode(['Unlimited access', 'Personal training', 'Spa access']),
            'group_access_limit' => 10,
            'unlimited_access' => false,
            'status' => 'active',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/packages', $packageData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'Premium Membership',
                'price' => 99.99,
            ]);

        $this->assertDatabaseHas('packages', [
            'name' => 'Premium Membership',
            'gym_id' => $this->gym->id,
        ]);
    }

    public function test_can_create_unlimited_access_package(): void
    {
        $packageData = [
            'gym_id' => $this->gym->id,
            'name' => 'VIP Membership',
            'price' => 199.99,
            'duration_days' => 365,
            'unlimited_access' => true,
            'group_access_limit' => null,
            'status' => 'active',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/packages', $packageData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'unlimited_access' => true,
            ]);
    }

    public function test_can_show_package(): void
    {
        $package = Package::factory()->create(['gym_id' => $this->gym->id]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/packages/{$package->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $package->id,
                'name' => $package->name,
            ]);
    }

    public function test_can_update_package(): void
    {
        $package = Package::factory()->create(['gym_id' => $this->gym->id]);

        $updateData = [
            'name' => 'Updated Package Name',
            'price' => 149.99,
            'status' => 'inactive',
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/packages/{$package->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Updated Package Name',
                'price' => 149.99,
                'status' => 'inactive',
            ]);

        $this->assertDatabaseHas('packages', [
            'id' => $package->id,
            'name' => 'Updated Package Name',
            'price' => 149.99,
        ]);
    }

    public function test_can_delete_package(): void
    {
        $package = Package::factory()->create(['gym_id' => $this->gym->id]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/packages/{$package->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'success' => true,
                'message' => 'Package deleted successfully',
            ]);

        $this->assertDatabaseMissing('packages', [
            'id' => $package->id,
        ]);
    }

    public function test_cannot_create_package_with_invalid_gym_id(): void
    {
        $packageData = [
            'gym_id' => 99999,
            'name' => 'Test Package',
            'price' => 99.99,
            'duration_days' => 30,
            'status' => 'active',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/packages', $packageData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['gym_id']);
    }

    public function test_cannot_create_package_with_negative_price(): void
    {
        $packageData = [
            'gym_id' => $this->gym->id,
            'name' => 'Test Package',
            'price' => -10.00,
            'duration_days' => 30,
            'status' => 'active',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/packages', $packageData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['price']);
    }

    public function test_cannot_create_package_without_required_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/packages', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['gym_id', 'name', 'price', 'duration_days', 'status']);
    }
}
