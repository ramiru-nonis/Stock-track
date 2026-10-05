# 📦 Simple Stock Track

<p align="center">
  <strong>Modern Multi-Role Inventory Management System & REST API</strong>
</p>

---

## 📖 Project Overview

**Simple Stock Track** is a full-featured, multi-role web application and REST API for inventory tracking and warehouse management built with **Laravel 11**, **Livewire 3**, **Alpine.js**, and **Tailwind CSS**.

The system enables businesses to maintain real-time visibility over product stocks, track all inventory movements (Stock In / Stock Out), receive low-stock alerts, convert financial valuations between LKR and USD, and manage staff accounts securely with strict Role-Based Access Control (RBAC).

---

## 💼 Business Problem

Small and medium-sized retail businesses face significant operational risks:
- **Inventory Discrepancies**: Lack of transaction history leads to unaccounted stock loss or theft.
- **Stockouts & Overstocking**: Inability to monitor threshold limits results in missed sales or tied-up capital.
- **Role & Security Risks**: Non-administrative staff having unrestricted access to modify pricing or delete products.
- **Multi-Currency Calculation Overhead**: Difficulty viewing business valuation in both local (LKR) and foreign (USD) currencies.

**Simple Stock Track** solves these challenges by enforcing atomic inventory movements, automated low-stock notifications, role-restricted administrative features, audit logs, dual-currency reporting, and a robust REST API for external integrations.

---

## 👥 Role Capabilities

### 👑 Owner Capabilities
- **Product Management**: Full CRUD (Create, Read, Update, Soft-Delete) for products, SKUs, pricing, threshold limits, and images.
- **Category Management**: Create, edit, and delete product categories.
- **Financial Analytics**: View total inventory valuation in both LKR and real-time USD.
- **Audit Logs (`/stock-history`)**: Access complete, filterable movement history (Date Range, User, Type, Reason, Notes).
- **Staff Administration (`/staff`)**: Create staff accounts, toggle active/inactive status, and delete staff accounts.

### 👷 Staff Capabilities
- **Inventory Dashboard**: View product counts, category counts, low-stock warnings, and basic metrics.
- **Stock In (`/stock-in`)**: Record incoming stock shipments with reason notes.
- **Stock Out (`/stock-out`)**: Record outgoing stock dispatches with quantity validation.
- **Low Stock Alerts (`/low-stock`)**: View real-time alert lists for products at or below their low-stock threshold.
- **Access Restrictions**: Restricted from editing product prices, deleting items, viewing staff management, or accessing owner-only history logs.

---

## 🛠️ Technology Stack

- **Backend Framework**: Laravel 11.x
- **Frontend Engine**: Livewire 3.x, Alpine.js, Blade Views
- **Styling**: Tailwind CSS + Custom Dark/Light Theme System (WCAG AA Contrast Compliant)
- **Authentication & Security**: Laravel Jetstream + Fortify + Sanctum API Tokens
- **Database & Storage**: MySQL / SQLite, dual-tier Cloudinary CDN & MySQL BLOB image storage engine
- **Third-Party Services**: ExchangeRate-API (live LKR/USD rates), Cloudinary API (cloud media storage)

---

## 📋 System Requirements

- **PHP**: `>= 8.2` (Extensions: `OpenSSL`, `PDO`, `Mbstring`, `Tokenizer`, `XML`, `Ctype`, `JSON`, `GD` / `Fileinfo`)
- **Composer**: `>= 2.x`
- **Node.js**: `>= 18.x` & **NPM**: `>= 9.x`
- **MySQL**: `>= 8.0` or **MariaDB**: `>= 10.4`

---

## 🚀 Installation & Setup

### 1. Clone Repository & Install Dependencies
```bash
git clone https://github.com/ramiru-nonis/Stock-track.git
cd simplestocktrack
composer install
npm install
```

### 2. Environment Configuration
Copy the example environment file and generate the application key:
```bash
cp .env.example .env
php artisan key:generate
```

### 3. MySQL Database Configuration
Configure your database connection in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simplestocktrack
DB_USERNAME=root
DB_PASSWORD=
```

#### MySQL Packet Size Configuration
For storing high-resolution image fallbacks in MySQL BLOB storage, ensure MySQL's `max_allowed_packet` is set to 64MB:
- **XAMPP (`my.ini`)**: Set `max_allowed_packet=64M` under `[mysqld]`.
- **Runtime SQL execution**:
  ```sql
  SET GLOBAL max_allowed_packet=67108864;
  ```

---

## 🗄️ Migrations & Database Seeding

Run database migrations and seed default administrative and staff user accounts:
```bash
php artisan migrate --seed
```

### Default Credentials
| Role | Email | Password |
| :--- | :--- | :--- |
| **Owner** | `owner@example.com` | `password` |
| **Staff** | `staff@example.com` | `password` |

---

## 🌐 Running Development Servers

### 1. Run Asset Compiler (Vite)
```bash
npm run dev
```

### 2. Run Laravel Local Server
```bash
php artisan serve
```
Access the application at `http://localhost:8000`.

---

## 🔄 Queue Worker & Scheduler Instructions

### Running Queue Worker
If asynchronous background jobs are enabled for notifications or heavy tasks:
```bash
php artisan queue:work
```

### Running Task Scheduler
To trigger scheduled tasks (e.g. currency rate refresh or periodic cleanup):
```bash
php artisan schedule:run
```
*For production servers, add the following cron entry:*
```cron
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 💱 ExchangeRate-API Environment Setup

The application fetches real-time currency conversion rates (LKR to USD).

1. Register for a free API key at [ExchangeRate-API](https://www.exchangerate-api.com/).
2. Add your credentials to `.env`:
   ```env
   EXCHANGE_RATE_API_KEY=your_api_key_here
   SHOP_BASE_CURRENCY=LKR
   SHOP_TARGET_CURRENCY=USD
   ```
3. `ExchangeRateService` caches conversion rates for 5 minutes to optimize performance and prevent rate-limiting, falling back safely if offline.

---

## ☁️ Cloudinary Image Configuration

Product images support dual-mode storage (Cloudinary CDN + Local DB BLOB):

1. Register at [Cloudinary](https://cloudinary.com/) and copy your credentials.
2. Add credentials to `.env`:
   ```env
   CLOUDINARY_CLOUD_NAME=your_cloud_name
   CLOUDINARY_API_KEY=your_api_key
   CLOUDINARY_API_SECRET=your_api_secret
   ```

---

## 🔑 Sanctum / REST API Usage

### 1. Authenticate & Obtain Token
```http
POST /api/login
Content-Type: application/json

{
  "email": "owner@example.com",
  "password": "password"
}
```
**Response**:
```json
{
  "token": "1|sanctum_api_token_here",
  "user": { "id": 1, "name": "Owner User", "role": "owner" }
}
```

### 2. Access Protected Endpoints
Include `Authorization: Bearer <token>` in headers:

| Method | Endpoint | Description | Access |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/products` | List all products with search & pagination | Owner / Staff |
| `POST` | `/api/products` | Create a new product | Owner Only |
| `GET` | `/api/products/{id}` | Get product details | Owner / Staff |
| `PUT` | `/api/products/{id}` | Update product details | Owner Only |
| `DELETE` | `/api/products/{id}` | Soft-delete product | Owner Only |
| `GET` | `/api/low-stock` | List low stock products | Owner / Staff |
| `GET` | `/api/categories` | List categories | Owner / Staff |
| `POST` | `/api/categories` | Create category | Owner Only |
| `GET` | `/api/stock-movements` | List stock audit movements | Owner / Staff |
| `POST` | `/api/stock-movements` | Record Stock In / Stock Out | Owner / Staff |
| `GET` | `/api/staff` | List staff members | Owner Only |
| `POST` | `/api/staff` | Register new staff member | Owner Only |
| `DELETE` | `/api/staff/{id}` | Delete staff member | Owner Only |

---

## 🧪 Running Automated Tests

Run the full PHPUnit / Pest automated test suite:
```bash
php artisan test
```
**Test Results**:
```text
Tests:    14 skipped, 39 passed (95 assertions)
Duration: 15.89s
Status:   100% PASSING
```

---

## 🏛️ Architecture & Security Overview

### Architecture Highlights
- **Domain Service Layer**: Decoupled business logic inside [`StockService`](file:///c:/Users/Ramir/simplestocktrack/app/Services/StockService.php), [`CloudinaryService`](file:///c:/Users/Ramir/simplestocktrack/app/Services/CloudinaryService.php), and [`ExchangeRateService`](file:///c:/Users/Ramir/simplestocktrack/app/Services/ExchangeRateService.php).
- **Policy Authorization**: Policy classes ([`ProductPolicy`](file:///c:/Users/Ramir/simplestocktrack/app/Policies/ProductPolicy.php)) and middleware ([`EnsureOwner`](file:///c:/Users/Ramir/simplestocktrack/app/Http/Middleware/EnsureOwner.php)) enforce fine-grained access control.
- **Fail-Safe Image Engine**: Prefers Cloudinary CDN delivery; falls back gracefully to MySQL database BLOB storage without breaking user experience.

---

## 🛡️ SECURITY CONTROLS

| Threat | Mitigation Used in System |
| :--- | :--- |
| **Unauthorized Product Modification** | Policy authorization (`ProductPolicy`) and `EnsureOwner` middleware restricting CRUD operations exclusively to Owner roles. |
| **SQL Injection** | PDO parameterized queries and Eloquent ORM query bindings throughout the database layer. |
| **CSRF (Cross-Site Request Forgery)** | Built-in Laravel CSRF token verification middleware (`VerifyCsrfToken`) on all POST/PUT/DELETE web routes. |
| **XSS (Cross-Site Scripting)** | Automatic Blade HTML entity escaping (`{{ }}`), Livewire state sanitation, and strict input validation. |
| **API Unauthorized Access** | Stateful & Token-based authentication via Laravel Sanctum Bearer tokens and role checks on API endpoints. |
| **Negative / Incorrect Stock** | Database transaction boundaries (`DB::transaction`), pessimistic row locking (`lockForUpdate`), and validation rules inside `StockService`. |
| **API Key Exposure** | Environment configuration (`.env`) for ExchangeRate-API & Cloudinary keys, strictly excluded from version control (`.gitignore`). |
| **External API Failure** | HTTP client request timeouts, try/catch exception handling, and local cache fallbacks for currency conversion & media uploading. |

---

## 📄 License

This application is open-source software licensed under the [MIT License](https://opensource.org/licenses/MIT).
