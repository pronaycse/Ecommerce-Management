# Small E-commerce Management System

A small multi-shop e-commerce management app built with **Laravel**.

Roles:

- **Admin** – manages shops, products and shop-owner accounts.
- **Shop Owner** – browses the shared catalog, adds products to their own shop, creates their own products, and customises price / details / thumbnail **per shop**.
- **Customer** (public, no login) – sees only products that are assigned to a shop, with that shop's price / details / thumbnail.

## Requirements

- PHP **8.3+** with extensions: `pdo_sqlite`, `sqlite3`, `mbstring`, `fileinfo`, `xml`, `curl`, `zip`
- Composer 2
- Node.js is **not required** (the UI uses plain inline CSS; no front-end build step)

SQLite is used, so you do not need to install MySQL.

## Setup (step by step)

```bash
# 1. Clone and enter the project
git clone <YOUR_REPO_URL> ecommerce-management
cd ecommerce-management

# 2. Install PHP dependencies
composer install

# 3. Create the environment file and app key
cp .env.example .env          # Windows (cmd): copy .env.example .env
php artisan key:generate

# 4. Create the SQLite database file
touch database/database.sqlite   # Windows (cmd): type nul > database\database.sqlite

# 5. Create tables and demo data
php artisan migrate --seed

# 6. Make uploaded images publicly reachable
php artisan storage:link

# 7. Run the app
php artisan serve
```

Open http://127.0.0.1:8000

> If you serve the app on another host/port, nothing needs to change: image URLs are built from the current request.

## Demo accounts (created by the seeder)

| Role | Email | Password | Shop |
|---|---|---|---|
| Admin | `admin@example.com` | `password` | – |
| Shop owner | `owner1@example.com` | `password` | Rafi Electronics |
| Shop owner | `owner2@example.com` | `password` | Nila Fashion House |
| Shop owner | `owner3@example.com` | `password` | Sajid Grocery |

Login page: http://127.0.0.1:8000/login  
Customer pages (no login): http://127.0.0.1:8000/

The seeder also creates **"Unassigned Demo Product"**, which belongs to no shop. It appears in the admin product list (badge "hidden") but never on any customer page.

## Run the tests

```bash
php artisan test
```

## Data model

| Table | Purpose |
|---|---|
| `users` | `role` = `admin` or `shop_owner` |
| `shops` | `name`, `description`, `owner_id` (unique → one shop per owner) |
| `products` | the **shared catalog**: `name`, `description`, default `price`, default `thumbnail`, `created_by` |
| `shop_product` (pivot) | which shop has which product + shop-specific `price`, `details`, `thumbnail` |

A product is visible to customers **only through** `shop_product`. Customer pages query `$shop->products()`, so a product with no pivot row can never be displayed.
When a shop field is empty (`null`), the product's default value is used.

## Routes overview

| Area | URL |
|---|---|
| Customer | `/` (shops), `/shops/{id}` (products of a shop) |
| Login / logout | `/login`, `POST /logout` |
| Admin | `/admin` , `/admin/shops`, `/admin/products`, `/admin/shop-owners` |
| Shop owner | `/owner`, `/owner/catalog`, `/owner/products/create`, `/owner/shop-products/{product}/edit` |

## Assumptions

1. **One shop per shop owner.** Enforced by a unique `owner_id` on `shops`.
2. Shop owners cannot self-register; only the admin creates their accounts and links them to a shop.
3. A product an owner creates goes into the shared catalog **and** is automatically added to the owner's shop.
4. An owner can edit the *base* data (name, default price/description/thumbnail) **only of products they created**. For every other product they can only change the *shop-specific* price / details / thumbnail, which affects their shop only.
5. Adding a catalog product to a shop copies the product's default price as the shop price (editable afterwards).
6. Deleting a shop owner keeps their shop (it becomes owner-less); deleting a shop or a product removes the related pivot rows and uploaded files.
7. Uploaded images: jpg, jpeg, png, webp, gif, up to 2 MB, stored in `storage/app/public/products`.
