# Odoo Vendor Bridge - Server (Backend)

<p align="center">
  <b>A robust Laravel-based backend server for the Odoo Vendor Bridge platform.</b><br>
  <i>Facilitates RFQs, Quotations, Approvals, and Vendor Management.</i>
</p>

<p align="center">
  <img alt="Laravel" src="https://img.shields.io/badge/Laravel-11.x/12.x-red?logo=laravel&logoColor=white">
  <img alt="PHP" src="https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white">
</p>

---

## 🚀 Overview

This repository contains the backend server for the **Odoo Vendor Bridge**. It acts as an API gateway that enables seamless communication and workflow execution between the company's internal procurement team (using Odoo) and external vendors.

It provides a secure, role-based RESTful API for handling vendors, Requests for Quotation (RFQs), Quotations, Approvals, Purchase Orders (POs), and Invoices.

## ✨ Features

- **Authentication & Security:** JWT-based secure authentication using Laravel Passport, complete with OTP verification features.
- **Role-Based Access Control (RBAC):** Powered by Spatie Permission. Distinct workflows for **Admin**, **Procurement**, **Manager**, and **Vendor** roles.
- **Procurement Workflows:** 
  - Procurement creates RFQs.
  - Vendors submit Quotations against assigned RFQs.
  - Procurement/Managers compare quotations and approve them.
- **Approvals & POs:** Multi-tier approval system for Managers and Admins.
- **Media Management:** Powered by Plank Mediable, integrated with S3 for secure file uploads via Signed URLs.
- **Push Notifications:** Integrated with OneSignal for real-time alerts.
- **Developer & Monitoring Tools:** Includes Laravel Telescope, Horizon, Pulse, and Log Viewer out of the box.

---

## 🛠️ Requirements

- **PHP** `^8.3`
- **Laravel** `12.x` / `13.x`
- **Composer**
- **Database** (PostgreSQL 18.x)
- **Redis** (for Queue and Cache management)

---

## 📦 Setup Instructions

Follow these steps to set up the project locally:

### 1. Clone the repository
```bash
git clone <repository-url>
cd odoo-vendor-bridge/server
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Environment Configuration
Copy the `.env.example` file to create your local `.env`.
```bash
cp .env.example .env
```
Update the `.env` file with your local database credentials, AWS/S3 settings, and OneSignal keys.

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Run Migrations & Seeders
This command will create the required tables and seed default roles/permissions.
```bash
php artisan migrate --seed
```

### 6. Install Passport (OAuth2)
Generate the encryption keys needed to create secure access tokens.
```bash
php artisan passport:install
```

### 7. Create Storage Link
Make uploaded files publicly accessible (if using local driver).
```bash
php artisan storage:link
```

### 8. Start the Development Server
```bash
php artisan serve
```
The API will now be accessible at `http://localhost:8000`.

---

## 🧑‍💻 Key Commands & Tools

### Queue Management
To process background jobs (like sending emails or notifications):
```bash
php artisan queue:work
```
Or, if you are using Horizon for queue monitoring:
```bash
php artisan horizon
```

### Clearing Cache
If you encounter caching issues with config or routes:
```bash
php artisan optimize:clear
```

### Code Quality & Testing
- **Format Code (Pint):**
  ```bash
  ./vendor/bin/pint
  ```
- **Static Analysis (PHPStan):**
  ```bash
  ./vendor/bin/phpstan analyse
  ```
- **Run Tests:**
  ```bash
  php artisan test
  ```

---

## 🔐 Roles & Permissions

- **Admin**: Full system access, can manage users, settings, and override approvals.
- **Procurement**: Can create RFQs, view all vendors, manage Purchase Orders, and manage Invoices.
- **Manager**: Responsible for reviewing and approving RFQs and POs, as well as viewing analytics.
- **Vendor**: Limited access. Can view assigned RFQs, submit Quotations, and view their specific POs and Invoices.

---

## 📚 API Documentation
API documentation is automatically generated.
Access the interactive Swagger UI at: `http://localhost:8000/api/documentation` (if L5-Swagger/Scramble is configured).
