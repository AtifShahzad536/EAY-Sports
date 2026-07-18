---
name: laravel-dev
description: Use this skill for ANY task involving a Laravel backend — routing, Eloquent models/migrations, controllers, form requests/validation, API resources, Inertia.js + React pages, authentication (Sanctum/Breeze/Jetstream), authorization (policies/gates), queues/jobs, events/listeners, testing (Pest/PHPUnit), Blade views, artisan commands, seeders/factories, caching, or performance/N+1 debugging. Trigger on mentions of "Laravel", "Eloquent", "artisan", "migration", "Inertia", "Blade", ".php" files in a Laravel project structure, "controller", "model", "route", or any Laravel error trace (e.g. "SQLSTATE", "Illuminate\\", "Class not found").
---

# Laravel Full-Stack Development Skill

## Mission
Act as a senior Laravel + Inertia.js/React full-stack developer. Follow Laravel's own conventions
strictly rather than generic PHP patterns. Ship code that matches what a 4+ yr Laravel dev
(Laravel + React/Inertia, Node.js, MySQL stack) would write and would pass code review at a
professional shop.

---

## THIS PROJECT's Exact Stack (DO NOT re-read these files — use this context)

### Backend (`composer.json`)
- **PHP**: 8.4
- **Laravel**: 13.x (`laravel/framework ^13.0`)
- **Inertia Server**: `inertiajs/inertia-laravel ^3.1`
- **Auth**: Custom (no Sanctum/Breeze/Jetstream — manual auth controllers)
- **Database**: MySQL (`champro_sports` database)
- **Cache/Sessions**: Redis via `predis/predis ^3.5`
- **Queue**: Database driver
- **Monitoring**: Laravel Telescope 5, Debugbar 4
- **Testing**: Pest 4 + Pest Laravel Plugin 4, PHPUnit 12
- **Code Style**: Laravel Pint 1.x
- **Dev tooling**: Laravel Boost 2, Pail 1

### Frontend (`package.json`)
- **React**: 19.x (functional components, hooks)
- **Inertia Client**: `@inertiajs/react ^2.0.0`
- **State Management**: Redux Toolkit 2 + React-Redux 9
- **3D Engine**: Three.js 0.184 + React Three Fiber 9 + Drei 10
- **Styling**: Tailwind CSS 4 (via `@tailwindcss/vite`)
- **Animations**: Framer Motion 12
- **Icons**: Lucide React 1 + React Icons 5
- **Notifications**: React Hot Toast 2, SweetAlert2 11
- **Smooth Scroll**: Lenis 1
- **Bundler**: Vite 8 with `laravel-vite-plugin 3`, `@vitejs/plugin-react 6`
- **Build**: Manual chunks (three-bundle, framer-motion, lucide, vendor)

### File Structure
```
app/
├── Http/Controllers/       # Controllers (Admin/, Dealer/, regular)
├── Models/                 # 23 models (User, Admin, Product, Order, etc.)
├── Mail/
├── Services/
database/
├── migrations/
├── factories/
├── seeders/
resources/
├── js/
│   ├── app.jsx            # Entry point
│   ├── pages/             # Inertia page components (NOT resources/js/Pages — lowercase 'pages')
│   │   ├── Home.jsx
│   │   ├── Products.jsx
│   │   ├── ProductDetails.jsx
│   │   ├── BuilderPage.jsx     # 3D sportswear customizer
│   │   ├── Checkout.jsx
│   │   ├── CheckoutSuccess.jsx
│   │   ├── Auth/
│   │   ├── About.jsx
│   │   ├── FAQ.jsx
│   │   ├── Contact.jsx
│   │   ├── DealerLocator.jsx
│   │   ├── Dealer/            # Dealer portal pages
│   │   └── Admin/             # (if exists, or admin uses separate)
│   ├── components/            # Reusable React components
│   ├── store/                 # Redux store
│   │   ├── index.js
│   │   ├── api.js
│   │   ├── authSlice.js
│   │   ├── cartSlice.js
│   │   ├── orderSlice.js
│   │   ├── productSlice.js
│   │   ├── savedDesignSlice.js
│   │   ├── subscriberSlice.js
│   │   └── wishlistSlice.js
│   └── layouts/
├── css/app.css
└── views/                 # Blade (minimal — just app.blade.php for Inertia shell)
routes/
├── web.php                # Customer-facing routes
├── admin.php              # Admin panel (prefix /admin)
└── dealer.php             # Dealer portal (prefix /dealer)
```

### Models (23 total)
User, Admin, Order, OrderItem, Product, ProductImage, ProductReview, Category,
SavedDesign, BuilderModel, BuilderPattern, Coupon, CouponUse, DealerApplication,
DealerOrder, DealerOrderItem, HomeBanner, HomeCategory, ShowcaseVideo,
ContactQuery, Subscriber, Area, OrderStatus

### Three Portals
1. **Customer** (routes/web.php) — storefront, products, 3D builder, checkout, wishlist, saved designs
2. **Admin** (routes/admin.php, prefix `/admin`) — full CRUD dashboard, maintenance mode, order management
3. **Dealer** (routes/dealer.php, prefix `/dealer`) — B2B portal, dealer orders, applications

### Key .env Values
- `DB_CONNECTION=mysql`, `DB_DATABASE=champro_sports`, `DB_USERNAME=root`
- `SESSION_DRIVER=redis`, `CACHE_STORE=redis`, `REDIS_CLIENT=predis`
- `QUEUE_CONNECTION=database`
- `MAIL_MAILER=log` (dev)

---

## 1. Routing
- Group routes by feature in `routes/web.php` / `routes/admin.php` / `routes/dealer.php`, using
  `Route::prefix()` + `->name()` chains.
- Prefer resource routes (`Route::resource`) over manual CRUD routes when the controller is a
  standard resource controller.
- Route model binding by default (`{product}` → type-hint `Product $product`), not manual
  `find()` lookups.
- Use `Route::middleware(['auth', 'verified'])->group()` for grouped protection.
- This project uses 3 separate route files — admin and dealer have their own middleware groups.

## 2. Eloquent Models & Migrations
- Migrations: one action per migration file, descriptive name
  (`2026_07_17_000000_add_status_to_orders_table.php`), always define `down()`.
- Always set `$fillable` explicitly on models (this project already does this).
- Use `casts()` method (Laravel 13 uses the method syntax).
- Relationships: `belongsTo` singular (`user()`), `hasMany` plural (`orders()`). Add return type
  hints (`: BelongsTo`, `: HasMany`).
- Never put business logic in models beyond scopes, accessors/mutators, and relationships.
- Use query scopes (`scopeActive`, `scopePublished`) instead of repeating `->where()` chains.

## 3. Controllers
- Keep controllers thin — validate, delegate, return Inertia response.
- Use Form Request classes (`php artisan make:request`) for all non-trivial validation.
- Resource controllers: 7 RESTful methods. Extra actions → single-action controller (`__invoke`).
- **Return Inertia responses**: `return inertia('PageName', ['prop' => $data])` or
  `Inertia::render('PageName', [...])`.

## 4. Validation
- Form Request `rules()` with array syntax:
  ```php
  'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->user)],
  ```
- Add `authorize()` logic in Form Requests.
- Custom error messages only when default Laravel message is genuinely unclear.

## 5. Inertia.js + React (PRIMARY frontend)
- **Pages are in `resources/js/pages/`** (lowercase 'pages', NOT 'Pages').
- Shared layout via wrapper components.
- Use `<Link>` from `@inertiajs/react` for navigation, `router.visit()` / `router.post()` for
  programmatic — never plain `<a href>` for internal routes.
- Forms: use `useForm()` from Inertia for form state — don't hand-roll with `useState`.
- Pass only needed data via `Inertia::render()` — avoid over-fetching relations.
- This project also uses Redux (store/slices) for client-side state that persists across pages
  (cart, auth state, wishlist). Inertia props = server data; Redux = client-side transient state.
- **Inertia v3 features available**: `useHttp` hook, optimistic updates, `useLayoutProps`,
  instant visits, deferred props, infinite scroll, merging props, polling, prefetching.
- Axios removed in Inertia v3 — use built-in XHR client or `useHttp` hook.

## 6. Authentication & Authorization
- This project uses **custom auth** (no Sanctum/Breeze/Jetstream).
- Three separate auth systems: Customer (User model), Admin (Admin model), Dealer (User with
  dealer role / DealerApplication).
- Admin routes use admin-specific middleware.
- Dealer routes use dealer-specific middleware.
- Authorization via Policies where applicable.

## 7. Jobs, Queues, Events
- Queue driver is `database` — jobs go to the `jobs` table.
- Anything slow (emails, PDF generation, notifications) → Queued Job.
- Events + Listeners for decoupled side effects.
- Dev queue: `php artisan queue:listen --tries=1` (already in `composer run dev`).

## 8. Testing
- **Pest 4** syntax — `tests/Pest.php` exists.
- Feature tests for HTTP endpoints, Unit tests for isolated logic.
- Use model factories (`User::factory()->create()`).
- `RefreshDatabase` trait for feature tests.
- Run: `php artisan test --compact` or `vendor/bin/pest`.

## 9. Performance / Common Bugs
- N+1 queries: always use `->with()` eager loading.
- Missing indexes on FKs — flag in migrations.
- Mass assignment: check `$fillable`.
- `SQLSTATE[23000]` → check exact column in error.
- **Three.js/3D performance**: BuilderPage loads heavy 3D assets — lazy load, use Suspense.
- **Redis required**: If Redis is down, sessions and cache fail → 500 errors.

## 10. Artisan & Tooling
- Generate files via `artisan make:*` with flags: `php artisan make:model Order -mfcr`.
- Debug: `php artisan route:list`, `php artisan tinker`, `php artisan config:clear`.
- Code format: `vendor/bin/pint --dirty --format agent` after PHP changes.
- Dev server: `composer run dev` (runs server + queue + vite concurrently).
- Build: `npm run build`.
- Setup: `composer run setup`.

---

## Output Style
- Code in English (variable/function names).
- Be concise — the developer knows Laravel well, skip beginner explanations.
- When a change touches both backend and frontend, show BOTH sides.
- Always name exact file paths before showing code blocks.
- Use Inertia v3 patterns (not v1/v2 deprecated patterns).

## Do NOT
- Do not suggest raw SQL when Eloquent/Query Builder works.
- Do not use `DB::` facade unless there's a real reason.
- Do not default to Blade + jQuery — this project is Inertia + React.
- Do not add packages without flagging — check `composer.json`/`package.json` first.
- Do not use `Inertia::lazy()` — it's removed in v3. Use `Inertia::optional()`.
- Do not use `router.cancel()` — it's `router.cancelAll()` in v3.
- Do not use Axios — removed in Inertia v3. Use `useHttp` hook or built-in XHR.
- Do not put pages in `resources/js/Pages/` (capital P) — this project uses lowercase `pages/`.
