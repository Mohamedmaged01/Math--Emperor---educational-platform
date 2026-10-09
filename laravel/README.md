# منصة إمبراطور الرياضيات — Laravel edition

Laravel 12 + Blade + Tailwind CSS v4 + Alpine.js port of the original React/Vite app (since removed).

## Run it

```bash
composer install
npm install
cp .env.example .env        # SQLite by default
php artisan key:generate
php artisan migrate --seed  # demo accounts + seeded workspace
php artisan storage:link    # media uploads
npm run build               # or `npm run dev` while working
php artisan serve
```

Demo accounts (password `Emperor@2026`):

| Portal | Login |
| --- | --- |
| Imperial console, super admin | `admin` |
| Imperial console, teacher (restricted RBAC) | `fahad.zahrani@emperor.edu.sa` |
| Student portal | `student@emperor.sa`, `demo@emperor.sa` |

Tests: `php artisan test`

## Where things live

| Area | Files |
| --- | --- |
| Landing page sections and overlays | `resources/views/home.blade.php`, `resources/views/partials/{home,overlays}` |
| Shared UI (badge, button, card, modal, window, icon `<x-i>`) | `resources/views/components` |
| Design tokens, typography, motion layer | `resources/css/app.css` |
| Alpine components (simulator, advisor, search, auth, forms) | `resources/js/components/site.js` |
| Imperial console components (data table, RBAC, media, SEO…) | `resources/js/components/imperial.js` |
| Static marketing content | `app/Support/Content.php` |
| Module registry and RBAC matrix | `app/Support/Imperial.php` |
| Analytics engine, SEO registry | `app/Support/Analytics.php`, `app/Support/Seo.php` |
| Workspace persistence and audit trail | `app/Services/AdminStore.php` |
| Controllers | `app/Http/Controllers` (`Imperial/` holds the console) |

## Differences from the React build

Everything the original kept in `localStorage` now lives in the database: accounts, simulator scores, the imperial workspace, CMS/SEO/marketing settings, the audit log, newsletter signups and contact messages. On top of that:

- **Passwords** are hashed by Laravel, sessions are server-side, and login is rate-limited.
- **Password reset** emails a 6-digit code that step 2 requires. The original let anyone set a new password for any account. With `MAIL_MAILER=log` the code is written to `storage/logs/laravel.log`.
- **RBAC** is enforced by the server on every API call, not just hidden in the UI. Refused calls are written to the audit log.
- **Simulator scores** are computed on the server from the submitted answers.
- **Media center** stores uploaded files on the `public` disk instead of keeping only their metadata.
- **CMS, SEO overrides and AdSense settings** are applied to the rendered home page. SEO overrides were never applied in the original.
- **`/sitemap.xml` and `/robots.txt`** are real routes.
- **The audit log** is read-only and export-only; the original let super admins edit or delete audit entries.
- **Registration** also adds the new student to the console's "students" table.
- **Dead code** from the original (`CrmPage`, `CmsDashboardPage`, `src/cms/*`, `ImperialDashboard`, `components/About`) was not ported, because nothing in the UI reached it.
