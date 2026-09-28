# RB_C Agent Guide

## Project

RB_C is a Laravel 13 + Vue 3 + Inertia.js business command centre. It is a redesigned rebuild of a demo reference site, not a source-code mirror.

## Architecture

- Use Laravel web routes in `routes/web.php` and server-side Inertia props.
- Do not add `routes/api.php` or introduce a JSON API for workspace flows.
- Vue pages live in `resources/js/Pages/`; the shared authenticated shell is `resources/js/Layouts/AppLayout.vue`.
- Keep business rules in controllers/models and persist changes through migrations.
- Seeded demo data belongs in `database/seeders/DatabaseSeeder.php`.

## Local commands

Use PHP 8.5 for this project:

```bash
php8.5 artisan migrate:fresh --seed --force
php8.5 artisan test
npm run build
php8.5 artisan serve
```

The normal development entry point is `/`; authenticated workspace pages use `/admin/login`.

## Change rules

- Preserve unrelated user changes and keep edits scoped to this repository.
- Keep the existing calm, editorial visual system; prefer reusable classes in `resources/css/app.css`.
- Add migrations and feature tests for persistent workflows.
- Use safe archive/status transitions when records have transaction history; do not silently destroy accounting or stock history.
- Never commit `.env`, local SQLite files, build output, dependencies, passwords, API keys, cookies, or other secrets.
- The reference capture is outside this repository at `../reference-site/`; do not copy it into application code.

## Verification

Before handing off a change, run the relevant PHP syntax checks, `php8.5 artisan test`, and `npm run build` when frontend code changes. For route or visual work, perform a local smoke check with a normal browser user agent and stop the temporary server afterward.
