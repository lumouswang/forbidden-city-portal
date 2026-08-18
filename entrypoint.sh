#!/bin/sh
set -e

# Use Railway's PORT env var, default to 8080
RAILWAY_PORT="${PORT:-8080}"

echo "=== ENTRYPOINT START ==="
echo "Railway PORT: ${RAILWAY_PORT}"
echo "Substituting __PORT__ in nginx.conf..."

# Substitute the placeholder with the actual port
sed -i "s/listen __PORT__/listen ${RAILWAY_PORT}/g" /etc/nginx/nginx.conf

echo "nginx.conf after substitution:"
grep "listen " /etc/nginx/nginx.conf

echo "Starting php-fpm..."
php-fpm --allow-to-run-as-root -D

echo "Starting nginx on port ${RAILWAY_PORT}..."
exec nginx -g 'daemon off;'