<?php

namespace Tests\Feature\Modules\Group;

use App\Models\Group;
use App\Models\Gym;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupControllerTest extends TestCase
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

    public function test_can_list_groups(): void
    {
        Group::factory()->count(3)->create(['gym_id' => $this->gym->id]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/groups');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'gym_id',
                        'name',
                        'description',
                        'start_date',
                        'end_date',
                        'max_participants',
                        'status',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_can_filter_groups_by_gym(): void
    {
        $anotherGym = Gym::factory()->create();

        Group::factory()->count(2)->create(['gym_id' => $this->gym->id]);
        Group::factory()->count(3)->create(['gym_id' => $anotherGym->id]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/groups?gym_id=' . $this->gym->id);

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_create_group(): void
    {
        $groupData = [
            'gym_id' => $this->gym->id,
            'name' => 'Morning Yoga',
            'description' => 'Relaxing morning yoga session',
            'start_date' => now()->addDays(7)->format('Y-m-d'),
            'end_date' => now()->addDays(37)->format('Y-m-d'),
            'max_participants' => 20,
            'status' => 'active',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/groups', $groupData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'Morning Yoga',
                'max_participants' => 20,
            ]);

        $this->assertDatabaseHas('groups', [
            'name' => 'Morning Yoga',
            'gym_id' => $this->gym->id,
        ]);
    }

    public function test_can_create_group_with_locations(): void
    {
        $location1 = Location::factory()->create(['gym_id' => $this->gym->id]);
        $location2 = Location::factory()->create(['gym_id' => $this->gym->id]);

        $groupData = [
            'gym_id' => $this->gym->id,
            'name' => 'CrossFit Training',
            'status' => 'active',
            'location_ids' => [$location1->id, $location2->id],
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/groups', $groupData);

        $response->assertStatus(201);

        $group = Group::where('name', 'CrossFit Training')->first();
        $this->assertCount(2, $group->locations);
    }

    public function test_can_show_group(): void
    {
        $group = Group::factory()->create(['gym_id' => $this->gym->id]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/groups/{$group->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $group->id,
                'name' => $group->name,
            ]);
    }

    public function test_can_update_group(): void
    {
        $group = Group::factory()->create(['gym_id' => $this->gym->id]);

        $updateData = [
            'name' => 'Updated Group Name',
            'status' => 'cancelled',
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/groups/{$group->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Updated Group Name',
                'status' => 'cancelled',
            ]);

        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'name' => 'Updated Group Name',
            'status' => 'cancelled',
        ]);
    }

    public function test_can_update_group_locations(): void
    {
        $location1 = Location::factory()->create(['gym_id' => $this->gym->id]);
        $location2 = Location::factory()->create(['gym_id' => $this->gym->id]);
        $location3 = Location::factory()->create(['gym_id' => $this->gym->id]);

        $group = Group::factory()->create(['gym_id' => $this->gym->id]);
        $group->locations()->attach([$location1->id, $location2->id]);

        $updateData = [
            'location_ids' => [$location3->id],
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/groups/{$group->id}", $updateData);

        $response->assertStatus(200);

        $group->refresh();
        $this->assertCount(1, $group->locations);
        $this->assertEquals($location3->id, $group->locations->first()->id);
    }

    public function test_can_delete_group(): void
    {
        $group = Group::factory()->create(['gym_id' => $this->gym->id]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/groups/{$group->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'success' => true,
                'message' => 'Group deleted successfully',
            ]);

        $this->assertDatabaseMissing('groups', [
            'id' => $group->id,
        ]);
    }

    public function test_cannot_create_group_with_invalid_gym_id(): void
    {
        $groupData = [
            'gym_id' => 99999,
            'name' => 'Test Group',
            'status' => 'active',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/groups', $groupData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['gym_id']);
    }

    public function test_cannot_create_group_with_end_date_before_start_date(): void
    {
        $groupData = [
            'gym_id' => $this->gym->id,
            'name' => 'Test Group',
            'start_date' => now()->addDays(10)->format('Y-m-d'),
            'end_date' => now()->addDays(5)->format('Y-m-d'),
            'status' => 'active',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/groups', $groupData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['end_date']);
    }

    public function test_cannot_create_group_without_required_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/groups', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['gym_id', 'name', 'status']);
    }
}
