# Production scaling (100k+ users) — StayLBD / Laravel

## 1. Redis (required)

Set in `.env`:

- `CACHE_DRIVER=redis`
- `SESSION_DRIVER=redis`
- `QUEUE_CONNECTION=redis`
- `REDIS_HOST`, `REDIS_PASSWORD`, `REDIS_PORT` as appropriate

Run: `php artisan config:cache` after deploy.

## 2. Queue workers — Laravel Horizon

Horizon is installed. Production:

```bash
php artisan horizon
```

Use **Supervisor** or **systemd** to keep Horizon running. Example Supervisor program: `deployment/supervisor-horizon.example.conf` (adjust `command` path and `user`). Dashboard: `/horizon` (admin-only; see `AppServiceProvider`).

Tune `config/horizon.php` `production` `maxProcesses` for CPU/RAM.

Failed jobs: `failed_jobs` table + `php artisan queue:retry` / monitoring alerts.

## 3. CDN — Cloudflare + `ASSET_URL`

1. Put the site behind Cloudflare (orange cloud DNS).
2. Enable **Brotli** and **Auto Minify** (optional; you already ship minified Mix assets).
3. Cache rules: cache static extensions (`css`, `js`, `woff2`, images) at edge; **bypass cache** for HTML and `/api/*` unless you know what you are doing.
4. Set in `.env`:

   `ASSET_URL=https://cdn.yourdomain.com`

   Origin must serve `public/` (or mirror static files to R2/S3 and point CDN there).

5. Bump `APP_VERSION` or admin “clear cache” so `?v=` on assets invalidates browser cache.

## 4. Images (WebP + lazy load)

Product views use `getImageWebP` / WebP helpers where configured. Prefer **WebP/AVIF** at upload, **responsive** `srcset` for hero images, `loading="lazy"` below the fold (already on cards in many places).

## 5. Compression (origin)

If not using Cloudflare only: enable **gzip** and **Brotli** on Nginx/Apache. Apache: see `public/.htaccess` in this repo.

## 6. MySQL

- Slow query log + `EXPLAIN` on homepage and product listing queries.
- Indexes: see existing migrations under `database/migrations/*products*index*`.
- For very high read load: **read replicas** + sticky writes in app (advanced).

## 7. Load and speed tests

- **Lighthouse** (mobile + desktop) on production URL.
- **k6** / **Locust** for API: `tools/k6-realtime-stress.js` and `php artisan realtime:load-test`.
- Realtime: Pusher/Soketi capacity + `throttle:realtime_poll` tuning in `RouteServiceProvider`.

## 8. CSS size target (<150 KB compressed, Brotli)

Legacy `main.css` is split into `public/assets/templates/basic/css/main-legacy-*.css` and composed again via `main.css` (`@import`). Mix entrypoints pull **route-specific** subsets:

- **Home** (`tailwind-storefront-deferred-home`): no `main-legacy-product`, no `main-legacy-widgets`.
- **Default deferred** (`tailwind-storefront-deferred`): full legacy except widgets.
- **User dashboard** (`tailwind-storefront-deferred-account`): adds `main-legacy-widgets.css`.

**Production** deferred bundles run **PurgeCSS** (`postcss-purgecss-deferred.cjs`). If a page loses styles, add a safelist pattern there or set `STOREFRONT_DEFERRED_PURGE=0` for one build.

**Measure** after `npm run production`: `npm run measure:css` (raw / gzip / brotli for key files). Example targets (approximate, Brotli, three responses: blocking + critical + deferred): home ~99 KB, catalog/PDP ~116 KB — under 150 KB when the edge serves Brotli.

**Validation**: smoke-test homepage, product detail, cart, user dashboard, compare/wishlist. **Lighthouse** on production for LCP and “render-blocking” (blocking bundle remains `tailwind-homepage|tailwind-product` + `critical-storefront`; deferred stays `media="print"` swap).

**Tailwind**: `tailwind.safelist.storefront.cjs` keeps dynamic Blade-driven classes from being purged by Tailwind’s scanner.
