#!/bin/sh
set -e

# Check if $UID and $GID are set, else fallback to default (1000:1000)
USER_ID=${UID:-1000}
GROUP_ID=${GID:-1000}

# Fix file ownership and permissions using the passed UID and GID
# echo "Fixing file permissions with UID=${USER_ID} and GID=${GROUP_ID}..."
sudo chown -R 1000:1000 /var/www || echo "Some files could not be changed"sudo

# Clear configurations to avoid caching issues in development
echo "Clearing configurations..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

php artisan migrate
php artisan queue:work &

# Run the default command (e.g., php-fpm or bash)
exec "$@"
