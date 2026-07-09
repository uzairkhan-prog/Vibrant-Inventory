# Vibrant Inventory

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg?style=flat-square)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg?style=flat-square)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg?style=flat-square)](https://opensource.org/licenses/MIT)

---

## About Vibrant Inventory

Vibrant Inventory is an internal back-office inventory and accounting system for a trading/distribution business. It covers the full buy-sell cycle — products, customers, suppliers, purchases, sales, sale returns, payments, expenses and per-account ledgers — with PDF-exportable statements for customers, suppliers, products and expenses.

It's a classic server-rendered Laravel app (Blade + Alpine.js + Tailwind CSS, built with Vite) — no SPA framework, no public API. Access is behind authentication, with a lightweight admin role gate on reporting/analytics.

Live instance: `inventory.vibrantengineering.pk`

---

## Features

- **Products & Categories** — CRUD, packing/unit info, CSV import, per-product ledger
- **Customers & Suppliers** — CRUD with company name, outstanding balance tracking, full account ledger with PDF export
- **Purchases** — purchase orders from suppliers with line items, discount & tax, CSV/invoice export
- **Sales** — sales to customers with line items, discount & tax, an assignable sales **Agent**, CSV/invoice export
- **Sale Returns** — return handling linked back to the original sale
- **Payments** — customer payments and supplier payments, linked to sales/purchases, multiple configurable payment types
- **Expenses** — named expense categories, expense entries, dedicated expense ledger with filtering and PDF export
- **Assets** — simple company asset register
- **Reports & Analytics** — admin-only dashboard and reporting views
- **Authentication** — Laravel Breeze (login, registration, password reset, email verification, profile management)
- **Role-based access** — simple `admin` role gate on sensitive routes (reports/analytics)
- **Client-side PDF export** — ledger/statement PDFs generated in the browser with jsPDF + AutoTable (no server-side rendering dependency)

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12, PHP 8.2+ |
| Auth scaffolding | Laravel Breeze (Blade stack) |
| Frontend | Blade templates, Alpine.js, Tailwind CSS |
| Build tool | Vite |
| Database | MySQL |
| PDF export | jsPDF + jspdf-autotable (client-side) |
| Testing | PHPUnit |

---

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js & npm
- MySQL (or a compatible database)

---

## Installation

1. **Clone the repository**

   ```bash
   git clone https://github.com/uzairkhan-prog/Vibrant-Inventory.git
   cd Vibrant-Inventory
   ```

2. **Install PHP dependencies**

   ```bash
   composer install
   ```

3. **Install JS dependencies**

   ```bash
   npm install
   ```

4. **Configure environment**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Then edit `.env` and set your database connection (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) and mail settings if needed.

5. **Run database migrations**

   ```bash
   php artisan migrate
   ```

6. **Build frontend assets**

   ```bash
   npm run build
   ```

7. **Serve the application**

   ```bash
   php artisan serve
   ```

   Or, for local development with hot reload, queue listener and log tailing running together:

   ```bash
   composer run dev
   ```

The app will be available at `http://localhost:8000`.

---

## Configuration Notes

- Sessions, cache and queue all default to the `database` driver — make sure migrations have run before serving the app.
- Mail defaults to the `log` driver in `.env.example`; set real SMTP credentials for password reset / email verification to actually send mail.
- The `admin` role is stored on the `users` table (`role` column). Routes under the reports/analytics section are protected by an `AdminMiddleware` that checks `Auth::user()->role === 'admin'`. Set a user's role directly in the database (or via `php artisan tinker`) to grant admin access.

---

## Project Structure Highlights

```
app/Http/Controllers/   Product, Category, Customer, Supplier, Purchase, Sale,
                         SaleReturn, Payment (Customer/Supplier), Expense,
                         Asset, Agent, PaymentType, Reports, Analytics, User
app/Models/              Domain models mirroring the controllers above
app/Http/Middleware/     AdminMiddleware (role gate)
resources/views/         Blade views, organized per module, sidebar-based admin layout
routes/web.php           Authenticated application routes
routes/auth.php          Breeze authentication routes
database/migrations/     Full schema history for the modules above
DATABASE_SQL/latest/     Dated raw SQL snapshots used for deployment/restore
```

---

## Testing

```bash
php artisan test
```

Runs against an in-memory SQLite database (configured in `phpunit.xml`). Current coverage is the stock Breeze authentication/profile test suite; business-logic modules (products, sales, purchases, ledgers, etc.) don't yet have dedicated tests.

---

## Deployment

The app is deployed on shared hosting (Apache, via `.htaccess`) at `inventory.vibrantengineering.pk`. Periodic full database snapshots are kept under `DATABASE_SQL/latest/` alongside the versioned migrations.

---

## License

Licensed under the [MIT License](https://opensource.org/licenses/MIT).
