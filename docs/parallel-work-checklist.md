# Parallel Work Checklist

This file turns the migration plan into a simple execution checklist for four people.

## Goal

Keep the current project stable, keep the frontend simple, clean the PHP routing structure, and prepare the codebase for a later Symfony migration after validation.

## Team Split

### Person 1 - Frontend cleanup

Focus: HTML, CSS, and JavaScript organization.

Checklist:

- Review all HTML pages and remove unnecessary duplication.
- Keep shared layout parts consistent across pages.
- Verify each page only loads the CSS and JS it needs.
- Make the navbar and footer behavior consistent.
- Check for repeated JavaScript logic that can be moved to shared helpers.
- Keep the design simple and easy to explain to the professor.

### Person 2 - PHP routing and bootstrap

Focus: route structure and backend request flow.

Checklist:

- Document every current backend endpoint.
- Keep the current URLs unchanged.
- Split the centralized router into resource-based route files.
- Keep session start, CORS, and preflight handling in one shared bootstrap layer.
- Make sure old API calls still work exactly the same.
- Keep route files easy to read and easy to migrate later to Symfony.

### Person 3 - Controllers and API behavior

Focus: backend logic and response consistency.

Checklist:

- Review controller methods for duplicate validation logic.
- Keep success and error JSON payloads consistent.
- Confirm auth/session behavior works correctly.
- Check that database reads and writes are still safe.
- Keep controller responsibilities focused on business logic only.
- Avoid changing business rules while routing is being cleaned up.

### Person 4 - Testing and documentation

Focus: validation, notes, and handoff materials.

Checklist:

- Test all current endpoints manually.
- Check login, logout, register, profile, jobs, and posts.
- Verify session cookies still work from the frontend.
- Confirm CORS and OPTIONS requests behave correctly.
- Write a small route contract document.
- Keep track of regressions during the migration.
- Prepare a short note explaining why the current structure is valid before Symfony.

## Suggested Order

1. Person 4 documents the current route contract first.
2. Person 2 starts the routing split.
3. Person 3 checks controller behavior during the split.
4. Person 1 cleans the frontend once the API contract is stable.

## Validation Checklist

Before moving to Symfony, confirm:

- all existing URLs still work
- all forms still submit correctly
- session login persists
- profile and jobs endpoints still return the same data shape
- unknown routes still return a clear JSON error
- frontend pages still load without JavaScript errors

## Final Rule

Do not rewrite everything at once.

Make small controlled changes, test after each step, and keep the current project easy to defend in front of the professor.