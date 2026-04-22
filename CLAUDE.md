# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**MyGym** is a Laravel 12 fitness gym management SaaS with three user roles (admin, instructor, member), AI chat, booking/scheduling, earnings tracking, and workout planning. The AI chat uses Groq API (primary) with OpenRouter as fallback.

## Common Commands

```bash
# Development
composer dev          # Start server, queue worker, log tail, and Vite concurrently
composer setup        # Full first-time setup (install, .env, key generate, migrate, npm install)

# Individual dev services
php artisan serve
php artisan queue:listen
php artisan pail      # Stream logs in terminal
npm run dev           # Vite with hot reload

# Testing
composer test         # Run all tests
php artisan test --filter=ClassName          # Run a single test class
php artisan test --filter=test_method_name  # Run a single test method

# Code style
./vendor/bin/pint     # Fix code style (Laravel Pint)

# Database
php artisan migrate
php artisan migrate:fresh --seed  # Reset and seed all data
php artisan db:seed --class=UserSeeder  # Run a specific seeder

# Maintenance
php artisan clear-old-logs  # Custom command: clean up old log entries
```

## Architecture

### Multi-Role Authorization
Routes are grouped by role middleware (`role:admin`, `role:instructor`, `role:member`) in `routes/web.php`. Authorization is enforced by:
- **Spatie `laravel-permission`** for role checks at the route/middleware level
- **Model Policies** (e.g., `ScheduledClassPolicy`) for resource-level authorization
- Middleware in `app/Http/Middleware/` for custom guards (e.g., `RateLimitChat`)

### Service Layer
`app/Services/AIChatService.php` handles all AI interactions. It's bound in `app/Providers/AIChatServiceProvider.php` and injected into controllers via the container. The service tries Groq first, falls back to OpenRouter, then falls back to local intelligent responses if no API keys are configured. Context for AI responses (user profile, memberships, upcoming classes) is assembled in the service before each API call.

### Frontend Stack
Blade templates with Tailwind CSS and Alpine.js. Vite bundles assets. The layout files in `resources/views/layouts/` define the per-role shells (admin/instructor/member sidebars). View components live in `app/View/Components/` with corresponding Blade partials in `resources/views/components/`.

### Key Models & Relationships
- `User` → has roles, many `Booking`s, many `ChatSession`s, many `ScheduledClass`es (as instructor)
- `ScheduledClass` → belongs to `User` (instructor), has many `Booking`s, belongs to `ClassType`
- `Booking` → belongs to `User` (member) and `ScheduledClass`, generates `Receipt`
- `ChatSession` → belongs to `User`, has many `ChatMessage`s
- `Workout` → belongs to `User`, has many `WorkoutExercise`s → each belongs to `Exercise`

### REST API
`routes/api.php` exposes Sanctum-protected endpoints for mobile/external clients covering auth, classes, bookings, profile, and receipts. Controllers are in `app/Http/Controllers/Api/`.

### Queue & Jobs
Background work uses the database queue driver. `NotifyClassCanceledJob` dispatches `ClassCanceledNotification` to booked members. Run `php artisan queue:listen` during development (included in `composer dev`).

### Environment Variables
Key non-obvious variables in `.env`:
```
GROQ_API_KEY=          # Primary AI — llama-3.1-8b-instant
GROQ_MODEL=llama-3.1-8b-instant
OPENROUTER_API_KEY=    # Fallback AI provider
```
Testing uses SQLite in-memory (configured in `phpunit.xml`), array cache, and log mailer — no real DB or mail needed for tests.

### Internationalization
Eight languages (en, es, fr, de, it, pt, sw, ar) in `resources/lang/`. Use `__('key')` helpers in Blade and controllers; add translations to all language files when adding new user-facing strings.

### PDF & Excel Exports
DomPDF handles receipt/report PDFs. Maatwebsite Excel handles spreadsheet exports. Export classes live in `app/Exports/`.
