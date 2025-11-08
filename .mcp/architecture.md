# Architecture & Code Organization

## Directory Structure

```
app/
├── Models/                    # Eloquent models
│   ├── User.php
│   ├── Gym.php
│   ├── Location.php
│   ├── Group.php
│   └── Package.php
├── Modules/                   # Modular architecture (primary pattern)
│   ├── User/
│   │   ├── Controllers/
│   │   │   └── Admin/
│   │   │       └── UserAdminController.php
│   │   ├── Repositories/
│   │   │   └── UserRepository.php
│   │   ├── Resources/
│   │   │   ├── UserResource.php
│   │   │   ├── UserFullInfoResource.php
│   │   │   └── UserCollection.php
│   │   └── Requests/
│   │       ├── StoreUserRequest.php
│   │       └── UpdateUserRequest.php
│   ├── Gym/                   # Same structure
│   ├── Location/              # Same structure
│   ├── Group/                 # Same structure
│   └── Package/               # Same structure
└── Library/                   # Shared utilities
    ├── Controller.php         # Base controller
    └── FormatDataTrait.php    # Date formatting helper

database/
├── migrations/
├── factories/                 # Model factories for testing/seeding
│   ├── UserFactory.php
│   ├── GymFactory.php
│   ├── LocationFactory.php
│   ├── GroupFactory.php
│   └── PackageFactory.php
└── seeders/
    ├── DatabaseSeeder.php
    ├── GymSeeder.php
    ├── LocationSeeder.php
    ├── GroupSeeder.php
    ├── PackageSeeder.php
    └── UserSeeder.php

tests/Feature/Modules/
├── Gym/
│   └── GymControllerTest.php
├── Location/
│   └── LocationControllerTest.php
├── Group/
│   └── GroupControllerTest.php
└── Package/
    └── PackageControllerTest.php

resources/
└── ts/
    ├── pages/
    │   ├── users.vue          # Users listing page
    │   └── AddNewUserDrawer.vue
    └── services/
        └── userService.ts     # API client for users

routes/
├── api.php                    # API routes (Sanctum protected)
└── web.php                    # Frontend routes (SPA fallback)
```

## Module Pattern

Each module follows this consistent structure:

```
ModuleName/
├── Controllers/
│   └── ModuleNameController.php   # CRUD endpoints
├── Repositories/
│   └── ModuleNameRepository.php   # Data access layer
├── Resources/
│   ├── ModuleNameResource.php     # Single item transformer
│   └── ModuleNameCollection.php   # Collection transformer
└── Requests/
    ├── StoreModuleNameRequest.php # Create validation
    └── UpdateModuleNameRequest.php # Update validation
```

## Layer Responsibilities

### Controllers
- Handle HTTP requests/responses
- Call repository methods
- Return API resources
- TODO comments for policy checks: `// TODO: Add policy check`

### Repositories
- Interact with Eloquent models
- Handle complex queries
- Manage relationships
- Return models or collections

### Resources (Transformers)
- Transform models to JSON
- Control API response structure
- Include/exclude relationships conditionally
- Use `FormatDataTrait` for date formatting

### Requests (Form Requests)
- Validate incoming data
- TODO comments for authorization: `// TODO: Implement policy check`
- Return validation rules

## API Response Format

### Single Resource
```json
{
  "data": {
    "id": 1,
    "name": "FitZone Gym",
    "slug": "fitzone-gym",
    "status": "active",
    "created_at": "2025-11-06 20:00:00",
    "updated_at": "2025-11-06 20:00:00"
  }
}
```

### Collection (Paginated)
```json
{
  "data": [...],
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": "..."
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 3,
    "per_page": 10,
    "to": 10,
    "total": 25
  }
}
```

## Database Conventions

- All tables use `id` as primary key (auto-increment)
- Foreign keys: `{model}_id` (e.g., `gym_id`, `user_id`)
- Timestamps: `created_at`, `updated_at` on all tables
- Soft deletes: NOT used currently
- Pivot tables: alphabetically ordered names (e.g., `group_location`, not `location_group`)
- Pivot table additional columns: timestamps, status, metadata

## Testing Conventions

- Use `RefreshDatabase` trait in all feature tests
- Create authenticated user in `setUp()` method
- Use factories for test data
- Test structure:
  - Positive cases (can do X)
  - Negative cases (cannot do X)
  - Validation tests
  - Authentication tests
- Use descriptive test method names: `test_can_create_gym_with_locations()`

## Important Notes

1. **Always use factories** - Never hardcode test data in tests
2. **Policy placeholders exist** - Look for TODO comments, implement when ready
3. **Frontend uses ofetch** - Not axios
4. **SQLite in dev** - Use `:memory:` for tests
5. **Modular over traditional** - New features go in `app/Modules/`
6. **Resources everywhere** - Never return models directly from controllers
