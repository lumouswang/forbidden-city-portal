FROM php:8.2-fpm-alpine

# Install nginx and supervisor (to keep both running)
RUN apk add --no-cache nginx supervisor

WORKDIR /app

# Copy all files
COPY . /app/

# Copy nginx config
COPY nginx.conf /etc/nginx/nginx.conf

# Copy supervisor config (keeps nginx and php-fpm alive)
COPY supervisord.conf /etc/supervisord.conf

# Make sure runtime dirs exist
RUN mkdir -p /tmp /var/log/nginx /var/log/supervisor /var/run && \
    chown -R nginx:nginx /var/log/nginx 2>/dev/null || true

# Expose port
EXPOSE 8080

# Use supervisord to manage both nginx and php-fpm in foreground
CMD ["supervisord", "-c", "/etc/supervisord.conf", "-n"]