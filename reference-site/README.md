# redcom.cloud reference capture

Captured on 2026-09-28 for a Laravel + Vue.js rebuild study.

## Scope

- Public landing page: `https://redcom.cloud/`
- Public registration flows: `/register/2`, `/register/3`, `/register/4`
- Admin SPA shell: `https://redcom.cloud/admin/login`
- Authenticated read-only API shape checks using the supplied demo account
- No create, update, delete, payment, registration, logout, or other mutating request was sent

## Capture results

- 167 client routes extracted from the admin Vue route map, plus the admin root
- 130 literal admin routes returned the SPA shell with HTTP 200
- 1 admin root route redirected to login
- 37 parameterized routes were captured again using non-mutating sample IDs; all returned HTTP 200
- 380 API references extracted from the admin bundle
- 419 admin JavaScript/CSS assets mirrored; all returned HTTP 200
- 3 public registration pages mirrored; all returned HTTP 200
- 22 same-origin public assets mirrored; all returned HTTP 200

## Functional areas visible in the route and bundle inventory

Accounting and ledger management, customers and vendors, banking and cheques, branches and warehouses, employees, inventory and variants, barcode and price labels, purchase and returns, sales and quotations, POS, transfers, production/BOM, SMS reports, dashboards, cost centres, payments, invoices, and financial/stock reports.

## Important implementation observations

- The admin UI is a Vue SPA mounted at `/admin`; route HTML is a shared shell, while the page implementations are lazy-loaded chunks under `assets/chunks/`.
- Login posts JSON fields `loginEmail` and `loginPassword` to `/admin/api/login` and receives a token plus user context.
- The Laravel session requires the browser XSRF cookie/header pair before login.
- Authenticated API calls require the returned token in the `Bearer` authorization form.
- The demo account exposes 8 branches in its user context. Raw account and business records were not copied into this folder.
- Several dashboard/report endpoints require branch, date, or other query parameters; requests without those parameters returned validation responses rather than indicating missing screens.
- Literal placeholder URLs such as `/orders/:id` are expected to fail; concrete sample-ID route snapshots are under `snapshots/routes/dynamic/`.

## Files

- `snapshots/public-home.html`: landing page
- `snapshots/public/`: package registration pages
- `snapshots/admin-shell.html`: admin SPA shell
- `snapshots/routes/`: route-level shell captures
- `snapshots/routes/dynamic/`: parameterized route captures
- `assets/`: admin entry assets and mirrored lazy chunks
- `assets/public/`: public registration/landing assets
- `metadata/client-routes.txt`: client route inventory
- `metadata/api-endpoints.txt`: bundle API reference inventory
- `metadata/admin-assets.txt`: admin asset manifest
- `metadata/route-fetch-status.tsv`: literal route status/size results
- `metadata/dynamic-route-fetch-status.tsv`: parameterized route status/size results
- `metadata/api-sample-shapes.tsv`: sanitized API response shapes from read-only checks

