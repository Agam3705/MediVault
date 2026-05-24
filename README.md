# MediVault

MediVault is a Laravel-based medical record and care coordination platform built for patients, doctors, and administrators. It focuses on secure record sharing, access control, emergency access, notifications, and auditability.

## Highlights

- Patient and doctor account flows with authentication, verification, and password recovery.
- Medical record management with version history and sharing controls.
- Access grants, access requests, and emergency card workflows.
- In-app messaging, notifications, and support ticket handling.
- Session tracking and audit logs for security visibility.
- Social login support and file generation features through Cloudinary, MongoDB, and DomPDF integration.

## Tech Stack

- Laravel 12
- PHP 8.2+
- MongoDB via mongodb/laravel-mongodb
- Vite, Tailwind CSS, Alpine.js
- Laravel Breeze authentication
- Cloudinary for media handling
- Laravel Socialite for OAuth login
- DomPDF for PDF generation

## Local Setup

1. Install PHP and Node.js dependencies.

```bash
composer install
npm install
```

2. Create your environment file and application key.

```bash
cp .env.example .env
php artisan key:generate
```

3. Configure your database and external service credentials in `.env`.

4. Run database migrations.

```bash
php artisan migrate
```

5. Start the development servers.

```bash
php artisan serve
npm run dev
```

## Build

```bash
npm run build
```

## Testing

```bash
php artisan test
```

## Notes

- The repository uses MongoDB-specific schema/index support.
- Configure any OAuth, Cloudinary, and mail settings before enabling related features.
