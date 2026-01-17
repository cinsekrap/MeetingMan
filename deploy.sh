#!/bin/bash

# MeetingMan Deploy Script
# Run this after pulling latest code from git
#
# Note: Assets are built locally and committed to git
#       (Node.js not required on server)

set -e  # Exit on error

echo "Starting deployment..."

# Install/update PHP dependencies
echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Run database migrations
echo "Running migrations..."
php artisan migrate --force

# Clear and rebuild caches
echo "Clearing caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
echo "Setting permissions..."
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo "Deployment complete!"
