# Database Schema Reference

## Core Tables

### users
Primary user table supporting all roles in the system.

```sql
- id: bigint PK
- gym_id: bigint FK nullable (tenant isolation)
- primary_location_id: bigint FK nullable (main work location)
- first_name: string
- last_name: string
- email: string unique
- mobile: string nullable
- phone: string nullable
- status: string (active, inactive, suspended)
- birthdate: date nullable
- gender: enum(male, female, other) nullable
- role: string (super_admin, gym_admin, trainer, trainee)
- password: string
- email_verified_at: timestamp nullable
- remember_token: string nullable
- created_at, updated_at: timestamps
```

**Model Accessor:**
- `name` attribute: concatenates first_name + last_name

**Role Constants (in User model):**
- `ROLE_SUPER_ADMIN = 'super_admin'`
- `ROLE_GYM_ADMIN = 'gym_admin'`
- `ROLE_TRAINER = 'trainer'`
- `ROLE_TRAINEE = 'trainee'`

### gyms
Top-level tenant table representing fitness businesses.

```sql
- id: bigint PK
- name: string
- slug: string unique
- description: text nullable
- email: string nullable
- phone: string nullable
- website: string nullable
- status: string (active, inactive, suspended)
- created_at, updated_at: timestamps
```

### locations
Physical gym locations (branches).

```sql
- id: bigint PK
- gym_id: bigint FK (cascade on delete)
- name: string
- address: string nullable
- city: string nullable
- state: string nullable
- zip: string nullable
- country: string default 'USA'
- phone: string nullable
- email: string nullable
- status: string (active, inactive)
- created_at, updated_at: timestamps
```

### groups
Training classes/courses/programs.

```sql
- id: bigint PK
- gym_id: bigint FK (cascade on delete)
- name: string
- description: text nullable
- start_date: datetime nullable
- end_date: datetime nullable
- max_participants: integer nullable
- status: string (active, inactive, cancelled, completed)
- created_at, updated_at: timestamps
```

### packages
Membership plans/subscriptions.

```sql
- id: bigint PK
- gym_id: bigint FK (cascade on delete)
- name: string
- description: text nullable
- price: decimal(10,2)
- duration_days: integer (package validity period)
- benefits: json nullable (array of benefits)
- group_access_limit: integer nullable (how many groups user can join)
- unlimited_access: boolean default false
- status: string (active, inactive)
- created_at, updated_at: timestamps
```

## Pivot Tables

### location_user
Tracks which users work at which locations.

```sql
- id: bigint PK
- location_id: bigint FK
- user_id: bigint FK
- created_at, updated_at: timestamps
```

**Use case:** Trainers and gym admins can work at multiple locations.

### group_location
Links groups to the locations where they occur.

```sql
- id: bigint PK
- group_id: bigint FK
- location_id: bigint FK
- created_at, updated_at: timestamps
```

**Use case:** A "Morning Yoga" group might occur at 3 different locations.

### group_user
Enrollment tracking - which users are enrolled in which groups.

```sql
- id: bigint PK
- group_id: bigint FK
- user_id: bigint FK
- status: string (enrolled, completed, cancelled)
- enrolled_at: timestamp
- created_at, updated_at: timestamps
```

### package_user
Purchase tracking - which users bought which packages.

```sql
- id: bigint PK
- package_id: bigint FK
- user_id: bigint FK
- price_paid: decimal(10,2) (actual price paid)
- purchased_at: datetime
- starts_at: datetime nullable
- expires_at: datetime nullable (based on duration_days)
- status: string (active, expired, cancelled)
- payment_status: string (pending, completed, failed, refunded)
- payment_method: string nullable
- transaction_id: string nullable
- created_at, updated_at: timestamps
```

## Key Relationships

### User Relationships
```php
gym(): BelongsTo                    // User's gym
primaryLocation(): BelongsTo        // User's main location
locations(): BelongsToMany          // All assigned locations
groups(): BelongsToMany             // Enrolled groups
packages(): BelongsToMany           // Purchased packages
activePackages(): BelongsToMany     // Only active packages
```

### Gym Relationships
```php
locations(): HasMany
users(): HasMany
groups(): HasMany
packages(): HasMany
```

### Location Relationships
```php
gym(): BelongsTo
users(): BelongsToMany              // via location_user
groups(): BelongsToMany             // via group_location
```

### Group Relationships
```php
gym(): BelongsTo
locations(): BelongsToMany          // via group_location
users(): BelongsToMany              // via group_user
```

### Package Relationships
```php
gym(): BelongsTo
users(): BelongsToMany              // via package_user
activeSubscriptions(): BelongsToMany // only active purchases
```

## Business Logic Notes

1. **Tenant Isolation**: Users with `gym_id` can only see/access data for their gym
2. **SuperAdmin Exception**: SuperAdmin has no `gym_id` and can access all gyms
3. **Package Expiration**: Calculate from `purchased_at + duration_days`
4. **Group Capacity**: Check `max_participants` against enrolled count
5. **Active Packages**: Filter by `status = 'active'` and `expires_at > now()`

## Migration Order (Important!)
```
1. users (base table)
2. gyms
3. locations (depends on gyms)
4. add gym/location to users (depends on gyms, locations)
5. groups (depends on gyms)
6. packages (depends on gyms)
7. pivot tables (depend on all above)
```

## Indexes
Primary keys are auto-indexed. Consider adding indexes for:
- `gyms.slug` (unique, for lookups)
- `users.gym_id` (filtering)
- `locations.gym_id` (filtering)
- `groups.gym_id` (filtering)
- `packages.gym_id` (filtering)
- `package_user.expires_at` (for active package queries)
