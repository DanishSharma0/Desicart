#!/bin/sh
set -e

cd /var/www/html

# Ensure .env exists
if [ ! -f .env ]; then
  if [ -f .env.example ]; then
    cp .env.example .env
  else
    echo ".env.example not found, create .env manually" && exit 1
  fi
  php artisan key:generate
fi

# Storage link (safe) — migrations are run separately via Render job or manually
php artisan storage:link || true

# Ensure permissions
chown -R www-data:www-data storage bootstrap/cache || true

# Start php-fpm in background and nginx in foreground
php-fpm -D || true
nginx -g 'daemon off;'
