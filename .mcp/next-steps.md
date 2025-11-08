# Development Roadmap & Next Steps

## Immediate Priorities

### 1. Authorization & Policies
**Status:** Placeholders exist (TODO comments throughout codebase)

**Tasks:**
- [ ] Create policies for all models (Gym, Location, Group, Package, User)
- [ ] Implement authorization in controllers (uncomment TODO lines)
- [ ] Add policy checks in FormRequests
- [ ] Test authorization with different roles

**Location of TODOs:**
- `app/Modules/*/Controllers/*Controller.php` - Line ~24, ~48, ~75
- `app/Modules/*/Requests/*Request.php` - Line ~12

**Example Implementation:**
```php
// In GymController.php
public function index(Request $request): GymCollection
{
    $this->authorize('viewAny', Gym::class); // Uncomment and implement
    // ...
}

// Create app/Policies/GymPolicy.php
public function viewAny(User $user): bool
{
    return $user->isSuperAdmin() || $user->isGymAdmin();
}
```

### 2. Frontend Development
**Status:** Only Users module has frontend

**Tasks:**
- [ ] Create Gyms management page (list, create, edit, delete)
- [ ] Create Locations management page
- [ ] Create Groups management page with location assignment
- [ ] Create Packages management page
- [ ] Add role-based navigation/menu
- [ ] Create dashboard for different roles

**Pattern to Follow:**
Look at existing: `resources/ts/pages/users.vue` and `resources/ts/pages/AddNewUserDrawer.vue`

**Services to Create:**
- `gymService.ts`
- `locationService.ts`
- `groupService.ts`
- `packageService.ts`

### 3. Package Purchase Workflow
**Status:** Database ready, no implementation

**Tasks:**
- [ ] Create endpoint: `POST /packages/{id}/purchase`
- [ ] Implement payment processing (Stripe/PayPal integration)
- [ ] Calculate `expires_at` based on `duration_days`
- [ ] Send purchase confirmation email
- [ ] Create user dashboard to view active packages
- [ ] Add endpoint: `GET /me/packages` (user's purchased packages)
- [ ] Handle package expiration (scheduled job)

**Tables Involved:**
- `package_user` pivot table (has payment fields ready)

### 4. Group Enrollment Workflow
**Status:** Database ready, no implementation

**Tasks:**
- [ ] Create endpoint: `POST /groups/{id}/enroll`
- [ ] Check `max_participants` capacity
- [ ] Check if user has active package
- [ ] Check package's `group_access_limit`
- [ ] Create endpoint: `GET /me/groups` (user's enrolled groups)
- [ ] Create trainer view: `GET /groups/{id}/participants`
- [ ] Add ability to mark attendance

**Tables Involved:**
- `group_user` pivot table

## Secondary Features

### 5. Email Notifications
- [ ] Welcome email on registration
- [ ] Package purchase confirmation
- [ ] Package expiration warning (7 days, 1 day)
- [ ] Group enrollment confirmation
- [ ] Group reminder (1 day before start)

### 6. Reporting & Analytics
- [ ] Gym dashboard: member count, revenue, active groups
- [ ] Location analytics: utilization, popular times
- [ ] Package sales report
- [ ] Group attendance tracking

### 7. Advanced Features
- [ ] Calendar view for groups/classes
- [ ] Recurring groups (weekly classes)
- [ ] Waitlist for full groups
- [ ] Package renewals (auto-renew option)
- [ ] Trainer schedule management
- [ ] Equipment/resource booking
- [ ] Member check-in system (QR codes)

## Code Quality & DevOps

### 8. Testing
**Current:** 39 tests, 270 assertions (all passing)

**To Add:**
- [ ] Policy tests
- [ ] Package purchase flow tests
- [ ] Group enrollment flow tests
- [ ] Email notification tests
- [ ] Integration tests for complex workflows

### 9. Documentation
- [x] MCP context files
- [ ] API documentation (OpenAPI/Swagger)
- [ ] Deployment guide
- [ ] User manual
- [ ] Admin guide

### 10. Production Readiness
- [ ] Configure production database (MySQL/PostgreSQL)
- [ ] Set up queues (Redis/Database)
- [ ] Configure production mail driver
- [ ] Set up logging (Sentry, Papertrail)
- [ ] Configure caching (Redis)
- [ ] Set up scheduled tasks (package expiration checks)
- [ ] Security audit
- [ ] Performance optimization
- [ ] Deploy to staging environment

## Quick Wins (Can be done anytime)

- [ ] Add soft deletes to models
- [ ] Add profile photo upload for users
- [ ] Add gym/location logo upload
- [ ] Add package thumbnail images
- [ ] Improve validation error messages
- [ ] Add search/filtering to list endpoints
- [ ] Add sorting options to collections
- [ ] Create API rate limiting
- [ ] Add activity logging (audit trail)

## How to Pick Up Development

1. **Start with Authorization** - It's fundamental and blocks other features
   - Begin with `app/Policies/GymPolicy.php`
   - Register in `app/Providers/AuthServiceProvider.php`
   - Uncomment TODO comments in controllers
   - Write tests in `tests/Feature/Modules/Gym/GymPolicyTest.php`

2. **Then Build Workflows** - Package purchase or group enrollment
   - These are core business features
   - Will require new controllers/services
   - Frontend components needed

3. **Finally Polish** - Frontend, notifications, reports
   - Makes the system usable
   - Improves UX

## Important Reminders

- **Always write tests first** - We have good coverage, maintain it
- **Use factories** - Don't hardcode test data
- **Follow the module pattern** - Controller → Repository → Model
- **Use API Resources** - Never return models directly
- **Check TODO comments** - They're everywhere and need attention
- **Run tests before committing** - `php artisan test`
- **Update these MCP docs** - As you build features
