# ExchoLicense

A centralized licensing, activation, and product management platform built with [Laravel](https://laravel.com?utm_source=chatgpt.com) and [Livewire](https://livewire.laravel.com?utm_source=chatgpt.com) for desktop applications, web applications, hybrid systems, and offline-first software products.

ExchoLicense allows ExchoSoft products such as CoreOps, PGOps, Michelle POS, Luvora, and future software products to share one secure licensing infrastructure.

---

# Features

## Product Management

Manage all software products from a single admin dashboard.

* Add unlimited applications
* Desktop, SaaS, or hybrid product types
* Product editions and tiers
* Product version tracking
* Product pricing management
* Update channels
* Product activation policies

---

## License Key Generation

Generate secure software license keys.

* Lifetime licenses
* Monthly subscriptions
* Annual subscriptions
* Trial licenses
* Device-locked licenses
* Floating licenses
* Multi-device licenses
* Offline activation support

Example key format:

```text
CORX-9F2A-KL88-PQ21
```

---

## Offline Desktop Activation

Supports one-time online activation with fully offline subsequent usage.

### Activation Flow

1. Desktop app sends:

   * product code
   * license key
   * device fingerprint

2. ExchoLicense validates:

   * license status
   * expiry
   * activation limits

3. API returns signed activation payload

4. Application stores encrypted local license file

5. Future launches work fully offline

---

## Cryptographically Signed Licenses

Every license payload is digitally signed using RSA or Ed25519.

This prevents:

* License tampering
* Expiry modification
* Edition spoofing
* Unauthorized activations

---

## Device Monitoring

Track every activated machine.

* Device IDs
* Device names
* Platform information
* IP address
* Last seen timestamps
* Activation history
* Failed activation attempts

---

## Customer Management

Manage customers and software ownership.

* Individual customers
* Company customers
* Multiple licenses per customer
* Subscription tracking
* Renewal tracking

---

## Admin Dashboard

Built entirely with Livewire 4 One File Page Components.

### Dashboard Features

* Product management
* License generation
* Activation monitoring
* Revenue tracking
* Subscription management
* Expiry alerts
* Device analytics
* Failed validation logs
* Revoked license management

---

# Tech Stack

## Backend

* [Laravel](https://laravel.com?utm_source=chatgpt.com)
* [Laravel Sanctum](https://laravel.com/docs/sanctum?utm_source=chatgpt.com)
* [Livewire](https://livewire.laravel.com?utm_source=chatgpt.com)
* [Tailwind CSS](https://tailwindcss.com?utm_source=chatgpt.com)

---

## Admin UI

* Livewire 4 One File pages documentation here (https://livewire.laravel.com/docs/4.x/pages)
* Alpine.js
* ApexCharts
* Responsive dashboard layout

---

## Security

* RSA / Ed25519 signatures
* Device fingerprint verification
* Rate limiting
* Signed activation payloads
* API throttling
* Encrypted local license storage

---

# Project Structure

```text
app/
├── Livewire/
│   ├── Dashboard/
│   ├── Products/
│   ├── Licenses/
│   ├── Customers/
│   ├── Activations/
│   └── Pricing/
│
├── Models/
│   ├── Product.php
│   ├── License.php
│   ├── Customer.php
│   ├── LicenseActivation.php
│   └── Subscription.php
│
├── Services/
│   ├── LicenseService.php
│   ├── SignatureService.php
│   ├── DeviceFingerprintService.php
│   └── ActivationService.php
│
routes/
├── web.php
└── api.php
```

---

# Database Structure

## products

```text
id
name
slug
product_code
platform
current_version
pricing_type
created_at
updated_at
```

---

## licenses

```text
id
product_id
customer_id
license_key
edition
expires_at
max_activations
status
created_at
updated_at
```

---

## license_activations

```text
id
license_id
device_id
device_name
platform
ip_address
activated_at
last_seen_at
created_at
updated_at
```

---

## customers

```text
id
name
email
company
phone
created_at
updated_at
```

---

## subscriptions

```text
id
license_id
billing_cycle
next_billing_date
provider
provider_reference
status
created_at
updated_at
```

---

# API Endpoints

# License Validation

```http
POST /api/v1/licenses/validate
```

### Request

```json
{
  "product": "coreops",
  "license_key": "CORX-XXXX-XXXX",
  "device_id": "hashed-device-id"
}
```

### Success Response

```json
{
  "valid": true,
  "license": {
    "payload": {
      "license_key": "CORX-XXXX-XXXX",
      "edition": "enterprise",
      "expires_at": "2027-01-01",
      "device_id": "hashed-device-id"
    },
    "signature": "base64-signature"
  }
}
```

---

# License Renewal

```http
POST /api/v1/licenses/renew
```

---

# License Status

```http
GET /api/v1/licenses/status
```

---

# Device Deactivation

```http
POST /api/v1/licenses/deactivate
```

---

# Example Desktop Activation Flow

```text
Desktop App
     │
     ▼
POST /api/v1/licenses/validate
     │
     ▼
ExchoLicense validates:
- key
- expiry
- device
- activation limits
     │
     ▼
Signed license payload returned
     │
     ▼
App stores encrypted license.json
     │
     ▼
Future launches run offline
```

---

# Planned Features

* Auto update server
* Download delivery system
* Customer portal
* Billing integration
* Subscription automation
* Invoice generation
* Multi-tenant support
* Usage analytics
* Webhook system
* Team licensing
* Floating network licenses

---

# Admin Dashboard Modules

| Module      | Description              |
| ----------- | ------------------------ |
| Products    | Manage software products |
| Licenses    | Generate and revoke keys |
| Customers   | Manage customers         |
| Activations | Monitor devices          |
| Pricing     | Configure plans          |
| Revenue     | Track earnings           |
| Analytics   | Usage insights           |
| Renewals    | Subscription renewals    |
| Logs        | API and activation logs  |

---

# Example Use Cases

## Desktop Applications

* POS systems
* Inventory systems
* Accounting software
* School management systems
* Offline enterprise applications

---

## SaaS Applications

* Subscription platforms
* Membership systems
* Online dashboards

---

## Hybrid Systems

* Local-first applications
* Sync-enabled software
* Offline-capable enterprise tools

---

# Installation

## Clone Repository

```bash
git clone https://github.com/exchosoft/excholicense.git
```

---

## Install Dependencies

```bash
composer install
npm install
```

---

## Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

---

## Run Migrations

```bash
php artisan migrate
```

---

## Start Development Server

```bash
php artisan serve
npm run dev
```

---

# License Philosophy

ExchoLicense is designed to provide:

* Centralized software licensing
* Secure offline activation
* Unified product management
* Cross-platform licensing
* Long-term scalability for commercial software

---

# Author

Built and maintained by [ExchoSoft](https://exchosoft.com?utm_source=chatgpt.com)

---

# License

Proprietary Commercial Software License.

Unauthorized redistribution or resale is prohibited.
