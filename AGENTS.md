# Repository Guidelines

## Project Structure & Module Organization
- Backend: Laravel app in `app/`, HTTP routes in `routes/` (`web.php`, `auth.php`, etc.), config in `config/`, views Inertia-driven under `resources/js/pages`.
- Frontend: Vite + Vue 3 in `resources/js` (components, pages, layouts), styles in `resources/css`.
- Tests: Pest in `tests/Feature` and `tests/Unit`.
- Assets/public: Built assets served via Vite; public files in `public/`.

## Build, Test, and Development Commands
- `composer run dev`: Run PHP server, queue listener, logs, and Vite concurrently for local dev.
- `composer run dev:ssr`: Start server with Inertia SSR.
- `npm run dev`: Vite dev server (frontend only).
- `npm run build` / `npm run build:ssr`: Build client (and SSR) assets.
- `composer test`: Clear config and run Pest via `artisan test`.

## Coding Style & Naming Conventions
- PHP: PSR-12 via Laravel Pint (`vendor/bin/pint`). Classes `StudlyCase`, methods `camelCase`. Controllers under `App\\Http\\Controllers`.
- JS/TS/Vue: ESLint + Prettier enforced (`npm run lint`, `npm run format`). Use single quotes, semicolons, 4-space indent, 150-char print width.
- Files/dirs: Vue components `PascalCase.vue`; tests `SomethingTest.php` under matching feature folder.

## Testing Guidelines
- Framework: Pest + Laravel (`tests/Feature`, `tests/Unit`).
- Naming: Use `test('does something', ...)` with clear descriptions; group by feature directory.
- Run: `composer test` or `./vendor/bin/pest`.
- Coverage: Keep meaningful assertions; prefer feature tests for routes and auth flows. SQLite in-memory is configured in `phpunit.xml`.

## Commit & Pull Request Guidelines
- Commits: Imperative mood, concise scope (e.g., `feat: add profile form validation`). Squash trivial fixups.
- PRs: Target `develop` or `main` as appropriate. Include description, linked issues, and screenshots for UI changes.
- CI: Lint and tests run on PRs and pushes to `develop`/`main` (see `.github/workflows/`). Ensure `npm run format`, `npm run lint`, and `vendor/bin/pint` pass locally.

## Security & Configuration Tips
- Copy env: `cp .env.example .env` then `php artisan key:generate` for first run.
- Secrets: Never commit `.env` or credentials. Use `.env` for local, GitHub secrets for CI.
- Telegram Mini Apps: Dev mock lives in `resources/js/telegram/mockEnv.ts` (see inline LaunchParams notes) and full guide in `docs/tma-integration.md`. Do not enable mocks in production.
