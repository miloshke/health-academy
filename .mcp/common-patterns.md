# Common Patterns & Code Examples

## Creating a New Module

When adding a new entity (e.g., "Membership", "Equipment", "Schedule"):

### 1. Create Migration
```bash
php artisan make:migration create_memberships_table
```

### 2. Create Model
```php
// app/Models/Membership.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Membership extends Model
{
    use HasFactory;

    protected $fillable = [
        'gym_id',
        'name',
        'description',
        'status',
    ];

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }
}
```

### 3. Create Factory
```php
// database/factories/MembershipFactory.php
namespace Database\Factories;

use App\Models\Gym;
use App\Models\Membership;
use Illuminate\Database\Eloquent\Factories\Factory;

class MembershipFactory extends Factory
{
    protected $model = Membership::class;

    public function definition(): array
    {
        return [
            'gym_id' => Gym::factory(),
            'name' => fake()->words(2, true),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['active', 'inactive']),
        ];
    }
}
```

### 4. Create Module Structure
```bash
mkdir -p app/Modules/Membership/{Controllers,Repositories,Resources,Requests}
```

### 5. Create Repository
```php
// app/Modules/Membership/Repositories/MembershipRepository.php
namespace App\Modules\Membership\Repositories;

use App\Models\Membership;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MembershipRepository
{
    public function getAll(?int $perPage = 10, ?int $gymId = null): LengthAwarePaginator
    {
        $query = Membership::with('gym');

        if ($gymId) {
            $query->where('gym_id', $gymId);
        }

        return $query->paginate($perPage);
    }

    public function find(int $id): ?Membership
    {
        return Membership::with('gym')->find($id);
    }

    public function create(array $data): Membership
    {
        return Membership::create($data);
    }

    public function update(int $id, array $data): Membership
    {
        $membership = Membership::findOrFail($id);
        $membership->update($data);
        return $membership->fresh();
    }

    public function delete(int $id): bool
    {
        $membership = Membership::findOrFail($id);
        return $membership->delete();
    }
}
```

### 6. Create Resources
```php
// app/Modules/Membership/Resources/MembershipResource.php
namespace App\Modules\Membership\Resources;

use App\Library\FormatDataTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MembershipResource extends JsonResource
{
    use FormatDataTrait;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'gym_id' => $this->gym_id,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'created_at' => $this->dateAt($this->created_at),
            'updated_at' => $this->dateAt($this->updated_at),
        ];
    }
}

// app/Modules/Membership/Resources/MembershipCollection.php
namespace App\Modules\Membership\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MembershipCollection extends ResourceCollection
{
    public $collects = MembershipResource::class;

    public function toArray(Request $request): array
    {
        return ['data' => $this->collection];
    }
}
```

### 7. Create Form Requests
```php
// app/Modules/Membership/Requests/StoreMembershipRequest.php
namespace App\Modules\Membership\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMembershipRequest extends FormRequest
{
    public function authorize(): bool
    {
        // TODO: Implement policy check
        return true;
    }

    public function rules(): array
    {
        return [
            'gym_id' => ['required', 'integer', 'exists:gyms,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ];
    }
}

// app/Modules/Membership/Requests/UpdateMembershipRequest.php
namespace App\Modules\Membership\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMembershipRequest extends FormRequest
{
    public function authorize(): bool
    {
        // TODO: Implement policy check
        return true;
    }

    public function rules(): array
    {
        return [
            'gym_id' => ['sometimes', 'required', 'integer', 'exists:gyms,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'required', 'string', 'in:active,inactive'],
        ];
    }
}
```

### 8. Create Controller
```php
// app/Modules/Membership/Controllers/MembershipController.php
namespace App\Modules\Membership\Controllers;

use App\Library\Controller;
use App\Modules\Membership\Repositories\MembershipRepository;
use App\Modules\Membership\Requests\StoreMembershipRequest;
use App\Modules\Membership\Requests\UpdateMembershipRequest;
use App\Modules\Membership\Resources\MembershipCollection;
use App\Modules\Membership\Resources\MembershipResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    public function __construct(readonly private MembershipRepository $membershipRepository) {}

    public function index(Request $request): MembershipCollection
    {
        // TODO: Add policy check
        $perPage = $request->get('per_page', 10);
        $gymId = $request->get('gym_id');
        $memberships = $this->membershipRepository->getAll($perPage, $gymId);
        return new MembershipCollection($memberships);
    }

    public function store(StoreMembershipRequest $request): JsonResponse
    {
        $membership = $this->membershipRepository->create($request->validated());
        return (new MembershipResource($membership))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $id): MembershipResource
    {
        // TODO: Add policy check
        $membership = $this->membershipRepository->find($id);
        if (!$membership) {
            abort(404, 'Membership not found');
        }
        return new MembershipResource($membership);
    }

    public function update(UpdateMembershipRequest $request, int $id): MembershipResource
    {
        $membership = $this->membershipRepository->update($id, $request->validated());
        return new MembershipResource($membership);
    }

    public function destroy(int $id): JsonResponse
    {
        // TODO: Add policy check
        $deleted = $this->membershipRepository->delete($id);
        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'Membership deleted successfully' : 'Failed to delete membership',
        ]);
    }
}
```

### 9. Add Routes
```php
// routes/api.php
use App\Modules\Membership\Controllers\MembershipController;

Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
    // ... existing routes ...

    // Membership Management Routes
    Route::prefix('memberships')->name('memberships.')->group(function () {
        Route::get('/', [MembershipController::class, 'index'])->name('index');
        Route::post('/', [MembershipController::class, 'store'])->name('store');
        Route::get('/{membership}', [MembershipController::class, 'show'])->name('show');
        Route::put('/{membership}', [MembershipController::class, 'update'])->name('update');
        Route::delete('/{membership}', [MembershipController::class, 'destroy'])->name('destroy');
    });
});
```

### 10. Create Tests
```php
// tests/Feature/Modules/Membership/MembershipControllerTest.php
namespace Tests\Feature\Modules\Membership;

use App\Models\Gym;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MembershipControllerTest extends TestCase
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

    public function test_can_list_memberships(): void
    {
        Membership::factory()->count(3)->create(['gym_id' => $this->gym->id]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/memberships');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_membership(): void
    {
        $data = [
            'gym_id' => $this->gym->id,
            'name' => 'Premium Membership',
            'description' => 'Full access membership',
            'status' => 'active',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/memberships', $data);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Premium Membership']);

        $this->assertDatabaseHas('memberships', ['name' => 'Premium Membership']);
    }

    // Add more tests...
}
```

### 11. Create Seeder
```php
// database/seeders/MembershipSeeder.php
namespace Database\Seeders;

use App\Models\Gym;
use App\Models\Membership;
use Illuminate\Database\Seeder;

class MembershipSeeder extends Seeder
{
    public function run(): void
    {
        $gyms = Gym::all();

        foreach ($gyms as $gym) {
            Membership::factory()->count(3)->create([
                'gym_id' => $gym->id,
            ]);
        }
    }
}

// Don't forget to add to DatabaseSeeder.php
```

## Common Query Patterns

### Filter by Gym (Tenant Isolation)
```php
// In repository
public function getAll(?int $gymId = null): Collection
{
    $query = Model::query();

    if ($gymId) {
        $query->where('gym_id', $gymId);
    }

    return $query->get();
}
```

### Eager Loading Relationships
```php
// In repository
public function find(int $id): ?Model
{
    return Model::with(['gym', 'locations', 'users'])->find($id);
}
```

### Counting Relationships
```php
// In repository
public function getAll(): Collection
{
    return Model::withCount(['users', 'locations'])->get();
}
```

### Syncing Many-to-Many
```php
// In repository - replace all relationships
$group->locations()->sync($locationIds);

// Attach without detaching
$group->locations()->attach($locationIds);

// Detach specific
$group->locations()->detach($locationIds);
```

## Testing Patterns

### Test Authenticated Request
```php
$response = $this->actingAs($this->user)
    ->getJson('/api/endpoint');
```

### Test Unauthenticated
```php
$response = $this->getJson('/api/endpoint');
$response->assertStatus(401);
```

### Test Validation Errors
```php
$response = $this->actingAs($this->user)
    ->postJson('/api/endpoint', []);

$response->assertStatus(422)
    ->assertJsonValidationErrors(['field_name']);
```

### Test Relationship Creation
```php
$location1 = Location::factory()->create();
$location2 = Location::factory()->create();

$response = $this->actingAs($this->user)
    ->postJson('/api/groups', [
        'gym_id' => $gym->id,
        'name' => 'Test Group',
        'location_ids' => [$location1->id, $location2->id],
    ]);

$group = Group::where('name', 'Test Group')->first();
$this->assertCount(2, $group->locations);
```

## Frontend Service Pattern

```typescript
// resources/ts/services/membershipService.ts
import type { ApiResponse, Membership } from '@/types'

const endpoint = '/memberships'

export const membershipService = {
  async getAll(page = 1, perPage = 10, gymId?: number) {
    const params = new URLSearchParams({
      page: String(page),
      per_page: String(perPage),
    })

    if (gymId) {
      params.append('gym_id', String(gymId))
    }

    return $fetch<ApiResponse<Membership[]>>(`/api${endpoint}?${params}`)
  },

  async getById(id: number) {
    return $fetch<ApiResponse<Membership>>(`/api${endpoint}/${id}`)
  },

  async create(data: Partial<Membership>) {
    return $fetch<ApiResponse<Membership>>(`/api${endpoint}`, {
      method: 'POST',
      body: data,
    })
  },

  async update(id: number, data: Partial<Membership>) {
    return $fetch<ApiResponse<Membership>>(`/api${endpoint}/${id}`, {
      method: 'PUT',
      body: data,
    })
  },

  async delete(id: number) {
    return $fetch<{ success: boolean; message: string }>(`/api${endpoint}/${id}`, {
      method: 'DELETE',
    })
  },
}
```

This pattern keeps code DRY and consistent across the entire application!
