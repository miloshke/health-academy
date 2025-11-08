# API Endpoints Reference

Base URL: `/api`

All endpoints require authentication via Laravel Sanctum (`auth:sanctum` middleware) except where noted.

## Authentication Endpoints

### Public Routes

```
POST   /auth/login                  # Login
POST   /auth/register               # Register new user
POST   /auth/resend-verification    # Resend email verification
POST   /auth/forgot-password        # Request password reset
POST   /auth/reset-password         # Reset password with token
GET    /auth/reset-password/{token} # Password reset redirect
```

### Authenticated Routes

```
GET    /auth/user                   # Get current user
GET    /auth/logout                 # Logout
```

## Admin Routes

All admin routes require `auth:sanctum` and `verified` middleware.

### Users

```
GET    /admin/users                 # List users (paginated)
POST   /admin/users                 # Create user
GET    /admin/users/{id}            # Show user details
PUT    /admin/users/{id}            # Update user
DELETE /admin/users/{id}            # Delete user
```

**Query Parameters:**
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 10)

**Response Format:** See UserResource/UserCollection

## Gym Management

```
GET    /gyms                        # List gyms (paginated)
POST   /gyms                        # Create gym
GET    /gyms/{id}                   # Show gym details
PUT    /gyms/{id}                   # Update gym
DELETE /gyms/{id}                   # Delete gym
```

**Query Parameters:**
- `page`: Page number
- `per_page`: Items per page

**Create/Update Fields:**
```json
{
  "name": "string (required on create)",
  "slug": "string (required on create, unique)",
  "description": "string (optional)",
  "email": "string (optional, email format)",
  "phone": "string (optional)",
  "website": "string (optional, url format)",
  "status": "string (required, enum: active|inactive|suspended)"
}
```

## Location Management

```
GET    /locations                   # List locations (paginated)
POST   /locations                   # Create location
GET    /locations/{id}              # Show location details
PUT    /locations/{id}              # Update location
DELETE /locations/{id}              # Delete location
```

**Query Parameters:**
- `page`: Page number
- `per_page`: Items per page
- `gym_id`: Filter by gym (optional)

**Create/Update Fields:**
```json
{
  "gym_id": "integer (required on create)",
  "name": "string (required on create)",
  "address": "string (optional)",
  "city": "string (optional)",
  "state": "string (optional)",
  "zip": "string (optional)",
  "country": "string (optional)",
  "phone": "string (optional)",
  "email": "string (optional, email format)",
  "status": "string (required, enum: active|inactive)"
}
```

## Group Management

```
GET    /groups                      # List groups (paginated)
POST   /groups                      # Create group
GET    /groups/{id}                 # Show group details
PUT    /groups/{id}                 # Update group
DELETE /groups/{id}                 # Delete group
```

**Query Parameters:**
- `page`: Page number
- `per_page`: Items per page
- `gym_id`: Filter by gym (optional)

**Create/Update Fields:**
```json
{
  "gym_id": "integer (required on create)",
  "name": "string (required on create)",
  "description": "string (optional)",
  "start_date": "date (optional, YYYY-MM-DD)",
  "end_date": "date (optional, must be after start_date)",
  "max_participants": "integer (optional, min: 1)",
  "status": "string (required, enum: active|inactive|cancelled|completed)",
  "location_ids": "array (optional, integers, locations where group occurs)"
}
```

**Special Behavior:**
- `location_ids` on create: attaches locations
- `location_ids` on update: syncs locations (replaces existing)

## Package Management

```
GET    /packages                    # List packages (paginated)
POST   /packages                    # Create package
GET    /packages/{id}               # Show package details
PUT    /packages/{id}               # Update package
DELETE /packages/{id}               # Delete package
```

**Query Parameters:**
- `page`: Page number
- `per_page`: Items per page
- `gym_id`: Filter by gym (optional)

**Create/Update Fields:**
```json
{
  "gym_id": "integer (required on create)",
  "name": "string (required on create)",
  "description": "string (optional)",
  "price": "numeric (required on create, min: 0)",
  "duration_days": "integer (required on create, min: 1)",
  "benefits": "json string (optional, array of benefits)",
  "group_access_limit": "integer (optional, min: 0)",
  "unlimited_access": "boolean (default: false)",
  "status": "string (required, enum: active|inactive)"
}
```

## Development/Test Routes

Only available when `APP_ENV !== production`:

```
GET    /test                        # Simple test endpoint
GET    /test-auth                   # Test authentication
GET    /admin/users/test            # Test users endpoint (no auth)
```

## HTTP Status Codes

- `200 OK` - Successful GET, PUT, DELETE
- `201 Created` - Successful POST
- `401 Unauthorized` - Not authenticated
- `403 Forbidden` - Authenticated but not authorized (when policies implemented)
- `404 Not Found` - Resource not found
- `422 Unprocessable Entity` - Validation errors

## Error Response Format

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field_name": [
      "Error message 1",
      "Error message 2"
    ]
  }
}
```

## Pagination Response Format

All collection endpoints return paginated results:

```json
{
  "data": [...],
  "links": {
    "first": "http://example.com/api/gyms?page=1",
    "last": "http://example.com/api/gyms?page=5",
    "prev": null,
    "next": "http://example.com/api/gyms?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "path": "http://example.com/api/gyms",
    "per_page": 10,
    "to": 10,
    "total": 47
  }
}
```

## Authorization Notes (TODO)

Currently, all endpoints have TODO comments for policy checks:
```php
// TODO: Add policy check
// $this->authorize('viewAny', Gym::class);
```

When implementing policies:
- SuperAdmin: Access to everything
- GymAdmin: Access to their gym's data only
- Trainer: Read access to their gym's data
- Trainee: Limited access (profile, packages, groups they're enrolled in)
