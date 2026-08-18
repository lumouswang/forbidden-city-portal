FROM php:8.2-fpm-alpine

# Install nginx
RUN apk add --no-cache nginx

WORKDIR /app

# Copy all files
COPY . /app/

# Copy nginx config
COPY nginx.conf /etc/nginx/nginx.conf

# Make sure runtime dirs exist
RUN mkdir -p /tmp /var/log/nginx /var/run/nginx /var/lib/nginx/tmp && \
    chown -R nginx:nginx /var/log/nginx /var/run/nginx /var/lib/nginx 2>/dev/null || true

# Expose port
EXPOSE 8080

# Start php-fpm in background, nginx in foreground (Railway restarts on exit)
CMD ["sh", "-c", "php-fpm --allow-to-run-as-root -D && exec nginx -g 'daemon off;'"]