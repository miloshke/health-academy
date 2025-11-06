# Health Academy - Database Architecture

## Multi-Tenant Gym Management System

### Entity Relationship Overview

```
Gym (1) ──┬── (M) Locations
          ├── (M) Users
          ├── (M) Groups
          └── (M) Packages

Location (M) ──┬── (M) Users (via location_user pivot)
               └── (M) Groups (via group_location pivot)

User (M) ──┬── (M) Groups (via group_user pivot)
           └── (M) Packages (via package_user pivot - purchases)

Package (M) ──── (M) Users (via package_user pivot)
```

### Tables

#### 1. **gyms**
Main tenant/organization table.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| name | string | Gym/organization name |
| slug | string | Unique URL-friendly identifier |
| description | text | Optional description |
| email | string | Contact email |
| phone | string | Contact phone |
| website | string | Website URL |
| status | string | `active`, `inactive`, `suspended` |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes**: `slug` (unique)

---

#### 2. **locations**
Physical locations/branches for each gym.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| gym_id | bigint | Foreign key to gyms |
| name | string | Location name |
| address | string | Street address |
| city | string | City |
| state | string | State/Province |
| zip | string | ZIP/Postal code |
| country | string | Default: 'USA' |
| phone | string | Location phone |
| email | string | Location email |
| status | string | `active`, `inactive` |
| created_at | timestamp | |
| updated_at | timestamp | |

**Foreign Keys**: `gym_id` → gyms.id (cascade delete)
**Indexes**: `(gym_id, status)`

---

#### 3. **users**
User accounts assigned to gyms and locations.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| gym_id | bigint | Foreign key to gyms (nullable) |
| primary_location_id | bigint | Main location assignment |
| first_name | string | First name |
| last_name | string | Last name |
| email | string | Email (unique) |
| mobile | string | Mobile number |
| phone | string | Phone number |
| status | string | `active`, `inactive`, `pending` |
| birthdate | date | Birth date |
| gender | enum | `male`, `female`, `other` |
| role | string | User role (default: `user`) |
| email_verified_at | timestamp | Email verification |
| password | string | Hashed password |
| remember_token | string | |
| created_at | timestamp | |
| updated_at | timestamp | |

**Foreign Keys**:
- `gym_id` → gyms.id (null on delete)
- `primary_location_id` → locations.id (null on delete)

**Indexes**: `(gym_id, status)`, `email` (unique)

**Virtual Attributes**: `name` (computed from first_name + last_name)

---

#### 4. **groups**
Training courses/sessions/classes.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| gym_id | bigint | Foreign key to gyms |
| name | string | Group/course name |
| description | text | Description |
| start_date | datetime | Start date/time |
| end_date | datetime | End date/time |
| max_participants | integer | Maximum enrollment |
| status | string | `active`, `completed`, `cancelled` |
| created_at | timestamp | |
| updated_at | timestamp | |

**Foreign Keys**: `gym_id` → gyms.id (cascade delete)
**Indexes**: `(gym_id, status)`, `start_date`

---

#### 5. **location_user** (Pivot Table)
Many-to-many relationship: Users can be assigned to multiple locations.

| Column | Type | Description |
|--------|------|-------------|
| location_id | bigint | Foreign key to locations |
| user_id | bigint | Foreign key to users |
| created_at | timestamp | |
| updated_at | timestamp | |

**Primary Key**: `(location_id, user_id)`
**Foreign Keys**: Both cascade on delete

---

#### 6. **group_location** (Pivot Table)
Many-to-many relationship: Groups can be assigned to multiple locations.

| Column | Type | Description |
|--------|------|-------------|
| group_id | bigint | Foreign key to groups |
| location_id | bigint | Foreign key to locations |
| created_at | timestamp | |
| updated_at | timestamp | |

**Primary Key**: `(group_id, location_id)`
**Foreign Keys**: Both cascade on delete

---

#### 7. **group_user** (Pivot Table)
Many-to-many relationship: Users can enroll in multiple groups.

| Column | Type | Description |
|--------|------|-------------|
| group_id | bigint | Foreign key to groups |
| user_id | bigint | Foreign key to users |
| status | string | `enrolled`, `completed`, `cancelled` |
| enrolled_at | datetime | Enrollment timestamp |
| created_at | timestamp | |
| updated_at | timestamp | |

**Primary Key**: `(group_id, user_id)`
**Foreign Keys**: Both cascade on delete
**Indexes**: `status`

---

#### 8. **packages**
Membership packages/plans that users can purchase.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| gym_id | bigint | Foreign key to gyms |
| name | string | Package name (e.g., "Premium", "Basic") |
| description | text | Package description |
| price | decimal(10,2) | Package price |
| duration_days | integer | Validity period in days |
| benefits | json | Array of benefits/features |
| group_access_limit | integer | Max groups (null = unlimited) |
| unlimited_access | boolean | Full gym access flag |
| status | string | `active`, `inactive`, `archived` |
| created_at | timestamp | |
| updated_at | timestamp | |

**Foreign Keys**: `gym_id` → gyms.id (cascade delete)
**Indexes**: `(gym_id, status)`

---

#### 9. **package_user** (Pivot Table)
Tracks user package purchases and subscriptions.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| user_id | bigint | Foreign key to users |
| package_id | bigint | Foreign key to packages |
| price_paid | decimal(10,2) | Amount paid at purchase |
| purchased_at | datetime | Purchase timestamp |
| starts_at | datetime | When package activates |
| expires_at | datetime | Expiration date |
| status | string | `active`, `expired`, `cancelled`, `suspended` |
| payment_status | string | `pending`, `paid`, `refunded`, `failed` |
| payment_method | string | Payment method used |
| transaction_id | string | Payment transaction ID |
| notes | text | Additional notes |
| created_at | timestamp | |
| updated_at | timestamp | |

**Foreign Keys**: Both cascade on delete
**Indexes**: `(user_id, status)`, `(package_id, status)`, `expires_at`

---

## User Roles

### Role Hierarchy

| Role | Value | Permissions |
|------|-------|-------------|
| **Super Admin** | `super_admin` | Full system access, manage all gyms |
| **Gym Admin** | `gym_admin` | Manage gym, locations, groups, packages, trainers |
| **Trainer** | `trainer` | Manage assigned groups, view trainees |
| **Trainee** | `trainee` | Regular user, enroll in groups, purchase packages |

### Role-Based Access Control

#### Super Admin
- Create/edit/delete any gym
- Access all data across all gyms
- Manage system-wide settings

#### Gym Admin
- Manage their gym's profile
- Create/edit/delete locations for their gym
- Create/edit/delete groups for their gym
- Create/edit/delete packages for their gym
- Manage trainers and trainees within their gym
- View reports and analytics for their gym

#### Trainer
- View assigned groups
- Manage group enrollments
- View trainee profiles (in their groups)
- Mark attendance
- Cannot manage packages or locations

#### Trainee
- View available groups at their locations
- Enroll in groups (based on package limits)
- Purchase packages
- View their enrollment history
- View their active packages

---

## Data Scoping Rules

### Tenant Isolation
- All queries must be scoped to the authenticated user's `gym_id`
- Super admins can access all gyms
- Regular users only see data from their assigned gym

### Location-Based Access
- Users can be assigned to multiple locations via `location_user` pivot
- Primary location is stored in `users.primary_location_id`
- Groups visible to user based on location assignments

### Group Access
- Groups belong to a gym
- Groups can be assigned to multiple locations
- Users can enroll in groups where they have location access
- Group enrollment may be limited by user's active package

### Package Access
- Packages are gym-specific
- Users can purchase multiple packages (but typically one active at a time)
- Package benefits determine group access limits
- Expired packages prevent group enrollments unless renewed

---

## Business Logic

### Package Purchase Flow
1. User selects a package from their gym
2. Payment is processed (status: `pending`)
3. Upon successful payment:
   - `payment_status` → `paid`
   - `starts_at` → current datetime
   - `expires_at` → starts_at + duration_days
   - `status` → `active`
4. System automatically expires packages when `expires_at` is reached

### Group Enrollment Rules
1. User must have an active package (unless unlimited access)
2. User must be assigned to at least one location where group is offered
3. Group must not be at max capacity
4. User's package must allow group enrollment (check `group_access_limit`)

### Multi-Tenant Isolation
- All API requests must include gym context (except SuperAdmin)
- Middleware checks:
  - SuperAdmin: No restrictions
  - GymAdmin/Trainer/Trainee: Filter by `gym_id`
- Users can only see data from their assigned gym

---

## Next Steps

1. ✅ Database migrations created
2. ✅ User roles defined
3. ⏳ Create Eloquent Models with relationships
4. ⏳ Create Repositories for each entity
5. ⏳ Create API Resources (transformers)
6. ⏳ Create Controllers with role-based access
7. ⏳ Add middleware for tenant isolation and role checks
8. ⏳ Create comprehensive seed data
9. ⏳ Build frontend UI for all modules
10. ⏳ Implement package purchase workflow
11. ⏳ Implement group enrollment workflow
