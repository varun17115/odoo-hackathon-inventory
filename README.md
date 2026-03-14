# Invento Market

A Laravel 12 inventory management system built for the Odoo Hackathon. Covers the full warehouse lifecycle — receipts, transfers, deliveries, stock adjustments, reorder rules, and a real-time dashboard.

---

## Requirements

- PHP 8.2+
- Composer
- Node.js 18+ & npm
- SQLite (default) or MySQL/PostgreSQL

---

## Setup

**1. Clone and install dependencies**

```bash
git clone <repo-url>
cd invento-market-main
composer install
npm install
```

**2. Configure environment**

```bash
cp .env.example .env
php artisan key:generate
```

The default config uses SQLite — no database server needed. If you want MySQL/PostgreSQL, update these values in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=invento_market
DB_USERNAME=root
DB_PASSWORD=
```

**3. Run migrations and seed**

```bash
php artisan migrate --seed
```

**4. Build frontend assets**

```bash
npm run build
```

**5. Start the dev server**

```bash
php artisan serve
```

Visit [http://localhost:8000](http://localhost:8000)

---

## Default Credentials

After seeding, a default user is created:

| Field    | Value              |
|----------|--------------------|
| Email    | test@example.com   |
| Password | password           |

To create an admin user, register via `/register` or update the user's `role` column to `admin` directly in the database.

---

## User Roles

| Role    | Access                                                                 |
|---------|------------------------------------------------------------------------|
| `admin` | Full access — CRUD on all resources, user management, stock adjustments, reorder rules |
| `staff` | Read access + create receipts, deliveries, and transfers               |

---

## Features

- Dashboard with KPIs, low-stock alerts, and activity feed
- Product & category management
- Multi-warehouse support with rack/location tracking
- Supplier management
- Receipts (goods-in) with verification workflow
- Deliveries with pick → pack → ship workflow
- Inter-warehouse transfers
- Stock adjustments (admin only)
- Reorder rules with triggered alerts
- Stock movement ledger
- Inventory summary by warehouse and category
- User management with role-based access control
- Password reset via OTP

---

## One-Command Setup (via Composer script)

```bash
composer run setup
```

This runs `composer install`, copies `.env`, generates the app key, runs migrations, installs npm packages, and builds assets.

---

## Running in Dev Mode (with Vite HMR)

```bash
composer run dev
```

This concurrently starts the Laravel server, queue worker, log watcher (Pail), and Vite dev server.

---

## License

MIT
