# RB_C — Redbook One Rebuild

A new Laravel + Vue 3 + Inertia implementation inspired by the redcom.cloud reference capture. The reference is used for product vocabulary and workflow coverage; the interface is intentionally redesigned with a calmer, more editorial visual system.

## Stack

- Laravel 13 with server-side web routes
- Inertia Laravel + Vue 3
- Vite 7
- Custom CSS design system
- SQLite demo data with Eloquent-backed workspace records
- Session authentication at `/admin/login`
- No `routes/api.php` or JSON API layer

## Current vertical slice

- Responsive application shell with workspace switcher, navigation, search, notifications, and mobile sidebar
- Overview dashboard with revenue trend, KPIs, branch performance, live activity, and product velocity
- Inventory catalogue with search and low-stock states
- Product metadata, barcode search, variants, bulk update endpoint, and print-friendly price labels
- Validated add-product flow using an Inertia form and Laravel POST route
- Sales board with a persistent new-sale flow
- Purchase orders with a persistent draft flow
- Accounting overview with live ledger metrics, chart of accounts, and recent journal activity
- Searchable posted journal with debit/credit voucher creation and account-balance updates
- Dynamic management reports derived from sales, purchases, returns, inventory, receivables, and branches
- Persistent add-branch flow with branch performance view
- Employee directory with branch assignment and search
- Warehouse network with capacity/utilization overview and create flow
- Invoice register with status filters and payment activity ledger
- Payment recording that automatically closes fully collected invoices
- POS checkout with product lines, stock validation, and immediate payment capture
- Returns with sale-line validation, refund tracking, and stock restocking
- Stock transfer log between branches with auditable movement records
- Branch-level stock allocations used by POS, returns, and transfer receiving
- Persistent add-contact flow and searchable contacts list
- Company/settings view with local preference controls
- Banking accounts, cheque register, and cost-centre budgets
- BOM recipes and production orders with component consumption and finished-stock posting
- Administrator-only role/access management with role capability map
- Public landing page and three-step workspace onboarding request flow

## Run locally

```bash
php8.5 artisan migrate --seed
php8.5 artisan serve
npm run dev
```

For a production asset build:

```bash
npm run build
php8.5 artisan test
```

The local demo workspace enters through `/admin/login`; the seeded user is intended for development only. The seeded demo workspace includes an admin-facing dataset for branches, products, customers, vendors, sales, purchase orders, and ledger accounts. Forms submit through regular Laravel web routes and return Inertia redirects with session flash feedback; there is no JSON API layer.

The original site mirror remains in the parent workspace at `../reference-site/` and is not used as application code. The rebuild intentionally uses redesigned visuals and server-side Inertia props rather than reproducing the broken reference UI.

# RB_C
