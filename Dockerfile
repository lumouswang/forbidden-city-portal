FROM php:8.2-fpm-alpine

# Install nginx
RUN apk add --no-cache nginx

WORKDIR /app

# Copy all files
COPY . /app/

# Copy nginx config
COPY nginx.conf /etc/nginx/nginx.conf

# Make sure runtime dirs exist
RUN mkdir -p /tmp /var/log/nginx && \
    chown -R nginx:nginx /var/log/nginx 2>/dev/null || true

# Expose common ports Railway may use
EXPOSE 80 8080

# Start php-fpm, then nginx in foreground (listens on both 80 and 8080)
CMD ["sh", "-c", "php-fpm --allow-to-run-as-root -D && nginx -g 'daemon off;'"]