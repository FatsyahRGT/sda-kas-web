#!/bin/sh
set -e

# 1. Pastikan file .env ada
if [ ! -f /var/www/.env ]; then
    echo "==> .env tidak ditemukan, membuat otomatis..."
    if [ -f /var/www/.env.docker ]; then
        cp /var/www/.env.docker /var/www/.env
    elif [ -f /var/www/.env.example ]; then
        cp /var/www/.env.example /var/www/.env
    else
        touch /var/www/.env
    fi
fi

# 2. Pastikan vendor dependencies ada
if [ ! -d /var/www/vendor ] || [ ! -f /var/www/vendor/autoload.php ]; then
    echo "==> Menginstall dependencies composer..."
    composer install --no-dev --optimize-autoloader --no-interaction
fi

# 3. Generate APP_KEY jika belum terisi
if ! grep -q '^APP_KEY=base64:' /var/www/.env; then
    echo "==> Generating APP_KEY..."
    php artisan key:generate --force
fi

# 4. Buat folder dan set permission 777 untuk storage & bootstrap/cache
echo "==> Menyiapkan direktori storage & bootstrap/cache..."
mkdir -p /var/www/storage/framework/cache/data
mkdir -p /var/www/storage/framework/sessions
mkdir -p /var/www/storage/framework/views
mkdir -p /var/www/storage/app/public
mkdir -p /var/www/bootstrap/cache

chmod -R 777 /var/www/storage /var/www/bootstrap/cache
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
php artisan storage:link || true

# 5. Tunggu database MySQL siap
echo "==> Menunggu database MySQL di host '${DB_HOST:-db}' port '${DB_PORT:-3306}'..."
MAX_TRIES=30
COUNT=0
until nc -z "${DB_HOST:-db}" "${DB_PORT:-3306}" || [ $COUNT -eq $MAX_TRIES ]; do
    sleep 2
    COUNT=$((COUNT + 1))
done

if [ $COUNT -eq $MAX_TRIES ]; then
    echo "==> Database belum merespon setelah 60s, melanjutkan..."
else
    echo "==> Database MySQL terhubung!"
fi

# 6. Jalankan migrasi
echo "==> Menjalankan database migration..."
php artisan migrate --force || true

# 7. Seed jika diminta
if [ "$FRESH_SEED" = "true" ]; then
    echo "==> Seeding database..."
    php artisan db:seed --force || true
fi

# 8. Optimasi cache untuk production
if [ "$APP_ENV" = "production" ]; then
    echo "==> Optimasi cache Laravel..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

echo "==> Memulai PHP-FPM..."
exec "$@"
