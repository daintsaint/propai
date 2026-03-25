#!/bin/bash
set -e

echo "===== Starting Railway Build Process ====="

# Install PHP dependencies
echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Install Node.js dependencies and build frontend
echo "Installing Node.js dependencies..."
npm install

echo "Building Vue 3 frontend..."
npm run build

# Laravel optimizations
echo "Running Laravel optimizations..."
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions for storage
echo "Setting up storage directories..."
mkdir -p storage/logs storage/framework/cache storage/framework/views storage/framework/sessions
chmod -R 775 storage bootstrap/cache

echo "===== Build Complete ====="
