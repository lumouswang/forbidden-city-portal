FROM php:8.2-fpm-alpine

# Install nginx
RUN apk add --no-cache nginx

WORKDIR /app

# Copy all files
COPY . /app/

# Copy nginx config and custom PHP-FPM pool config
COPY nginx.conf /etc/nginx/nginx.conf
COPY www.conf /usr/local/etc/php-fpm.d/www.conf

# Make sure runtime dirs exist
RUN mkdir -p /tmp /var/log/nginx /var/run/nginx /var/lib/nginx/tmp && \
    chown -R nginx:nginx /var/log/nginx /var/run/nginx /var/lib/nginx 2>/dev/null || true

# Expose ports
EXPOSE 9000 8080

# Start php-fpm in background (listen 9001), nginx in foreground (listen 8080 + 9000)
CMD ["sh", "-c", "php-fpm --allow-to-run-as-root -D && exec nginx -g 'daemon off;'"]