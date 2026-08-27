# TechniTrack

## Project
Application web et mobile pour la gestion des interventions techniques.

## Backend
- Laravel 12
- PHP 8.2
- MySQL
- Laravel Sanctum

## Frontend Web
- Blade
- Tailwind CSS
- Alpine.js
- Livewire

## Architecture
Follow:
Controller → Service → Model

Business logic should stay in Services.
Controllers should remain thin.

## Main Roles
- Super Admin
- Admin
- Commercial
- Client
- Technician

## Important Rules
- Do not modify the database structure without checking existing migrations.
- Do not delete existing features without asking.
- Reuse existing Services when possible.
- Follow the existing project architecture.
- Before making large changes, inspect the related files first.
- Do not duplicate existing logic.
- Keep the UI professional and consistent.

## Development Workflow
Before implementing a feature:
1. Inspect the existing implementation.
2. Identify related models, services, controllers and views.
3. Explain the planned changes.
4. Implement the smallest safe change.
5. Test the modification.
6. Report what was changed.