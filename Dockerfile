FROM php:8.2-fpm-alpine

# Install nginx
RUN apk add --no-cache nginx

WORKDIR /app

# Copy all files
COPY . /app/

# Copy nginx config
COPY nginx.conf /etc/nginx/nginx.conf

# Expose port (Railway will set PORT env var)
EXPOSE 8080

# Make sure runtime dirs exist with correct perms
RUN mkdir -p /var/run /var/log/nginx /var/lib/nginx/tmp /run/nginx && \
    chown -R nginx:nginx /var/run /var/log/nginx /var/lib/nginx /run/nginx 2>/dev/null || true

# Start php-fpm, then nginx in foreground
CMD ["sh", "-c", "php-fpm --allow-to-run-as-root -D && nginx -g 'daemon off;'"]