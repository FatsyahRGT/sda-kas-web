#!/bin/sh
set -e

echo "==> Menunggu database MySQL siap di host '${DB_HOST:-db}'..."
while ! nc -z "${DB_HOST:-db}" "${DB_PORT:-3306}"; do
    sleep 2
done
echo "==> Database MySQL terhubung!"

# Generate app key if needed
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo "==> Generating APP_KEY..."
    php artisan key:generate --force
fi

# Ensure storage link
echo "==> Membuat storage symlink..."
php artisan storage:link || true

# Run database migrations
echo "==> Menjalankan database migration..."
php artisan migrate --force

# Seed database if FRESH_SEED=true
if [ "$FRESH_SEED" = "true" ]; then
    echo "==> Seeding database..."
    php artisan db:seed --force
fi

# Cache configuration for production performance
echo "==> Mengoptimalkan cache Laravel..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Set storage directory permissions
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

echo "==> Memulai PHP-FPM..."
exec "$@"
