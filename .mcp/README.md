# MCP Context Files

This directory contains comprehensive context documentation for the Health Academy project. These files are designed to provide complete context to AI assistants (like Claude Code) so development can continue seamlessly without needing to re-explain the project architecture.

## Files Overview

### 📋 [project-overview.md](./project-overview.md)
**Read this first!** High-level introduction to the project.
- What the project is (multi-tenant gym management SaaS)
- Tech stack
- User roles
- Core entities
- Current status
- Development commands

### 🏗️ [architecture.md](./architecture.md)
Deep dive into code organization and patterns.
- Directory structure
- Module pattern explanation
- Layer responsibilities (Controllers, Repositories, Resources, Requests)
- API response formats
- Database conventions
- Testing conventions

### 🗄️ [database-schema.md](./database-schema.md)
Complete database reference.
- All table schemas
- Relationships
- Business logic notes
- Migration order
- Indexing strategy

### 🔌 [api-endpoints.md](./api-endpoints.md)
API documentation and reference.
- All available endpoints
- Request/response formats
- Query parameters
- Status codes
- Authorization notes (TODOs)

### 🚀 [next-steps.md](./next-steps.md)
Development roadmap and priorities.
- Immediate priorities (authorization, frontend, workflows)
- Secondary features
- Code quality tasks
- Production readiness checklist
- How to pick up development

### 📝 [common-patterns.md](./common-patterns.md)
Practical code examples and patterns.
- Step-by-step guide to create a new module
- Common query patterns
- Testing patterns
- Frontend service patterns
- Copy-paste examples for new features

## How to Use These Files

### For AI Assistants (Claude Code, etc.)
When starting a new session on this project:

1. **Quick context**: Read `project-overview.md` for basic understanding
2. **Architecture questions**: Refer to `architecture.md` for structure/patterns
3. **Database queries**: Check `database-schema.md` for table structures
4. **API work**: Use `api-endpoints.md` for endpoint reference
5. **New features**: Follow `common-patterns.md` templates
6. **Task planning**: Check `next-steps.md` for priorities

### For Human Developers
These files serve as:
- Onboarding documentation for new team members
- Quick reference for patterns and conventions
- Roadmap for future development
- Technical specification document

## Keeping Documentation Updated

When making significant changes:
- ✅ Added new module → Update `architecture.md`, `api-endpoints.md`, and `common-patterns.md`
- ✅ Changed database schema → Update `database-schema.md`
- ✅ Completed a major feature → Update `project-overview.md` (status) and `next-steps.md`
- ✅ Changed patterns/conventions → Update `common-patterns.md`

## Quick Reference

### Project Commands
```bash
# Development
php artisan serve
npm run dev

# Database
php artisan migrate:fresh --seed

# Testing
php artisan test
php artisan test --filter=GymControllerTest

# Generate resources
php artisan make:model Membership -mfs
php artisan make:request StoreMembershipRequest
php artisan make:resource MembershipResource
```

### Test Credentials
- SuperAdmin: `superadmin@example.com` / `password`
- Gym Admin: `admin@{gym-slug}.com` / `password`
- All users: password is `password`

### Key File Locations
- Models: `app/Models/`
- Modules: `app/Modules/{ModuleName}/`
- Tests: `tests/Feature/Modules/{ModuleName}/`
- API Routes: `routes/api.php`
- Factories: `database/factories/`
- Seeders: `database/seeders/`

## Current Project State

✅ **Completed:**
- Database schema (5 entities: Gym, Location, Group, Package, User)
- Full CRUD API for all entities
- 39 feature tests (all passing)
- Factories and seeders
- Basic frontend (Users module only)

⏳ **In Progress / TODO:**
- Authorization & policies (placeholders exist)
- Frontend for Gym, Location, Group, Package modules
- Package purchase workflow
- Group enrollment workflow

See `next-steps.md` for detailed roadmap.

---

**Last Updated:** 2025-11-06
**Project Version:** Alpha (pre-launch)
