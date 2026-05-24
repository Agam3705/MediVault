#!/usr/bin/env bash
# Exit immediately if a command exits with a non-zero status
set -o errexit

echo "🚀 Starting build process..."

# 1. Install PHP dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# 2. Install Node dependencies and build assets
echo "📦 Installing Node packages & compiling assets..."
npm install
npm run build

# 3. Cache Laravel configurations for production performance
echo "⚙️ Caching Laravel configuration and routes..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Build completed successfully!"
