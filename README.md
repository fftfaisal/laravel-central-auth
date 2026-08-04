# Central Authentication Server (SSO Identity Provider)

A modern, central Single Sign-On (SSO) Identity Provider built with **Laravel 13.x**, **Vue 3**, **Inertia.js**, **TypeScript**, and **Laravel Passport (OAuth2)**.

This central server acts as an OAuth2 Authorization Server (like Google Accounts or Okta), allowing multiple independent tenant applications to authenticate users through a single, central login portal.

---

## 🌟 Key Features

* **Central OAuth2 Identity Provider**: Powered by Laravel Passport (Authorization Code Grant flow).
* **Account Portal Dashboard**: Displays active account status, connected tenant applications, and security specifications.
* **OAuth Application Management**: Full CRUD interface for registering, managing, editing, and revoking tenant OAuth clients.
* **Vue 3 Consent Dialog**: Custom Inertia consent screen (`/oauth/authorize`) matching the modern dark-mode design system.
* **Security & Authentication**:
  * Passkey & Two-Factor Authentication (2FA)
  * Active Browser Session Management
  * Email Verification & Password Resets
* **Protected Profile API**: `GET /api/user` endpoint secured with Passport's `auth:api` guard for tenant profile fetching.
* **Single Sign-Out (SLO)**: `GET /sso/logout` endpoint for central session destruction upon tenant logout.

---

## 🚀 Tech Stack

* **Backend**: Laravel 13.x, Laravel Passport v13, Laravel Fortify
* **Frontend**: Vue 3, Inertia.js v2, TypeScript, Tailwind CSS v4, `@lucide/vue`
* **Testing**: Pest PHP (100% test pass rate across 44 feature & unit tests)
* **Build System**: Vite v8 + Wayfinder typed route generation

---

## 🛠️ Local Installation & Setup

### Prerequisites

* PHP >= 8.3
* Composer v2
* Node.js >= 22
* pnpm >= 9

### Step-by-Step Setup

1. **Clone the repository and enter directory**:
   ```bash
   git clone https://github.com/fftfaisal/laravel-central-auth.git
   cd laravel-central-auth
   ```

2. **Run Composer Setup** (Installs packages, copies `.env`, generates app key, runs migrations, installs & builds npm dependencies):
   ```bash
   composer setup
   ```

3. **Install & Initialize Passport**:
   ```bash
   php artisan passport:install
   ```

4. **Seed Database** (Populates test user and default tenant OAuth clients):
   ```bash
   php artisan db:seed
   ```

5. **Start Development Server**:
   ```bash
   composer dev
   ```

---

## 🔑 Pre-seeded Local Test Credentials

### Central User Account
* **Email**: `user@central.test`
* **Password**: `password`

### Pre-configured OAuth Tenants

| Tenant Name | Client ID | Client Secret | Callback URL |
| :--- | :--- | :--- | :--- |
| **Tenant App One** | `11111111-1111-1111-1111-111111111111` | `app-one-secret` | `http://app-one.local.test/auth/callback` |
| **Tenant App Two** | `22222222-2222-2222-2222-222222222222` | `app-two-secret` | `http://app-two.local.test/auth/callback` |

---

## 🔗 Testing the OAuth Authorization Flow

You can test the OAuth authorization prompt directly in your browser:

```text
http://127.0.0.1:8000/oauth/authorize?client_id=11111111-1111-1111-1111-111111111111&redirect_uri=http://app-one.local.test/auth/callback&response_type=code&state=xyz123
```

1. If unauthenticated, you will be prompted to log in (`user@central.test` / `password`).
2. Once logged in, the Vue 3 consent screen will display requested permissions.
3. Clicking **Authorize** redirects to your tenant callback with an authorization code (`?code=...`).

---

## 🧪 Testing & Quality Assurance

Run the automated Pest test suite:

```bash
php artisan test
```

Run frontend linting & type checks:

```bash
pnpm run lint:check
pnpm run types:check
```

Build production assets:

```bash
pnpm run build
```

---

## 📄 License

The Laravel framework and this SSO application are open-sourced software licensed under the [MIT license](LICENSE).
