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

# Ensure SQLite database exists and has correct permissions
if [ "${DB_CONNECTION}" = "sqlite" ] || [ -z "${DB_CONNECTION}" ]; then
  touch database/database.sqlite
  chown www-data:www-data database/database.sqlite || true
  chmod 664 database/database.sqlite || true
fi

# Run migrations automatically
php artisan migrate --force

# Ensure permissions
chown -R www-data:www-data storage bootstrap/cache database || true

# Start php-fpm in background and nginx in foreground
php-fpm -D || true
nginx -g 'daemon off;'
