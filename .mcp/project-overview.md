# Health Academy - Project Overview

## Project Type
Multi-tenant gym management system built with Laravel 12 + Vue.js 3 + TypeScript

## Core Purpose
A SaaS platform that allows gym owners to manage multiple locations, create training groups/courses, sell membership packages, and manage staff and members.

## Tech Stack
- **Backend**: Laravel 12, PHP 8.3+
- **Database**: SQLite (dev), supports MySQL/PostgreSQL (production)
- **Authentication**: Laravel Sanctum (stateful API)
- **Frontend**: Vue.js 3 with TypeScript, Vite
- **HTTP Client**: ofetch
- **Testing**: PHPUnit with Feature Tests

## User Roles
1. **SuperAdmin** - Platform administrator, can create/manage all gyms
2. **GymAdmin** - Gym owner/manager, manages their gym(s)
3. **Trainer** - Staff member, conducts training sessions
4. **Trainee** - Regular gym member/customer

## Core Entities
1. **Gym** - Top-level tenant (fitness business)
2. **Location** - Physical gym location (one gym can have many)
3. **Group** - Training courses/classes (e.g., "Morning Yoga", "CrossFit")
4. **Package** - Membership plans with pricing and benefits
5. **User** - All system users with roles

## Key Relationships
- Gyms → Locations (1:many)
- Gyms → Groups (1:many)
- Gyms → Packages (1:many)
- Gyms → Users (1:many)
- Groups → Locations (many:many) - groups can happen at multiple locations
- Groups → Users (many:many) - enrollment tracking
- Packages → Users (many:many) - purchase tracking with payment details
- Users → Locations (many:many) - assignment tracking

## Current Status
- ✅ Database schema and migrations complete
- ✅ Models with full relationships
- ✅ Modular architecture (Gym, Location, Group, Package modules)
- ✅ API endpoints with CRUD operations
- ✅ Factories and seeders
- ✅ Comprehensive feature tests (39 tests, 270 assertions)
- ✅ User module with full CRUD
- ⏳ Policies/authorization (placeholders with TODOs)
- ⏳ Frontend components (users page exists, others pending)
- ⏳ Package purchase workflow
- ⏳ Group enrollment workflow

## Development Commands
```bash
# Run migrations and seeders
php artisan migrate:fresh --seed

# Run tests
php artisan test

# Run specific test suite
php artisan test --filter=GymControllerTest

# Start dev server
php artisan serve

# Frontend dev server
npm run dev
```

## Test Credentials
- **SuperAdmin**: superadmin@example.com / password
- **Gym Admin**: admin@{gym-slug}.com / password
- All test users have password: `password`
