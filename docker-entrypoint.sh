#!/bin/bash
set -e

# Render provides the PORT environment variable (default 80 if unset)
PORT="${PORT:-80}"

echo "Configuring Apache to listen on port: $PORT"
sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:$PORT>/g" /etc/apache2/sites-available/000-default.conf

# Ensure required storage and cache directories exist with proper permissions
mkdir -p /var/www/html/storage/framework/{cache,sessions,views}
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# If SQLite is selected and database file doesn't exist, initialize it
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    mkdir -p /var/www/html/database
    if [ ! -f /var/www/html/database/database.sqlite ]; then
        touch /var/www/html/database/database.sqlite
    fi
    chown -R www-data:www-data /var/www/html/database
    chmod -R 775 /var/www/html/database
fi

# Ensure storage link exists
php artisan storage:link --force || true

# Run migrations automatically if DB is provided
if [ -n "$DB_HOST" ] || [ -n "$DB_URL" ] || [ "$DB_CONNECTION" = "sqlite" ]; then
    echo "Running database migrations..."
    php artisan migrate --force || echo "Migration skipped or failed (check DB connection)."
fi

# Cache configuration, routes, and views for production performance
echo "Caching Laravel configuration and routes..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "Starting Apache on port $PORT..."
exec "$@"
