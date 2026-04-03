#!/bin/bash
# StayLBD Deployment Script
# Run from project root: bash core/deploy/deploy.sh

set -e
cd "$(dirname "$0")/.."

echo ">>> Validating environment..."
php deploy/env-validate.php || true

echo ">>> Running migrations..."
php artisan migrate --force

echo ">>> Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo ">>> Caching for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ">>> Deployment complete."
echo ">>> Remember: php artisan queue:work (or Supervisor) for queue processing."
