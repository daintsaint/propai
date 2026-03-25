#!/bin/bash

# Build script for PropAI Laravel application
# This script runs during Railway deployment

set -e

echo "Starting Laravel build process..."

# Install system dependencies
echo "Installing system dependencies..."
apt-get update
apt-get install -y git unzip curl

# Install PHP dependencies
echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Install Node.js dependencies and build assets
echo "Installing Node.js dependencies and building assets..."
npm install
npm run build

# Create necessary directories
echo "Creating necessary directories..."
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Set proper permissions
echo "Setting permissions..."
chmod -R 777 storage bootstrap/cache

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    php artisan key:generate
fi

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Clear and cache configuration
echo "Caching configuration and routes..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Seed vertical bundles if first deployment
echo "Seeding initial data..."
php artisan db:seed --class=VerticalBundleSeeder --force

echo "Build completed successfully!"
