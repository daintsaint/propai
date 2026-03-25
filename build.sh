#!/bin/bash
set -e

echo "===== Starting Railway Build Process for Laravel 11 ====="

# Install PHP dependencies
echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Install Node.js dependencies and build frontend
echo "Installing Node.js dependencies..."
npm install

echo "Building Vite frontend..."
npm run build

# Laravel optimizations
echo "Running Laravel optimizations..."
php artisan config:cache
php artisan event:cache
php artisan route:cache

# Set up storage directories
echo "Setting up storage directories..."
mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views
chmod -R 775 storage bootstrap/cache

# Create SQLite database directory
mkdir -p data

echo "===== Build Complete ====="
