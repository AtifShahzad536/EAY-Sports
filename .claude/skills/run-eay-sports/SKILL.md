---
name: run-eay-sports
description: Build, run, and drive the EAY Sports Laravel+React e-commerce app. Use when asked to run, start, dev, build, test, or verify the app is working.
---

# Run EAY Sports

EAY Sports is a Laravel 13 + React 19 (Inertia.js v3) sportswear e-commerce platform with a 3D customizer. It runs as a standard Laravel app with Vite for frontend bundling.

All paths below are relative to the project root (`EAY-Sports/`).

## Prerequisites

- PHP 8.4 with extensions: mbstring, xml, curl, mysql, redis
- Composer 2.x
- Node.js 20+ and npm
- MySQL running on port 3306 (database: `champro_sports`)
- Redis running on port 6379 (used for sessions and cache)

### Windows (this project's environment)

PHP, Composer, Node, MySQL, and Redis should be available in PATH. If MySQL or Redis aren't running:

```bash
net start mysql
net start redis
```

## Setup (first time)

```bash
composer run setup
```

This runs: `composer install`, copies `.env.example` to `.env`, generates app key, runs migrations, installs npm packages, and builds frontend assets.

If `.env` already exists but needs a fresh database:

```bash
php artisan migrate:fresh --seed --force
```

## Build

Frontend only:

```bash
npm run build
```

## Run (agent path — headless verification)

For verifying changes without a browser, use the smoke driver:

```bash
bash .claude/skills/run-eay-sports/smoke.sh
```

The driver:
1. Starts the Laravel dev server on port 8000 (background)
2. Waits for it to be ready
3. Hits key routes with `curl` and checks for expected content
4. Reports pass/fail
5. Kills the server on exit

To verify a specific route manually:

```bash
php artisan serve --port=8000 &
SERVER_PID=$!
sleep 2
curl -s http://127.0.0.1:8000 | head -50
kill $SERVER_PID
```

## Run (dev mode — human path)

Starts Laravel server + queue worker + Vite dev server concurrently:

```bash
composer run dev
```

This launches:
- Laravel server at `http://localhost:8000`
- Queue listener (for background jobs)
- Vite dev server (HMR for React components)

Stop with Ctrl+C.

## Test

```bash
php artisan test --compact
```

Or with Pest directly:

```bash
vendor/bin/pest
```

## Code Formatting

After modifying PHP files:

```bash
vendor/bin/pint --dirty --format agent
```

## Key Routes to Verify

| Route | Expected |
|-------|----------|
| `GET /` | Homepage with banners |
| `GET /products` | Product catalog |
| `GET /builder` | 3D customizer page |
| `GET /admin` | Admin login/dashboard |
| `GET /dealer/login` | Dealer portal login |

## Gotchas

- **Redis required for sessions**: If Redis isn't running, every request returns a 500. Either start Redis or temporarily change `SESSION_DRIVER=file` and `CACHE_STORE=file` in `.env`.
- **Vite manifest error**: If you see `ViteException: Unable to locate file in Vite manifest`, run `npm run build` or start `npm run dev`.
- **MySQL database must exist**: Create `champro_sports` database manually if `migrate` fails with "unknown database."
- **Node modules**: The project has `node_modules` checked in or pre-installed. If missing, `npm install --ignore-scripts` first.
- **3D Builder assets**: The builder page loads Three.js models. If models aren't seeded, the page renders but the canvas is empty — this is expected without seed data.

## Troubleshooting

| Symptom | Fix |
|---------|-----|
| `SQLSTATE[HY000] [2002] Connection refused` | MySQL not running. Start it. |
| `SQLSTATE[HY000] [1049] Unknown database` | Run `mysql -u root -e "CREATE DATABASE champro_sports;"` |
| `Redis connection refused` | Start Redis, or switch to file driver in `.env` |
| `Vite manifest not found` | Run `npm run build` |
| Port 8000 already in use | Kill existing process or use `--port=8001` |
