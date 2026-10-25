#!/bin/bash
set -e

# Render provides the PORT environment variable (default 80 if unset)
PORT="${PORT:-80}"

echo "Configuring Apache to listen on port: $PORT"
sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:$PORT>/g" /etc/apache2/sites-available/000-default.conf

# Ensure required storage and cache directories exist
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache
touch /var/www/html/storage/logs/laravel.log

# If SQLite is selected and database file doesn't exist, initialize it
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    mkdir -p /var/www/html/database
    if [ ! -f /var/www/html/database/database.sqlite ]; then
        touch /var/www/html/database/database.sqlite
    fi
fi

# Ensure storage link exists
php artisan storage:link --force || true

# Run package discovery now that runtime environment is available
php artisan package:discover --ansi || true

# Run database migrations and seeding if DB is provided
if [ -n "$DB_HOST" ] || [ -n "$DB_URL" ] || [ "$DB_CONNECTION" = "sqlite" ]; then
    echo "Running database migrations..."
    php artisan migrate --force || echo "Migration skipped or failed (check DB connection)."
    echo "Seeding initial database records..."
    php artisan db:seed --force || echo "Seeding skipped or already done."
fi

# Cache configuration, routes, and views for production performance
echo "Caching Laravel configuration and routes..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# CRITICAL: Fix all permissions AFTER artisan commands run as root
# This ensures Apache (www-data) can read/write logs, sessions, views, and database
touch /var/www/html/storage/logs/laravel.log
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database 2>/dev/null || true
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database 2>/dev/null || true

echo "Starting Apache on port $PORT..."
exec "$@"
