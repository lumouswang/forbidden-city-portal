#!/bin/sh
set -e

# Detect Railway's expected port
RAILWAY_PORT="${PORT:-8080}"

# Substitute PORT in nginx config
sed -i "s/listen 8080/listen ${RAILWAY_PORT}/g" /etc/nginx/nginx.conf

echo "Starting php-fpm..."
php-fpm --allow-to-run-as-root -D

echo "Starting nginx on port ${RAILWAY_PORT}..."
exec nginx -g 'daemon off;'